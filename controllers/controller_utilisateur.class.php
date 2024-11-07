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
        $template = $this->getTwig()->load('inscription.html.twig');
        echo $template->render();
    }

    function listerContacts() {
        $pdo = $this->getPdo();
        $manager = new UtilisateurDao($pdo);

        $utilisateurs = $manager->find(1);

        $template = $this->getTwig()->load('creneauLibre.html.twig');
        echo $template->render(
            array(
                'res' => $utilisateurs,
            )
        );
    }
}
