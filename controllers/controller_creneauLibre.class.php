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
        //function obtenir($urlIcs, $debut, $fin) {
        // Exemple d'utilisation
        $urlIcs = "https://calendar.google.com/calendar/ical/thibault.latxague%40gmail.com/public/basic.ics"; // URL de votre calendrier ICS
        // Dates de début et de fin pour vérifier les créneaux libres
        $debut = '2024-10-07 00:00:00';
        $fin = '2024-10-12 18:45:00';

        // Récupérer les événements de l'agenda
        $evenements = $this->recuperationEvenementsAgenda($urlIcs, $debut, $fin);

        // Trier les événements par date de début
        $evenements = $this->triEvenementsOrdreArrivee($evenements);

        // Recherche des créneaux libres
        $tabCreneaux = $this->recherche('Europe/Paris', $debut, $fin, $evenements);

        // Vérifier s'il reste des créneaux libres après le dernier événement
        // La fonction recherche renvoie un tableau avec 3 éléments, d'où leur accès avec []
        $this->verifCreneauDernierEvent($tabCreneaux[1], $tabCreneaux[2], $tabCreneaux[0]);
        
        // Générer la vue
        // Nouvel appel du tableau car fonction precedente a modifié le tableau grâce à & (passage par référence)
        $this->genererVue($tabCreneaux[0]);
    }

    function recherche(?String $timeZone, ?string $debut, ?string $fin, ?array $evenements): ?array {
        $fuseauHoraire = new DateTimeZone($timeZone);
        $debutCourant = new DateTime($debut, $fuseauHoraire);
        $finCourant = new DateTime($fin, $fuseauHoraire);
        // Recherche des créneaux libres
        $creneauxLibres = array();

        foreach ($evenements as $evenement) {
            // Convertir les heures d'événements en fuseau horaire local
            $debutEvenement = new DateTime($evenement->dtstart, new DateTimeZone('UTC'));
            $finEvenement = new DateTime($evenement->dtend, new DateTimeZone('UTC'));
            $debutEvenement->setTimezone($fuseauHoraire);
            $finEvenement->setTimezone($fuseauHoraire);
            if ($debutEvenement > $debutCourant) {
                $creneauxLibres[] = [
                    'debut' => $debutCourant->format('Y-m-d H:i:s'),
                    'fin' => $debutEvenement->format('Y-m-d H:i:s'),
                ];
            }
            $debutCourant = max($debutCourant, $finEvenement);
        }
        return [$creneauxLibres, $debutCourant, $finCourant];
    }

    function verifCreneauDernierEvent(?DateTime $debutCourant, ?DateTime $finCourant, ?array &$creneauxLibres) {
        // Vérifier s'il reste des créneaux libres après le dernier événement
        if ($debutCourant < $finCourant) {
            $creneauxLibres[] = [
                'debut' => $debutCourant->format('Y-m-d H:i:s'),
                'fin' => $finCourant->format('Y-m-d H:i:s'),
            ];
        }
    }

    function genererVue(?array $creneaux) {
        //Génération de la vue
        $template = $this->getTwig()->load('creneauLibre.html.twig');
        echo $template->render(array(
            'creneauxLibres' => $creneaux
        ));
    }

    function recuperationEvenementsAgenda(?string $url, ?string $debut, ?string $fin): ?array {
        // Charger le calendrier ICS via l'URL
        $calendrier = new ICal($url);
        // Récupérer tous les événements
        $evenements = $calendrier->eventsFromRange($debut, $fin);
        return $evenements;
    }

    function triEvenementsOrdreArrivee(?array $evenement): ?array {
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
}