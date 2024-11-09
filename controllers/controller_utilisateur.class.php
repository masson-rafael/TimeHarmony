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
        $template = $this->getTwig()->load('connexion.html.twig');
        echo $template->render();
    }

    function inscription()
    {
        //Génération de la vue
        $pdo = $this->getPdo();

        if (isset($_POST['email']) && isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['pwd']) && isset($_POST['pwdConfirme'])) {
            $manager = new UtilisateurDao($pdo); //Lien avec PDO
            $utilisateurExiste = $manager->findMail($_POST['email']); //Appel fonction et stocke bool pour savoir si utilisateur existe deja avec email
            $mdpContientBonsCaracteres = preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W)[a-zA-Z\d\W]{8,16}$/', $_POST['pwd']);

            if (!$utilisateurExiste && $_POST['pwd'] == $_POST['pwdConfirme'] && $mdpContientBonsCaracteres) {
                $nouvelUtilisateur = new Utilisateur();
                $nouvelUtilisateur->setEmail($_POST['email']);
                $nouvelUtilisateur->setNom($_POST['nom']);
                $nouvelUtilisateur->setPrenom($_POST['prenom']);
                $nouvelUtilisateur->setMotDePasse($_POST['pwd']);
                $nouvelUtilisateur->setPhotoDeProfil("photoProfil.jpg");
                $nouvelUtilisateur->setEstAdmin(false);
                $manager->ajouterUtilisateur($nouvelUtilisateur); //Appel du script pour ajouter utilisateur dans bd
                
                $this->genererVue($_POST['email'], $utilisateurExiste, "INSCRIPTION REUSSIE");
            } else if (!$utilisateurExiste && $_POST['pwd'] != $_POST['pwdConfirme']) {
                $this->genererVue($_POST['email'], $utilisateurExiste, "MOTS DE PASSE NON IDENTIQUES");
            } else if (!$utilisateurExiste) {
                $this->genererVue($_POST['email'], $utilisateurExiste, "MOTS DE PASSE NE SUIT PAS LA REGLE");
            }else {
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
