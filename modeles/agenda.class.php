<?php

use ICal\ICal;

/**
 * @author Thibault Latxague
 * @brief Classe des agendas
 * @version 0.1
 */

class Agenda
{
    /**
     *
     * @var integer|null id de l'agenda
     */
    private ?int $id;

    /**
     *
     * @var string url de l'agenda
     */
    private string $url;

    /**
     *
     * @var string couleur de l'agenda
     */
    private string $couleur;

    /**
     *
     * @var string nom de l'agenda
     */
    private string $nom;

    /**
     *
     * @var integer|null idUtilisateur de l'utilisateur à qui appartient l'agenda
     */
    private ?int $idUtilisateur;

    /**
     * Constructeur par défaut (avec idUtilisateur par défaut)
     *
     * @param integer $id de l'agenda
     * @param string $url permettant d'accéder à l'agenda
     * @param string $couleur de l'agenda
     * @param string $nom de l'agenda
     * @param integer $idUtilisateur de l'utilisateur à qui appartient l'agenda
     */
    public function __construct(?string $url = null, ?string $couleur = null, ?string $nom = null, ?int $idUtilisateur = 1, ?int $id = null)
    {
        $this->url = $url;
        $this->couleur = $couleur;
        $this->nom = $nom;
        $this->idUtilisateur = $idUtilisateur;
        $this->id = $id;
    }

    /* ------------- GETTERS ------------- */
    /**
     * Get l'id de l'agenda
     *
     * @return integer $id de l'agenda
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get l'url de l'agenda
     *
     * @return string $url de l'agenda
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Get la couleur de l'agenda
     *
     * @return string $couleur de l'agenda
     */
    public function getCouleur(): string
    {
        return $this->couleur;
    }

    /**
     * Get le nom de l'agenda
     * 
     * @return string $nom de l'agenda
     */
    public function getNom(): string
    {
        return $this->nom;
    }

    /**
     * Get l'id de l'utilisateur à qui appartient l'agenda
     *
     * @return integer $idUtilisateur de l'utilisateur
     */
    public function getIdUtilisateur(): ?int
    {
        return $this->idUtilisateur;
    }

    /**
     * Set l'id de l'agenda
     *
     * @param integer $id de l'agenda
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Set l'url de l'agenda
     *
     * @param string $url de l'agenda
     * @return void
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * Set la couleur de l'agenda
     *
     * @param string $couleur de l'agenda
     * @return void
     */
    public function setCouleur(string $couleur): void
    {
        $this->couleur = $couleur;
    }

    /**
     * Set le nom de l'agenda
     *
     * @param string $nom de l'agenda
     * @return void
     */
    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    /**
     * set l'id de l'utilisateur à qui appartient l'agenda
     *
     * @param integer $idUtilisateur de l'utilisateur
     * @return void
     */
    public function setIdUtilisateur(int $idUtilisateur): void
    {
        $this->idUtilisateur = $idUtilisateur;
    }

    /**
     * Recupere les evenements des agendas
     *
     * @param string|null $url de l'agenda
     * @param string|null $debut date de debut de la recherche
     * @param string|null $fin date de fin de la recherche
     * @return array|null tableau des evenements
     */
    public function recuperationEvenementsAgenda(?string $urlIcs, ?string $debut, ?string $fin, $allEvents): ?array
    {
        $newFin = date('Y-m-d', strtotime($fin . ' +1 day'));
        // Charger les événements du calendrier à partir de l'URL
        $calendrier = new ICal($urlIcs);
        $evenements = $calendrier->eventsFromRange($debut, $newFin);

        // Ajouter les événements à un tableau global
        $allEvents = array_merge($allEvents, $evenements);
        
        return $allEvents;
    }

    /**
     * Trie des evenements par ordre d'arrivee
     *
     * @param array|null $evenement tableau d'evenements bruts a trier
     * @return array|null tableau d'evenements tries
     */
    private function triEvenementsOrdreArrivee(?array $evenement): ?array
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

    /**
     * Fonction de recherche des créneaux libres
     *
     * @param string|null $timeZone Fuseau horaire de la recherche
     * @param string|null $debut date de debut de la recherche
     * @param string|null $fin Date de fin de la recherche
     * @param array|null $evenements tableau d'evenements triés
     * @return void
     */
    public function recherche(?string $timeZone, ?string $debut, ?string $fin, ?array $evenements): array
    {
        $creneauxLibres = array();

        $fuseauHoraire = new DateTimeZone($timeZone);
        $debutCourant = new DateTime($debut, $fuseauHoraire);
        $finCourant = new DateTime($fin, $fuseauHoraire);
        $finCourant = $finCourant->modify('+1 day');

        foreach ($evenements as $evenement) {
            // convertir les heures d'événements en fuseau horaire local
            $debutEvenement = new DateTime($evenement->dtstart, $fuseauHoraire);
            $finEvenement = new DateTime($evenement->dtend, $fuseauHoraire);
            $debutEvenement->setTimezone($fuseauHoraire);
            $finEvenement->setTimezone($fuseauHoraire);
            // var_dump($id);
            if ($debutEvenement > $debutCourant) {
                // $managerCreneau->ajouterCreneauLibre(new CreneauLibre(null, $debutCourant, $debutEvenement, $idAgenda));
                $creneauxLibres[] = new CreneauLibre(null, $debutCourant, $debutEvenement, null);
            }
            $debutCourant = max($debutCourant, $finEvenement);
        }

        // vérifier s'il reste des créneaux libres après le dernier événement
        if ($debutCourant < $finCourant) {
            // $managerCreneau->ajouterCreneauLibre(new CreneauLibre(null, $debutCourant, $finCourant, $idAgenda));
            $creneauxLibres[] = new CreneauLibre(null, $debutCourant, $finCourant, null);
        }

        // var_dump($creneauxLibres);

        return $creneauxLibres;
    }

    /**
     * Fusionner les evenements récupérer afin d'avoir des évènements triés et fusionnés
     *
     * @param string|null $events évènements à triés et à fusionner
     * @return array|object $mergedEvents tableau d'évènements
     */
    function mergeAgendas($events): array|object
    {
        // Convertir les plages horaires en DateTime et organiser les événements par ordre croissant
        usort($events, function ($a, $b) {
            return strtotime($a->dtstart) - strtotime($b->dtstart);
        });

        $mergedEvents = [];

        foreach ($events as $event) {
            $start = new DateTime($event->dtstart);
            $end = new DateTime($event->dtend);

            if (empty($mergedEvents)) {
                // Ajouter le premier événement
                $mergedEvents[] = $event;
            } else {
                // Dernier événement dans la liste fusionnée
                $lastMerged = end($mergedEvents);
                $lastMergedStart = new DateTime($lastMerged->dtstart);
                $lastMergedEnd = new DateTime($lastMerged->dtend);

                if ($start <= $lastMergedEnd) {
                    // Fusionner si chevauchement
                    $lastMerged->dtend = max($end, $lastMergedEnd)->format('Ymd\THis\Z');
                } else {
                    // Ajouter un nouvel événement
                    $mergedEvents[] = $event;
                }
            }
        }

        return $mergedEvents;
    }
}
