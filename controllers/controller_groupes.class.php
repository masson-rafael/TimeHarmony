<?php

/**
 * @author Thibault Latxague
 * @describe Controller de la page des groupes
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
     * @return void
     */
    public function lister(): void {
        $tableauGroupes = $this->listerGroupesUtilisateur();
        //$nombrePersonnes = $this->getNombrePersonnes($tableauGroupes);

        $template = $this->getTwig()->load('groupes.html.twig'); // Generer la page de réinitialisation mdp avec tableau d'erreurs
        echo $template->render(array('groupes' => $tableauGroupes));
    }

    /**
     * Fonction qui renvoie la liste des groupes de l'utilisateur connecté (no object)
     * @return array|null tableau des groupes
     */
    public function listerGroupesUtilisateur(): ?array {
        $pdo = $this->getPdo();
        $manager = new GroupeDao($pdo);
        $tableauGroupes = $manager->getGroupeFromUserId($_SESSION['utilisateur']->getId());
        return $tableauGroupes;
    }

    /**
     * Fonction permettant de supprimer le groupe dont on récupère l'id du lien
     * @return void
     */
    public function supprimer(): void {
        $id = $_GET['id'];
        $pdo = $this->getPdo();
        $manager = new GroupeDao($pdo);
        $tableauGroupes = $manager->supprimerGroupe($id);
        $this->lister();
    }

    /**
     * Fonction dont le but est d'afficher la page de modification lorsque le bouton modifier d'un groupe est cliqué
     * @return void 
     */
    public function afficherPageModification(): void {
        $id = $_GET['id'];
        $manager = new GroupeDao($this->getPdo());
        $groupeCourant = $manager->find($id);
        $contacts = $this->getListeContacts();
        $template = $this->getTwig()->load('groupes.html.twig'); // Generer la page de réinitialisation mdp avec tableau d'erreurs
        echo $template->render(array('modification' => true, 'groupeCourant' => $groupeCourant, 'contacts' => $contacts));
    }

    /**
     * Fonction donc le but est de renvoyer la liste des contacts de l'utilisateur qui créé le groupe
     * @return array|null tableau des contacts
     */
    public function getListeContacts(): ?array {
        $pdo = $this->getPdo();
        $managerUtilisateur = new UtilisateurDao($pdo);
        $contactsId = $managerUtilisateur->findAllContact($_SESSION['utilisateur']->getId());  
        $contacts=array();     
        foreach ($contactsId as $contact) {
            $contacts[] = $managerUtilisateur->find($contact['idUtilisateur2']);
        }
        return $contacts;
    }

    /**
     * Fonction appellee lors du clic sur le bouton creer un groupe. Appel du twig de création
     * @return void
     */
    public function ajouter(): void {
        $contacts = $this->getListeContacts();
        $template = $this->getTwig()->load('groupes.html.twig');
        echo $template->render(array('creation' => true, 'contacts' => $contacts));
    }

    /**
     * Fonction qui s'occupe de la création d'un groupe. Verif des champs du formulaire
     * @return void
     */
    public function creer(): void {
        $tableauErreurs = [];
        $tableauContacts = $_POST['contacts']; //2, 3, 4, 5 -> id des utilisateurs + verif classe
        $nomValide = utilitaire::validerNom($_POST['nom'], $tableauErreurs);
        $descriptionValide = utilitaire::validerDescription($_POST['description'], $tableauErreurs);

        var_dump($nomValide);
        var_dump($descriptionValide);

        if($nomValide && $descriptionValide) {
            // Etape 1 : créer groupe
            $manager = new GroupeDao($this->getPdo());
            $manager->creerGroupe($_SESSION['utilisateur']->getId(), $_POST['nom'], $_POST['description']);
            echo "creation groupe finie";
            $manager = new GroupeDao($this->getPdo());
            $idGroupe = $manager->getIdGroupe($_SESSION['utilisateur']->getId(), $_POST['nom'], $_POST['description']);
            echo "getidgroupe fini";

            // Etape 2 : ajouter membres
            $this->ajouterMembres($idGroupe['id'], $_POST['contacts']);
            echo "ajoutermembres fini";

            $this->lister();
        }
    }

    /**
     * Fonction qui permet d'ajouter des utilisateurs à un groupe
     * @param int|null $idGroupe id du groupe dont on veut ajouter l'utilisateur
     * @param array|null $contacts tableau d'utilisateurs que l'on veut ajouter
     * @return void
     */
    public function ajouterMembres(?int $idGroupe, ?array $contacts): void {
        $manager = new GroupeDao($this->getPdo());
        $manager->ajouterMembreGroupe($idGroupe, $_SESSION['utilisateur']->getId());

        if($contacts) {   // Condition si estvide
            foreach ($contacts as $contact) {
                $manager->ajouterMembreGroupe($idGroupe, $contact);
            }
        }
    }
}