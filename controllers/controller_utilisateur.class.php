<?php
/**
 * @author Thibault Latxague
 * @describe Controller de la page des utilisateur
 * @version 0.1
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
            // On verifie que le mdp est bon 
            $mavar = password_verify($_POST['pwd'], $motDePasse[1]);

            // Si les mdp sont les mêmes
            if ($motDePasse[0] && password_verify($_POST['pwd'], $motDePasse[1])) {
                $this->genererVueConnexion("CONNEXION REUSSIE");
            } else {
                $this->genererVueConnexion("CONNEXION ECHOUEE");
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
    function genererVueConnexion(?string $message) {
        //Génération de la vue
        $template = $this->getTwig()->load('connexion.html.twig');
        echo $template->render(
            array(
                'message' => $message,
                'etat' => "connecte",
            )
        );
    }

    /**
     * Listage de tous les utilisateurs
     *
     * @return void
     */
    function listerContacts() {
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
}
