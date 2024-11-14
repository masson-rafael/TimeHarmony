<?php
/**
 * @author Thibault Latxague
 * @describe Classe des creneaux de RDV
 * @version 0.1
 */

class CreneauRDV {
    /**
     *
     * @var integer|null id du creneau de RDV
     */
    private int|null $id;
    /**
     *
     * @var DateTime|null date de début du creneau de RDV
     */
    private DateTime|null $dateDeb;
    /**
     *
     * @var DateTime|null date de fin du creneau de RDV
     */
    private DateTime|null $dateFin;
    /**
     *
     * @var string|null heure de fin du creneau de RDV
     */
    private string|null $heureFin;
    /**
     *
     * @var integer|null proportion utilisateur du groupe sur ceux disponibles
     */
    private int|null $pourcentageCoincidence;

    /**
     * Constructeur par défaut
     *
     * @param integer|null $id du creneau de RDV
     * @param DateTime|null $dateDeb du creneau de RDV
     * @param DateTime|null $dateFin du creneau de RDV
     * @param string|null $heureFin du creneau de RDV
     * @param integer|null $pourcentageCoincidence utilisateur du groupe sur ceux disponibles
     */
    public function __construct(?int $id, ?DateTime $dateDeb, ?DateTime $dateFin, ?string $heureFin, ?int $pourcentageCoincidence) {
        $this->id = $id;
        $this->dateDeb = $dateDeb;
        $this->dateFin = $dateFin;
        $this->heureFin = $heureFin;
        $this->pourcentageCoincidence = $pourcentageCoincidence;
    }

    /**
     * Get l'id du creneau de RDV
     *
     * @return integer $id du creneau de RDV
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * Get la date de début du creneau de RDV
     *
     * @return DateTime $dateDeb du creneau de RDV
     */
    public function getDateDeb(): DateTime {
        return $this->dateDeb;
    }

    /**
     * Get la date de fin du creneau de RDV
     *
     * @return DateTime $dateFin du creneau de RDV
     */
    public function getDateFin(): DateTime {
        return $this->dateFin;
    }

    /**
     * Get l'heure de fin du creneau de RDV
     *
     * @return string $heureFin du creneau de RDV
     */
    public function getHeureFin(): string {
        return $this->heureFin;
    }

    /**
     * Get la proportion utilisateur du groupe sur ceux disponibles
     *
     * @return integer $pourcentageCoincidence utilisateur du groupe sur ceux disponibles
     */
    public function getPourcentageCoincidence(): int {
        return $this->pourcentageCoincidence;
    }

    /**
     * Set l'id du creneau de RDV
     *
     * @param integer $id du creneau de RDV
     */
    public function setId(int $id): void {
        $this->id = $id;
    }

    /**
     * Set la date de début du creneau de RDV
     *
     * @param DateTime $dateDeb du creneau de RDV
     */
    public function setDateDeb(DateTime $dateDeb): void {
        $this->dateDeb = $dateDeb;
    }

    /**
     * Set la date de fin du creneau de RDV
     *
     * @param DateTime $dateFin du creneau de RDV
     */
    public function setDateFin(DateTime $dateFin): void {
        $this->dateFin = $dateFin;
    }

    /**
     * Set la proportion utilisateur du groupe sur ceux disponibles
     *
     * @param integer $pourcentageCoincidence utilisateur du groupe sur ceux disponibles
     */
    public function setPourcentageCoincidence(int $pourcentageCoincidence): void {
        $this->pourcentageCoincidence = $pourcentageCoincidence;
    }
}