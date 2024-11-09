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
        if (isset($_POST['email'])) {
            $manager = new UtilisateurDao($pdo);
            $existe = $manager->findMail($_POST['email']);
        }
        if ($existe) {
            $template = $this->getTwig()->load('inscription.html.twig');
            echo $template->render(
                array(
                    'mail' => $_POST['email'],
                    'existe' => $existe,
                )
            );
        } else if (isset($_POST['email']) && isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['pwd']) && isset($_POST['pwdConfirme'])) {
            $template = $this->getTwig()->load('inscription.html.twig');
            echo $template->render(
                array(
                    'mail' => $_POST['email'],
                    'existe' => $existe,
                    'message' => "NOUVEL UTILISATEUR",
                )
            );
        } else {
            $template = $this->getTwig()->load('inscription.html.twig');
            echo $template->render(
                array(
                    'mail' => $_POST['email'],
                    'existe' => $existe,
                    'message' => "ERREUR",
                )
            );
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
