<?php
/**
 * @author Thibault Latxague
 * @describe Classe des agendas
 * @version 0.1
 */

class Agenda {
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
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * Get l'url de l'agenda
     *
     * @return string $url de l'agenda
     */
    public function getUrl(): string {
        return $this->url;
    }

    /**
     * Get la couleur de l'agenda
     *
     * @return string $couleur de l'agenda
     */
    public function getCouleur(): string {
        return $this->couleur;
    }

    /**
     * Get le nom de l'agenda
     * 
     * @return string $nom de l'agenda
     */ 
    public function getNom(): string {
        return $this->nom;
    }

    /**
     * Get l'id de l'utilisateur à qui appartient l'agenda
     *
     * @return integer $idUtilisateur de l'utilisateur
     */
    public function getIdUtilisateur(): ?int {
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