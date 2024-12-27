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
        $resultat = null;
        $sql = "SELECT * FROM ".PREFIXE_TABLE."groupe WHERE id = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id" => $id));
        
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $tableau = $pdoStatement->fetch();
        $groupe = $this->hydrate($tableau);
    
        
        if ($groupe) {
            $resultat = $groupe;
        }
        
        return $resultat;
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

    /**
     * Fonction qui retourne un tableau de groupes dont l'id du chef est passé en parametre
     * @param int|null $id id du chef de groupe
     * @return array|null tableau des groupes
     */
    public function getGroupeFromUserId(?int $id): ?array {
        $resultat = null;
        $sql = "SELECT * FROM ".PREFIXE_TABLE."groupe WHERE idChef = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id" => $id));
        
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $tableau = $pdoStatement->fetchAll();
        
        if ($tableau) {
            $resultat = $tableau;
        }
        
        return $resultat;
    }

    /**
     * Fonction permettant de supprimer un groupe
     * @param int|null $id id du groupe
     * @return void
     */
    public function supprimerGroupe(?int $id): void {
        $sql = "DELETE FROM ".PREFIXE_TABLE."groupe WHERE id = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id" => $id));
    }

    /**
     * Fonction permettant de créer un groupe
     * @param int|null $idLeader id de l'utilisateur qui cree le groupe
     * @param string|null $nom nom du groupe
     * @param string|null $description description du groupe
     * @return void
     */
    public function creerGroupe(?int $idLeader, ?string $nom, ?string $description): void {
        $sql = "INSERT INTO ".PREFIXE_TABLE."groupe (nom, description, idChef) VALUES (:nom, :description, :idChef)";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array(
            ":nom" => $nom,
            ":description" => $description,
            ":idChef" => $idLeader,
        ));
    }

    /**
     * Fonction permettant de renvoyer l'id du groupe correspondant aux champs donnés en parametre
     * @param int|null $idLeader id de l'utilisateur qui a cree le groupe
     * @param string|null $nom nom du groupe
     * @param string|null $description description du groupe
     * @return array|null $tableau le tableau de réponses. Généralement l'id du groupe correspondant
     */
    public function getIdGroupe(?int $idLeader, ?string $nom, ?string $description): ?array {
        $sql = "SELECT id FROM ".PREFIXE_TABLE."groupe WHERE nom = :nom AND description = :description AND idChef = :idChef";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array(
            ":nom" => $nom,
            ":description" => $description,
            ":idChef" => $idLeader,
        ));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $tableau = $pdoStatement->fetch();
        
        return $tableau;
    }

    /**
     * Fonction permettant d'ajouter des utilisateurs à un groupe donné
     * @param int|null $id id du groupe
     * @param int|null $idContact id de la personne que l'on ajoute au groupe
     * @return void
     */
    public function ajouterMembreGroupe(?int $id, ?int $idContact): void {
        $sql = "INSERT INTO ".PREFIXE_TABLE."composer (idGroupe, idUtilisateur) VALUES (:idGroupe, :idUtilisateur)";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array(
            ":idGroupe" => $id,
            ":idUtilisateur" => $idContact,
        ));
    }

    /**
     * Fonction permettant de verifier si un groupe existe déjà en BD
     * @param string|null $nom nom du groupe
     * @param string|null $description description du groupe
     * @return bool true si le groupe existe, false sinon
     */
    public function groupeExiste(?string $nom, ?string $description): bool {
        $resultat = false;
        $sql = "SELECT * FROM ".PREFIXE_TABLE."groupe WHERE nom = :nom AND description = :description";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array(
            ":nom" => $nom,
            ":description" => $description,
        ));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $tableau = $pdoStatement->fetch();
        
        if ($tableau) {
            $resultat = true;
        }
        
        return $resultat;
    }
}