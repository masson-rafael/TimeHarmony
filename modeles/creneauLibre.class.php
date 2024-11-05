<?php

class CreneauLibre {
    private int|null $id;
    private DateTime|null $dateCreneau;
    private DateTime|null $heureDebut;
    private DateTime|null $heureFin;

    //private DateTime|null $dateDebut;     -> mieux car résultat algo principal
    //private DateTime|null $dateFin;

    private int|null $idAgenda;

    /* ------------- CONSTRUCTEUR ------------- */
    public function __construct(int $id, DateTime $dateCreneau, DateTime $heureDebut, DateTime $heureFin, int $idAgenda) {
        $this->id = $id;
        $this->dateCreneau = $dateCreneau;
        $this->heureDebut = $heureDebut;
        $this->heureFin = $heureFin;
        $this->idAgenda = $idAgenda;
    }

    /* ------------- GETTERS ------------- */
    public function getId(): int {
        return $this->id;
    }

    public function getDateCreneau(): DateTime {
        return $this->dateCreneau;
    }

    public function getHeureDebut(): DateTime {
        return $this->heureDebut;
    }

    public function getHeureFin(): DateTime {
        return $this->heureFin;
    }

    public function getIdAgenda(): int {
        return $this->idAgenda;
    }

    /* ------------- SETTERS ------------- */
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setDateCreneau(DateTime $dateCreneau): void {
        $this->dateCreneau = $dateCreneau;
    }

    public function setHeureDebut(DateTime $heureDebut): void {
        $this->heureDebut = $heureDebut;
    }

    public function setHeureFin(DateTime $heureFin): void {
        $this->heureFin = $heureFin;
    }

    public function setIdAgenda(int $idAgenda): void {
        $this->idAgenda = $idAgenda;
    }
}