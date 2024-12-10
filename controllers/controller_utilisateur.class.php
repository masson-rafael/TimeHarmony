<?php

use Twig\Profiler\Dumper\BaseDumper;
require_once 'include.php';

/**
 * @author Thibault Latxague
 * @describe Controller de la page des utilisateur
 * @version 0.1
 * @todo POURQUOI LA MESSAGE BOX NE FONCTIONNE PAS ?????? (a cause de l'insertion des scripts ? dans base_template)
 */

class ControllerUtilisateur extends Controller
{
    /**
     * Constructeur par défaut
     *
     * @param \Twig\Environment $twig Environnement twig
     * @param \Twig\Loader\FilesystemLoader $loader Loader de fichier
     */
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader) {
        parent::__construct($twig, $loader);
    }

    /**
     * Inscription de l'utilisateur à la BD. On utilise les fonctions de validation pour vérifier les champs
     * et ensuite on vérifie si l'utilisateur existe déjà dans la BD
     * avant de procéder à l'inscription de l'utilisateur dans la BD
     * @return void
     */
    public function inscription() {
        $pdo = $this->getPdo();
        $tableauErreurs = [];

        @$emailValide = utilitaire::validerEmail($_POST['email'], $tableauErreurs);
        @$nomValide = utilitaire::validerNom($_POST['nom'], $tableauErreurs);
        @$prenomValide = utilitaire::validerPrenom($_POST['prenom'], $tableauErreurs);
        @$mdpValide = utilitaire::validerMotDePasseInscription($_POST['pwd'], $_POST['pwdConfirme'], $tableauErreurs);

        //Vérification que le form est bien rempli
        if ($emailValide && $nomValide && $prenomValide && $mdpValide) {        // IF TABLEAU ERREURS VIDE ?
            $manager = new UtilisateurDao($pdo); //Lien avec PDO
            // Appel fonction et stocke bool pour savoir si utilisateur existe deja avec email
            $utilisateurExiste = $manager->findMail($_POST['email']); 
            /**
             * Verifie que l'utilisateur n'existe pas, 
             * que les mdp sont identiques, que le mdp contient les bons caracteres         // CHECK AVEC FONCTIONS VERIF
             * et que l'email est valide
             */
            if (!$utilisateurExiste) {
                // Hachage du mot de passe
                $mdpHache = password_hash($_POST['pwd'], PASSWORD_DEFAULT);
                // Création d'un nouvel utilisateur (instance)
                $nouvelUtilisateur = Utilisateur::createAvecParam(null, $_POST['nom'], $_POST['prenom'], $_POST['email'], $mdpHache, "utilisateurBase.png", false); 
                // Appel du script pour ajouter utilisateur dans bd
                $manager->ajouterUtilisateur($nouvelUtilisateur);
                $tableauErreurs[] = "Inscription réussie !";
            } else {
                // Si l'utilisateur existe deja
                $tableauErreurs[] = "L'utilisateur existe déjà ! Connectez-vous !";
            }
        }
        // Affichage de la page avec les erreurs ou le message de succès
        @$this->genererVue($_POST['email'], null, $tableauErreurs);
    }

    /**
     * Fonction de connexion au site à partir de la BD.
     * On utilise les fonctions de validation pour vérifier les champs email et mdp
     * et ensuite on vérifie si l'utilisateur existe dans la BD avec le bon mot de passe via password_verify
     *
     * @return void
     */
    public function connexion() {
        $pdo = $this->getPdo();
        $tableauErreurs = [];

        /**
         * Verifie que l'email et le mdp sont bien remplis et respectent les critères
         * Verifie ensuite si l'email existe dans la bd
         * Verifie si le mdp clair correspond au hachage dans bd
         */
        @$emailValide = utilitaire::validerEmail($_POST['email'], $tableauErreurs);
        @$passwdValide = utilitaire::validerMotDePasse($_POST['pwd'], $tableauErreurs);

        if($emailValide && $passwdValide) {
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
     *
     * @return void
     */
    public function premiereInscription() {
        $this->genererVueVide('inscription');
    }
    
    /**
     * Cette fonction est appelée lorsqu'on clique sur le bouton "Connexion" sur la navbar
     *
     * @return void
     */
    public function premiereConnexion() {
        $this->genererVueVide('connexion');
    }

    /**
     * Deconnexion de l'utilisateur et unset de la session
     *
     * @return void
     */
    public function deconnecter() {
        $this->getTwig()->addGlobal('utilisateurGlobal', null);
        unset($_SESSION['utilisateur']);
        $this->genererVueVide('menu');
    }

    /**
     * Genere la vue du twig
     *
     * @param string|null $mail de la personne qui s'inscrit
     * @param boolean|null $existe si l'utilisateur existe deja
     * @param string|null $message le message renvoyé par les fonctions (erreur détaillée ou reussite)
     * @return void
     */
    public function genererVue(?string $mail, ?bool $existe, ?array $messages) {
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
     *
     * @param string|null $page web dont on veut generer le twig
     * @return void
     */
    public function genererVueVide(?string $page) {
        //Génération de la vue
        $template = $this->getTwig()->load($page . '.html.twig');
        echo $template->render(
            array()
        );
    }

    /**
     * Genere la vue de la connexion
     *
     * @param Array|null $tableau de messages d'erreurs ou de reussite
     * @return void
     */
    // Dans votre contrôleur ou gestionnaire de connexion
    public function genererVueConnexion(?array $message, ?Utilisateur $utilisateur): void {
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
     *
     * @return void
     */
    public function listerContacts() {
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
     *
     * @return void
     */
    public function lister() {
        $pdo = $this->getPdo();
        $manager = new UtilisateurDao($pdo);
        $utilisateurs = $manager->findAll();
        $template = $this->getTwig()->load('administration.html.twig');
        echo $template->render(
            array(
                'listeUtilisateurs' => $utilisateurs,
            )
        );
    }

    /**
     * Fonction appellee par la corbeille pour supprimer un utilisateur (panel admin)
     *
     * @return void
     */
    public function supprimer() {
        // Récupération de l'id envoyé en parametre du lien
        $id = $_GET['id'];              // @todo vérification id
        $type = $_GET['type'];          // @todo vérification type
        $pdo = $this->getPdo();
        $manager = new UtilisateurDao($pdo);
        $manager->supprimerUtilisateur($id);
        if($type == 'admin') {
            $this->lister();
        } else {
            $this->deconnecter();
        }
    }

    /**
     * Fonction appellee par le bouton de mise a jour d'un utilisateur (panel admin)
     *
     * @return void
     */
    public function modifier() {
        $id = $_GET['id'];              // @todo vérification id
        $type = $_GET['type'];          // @todo vérification type
        $nom = $_POST['nom'];           // @todo vérification nom
        $prenom = $_POST['prenom'];     // @todo vérification prenom
        $role = $_POST['role'];         // @todo vérification role
    
        $pdo = $this->getPdo();
        $manager = new UtilisateurDao($pdo);
    
        // Gestion de l'upload de la photo de profil
        $cheminPhoto = $_SESSION['utilisateur']->getPhotoDeProfil(); // Récupérer l'ancien chemin
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {        // @todo vérification fichier
            $dossierDestination = 'image\\photo_user\\';
            $nomFichier = 'profil_' . $id . '_' . basename($_FILES['photo']['name']);
            $cheminPhoto = $dossierDestination . $nomFichier;
    
            // Déplacer le fichier uploadé dans le répertoire cible
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $cheminPhoto)) {
                // Mettre à jour le chemin de la photo dans la base de données
                $manager->modifierPhotoProfil($id, $nomFichier);
                $_SESSION['utilisateur']->setPhotoDeProfil($nomFichier);
            }
        }
    
        // Mise à jour du profil utilisateur
        if ($role == 'User') {
            $role = false;
        } else {
            $role = true;
        }
    
        $manager->modifierUtilisateur($id, $nom, $prenom, $role);
    
        if ($type == 'admin') {
            $this->lister();
        } else {
            $this->afficherProfil();
        }
    }
    

    /**
     * Affiche le profil de l'utilisateur connecté (page profil)
     *
     * @return void
     */
    public function afficherProfil():void {
        $pdo = $this->getPdo();
        $manager = new UtilisateurDao($pdo);
        $utilisateur = $manager->getUserMail($_SESSION['utilisateur']->getEmail());
        $template = $this->getTwig()->load('profil.html.twig');
        echo $template->render(
            array(
                'utilisateur' => $utilisateur,
            )
        );
    }

    /**
     * Modifie le profil de l'utilisateur connecté (page modifier profil)
     *
     * @return void
     */
    public function modifierProfil() {
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
