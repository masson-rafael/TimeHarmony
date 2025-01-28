<?php

/**
 * @author Thibault Latxague
 * @brief Controller de la page des groupes
 * @version 0.2
 */

/**
 * Undocumented class
 */
class ControllerGroupes extends Controller
{
    /**
     * Constructeur par défaut
     *
     * @param \Twig\Environment $twig Environnement twig
     * @param \Twig\Loader\FilesystemLoader $loader Loader de fichier
     */
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }

    /**
     * Fonction qui permet de lister les groupes dont l'utilisateur connecté est le chef
     * @param array|null $erreurs tableau des erreurs éventuelles
     * @param bool|null $contientErreurs true si le tableau contient des erreurs, false sinon
     * @return void
     */
    public function lister(?array $erreurs = null, ?bool $contientErreurs = false): void
    {
        $pdo = $this->getPdo();
        $manager = new GroupeDao($pdo);
        $tableauGroupes = $manager->getGroupesFromUserId($_SESSION['utilisateur']->getId());

        $template = $this->getTwig()->load('groupes.html.twig'); // Generer la page de réinitialisation mdp avec tableau d'erreurs
        echo $template->render(
            array(
                'menu' => "groupes",
                'groupes' => $tableauGroupes == null ? null : $tableauGroupes['groupe'],
                'message' => $erreurs,
                'nombrePersonnes' => $tableauGroupes == null ? null : $tableauGroupes['nombrePersonnes'],
                'contientErreurs' => $contientErreurs
            )
        );
    }

    /**
     * Fonction permettant de supprimer le groupe dont on récupère l'id du lien
     * @return void
     */
    public function supprimer(): void
    {
        $id = $_GET['id'];
        $pdo = $this->getPdo();
        $manager = new GroupeDao($pdo);
        $tableauGroupes = $manager->supprimerGroupe($id);
        $tableauMessages[] = "Le groupe a été supprimé avec succès !";
        $this->lister($tableauMessages, false);
    }

    /**
     * Fonction dont le but est d'afficher la page de modification lorsque le bouton modifier d'un groupe est cliqué
     * @return void 
     */
    public function afficherPageModification(): void
    {
        $id = $_GET['id'];
        $manager = new GroupeDao($this->getPdo());
        $groupeCourant = $manager->find($id);
        $membres = $manager->getUsersFromGroup($id);
        $contacts = $this->getListeContacts();
        $template = $this->getTwig()->load('groupes.html.twig'); // Generer la page de réinitialisation mdp avec tableau d'erreurs

        // Créer un tableau idsMembres qui contient les IDs des membres du groupe
        $idsMembres = array_map(function ($membre) {
            return $membre['idUtilisateur'];
        }, $membres);

        echo $template->render(array(
            'menu' => "groupes",
            'modification' => true,
            'groupeCourant' => $groupeCourant,
            'contacts' => $contacts,
            'idsMembres' => $idsMembres,
        ));
    }

    /**
     * Fonction donc le but est de renvoyer la liste des contacts de l'utilisateur qui créé le groupe
     * @return array|null tableau des contacts
     */
    public function getListeContacts(): ?array
    {
        $pdo = $this->getPdo();
        $managerUtilisateur = new UtilisateurDao($pdo);
        $contacts = $managerUtilisateur->findAllContact($_SESSION['utilisateur']->getId());
        return $contacts;
    }

    /**
     * Fonction appellee lors du clic sur le bouton creer un groupe. Appel du twig de création
     * @return void
     */
    public function ajouter(): void
    {
        $contacts = $this->getListeContacts();
        $template = $this->getTwig()->load('groupes.html.twig');
        echo $template->render(array('menu' => "groupes", 'creation' => true, 'contacts' => $contacts));
    }

    /**
     * Fonction qui s'occupe de la création d'un groupe. Verif des champs du formulaire
     * @return void
     */
    public function creer(): void
    {
        $nom = htmlspecialchars($_POST['nom'],ENT_NOQUOTES);
        $description = htmlspecialchars($_POST['description'],ENT_NOQUOTES);

        $tableauErreurs = [];
        $tableauContacts = $_POST['contacts']; //2, 3, 4, 5 -> id des utilisateurs + verif classe
        $nomValide = utilitaire::validerNom($nom, $tableauErreurs);
        $descriptionValide = utilitaire::validerDescription($description, $tableauErreurs);

        $manager = new GroupeDao($this->getPdo());
        $groupeExiste = $manager->groupeExiste($nom, $description);

        if ($groupeExiste) {
            $tableauErreurs[] = "Le groupe existe déjà";
            $this->lister($tableauErreurs);
        } elseif ($nomValide && $descriptionValide && !$groupeExiste) {
            // Etape 1 : créer groupe
            $manager = new GroupeDao($this->getPdo());
            $manager->creerGroupe($_SESSION['utilisateur']->getId(), $nom, $description);
            $manager = new GroupeDao($this->getPdo());
            $groupe = $manager->getGroupe($_SESSION['utilisateur']->getId(), $nom, $description);

            // Etape 2 : ajouter membres
            $this->ajouterMembres($groupe->getId(), $tableauContacts);

            $tableauErreurs[] = "Le groupe a été créé avec succès !";
            $this->lister($tableauErreurs, false);
        } else {
            $tableauErreurs[] = "Le groupe existe déjà !";
            $this->lister($tableauErreurs, true);
    }

    /**
     * Fonction qui permet d'ajouter des utilisateurs à un groupe
     * @param int|null $idGroupe id du groupe dont on veut ajouter l'utilisateur
     * @param array|null $contacts tableau d'utilisateurs que l'on veut ajouter
     * @return void
     */
    public function ajouterMembres(?int $idGroupe, ?array $contacts): void
    {
        $manager = new GroupeDao($this->getPdo());
        $manager->ajouterMembreGroupe($idGroupe, $_SESSION['utilisateur']->getId());

        if ($contacts) {   // Condition si est vide
            foreach ($contacts as $contact) {
                $manager->demanderAjoutMembreGroupe($idGroupe, $contact);
            }
        }
    }

    /**
     * Fonction qui permet de modifier le nom, la description et la composition d'un groupe 
     * @return void
     */
    public function modifier(): void
    {
        $nomGroupe = $_POST['nom']; // Nom du groupe
        $description = $_POST['description']; // Description du groupe

        if (isset($_POST['contacts']))
        {
            $checkedUsers = $_POST['contacts']; // Tableau des utilisateurs cochés
        } else {
            $checkedUsers = null;
        }

        $id = $_GET['id']; // id du groupe
        $currentIdUser = $_SESSION['utilisateur']->getId(); // id de l'utilisateur actuel

        $messages = [];

        $nomValide = utilitaire::validerNom($nomGroupe, $messages);
        $descriptionValide = utilitaire::validerDescription($description, $messages);
        // pas besoin de vérifier les contacts

        if ($nomValide && $descriptionValide) {
            $pdo = $this->getPdo();
            $manager = new GroupeDao($pdo);
            $managerUtilisateur = new UtilisateurDao($pdo);

            // Récupérer les membres du groupe
            $membres = $manager->getUsersFromGroup($id);

            // Convertir les ids des utilisateurs cochés en entier
            if (isset($checkedUsers)){
                foreach ($checkedUsers as $key => $value) {
                    $checkedUsers[$key] = (int)$value;
                }
            }
            
            // Parcourir les membres actuels du groupe
            foreach ($membres as $membre) {
                // Supprimer les utilisateurs non cochés qui font partie du groupe à part l'utilisateur actuel
                if ($membre['idUtilisateur'] != $currentIdUser && (!isset($checkedUsers) || (!in_array($membre['idUtilisateur'], $checkedUsers)))) {
                    $manager->supprimerMembreGroupe($id, $membre['idUtilisateur']);

                    // Récupérer le nom de l'utilisateur supprimé
                    $deletedUser = $managerUtilisateur->find($membre['idUtilisateur']);
                    $nom = $deletedUser->getNom();
                    $prenom = $deletedUser->getPrenom();

                    $messages[] = "Suppression de "  . $prenom . " " . $nom . " du groupe \"" . $nomGroupe . "\" ";
                }
            }

            // Ajouter les utilisateurs cochés qui ne font pas encore partie du groupe
            if(isset($checkedUsers)){
                foreach ($checkedUsers as $userId) {
                    if (!in_array($userId, array_column($membres, 'idUtilisateur'))) {
                        $manager->ajouterMembreGroupe($id, $userId);
    
                        // Récupérer le nom de l'utilisateur ajouté
                        $addedUser = $managerUtilisateur->find($userId);
                        $nom = $addedUser->getNom();
                        $prenom = $addedUser->getPrenom();
    
                        $messages[] = "Ajout de " . $prenom . " " . $nom . " au groupe \"" . $nomGroupe . "\" ";
                    }
                }
            }

            // Ajouter ou modifier les utilisateurs dans le groupe
            $messages[] = "Groupe modifié avec succès !";
            $manager->modifierGroupe($id, $nomGroupe, $_POST['description']);
            $this->lister($messages, false); // Afficher la page de modification avec les messages
        } else {
            $this->lister($messages, true); // Afficher la page de modification avec les messages
        }
    }
}
