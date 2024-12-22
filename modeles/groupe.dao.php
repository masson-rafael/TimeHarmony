<?php
/**
 * @author Thibault Latxague
 * @describe Classe DAO des groupes
 * @version 0.1
 */

class GroupeDao {
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

    public function findAll(?int $id): array {
        $sql="SELECT * FROM ".PREFIXE_TABLE."groupe g INNER JOIN ".PREFIXE_TABLE."composer c ON c.idGroupe = g.id  WHERE c.idUtilisateur = :id";
        $pdoStatement = $this->pdo->prepare($sql);

        // Ajout des parametres
        $pdoStatement->execute(array("id"=>$id));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $result = $pdoStatement->fetchAll();
        
        return $result;
    }

    public function find(?int $id): ?Groupe {
        $sql = "SELECT * FROM ".PREFIXE_TABLE."groupe WHERE id = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id" => $id));
        
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $tableau = $pdoStatement->fetch();
        $groupe = $this->hydrate($tableau);
    
        
        if (!$groupe) {
            return null;
        }
        
        return $groupe;
    }

    public function hydrate(array $tableau): Groupe{
        $groupe = new Groupe();
        $groupe->setId($tableau['id']);
        $groupe->setNom($tableau['nom']);
        $groupe->setDescription($tableau['description']);
        
        return $groupe;
    }

    public function hydrateAll(?array $tableau): ?array{
        $groupes = [];
        foreach($tableau as $tableauAssoc){
            $groupe = $this->hydrate($tableauAssoc);
            $groupes[] = $groupe;
        }
        return $groupes;
    }

    public function getGroupeFromUserId(?int $id): ?array {
        $sql = "SELECT * FROM ".PREFIXE_TABLE."groupe WHERE idChef = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id" => $id));
        
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $tableau = $pdoStatement->fetchAll();
        
        if (!$tableau) {
            return null;
        }
        
        return $tableau;
    }
}