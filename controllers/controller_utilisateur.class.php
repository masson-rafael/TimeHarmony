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
        $existe = false;

        if (isset($_POST['email']) && isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['pwd']) && isset($_POST['pwdConfirme'])) {
            $manager = new UtilisateurDao($pdo); //Lien avec PDO
            $utilisateurExiste = $manager->findMail($_POST['email']); //Appel fonction et stocke bool pour savoir si utilisateur existe deja avec email
            var_dump($utilisateurExiste);

            if (!$utilisateurExiste) {
                $nouvelUtilisateur = new Utilisateur();
                $nouvelUtilisateur->setEmail($_POST['email']);
                $nouvelUtilisateur->setNom($_POST['nom']);
                $nouvelUtilisateur->setPrenom($_POST['prenom']);
                $nouvelUtilisateur->setMotDePasse($_POST['pwd']);
                $nouvelUtilisateur->setPhotoDeProfil("photoProfil.jpg");
                $nouvelUtilisateur->setEstAdmin(false);
                $manager->ajouterUtilisateur($nouvelUtilisateur);
                $template = $this->getTwig()->load('inscription.html.twig');
                echo $template->render(
                    array(
                        'mail' => $_POST['email'],
                        'existe' => $utilisateurExiste,
                        'message' => "NOUVEL UTILISATEUR",
                    )
                );
            }
            else {
                $template = $this->getTwig()->load('inscription.html.twig');
                echo $template->render(
                    array(
                        'mail' => $_POST['email'],
                        'existe' => $utilisateurExiste,
                        'message' => "ERREUR",
                    )
                );
            }
        }
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
