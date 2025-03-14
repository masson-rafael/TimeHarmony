<?php
/**
 * @author Félix Autant
 * @brief Classe des créneaux libres (DAO)
 * @version 0.1
 */

class CreneauLibreDao{
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

    /**
     * Supprimer tous les créneaux libres
     *
     * @return void
     */
    public function supprimerCreneauxLibres(): void {
        $sql = "DELETE FROM ".PREFIXE_TABLE."creneauLibre";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
    }

    /**
     * Ajouter un créneau libre
     *
     * @param CreneauLibre $creneauLibre créneau libre à ajouter
     * @return void
     */
    public function ajouterCreneauLibre(CreneauLibre $creneauLibre): void{
        // Préparation de la requête SQL
        $sql = "INSERT INTO ".PREFIXE_TABLE."creneauLibre (dateDebut, dateFin, idAgenda) VALUES (:dateDebut, :dateFin, :idAgenda)";
        $pdoStatement = $this->pdo->prepare($sql);

        // Exécution de la requête avec les valeurs formatées
        $pdoStatement->execute(array(
            // "id" => $creneauLibre->getId(),
            "dateDebut" => $creneauLibre->getDateDebut()->format('Y-m-d H:i:s'),
            "dateFin" => $creneauLibre->getDateFin()->format('Y-m-d H:i:s'),
            "idAgenda" => $creneauLibre->getIdAgenda() // Si nécessaire
        ));
    }

    /**
     * Trouve tous les créneaux libres de la table des créneaux libres
     *
     * @return array|null tableau des créneaux libres
     */
    public function findAllByIdUtilisateur(int $idUtilisateur): ?array {
        $sql="SELECT * FROM ".PREFIXE_TABLE."creneauLibre c JOIN ".PREFIXE_TABLE."agenda a ON c.idAgenda = a.id WHERE a.idUtilisateur = :idUtilisateur" ;
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("idUtilisateur" => $idUtilisateur));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $creneauxLibres = $pdoStatement->fetchAll();

        return $creneauxLibres;
    }

    /**
     * set a hydrater le tableau associatif
     *
     * @param array|null $tableauAssoc tableau associatif
     * @return CreneauLibre|null créneau libre
     */
    public function hydrate(?array $tableauAssoc): ?CreneauLibre {
        //Conversion de String en DateTime
        $dateDebut = new DateTime($tableauAssoc['dateDebut']);
        $dateFin = new DateTime($tableauAssoc['dateFin']);
        $creneau = new CreneauLibre($tableauAssoc['id'], $dateDebut,$dateFin,$tableauAssoc['idAgenda']);
        return $creneau;
    }

    /**
     * set a hydrater tous les créneaux libres
     *
     * @param array|null $tableau tableau associatif
     * @return array|null tableau des créneaux libres
     */
    public function hydrateAll(?array $tableau): ?array{
        $creneaux = [];
        foreach($tableau as $tableauAssoc){
            $creneau = $this->hydrate($tableauAssoc);
            $creneaux[] = $creneau;
        }
        return $creneaux;
    }
}