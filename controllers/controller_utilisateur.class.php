<?php

use ICal\ICal;

class ControllerUtilisateur extends Controller
{
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }

    function connexion() {


        $template = $this->getTwig()->load('connection.html.twig');
        echo $template->render();
    }

    function inscription()
    {



        //Génération de la vue
        $template = $this->getTwig()->load('inscription.html.twig');
        echo $template->render();
    }
}
