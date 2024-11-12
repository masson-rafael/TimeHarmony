<?php

class CreneauLibre {
    private int|null $id;
    private DateTime|null $dateDebut;     //  -> mieux car résultat algo principal
    private DateTime|null $dateFin;

    private int|null $idAgenda;

    /* ------------- CONSTRUCTEUR ------------- */
    public function __construct(?int $id, ?DateTime $dateDebut, ?DateTime $dateFin, ?int $idAgenda) {
        $this->id = $id;
        $this->dateDebut = $dateDebut;
        $this->dateFin = $dateFin;
        $this->idAgenda = $idAgenda;
    }

    /* ------------- GETTERS ------------- */
    public function getId(): int {
        return $this->id;
    }

    public function getDateDebut(): DateTime {
        return $this->dateDebut;
    }

    public function getDateFin(): DateTime {
        return $this->dateFin;
    }

    public function getIdAgenda(): int {
        return $this->idAgenda;
    }

    /* ------------- SETTERS ------------- */
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setDateDebut(DateTime $dateDebut): void {
        $this->dateDebut = $dateDebut;
    }

    public function setDateFin(DateTime $dateFin): void {
        $this->dateFin = $dateFin;
    }

    public function setIdAgenda(int $idAgenda): void {
        $this->idAgenda = $idAgenda;
    }
}