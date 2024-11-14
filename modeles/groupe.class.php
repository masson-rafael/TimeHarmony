<?php
/**
 * @author Thibault Latxague
 * @describe Classe des groupes
 * @version 0.1
 */

class Groupe {
    /**
     *
     * @var integer|null id du groupe
     */
    private int|null $id;
    /**
     *
     * @var string|null nom du groupe
     */
    private string|null $nom;
    /**
     *
     * @var string|null description du groupe
     */
    private string|null $description;
    /**
     *
     * @var integer|null idUtilisateur de l'utilisateur à qui appartient le groupe (chef de groupe)
     */
    private int|null $idUtilisateur;

    /**
     * Constructeur par défaut
     *
     * @param integer $id de l'agenda
     * @param string $nom de l'agenda
     * @param string $description de l'agenda
     * @param integer $idUtilisateur de l'utilisateur à qui appartient l'agenda (chef de groupe)
     */
    public function __construct(int $id, string $nom, string $description, int $idUtilisateur) {
        $this->id = $id;
        $this->nom = $nom;
        $this->description = $description;
        $this->idUtilisateur = $idUtilisateur;
    }

    /**
     * Get l'id du groupe
     *
     * @return integer $id du groupe
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * Get le nom du groupe
     *
     * @return string $nom du groupe
     */
    public function getNom(): string {
        return $this->nom;
    }

    /**
     * Get la description du groupe
     *
     * @return string $description du groupe
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * Get l'idUtilisateur du groupe
     *
     * @return integer $idUtilisateur du groupe
     */
    public function getIdUtilisateur(): int {
        return $this->idUtilisateur;
    }

    /**
     * Set l'id du groupe
     *
     * @param integer $id du groupe
     * @return void
     */
    public function setId(int $id): void {
        $this->id = $id;
    }

    /**
     * Set le nom du groupe
     *
     * @param string $nom du groupe
     * @return void
     */
    public function setNom(string $nom): void {
        $this->nom = $nom;
    }

    /**
     * Set la description du groupe
     *
     * @param string $description du groupe
     * @return void
     */
    public function setDescription(string $description): void {
        $this->description = $description;
    }

    /**
     * Set l'idUtilisateur du groupe
     *
     * @param integer $idUtilisateur du groupe
     * @return void
     */
    public function setIdUtilisateur(int $idUtilisateur): void {
        $this->idUtilisateur = $idUtilisateur;
    }
}