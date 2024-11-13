<?php
/**
 * @author Thibault Latxague
 * @describe Classe des agendas
 * @version 0.1
 */

class AgendaDAO {
    /**
     *
     * @var PDO|null pdo
     */
    private ?PDO $pdo;

    /**
     * Constructeur par défaut
     *
     * @param PDO|null $pdo 
     */
    public function __construct(?PDO $pdo=null){
        $this->pdo = $pdo;
    }

    /**
     * Get la valeur du pdo
     *
     * @return PDO|null
     */
    public function getPdo(): ?PDO {
        return $this->pdo;
    }

    /**
     * Get la valeur du pdo
     *
     * @param PDO|null $pdo
     * @return void
     */
    public function setPdo(?PDO $pdo): void {
        $this->pdo = $pdo;
    }
}