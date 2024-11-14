<?php

use ICal\ICal;

class ControllerAgenda extends Controller
{
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }
    public function GenererVue() {
        //Création d'une fonction appellee 1 seule fois pour ne pas avoir de double twig
        // $this->genererVueAgenda('agenda');
        // $this->agenda();
        $template = $this->getTwig()->load('agenda.html.twig');
        echo $template->render(array());
    }

    public function genererVueAgenda(?string $message) {
        //Génération de la vue agenda
        $template = $this->getTwig()->load('agenda.html.twig');
        echo $template->render(
            array(
                'message' => $message
            )
        );
     }

     public function ajouterAgenda()
     {
         $pdo = $this->getPdo(); // Récupérer l'instance PDO
     
         // Vérifier si tous les champs du formulaire sont remplis
         if (isset($_POST['url'], $_POST['couleur'], $_POST['nom'])) {
             // Sanitize et valider les données
             $url = filter_var($_POST['url'], FILTER_SANITIZE_URL); // Sanitize l'URL
             $couleur = htmlspecialchars($_POST['couleur'], ENT_QUOTES, 'UTF-8'); // Sanitize la couleur
             $nom = htmlspecialchars($_POST['nom'], ENT_QUOTES, 'UTF-8'); // Sanitize le nom
     
             // Validation de l'URL
             if (!filter_var($url, FILTER_VALIDATE_URL)) {
                 $this->genererVueAgenda("L'URL fournie est invalide.");
                 return;
             }
     
             // Créer une instance de AgendaDao pour interagir avec la base de données
             $manager = new AgendaDao($pdo);
     
             // Vérifier si l'agenda existe déjà avec cette URL
             $agendaExiste = $manager->findURL($url);
     
             if (!$agendaExiste) {
                 // Créer une nouvelle instance d'Agenda avec les données du formulaire
                 $nouvelAgenda = new Agenda(null, $_POST['url'], $_POST['couleur'], $_POST['nom']);
     
                 // Ajouter l'agenda dans la base de données
                 $manager->ajouterAgenda($nouvelAgenda);
     
                 // Retourner un message de succès
                 $this->genererVueAgenda("Ajout réussi !");
             } else {
                 // Si l'agenda existe déjà, afficher un message d'erreur
                 $this->genererVueAgenda("Agenda avec cette URL existe déjà !");
             }
         } else {
             // Si le formulaire n'est pas correctement rempli, afficher la vue générique
             $this->genererVue();
         }
     }
     
}