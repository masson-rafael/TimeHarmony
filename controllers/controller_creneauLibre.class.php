<?php

use ICal\ICal;
use Symfony\Component\Intl\Util\IcuVersion;

class ControllerCreneauLibre extends Controller
{
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }

    function obtenir()
    {

        $pdo = $this->getPdo();
            $managerCreneau = new CreneauLibreDao($pdo);

            // Vider la table pour éviter les récurrences
            $managerCreneau->supprimerCreneauxLibres();

        if (isset($_POST['urlIcs'])) {
            extract($_POST, EXTR_OVERWRITE);

            // Récupérer les événements de l'agenda
            $evenements = $this->recuperationEvenementsAgenda($urlIcs, $debut, $fin);

            // Trier les événements par date de début
            $evenements = $this->triEvenementsOrdreArrivee($evenements);

            // Recherche des créneaux libres
            $this->recherche($managerCreneau,'Europe/Paris', $debut, $fin, $evenements);

            $this->genererVueCreneaux($managerCreneau);
        } else {
            $this->genererVue();
        }
    }

    /*--------------------------------------------------------- Fonctions ----------------------------------------------------------- */

    function recuperationEvenementsAgenda(?string $url, ?string $debut, ?string $fin): ?array
    {
        // Charger le calendrier ICS via l'URL
        if (!$this->testerValiditeUrl($url)) {
            $this->genererVue();
            throw new Exception("L'URL n'est pas valide");
        } else {
            $calendrier = new ICal($url);
            // Récupérer tous les événements
            $evenements = $calendrier->eventsFromRange($debut, $fin);
        }
        return $evenements;
    }
    function triEvenementsOrdreArrivee(?array $evenement): ?array
    {
        // Trier les événements par date de début
        usort($evenement, function ($a, $b) {
            $dateDebutA = new DateTime($a->dtstart);
            $dateDebutB = new DateTime($b->dtstart);
            return $dateDebutA <=> $dateDebutB;
            /*L'opérateur <=> compare les deux valeurs et renvoie :
        -1 si $dateDebutA est inférieur à $dateDebutB,
        0 si $dateDebutA est égal à $dateDebutB,
        1 si $dateDebutA est supérieur à $dateDebutB.*/
        });
        return $evenement;
    }

        function recherche(?CreneauLibreDao $managerCreneau ,?String $timeZone, ?string $debut, ?string $fin, ?array $evenements)
        {

            $fuseauHoraire = new DateTimeZone($timeZone);
            $debutCourant = new DateTime($debut, $fuseauHoraire);
            $finCourant = new DateTime($fin, $fuseauHoraire);

            foreach ($evenements as $evenement) {
                // Convertir les heures d'événements en fuseau horaire local
                $debutEvenement = new DateTime($evenement->dtstart, $fuseauHoraire);
                $finEvenement = new DateTime($evenement->dtend, $fuseauHoraire);
                $debutEvenement->setTimezone($fuseauHoraire);
                $finEvenement->setTimezone($fuseauHoraire);

                if ($debutEvenement > $debutCourant) {
                    $managerCreneau->ajouterCreneauLibre(new CreneauLibre(null,$debutCourant,$debutEvenement,1));
                }
                $debutCourant = max($debutCourant, $finEvenement);
            }

                // Vérifier s'il reste des créneaux libres après le dernier événement
                if ($debutCourant < $finCourant) {
                    $managerCreneau->ajouterCreneauLibre(new CreneauLibre(null,$debutCourant,$finCourant,1));
        }
    }
    function genererVueCreneaux(?CreneauLibreDao $managerCreneau)
    {
        $tableau = $managerCreneau->findAllAssoc();
        $creneaux = $managerCreneau->hydrateAll($tableau);

        //Génération de la vue
        $template = $this->getTwig()->load('resultat.html.twig');
        echo $template->render(array(
            'creneauxLibres' => $creneaux
        ));
    }

    function genererVue()
    {
        //Génération de la vue
        $template = $this->getTwig()->load('creneauLibre.html.twig');
        echo $template->render(array());
    }

    
    function testerValiditeUrl(?string $url): bool
    {
        try {
            // @ nécessaire pour enlever les erreurs
            @$calendrier = new ICal($url);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    

    // function methodeTest()
    // {
    //     $this->genererVue();
    // }
}
