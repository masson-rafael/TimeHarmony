<?php
/**
 * @author Thibault Latxague
 * @describe Classe DAO des creneaux de Rdv
 * @version 0.1
 */

class CreneauRdvDao {
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

    public function findAll(){
        $sql="SELECT * FROM ".PREFIXE_TABLE."creneaulibre";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'CreneauLibre');
        $creneaux = $pdoStatement->fetchAll();
        return $creneaux;
    }
}
