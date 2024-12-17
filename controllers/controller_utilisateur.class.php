<?php

use Twig\Profiler\Dumper\BaseDumper;

require_once 'include.php';

/**
 * @author Thibault Latxague
 * @describe Controller de la page des utilisateur
 * @version 0.1
 * @todo POURQUOI LA MESSAGE BOX NE FONCTIONNE PAS ?????? (a cause de l'insertion des scripts ? dans base_template) + detailler doxygen nav connexion inscription
 */

class ControllerUtilisateur extends Controller
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
     * Inscription de l'utilisateur à la BD. On utilise les fonctions de validation pour vérifier les champs
     * et ensuite on vérifie si l'utilisateur existe déjà dans la BD
     * avant de procéder à l'inscription de l'utilisateur dans la BD
     * @return void
     */
    public function inscription()
    {
        $pdo = $this->getPdo();
        $tableauErreurs = [];

        $emailValide = utilitaire::validerEmail($_POST['email'], $tableauErreurs);
        $nomValide = utilitaire::validerNom($_POST['nom'], $tableauErreurs);
        $prenomValide = utilitaire::validerPrenom($_POST['prenom'], $tableauErreurs);
        $mdpValide = utilitaire::validerMotDePasseInscription($_POST['pwd'], $tableauErreurs, $_POST['pwdConfirme']);

        if ($emailValide && $nomValide && $prenomValide && $mdpValide) {
            $manager = new UtilisateurDao($pdo); //Lien avec PDO
            /**
             * Verifie que l'utilisateur n'existe pas.
             * On hash le mdp, on crée un nouvel utilisateur et on l'ajoute dans la bd
             */
            $utilisateurExiste = $manager->findMail($_POST['email']);
            if (!$utilisateurExiste) {
                $mdpHache = password_hash($_POST['pwd'], PASSWORD_DEFAULT);
                $nouvelUtilisateur = Utilisateur::createAvecParam(null, $_POST['nom'], $_POST['prenom'], $_POST['email'], $mdpHache, "utilisateurBase.png", false);
                $manager->ajouterUtilisateur($nouvelUtilisateur);
                $tableauErreurs[] = "Inscription réussie !";
            } else {
                // Si l'utilisateur existe deja
                $tableauErreurs[] = "L'utilisateur existe déjà ! Connectez-vous !";
            }
        }
        // Affichage de la page avec les erreurs ou le message de succès. Utilisation du @ car si l'utilisateur n'a pas renseigné de mail, cette action génère une erreur
        @$this->genererVue($_POST['email'], null, $tableauErreurs);
    }

    /**
     * Fonction de connexion au site à partir de la BD.
     * On utilise les fonctions de validation pour vérifier les champs email et mdp
     * et ensuite on vérifie si l'utilisateur existe dans la BD avec le bon mot de passe via password_verify
     *
     * @return void
     */
    public function connexion()
    {
        $pdo = $this->getPdo();
        $tableauErreurs = [];

        /**
         * Verifie que l'email et le mdp sont bien remplis et respectent les critères
         * Verifie ensuite si l'email existe dans la bd
         * Verifie si le mdp clair correspond au hachage dans bd
         */
        $emailValide = utilitaire::validerEmail($_POST['email'], $tableauErreurs);
        $passwdValide = utilitaire::validerMotDePasseInscription($_POST['pwd'], $tableauErreurs);

        if ($emailValide && $passwdValide) {
            $manager = new UtilisateurDao($pdo);
            // On recupere un tuple avec un booleen et le mdp hache
            $motDePasse = $manager->connexionReussie($_POST['email']);
            // Si les mdp sont les mêmes
            if ($motDePasse[0] && password_verify($_POST['pwd'], $motDePasse[1])) {
                // On recupere l'utilisateur
                $utilisateur = $manager->getUserMail($_POST['email']);
                $tableauErreurs[] = "Connexion réussie !";
                $this->genererVueConnexion($tableauErreurs, $utilisateur);
            } else {
                $tableauErreurs[] = "Mauvaise adresse mail ou mot de passe"; // Mauvais MDP
                $this->genererVueConnexion($tableauErreurs, null);
            }
        } else {
            $this->genererVueConnexion($tableauErreurs, null);
        }
    }

    /**
     * Cette fonction est appelée lorsqu'on clique sur le bouton "Inscription" sur la navbar
     * Son but est de générer le page nommée inscription : celle ou l'utilisateur peut remplir le formulaire
     * @return void
     */
    public function premiereInscription()
    {
        $this->genererVueVide('inscription');
    }

    /**
     * Cette fonction est appelée lorsqu'on clique sur le bouton "Connexion" sur la navbar
     * Son but est de générer le page nommée connexion : celle ou l'utilisateur peut remplir le formulaire
     * @return void
     */
    public function premiereConnexion()
    {
        $this->genererVueVide('connexion');
    }

    /**
     * Cette fonction est appelée lorsqu'on clique sur le logo dans  la navbar
     * Elle nous envoie donc à la page d'accueil où on peut sélectionner les menus
     * @return void
     */
    public function menuConnecte()
    {
        $this->genererVueVide('menu');
    }

    /**
     * Deconnexion de l'utilisateur et unset de la session
     * L'utilisateur est ensuite redirigé vers la page d'accueil où figure une vidéo de présentation de l'application
     * @return void
     */
    public function deconnecter()
    {
        $this->getTwig()->addGlobal('utilisateurGlobal', null);
        unset($_SESSION['utilisateur']);
        $this->genererVueVide('index');
    }

    /**
     * Genere la vue du twig
     * @todo verifier si useless
     * @param string|null $mail de la personne qui s'inscrit
     * @param boolean|null $existe si l'utilisateur existe deja
     * @param string|null $message le message renvoyé par les fonctions (erreur détaillée ou reussite)
     * @return void
     */
    public function genererVue(?string $mail, ?bool $existe, ?array $messages)
    {
        //Génération de la vue
        $template = $this->getTwig()->load('inscription.html.twig');
        echo $template->render(
            array(
                'mail' => $mail,
                'existe' => $existe,
                'message' => $messages,
            )
        );
    }

    /**
     * Genere le twig de la page vide
     * @todo verifier si sous cote. Peut etre utilisé partout si envoi de param dans lien et $_GET
     * @param string|null $page web dont on veut generer le twig
     * @return void
     */
    public function genererVueVide(?string $page)
    {
        //Génération de la vue
        $template = $this->getTwig()->load($page . '.html.twig');
        echo $template->render(
            array()
        );
    }

    /**
     * Genere la vue de la connexion et créé une session utilisateur globale
     *
     * @param Array|null $tableau de messages d'erreurs ou de reussite
     * @return void
     */
    // Dans votre contrôleur ou gestionnaire de connexion
    public function genererVueConnexion(?array $message, ?Utilisateur $utilisateur): void
    {
        if ($utilisateur !== null) {
            // Stockage en session et définition de la variable globale
            $utilisateur = Utilisateur::createWithCopy($utilisateur);
            $_SESSION['utilisateur'] = $utilisateur;
            $this->getTwig()->addGlobal('utilisateurGlobal', $utilisateur);
        }

        $template = $this->getTwig()->load('connexion.html.twig');
        echo $template->render([
            'message' => $message
        ]);
    }

    /**
     * Listage de l'utilisateur ayant l'id 2
     * @todo après merge : vérifier si utilisée sinon supprimer
     * @return void
     */
    public function listerContacts()
    {
        $pdo = $this->getPdo();
        $manager = new UtilisateurDao($pdo);
        $utilisateurs = $manager->find(2);
        $template = $this->getTwig()->load('creneauLibre.html.twig');
        echo $template->render(
            array(
                'res' => $utilisateurs,
            )
        );
    }

    /**
     * Listage de tous les utilisateurs
     * Redirection vers la page d'administration
     * @return void
     */
    public function lister(?array $tableauDErreurs = null)
    {
        $pdo = $this->getPdo();
        $manager = new UtilisateurDao($pdo);
        $utilisateurs = $manager->findAll();
        $template = $this->getTwig()->load('administration.html.twig');
        echo $template->render(
            array(
                'listeUtilisateurs' => $utilisateurs,
                'message' => $tableauDErreurs,
            )
        );
    }

    /**
     * Fonction appellee par la corbeille pour supprimer un utilisateur (panel admin)
     * Ici, pas besoin de vérifier les paramètres car ils sont passés par un lien
     * @todo vérifier la ligne else {$this->deconnecter();}. Suspecte car si la personne n'est pas admin on se déconnecte
     * @return void
     */
    public function supprimer()
    {
        $id = $_GET['id'];
        $type = $_GET['type'];

        $pdo = $this->getPdo();
        $manager = new UtilisateurDao($pdo);
        $manager->supprimerUtilisateur($id);
        $this->lister();
    }

    /**
     * Fonction appellee par le bouton de mise a jour d'un utilisateur (panel admin)
     *
     * @return void
     * @todo Modifier utilisateur ne fonctionne plus. Why ? 
     */
    public function modifier()
    {
        $id = $_GET['id'];              // Pas de vérification d'id car transmis par le lien d'accès à la page
        $type = $_GET['type'];          // Pas de vérification du type car transmis par le lien d'accès à la page

        $messageErreurs = [];
        $nomValide = utilitaire::validerNom($_POST['nom'], $messageErreurs);
        $prenomValide = utilitaire::validerPrenom($_POST['prenom'], $messageErreurs);
        //$roleValide = utilitaire::validerRole($_POST['role'], $messageErreurs);
        @$photoValide = utilitaire::validerPhoto($_FILES['photo'], $messageErreurs);

        // var_dump($nomValide);
        // var_dump($prenomValide);

        if ($nomValide && $prenomValide) {//} && $roleValide) {
            $pdo = $this->getPdo();
            $manager = new UtilisateurDao($pdo);

            // Gestion de l'upload de la photo de profil
            $cheminPhoto = $_SESSION['utilisateur']->getPhotoDeProfil(); // Récupérer l'ancien chemin
            if ($photoValide) {
                $dossierDestination = 'image/photo_user/';
                $nomFichier = 'profil_' . $id . '_' . basename($_FILES['photo']['name']);
                $cheminPhoto = $dossierDestination . $nomFichier;

                // Déplacer le fichier uploadé dans le répertoire cible
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $cheminPhoto)) {
                    // Mettre à jour le chemin de la photo dans la base de données
                    $manager->modifierPhotoProfil($id, $nomFichier);
                    $_SESSION['utilisateur']->setPhotoDeProfil($nomFichier);
                }
            }

            $role = $_POST['role'];
            // Mise à jour du profil utilisateur
            if ($role == '1' || $role == 'Admin') {
                $role = true;
            } else {
                $role = false;
            }

            @$nomFichier == null ? $nomFichier = $_SESSION['utilisateur']->getPhotoDeProfil() : $nomFichier;
            $manager->modifierUtilisateur($id, $_POST['nom'], $_POST['prenom'], $role, $nomFichier);
            $utilisateurTemporaire = $manager->find($id);
            $_SESSION['utilisateur'] = $utilisateurTemporaire;
            $this->getTwig()->addGlobal('utilisateurGlobal', $utilisateurTemporaire);
        }
        $this->afficherProfil($messageErreurs);
    }

    /**
     * Fonction appellee par le bouton de mise a jour d'un utilisateur (panel admin)
     *
     * @return void
     * @todo
     */
    public function modifierAdmin()
    {
        $id = $_GET['id'];              // Pas de vérification d'id car transmis par le lien d'accès à la page
        $type = $_GET['type'];          // Pas de vérification du type car transmis par le lien d'accès à la page

        $messageErreurs = [];
        $nomValide = utilitaire::validerNom($_POST['nom'], $messageErreurs);
        $prenomValide = utilitaire::validerPrenom($_POST['prenom'], $messageErreurs);
        $roleValide = utilitaire::validerRole($_POST['role'], $messageErreurs);
        @$photoValide = utilitaire::validerPhoto($_FILES['photo'], $messageErreurs);

        if ($nomValide && $prenomValide && $roleValide) {
            $pdo = $this->getPdo();
            $manager = new UtilisateurDao($pdo);

            // Gestion de l'upload de la photo de profil
            $utilisateurConcerne = $manager->find($id);
            $cheminPhoto = $utilisateurConcerne->getPhotoDeProfil(); // Récupérer l'ancien chemin
            if ($photoValide) {
                $dossierDestination = 'image/photo_user/';
                $nomFichier = 'profil_' . $id . '_' . basename($_FILES['photo']['name']);
                $cheminPhoto = $dossierDestination . $nomFichier;

                // Déplacer le fichier uploadé dans le répertoire cible
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $cheminPhoto)) {
                    // Mettre à jour le chemin de la photo dans la base de données
                    $manager->modifierPhotoProfil($id, $nomFichier);
                    $utilisateurConcerne->setPhotoDeProfil($nomFichier);
                }
            }

            $role = $_POST['role'] == 'Admin' ? 1 : 0;
            var_dump($role); // Vérification de la valeur numérique
            var_dump($_SESSION['utilisateur']);
            
            // Mise à jour du chemin de l'image
            $nomFichier = empty($nomFichier) ? $utilisateurConcerne->getPhotoDeProfil() : $nomFichier;
            
            // Mise à jour du profil utilisateur
            $manager->modifierUtilisateur($id, $_POST['nom'], $_POST['prenom'], $role, $nomFichier);
            $utilisateurTemporaire = $manager->find($id);
            
            if ($utilisateurTemporaire->getId() == $_SESSION['utilisateur']->getId()) {
                $_SESSION['utilisateur'] = $utilisateurTemporaire;
                $this->getTwig()->addGlobal('utilisateurGlobal', $utilisateurTemporaire);
            }
        }
        $_SESSION['utilisateur']->getEstAdmin() == false ? $this->afficherProfil($messageErreurs) : $this->lister($messageErreurs);
    }


    /**
     * Affiche le profil de l'utilisateur connecté (page profil)
     *
     * @return void
     */
    public function afficherProfil(?array $messagesErreur = null): void
    {
        $pdo = $this->getPdo();
        $manager = new UtilisateurDao($pdo);
        $utilisateur = $manager->getUserMail($_SESSION['utilisateur']->getEmail());
        $template = $this->getTwig()->load('profil.html.twig');
        echo $template->render(
            array(
                'utilisateur' => $utilisateur,
                'message' => $messagesErreur,
            )
        );
    }

    /**
     * Modifie le profil de l'utilisateur connecté (page modifier profil)
     *
     * @return void
     */
    public function modifierProfil()
    {
        $pdo = $this->getPdo();
        $manager = new UtilisateurDao($pdo);
        $utilisateur = $manager->getUserMail($_SESSION['utilisateur']->getEmail());
        $template = $this->getTwig()->load('modifierProfil.html.twig');
        echo $template->render(
            array(
                'utilisateur' => $utilisateur,
            )
        );
    }
}
