<?php

require_once 'include.php';

$template = $twig->load('index.html.twig');

echo $template->render(array(
    'menu' => 'index'
));

use ICal\ICal;

function obtenirCreneauxLibres($urlIcs, $debut, $fin) {
    // Charger le calendrier ICS via l'URL
    $calendrier = new ICal($urlIcs);
    $fuseauHoraire = new DateTimeZone('Europe/Paris');

    // Récupérer tous les événements
    $evenements = $calendrier->eventsFromRange($debut, $fin);

    // Trier les événements par date de début
    usort($evenements, function ($a, $b) {
        $dateDebutA = new DateTime($a->dtstart);
        $dateDebutB = new DateTime($b->dtstart);
        return $dateDebutA <=> $dateDebutB;
        /*L'opérateur <=> compare les deux valeurs et renvoie :
        -1 si $dateDebutA est inférieur à $dateDebutB,
        0 si $dateDebutA est égal à $dateDebutB,
        1 si $dateDebutA est supérieur à $dateDebutB.*/
    });

    // Recherche des créneaux libres
    $creneauxLibres = array();
    $debutCourant = new DateTime($debut, $fuseauHoraire);
    $finCourant = new DateTime($fin, $fuseauHoraire);

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

    // Vérifier s'il reste des créneaux libres après le dernier événement
    if ($debutCourant < $finCourant) {
        $creneauxLibres[] = [
            'debut' => $debutCourant->format('Y-m-d H:i:s'),
            'fin' => $finCourant->format('Y-m-d H:i:s'),
        ];
    }

    return $creneauxLibres;
}

// Exemple d'utilisation
$urlIcs = "https://calendar.google.com/calendar/ical/thibault.latxague%40gmail.com/public/basic.ics"; // URL de votre calendrier ICS

// Dates de début et de fin pour vérifier les créneaux libres
$debut = '2024-10-07 00:00:00';
$fin = '2024-10-12 18:45:00';

$creneauxLibres = obtenirCreneauxLibres($urlIcs, $debut, $fin);

// Afficher les créneaux libres
foreach ($creneauxLibres as $creneau) {
    echo "Créneau libre de : " . $creneau['debut'] . " à " . $creneau['fin'] . "\n <br> <br>";
}
?>
