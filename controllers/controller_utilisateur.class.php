<?php

use Twig\Profiler\Dumper\BaseDumper;

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
     * Fonction de connexion au site à partir de la BD
     *
     * @return void
     */
    function connexion() {
        $pdo = $this->getPdo();

        /**
         * Verifie que l'email et le mdp sont bien remplis
         * Verifie ensuite si l'email existe dans la bd
         * Verifie si le mdp clair correspond au hachage dans bd
         */
        if (isset($_POST['email']) && isset($_POST['pwd'])) {
            $manager = new UtilisateurDao($pdo);
            // On recupere un tuple avec un booleen et le mdp hache
            $motDePasse = $manager->connexionReussie($_POST['email']);

            // Si les mdp sont les mêmes
            if ($motDePasse[0] && password_verify($_POST['pwd'], $motDePasse[1])) {
                // On recupere l'utilisateur
                $utilisateur = $manager->getUserMail($_POST['email']);
                $this->genererVueConnexion("CONNEXION REUSSIE", $utilisateur);
            } else {
                $this->genererVueConnexion("CONNEXION ECHOUEE", null);
            }
        }
    }

    /**
     * Premiere inscription lorsqu'on clique sur inscription sur la navbar pour éviter double twig
     *
     * @return void
     */
    function premiereInscription() {
        $this->genererVueVide('inscription');
        $this->inscription();
    }
    
    /**
     * Premiere connexion lorsqu'on clique sur connexion sur la navbar pour éviter double twig
     *
     * @return void
     */
    function premiereConnexion() {
        $this->genererVueVide('connexion');
        $this->connexion();
    }

    /**
     * Inscription de l'utilisateur à la BD
     *
     * @return void
     */
    function inscription() {
        $pdo = $this->getPdo();

        //Vérification que le form est bien rempli
        if (isset($_POST['email']) && isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['pwd']) && isset($_POST['pwdConfirme'])) {
            $manager = new UtilisateurDao($pdo); //Lien avec PDO
            // Appel fonction et stocke bool pour savoir si utilisateur existe deja avec email
            $utilisateurExiste = $manager->findMail($_POST['email']); 
            // Verifie que le mdp contient les bons caracteres
            $mdpContientBonsCaracteres = preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W)[a-zA-Z\d\W]{8,16}$/', $_POST['pwd']);

            /**
             * Verifie que l'utilisateur n'existe pas, 
             * que les mdp sont identiques, que le mdp contient les bons caracteres 
             * et que l'email est valide
             */
            if (!$utilisateurExiste && $_POST['pwd'] == $_POST['pwdConfirme'] && $mdpContientBonsCaracteres && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                // Hachage du mot de passe
                $mdpHache = password_hash($_POST['pwd'], PASSWORD_DEFAULT);
                // Création d'un nouvel utilisateur (instance)
                $nouvelUtilisateur = Utilisateur::createAvecParam(null, $_POST['nom'], $_POST['prenom'], $_POST['email'], $mdpHache, "photoProfil.jpg", false); 
                // Appel du script pour ajouter utilisateur dans bd
                $manager->ajouterUtilisateur($nouvelUtilisateur);
                $this->genererVue($_POST['email'], $utilisateurExiste, "INSCRIPTION REUSSIE");
            } else if (!$utilisateurExiste && $_POST['pwd'] != $_POST['pwdConfirme']) {
                // Si les mdp ne sont pas identiques
                $this->genererVue($_POST['email'], $utilisateurExiste, "MOTS DE PASSE NON IDENTIQUES");
            } else if (!$utilisateurExiste && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
                // Si l'email n'est pas valide
                $this->genererVue($_POST['email'], $utilisateurExiste, "ADRESSE MAIL NON VALIDE");
            }else if (!$utilisateurExiste) {
                // Si le mdp ne contient pas les bons caracteres
                $this->genererVue($_POST['email'], $utilisateurExiste, "MOTS DE PASSE NE SUIT PAS LA REGLE");
            } else {
                // Si l'utilisateur existe deja
                $this->genererVue($_POST['email'], $utilisateurExiste, "UTILISATEUR EXISTE DEJA");
            }
        }
    }

    /**
     * Deconnexion de l'utilisateur et unset de la session
     *
     * @return void
     */
    function deconnecter() {
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
    public function genererVue(?string $mail, ?bool $existe, ?string $message) {
        //Génération de la vue
        $template = $this->getTwig()->load('inscription.html.twig');
        echo $template->render(
            array(
                'mail' => $mail,
                'existe' => $existe,
                'message' => $message,
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
     * @param string|null $message de succes ou erreur détaillé
     * @return void
     */
    // Dans votre contrôleur ou gestionnaire de connexion
    public function genererVueConnexion(?string $message, ?Utilisateur $utilisateur): void {
        if ($utilisateur !== null) {
            // Stockage en session et définition de la variable globale
            $utilisateur = Utilisateur::createAvecParam($utilisateur->getId(), $utilisateur->getNom(), $utilisateur->getPrenom(), $utilisateur->getEmail(), $utilisateur->getMotDePasse(), $utilisateur->getPhotoDeProfil(), $utilisateur->getEstAdmin());
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
        $id = $_GET['id'];
        $type = $_GET['type'];
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
        $id = $_GET['id'];
        $type = $_GET['type'];
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $role = $_POST['role'];
    
        $pdo = $this->getPdo();
        $manager = new UtilisateurDao($pdo);
    
        // Gestion de l'upload de la photo de profil
        $cheminPhoto = $_SESSION['utilisateur']->getPhotoDeProfil(); // Récupérer l'ancien chemin
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
            $dossierDestination = 'C:\wamp64\\www\TimeHarmony\\TimeHarmony\\image\\photo_user\\';
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
