<?php
/**
 * @author Félix Autant
 * @describe Controller de la page d'agendas
 * @version 0.1
 */

class CreneauLibre {
    /**
     *
     * @var integer|null id du créneau libre
     */
    private int|null $id;

    /**
     *
     * @var DateTime|null date de début du créneau libre
     */
    private DateTime|null $dateDebut;

    /**
     *
     * @var DateTime|null date de fin du créneau libre
     */
    private DateTime|null $dateFin;

    /**
     *
     * @var integer|null idAgenda de l'agenda auquel appartient le créneau libre
     */
    private int|null $idAgenda;

    /**
     * Constructeur par défaut
     *
     * @param integer|null $id du créneau libre 
     * @param DateTime|null $dateDebut du créneau libre
     * @param DateTime|null $dateFin du créneau libre
     * @param integer|null $idAgenda de l'agenda auquel appartient le créneau libre
     */
    public function __construct(?int $id, ?DateTime $dateDebut, ?DateTime $dateFin, ?int $idAgenda) {
        $this->id = $id;
        $this->dateDebut = $dateDebut;
        $this->dateFin = $dateFin;
        $this->idAgenda = $idAgenda;
    }

    /**
     * Get l'id du créneau libre
     *
     * @return integer
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * Get la date de début du créneau libre
     *
     * @return DateTime
     */
    public function getDateDebut(): DateTime {
        return $this->dateDebut;
    }

    /**
     * get la date de fin du créneau libre
     *
     * @return DateTime
     */
    public function getDateFin(): DateTime {
        return $this->dateFin;
    }

    /**
     * get l'idAgenda de l'agenda auquel appartient le créneau libre
     *
     * @return integer
     */
    public function getIdAgenda(): int {
        return $this->idAgenda;
    }

    /**
     * set l'id du créneau libre
     *
     * @param integer $id du créneau libre
     * @return void
     */
    public function setId(int $id): void {
        $this->id = $id;
    }

    /**
     * set la date de début du créneau libre
     *
     * @param DateTime $dateDebut du créneau libre
     * @return void
     */
    public function setDateDebut(DateTime $dateDebut): void {
        $this->dateDebut = $dateDebut;
    }

    /**
     * set la date de fin du créneau libre
     *
     * @param DateTime $dateFin du créneau libre
     * @return void
     */
    public function setDateFin(DateTime $dateFin): void {
        $this->dateFin = $dateFin;
    }

    /**
     * set l'idAgenda de l'agenda auquel appartient le créneau libre
     *
     * @param integer $idAgenda de l'agenda auquel appartient le créneau libre
     * @return void
     */
    public function setIdAgenda(int $idAgenda): void {
        $this->idAgenda = $idAgenda;
    }
}