<?php

use ICal\ICal;

require_once 'include.php';

$agenda = new Agenda(1, "https://calendar.google.com/calendar/ical/thibault.latxague%40gmail.com/public/basic.ics", "Rose", "AgendaTest", 1);
$calendrier = new ICal($agenda->getUrl());

$evenements = $calendrier->events();
$creneauxLibres = trouverCreneauxLibres($evenements, "2021-03-01", "2024-11-28");
var_dump($creneauxLibres);

echo '-------------------------------- <br>';

var_dump($evenements);

function trouverCreneauxLibres($evenements, $dateDeb, $dateFin) {
    $creneauxL = array();
    $dateDeb = new DateTime($dateDeb);
    $dateFin = new DateTime($dateFin);

    foreach($evenements as $evenement) {
        $dateDebut = new DateTime($evenement->dtstart);
        $dateFin = new DateTime($evenement->dtend);

        if($dateDebut < $dateDeb) {
            $dateDebut = $dateDeb;
        }

        if($dateFin > $dateFin) {
            $dateFin = $dateFin;
        }

        $creneauxL[] = new CreneauLibre(1, $dateDebut, $dateFin, 1);
    }

    return $creneauxL;
}