<?php

class Agenda {
    private int|null $id;
    private string|null $url;
    private string|null $couleur;
    private string|null $nom;
    private int|null $idUtilisateur;
    //private array|null $creneauxLibres;       -> nécessaire ?

    /* ------------- CONSTRUCTEUR ------------- */
    public function __construct(int $id, string $url, string $couleur, string $nom, int $idUtilisateur) {
        $this->id = $id;
        $this->url = $url;
        $this->couleur = $couleur;
        $this->nom = $nom;
        $this->idUtilisateur = $idUtilisateur;
    }

    /* ------------- GETTERS ------------- */
    public function getId(): int {
        return $this->id;
    }

    public function getUrl(): string {
        return $this->url;
    }

    public function getCouleur(): string {
        return $this->couleur;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function getIdUtilisateur(): int {
        return $this->idUtilisateur;
    }

    /* ------------- SETTERS ------------- */
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setUrl(string $url): void {
        $this->url = $url;
    }

    public function setCouleur(string $couleur): void {
        $this->couleur = $couleur;
    }

    public function setNom(string $nom): void {
        $this->nom = $nom;
    }

    public function setIdUtilisateur(int $idUtilisateur): void {
        $this->idUtilisateur = $idUtilisateur;
    }
}