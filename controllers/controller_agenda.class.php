<?php

use ICal\ICal;

/**
 * @author Félix Autant
 * @describe Controller de la page de recherche de créneaux libres
 * @todo Verifier que le undocumented class soit pas à remplir. S'il existe même
 * @version 0.1
 */

/**
 * Undocumented class
 */
class ControllerAgenda extends Controller
{
    /**
     * Constructeur par défaut
     *
     * @param \Twig\Environment $twig Environnement twig
     * @param \Twig\Loader\FilesystemLoader $loader Loader de fichier
     */

    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }

    /**
     * Generer la vue de la page creneauxLibres sans resultats
     *
     * @return void
     */

    public function ajouterAgenda()
    {
        $pdo = $this->getPdo(); // Récupérer l'instance PDO

        $tableauErreurs = [];
        $urlValide = utilitaire::validerURLAgenda($_POST['url'], $tableauErreurs);
        $couleurValide = utilitaire::validerCouleur($_POST['couleur'], $tableauErreurs);
        $nomValide = utilitaire::validerNom($_POST['nom'], $tableauErreurs);

        // Vérifier si tous les champs du formulaire sont remplis
        if ($urlValide && $couleurValide && $nomValide) {
            // Créer une instance de AgendaDao pour interagir avec la base de données
            $manager = new AgendaDao($pdo);

            // Vérifier si l'agenda existe déjà avec cette URL
            $agendaExiste = $manager->findURL($_POST['url']);

            if (!$agendaExiste) {
                // Créer une nouvelle instance d'Agenda avec les données du formulaire
                $nouvelAgenda = new Agenda($_POST['url'], $_POST['couleur'], $_POST['nom'], null);
                // Ajouter l'agenda dans la base de données
                $manager->ajouterAgenda($nouvelAgenda);

                // Retourner un message de succès
                $tableauErreurs[] = "Ajout réussi !";
                $this->genererVueAgenda($tableauErreurs);
            } else {
                // Si l'agenda existe déjà, afficher un message d'erreur
                $tableauErreurs[] = "Agenda avec cette URL existe déjà !";
                $this->genererVueAgenda($tableauErreurs);
            }
        } else {
            // Si le formulaire n'est pas correctement rempli, afficher la vue générique
            $this->genererVueAgenda($tableauErreurs);
        }
    }

    public function lister() {
        //recupération des catégories
        $manager = new AgendaDao($this->getPdo());
        $tableau = $manager->findAllAssoc();
        $agendas = $manager->hydrateAll($tableau);

        //Choix du template
        $template = $this->getTwig()->load('agenda.html.twig');


        //Affichage de la page
        echo $template->render(array(
            'agendas' => $agendas
        ));
    }

    


    function genererVue() {
        //Génération de la vue
        $template = $this->getTwig()->load('agenda.html.twig');
        echo $template->render(array());
    }

    public function genererVueAgenda(?array $erreurs) {
        //Génération de la vue agenda
        $template = $this->getTwig()->load('agenda.html.twig');
        echo $template->render(
            array(
                'message' => $erreurs
            )
        );
    }
}