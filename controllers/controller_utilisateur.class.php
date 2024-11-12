<?php

use ICal\ICal;

class ControllerUtilisateur extends Controller
{
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }

    function connexion() {
        //Génération de la vue
        $pdo = $this->getPdo();

        if (isset($_POST['email']) && isset($_POST['pwd'])) {
            $manager = new UtilisateurDao($pdo);
            $motDePasse = $manager->connexionReussie($_POST['email']);
            $mavar = password_verify($_POST['pwd'], $motDePasse[1]);
            var_dump($mavar);

            if ($motDePasse[0] && password_verify($_POST['pwd'], $motDePasse[1])) {
                $this->genererVueConnexion("CONNEXION REUSSIE");
            } else {
                $this->genererVueConnexion("CONNEXION ECHOUEE");
            }
        }
    }

    function premiereInscription() {
        //Création d'une fonction appellee 1 seule fois pour ne pas avoir de double twig
        $this->genererVueVide('inscription');
        $this->inscription();
    }

    function premiereConnexion() {
        //Création d'une fonction appellee 1 seule fois pour ne pas avoir de double twig
        $this->genererVueVide('connexion');
        $this->connexion();
    }

    function inscription()
    {
        $pdo = $this->getPdo();

        //Vérification que le form est bien rempli
        if (isset($_POST['email']) && isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['pwd']) && isset($_POST['pwdConfirme'])) {
            $manager = new UtilisateurDao($pdo); //Lien avec PDO
            $utilisateurExiste = $manager->findMail($_POST['email']); //Appel fonction et stocke bool pour savoir si utilisateur existe deja avec email
            $mdpContientBonsCaracteres = preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W)[a-zA-Z\d\W]{8,16}$/', $_POST['pwd']);

            if (!$utilisateurExiste && $_POST['pwd'] == $_POST['pwdConfirme'] && $mdpContientBonsCaracteres && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $mdpHache = password_hash($_POST['pwd'], PASSWORD_DEFAULT);
                $nouvelUtilisateur = Utilisateur::createAvecParam(null, $_POST['nom'], $_POST['prenom'], $_POST['email'], $mdpHache, "photoProfil.jpg", false); //Création d'un nouvel utilisateur (instance)
                $manager->ajouterUtilisateur($nouvelUtilisateur); //Appel du script pour ajouter utilisateur dans bd
                $this->genererVue($_POST['email'], $utilisateurExiste, "INSCRIPTION REUSSIE");
            } else if (!$utilisateurExiste && $_POST['pwd'] != $_POST['pwdConfirme']) {
                $this->genererVue($_POST['email'], $utilisateurExiste, "MOTS DE PASSE NON IDENTIQUES");
            } else if (!$utilisateurExiste && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
                $this->genererVue($_POST['email'], $utilisateurExiste, "ADRESSE MAIL NON VALIDE");
            }else if (!$utilisateurExiste) {
                $this->genererVue($_POST['email'], $utilisateurExiste, "MOTS DE PASSE NE SUIT PAS LA REGLE");
            } else {
                $this->genererVue($_POST['email'], $utilisateurExiste, "UTILISATEUR EXISTE DEJA");
            }
        } 
    }

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

    //////////////////////////////////////////////////////////////// OPTIMISER CETTE MERDE

    public function genererVueVide(?string $page) {
        //Génération de la vue
        $template = $this->getTwig()->load($page . '.html.twig');
        echo $template->render(
            array()
        );
    }

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
