<?php
/**
 * @author Thibault Latxague
 * @describe Controller de la page d'agendas
 * @version 0.1
 */

class Agenda {
    /**
     *
     * @var integer|null id de l'agenda
     */
    private int|null $id;

    /**
     *
     * @var string|null url de l'agenda
     */
    private string|null $url;

    /**
     *
     * @var string|null couleur de l'agenda
     */
    private string|null $couleur;

    /**
     *
     * @var string|null nom de l'agenda
     */
    private string|null $nom;

    /**
     *
     * @var integer|null idUtilisateur de l'utilisateur à qui appartient l'agenda
     */
    private int|null $idUtilisateur;

    /**
     * Constructeur par défaut
     *
     * @param integer $id de l'agenda
     * @param string $url permettant d'accéder à l'agenda
     * @param string $couleur de l'agenda
     * @param string $nom de l'agenda
     * @param integer $idUtilisateur de l'utilisateur à qui appartient l'agenda
     */
    public function __construct(int $id, string $url, string $couleur, string $nom, int $idUtilisateur) {
        $this->id = $id;
        $this->url = $url;
        $this->couleur = $couleur;
        $this->nom = $nom;
        $this->idUtilisateur = $idUtilisateur;
    }

    /**
     * Get l'id de l'agenda
     *
     * @return integer
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * Get l'url de l'agenda
     *
     * @return string
     */
    public function getUrl(): string {
        return $this->url;
    }

    /**
     * Get la couleur de l'agenda
     *
     * @return string
     */
    public function getCouleur(): string {
        return $this->couleur;
    }

    /**
     * Get le nom de l'agenda
     *
     * @return string
     */
    public function getNom(): string {
        return $this->nom;
    }

    /**
     * Get l'id de l'utilisateur à qui appartient l'agenda
     *
     * @return integer
     */
    public function getIdUtilisateur(): int {
        return $this->idUtilisateur;
    }

    /**
     * Set l'id de l'agenda
     *
     * @param integer $id de l'agenda
     * @return void
     */
    public function setId(int $id): void {
        $this->id = $id;
    }

    /**
     * Set l'url de l'agenda
     *
     * @param string $url de l'agenda
     * @return void
     */
    public function setUrl(string $url): void {
        $this->url = $url;
    }

    /**
     * Set la couleur de l'agenda
     *
     * @param string $couleur de l'agenda
     * @return void
     */
    public function setCouleur(string $couleur): void {
        $this->couleur = $couleur;
    }

    /**
     * Set le nom de l'agenda
     *
     * @param string $nom de l'agenda
     * @return void
     */
    public function setNom(string $nom): void {
        $this->nom = $nom;
    }

    /**
     * set l'id de l'utilisateur à qui appartient l'agenda
     *
     * @param integer $idUtilisateur de l'utilisateur
     * @return void
     */
    public function setIdUtilisateur(int $idUtilisateur): void {
        $this->idUtilisateur = $idUtilisateur;
    }
}