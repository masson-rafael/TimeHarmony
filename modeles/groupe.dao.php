<?php

/**
 * @author Thibault Latxague
 * @brief Classe DAO des groupes
 * @version 0.1
 */

class GroupeDao
{
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
    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

    /**
     * Get la valeur du pdo
     *
     * @return PDO|null
     */
    public function getPdo(): ?PDO
    {
        return $this->pdo;
    }

    /**
     * Get la valeur du pdo
     *
     * @param PDO|null $pdo
     * @return void
     */
    public function setPdo(?PDO $pdo): void
    {
        $this->pdo = $pdo;
    }

    /**
     * Fonction permettant de trouver tous les groupes d'un utilisateur
     * 
     * @param int|null $id id de l'utilisateur
     * @return array tableau des groupes
     */
    public function findAll(?int $id): array
    {
        $sql = "SELECT * FROM " . PREFIXE_TABLE . "groupe g INNER JOIN " . PREFIXE_TABLE . "composer c ON c.idGroupe = g.id  WHERE c.idUtilisateur = :id";
        $pdoStatement = $this->pdo->prepare($sql);

        // Ajout des parametres
        $pdoStatement->execute(array("id" => $id));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $result = $pdoStatement->fetchAll();
        $result = $this->hydrateAll($result);

        return $result;
    }

    /**
     * Fonction permettant de trouver un groupe en fonction de son id
     * 
     * @param int|null $id id du groupe
     * @return Groupe|null objet groupe associé
     */
    public function find(?int $id): ?Groupe
    {
        $resultat = null;
        $sql = "SELECT * FROM " . PREFIXE_TABLE . "groupe WHERE id = :id";
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

    /**
     * Fonction permettant de transformer un tableau en objet groupe
     * 
     * @param array $tableau le tableau qu'on doit transformer
     * @return Groupe notre objet groupe
     */
    public function hydrate(array $tableau): Groupe
    {
        $groupe = new Groupe();
        $groupe->setId($tableau['id']);
        $groupe->setNom($tableau['nom']);
        $groupe->setDescription($tableau['description']);

        return $groupe;
    }

    /**
     * Fonction permettant de transformer un tableau de tableaux en tableau d'objet groupe
     * 
     * @param array|null $tableau les tableaux que l'on doit transformer
     * @return array|null le tableau de groupes
     */
    public function hydrateAll(?array $tableau): ?array
    {
        $groupes = [];
        foreach ($tableau as $tableauAssoc) {
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
    public function getGroupesFromUserId(?int $id): ?array
    {
        $resultat = null;
        $sql = "SELECT * FROM " . PREFIXE_TABLE . "groupe WHERE idChef = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id" => $id));

        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $tableau = $pdoStatement->fetchAll();

        // if ($tableau) {
        //     $resultat = $this->hydrateAll($tableau);
        // }

        foreach ($tableau as $result) {
            $resultat['groupe'][] = $this->hydrate($result);
            $resultat['nombrePersonnes'][] = $this->getNombrePersonnes($result['id']);
        }

        return $resultat;
    }

    /**
     * Fonction permettant de renvoyer le nombre de personnes dans un groupe
     * @param int|null $id id du groupe
     * @return int $nombrePersonnes nombre de personnes dans le groupe
     */
    public function getNombrePersonnes(?int $id): int
    {
        $sql = "SELECT COUNT(*) FROM " . PREFIXE_TABLE . "composer WHERE idGroupe = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id" => $id));
        $result = $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $nombrePersonnes = $pdoStatement->fetchColumn();
        return $nombrePersonnes;
    }

    /**
     * Fonction permettant de supprimer un groupe
     * @param int|null $id id du groupe
     * @return void
     */
    public function supprimerGroupe(?int $id): void
    {
        $sql = "DELETE FROM " . PREFIXE_TABLE . "groupe WHERE id = :id";
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
    public function creerGroupe(?int $idLeader, ?string $nom, ?string $description): void
    {
        $sql = "INSERT INTO " . PREFIXE_TABLE . "groupe (nom, description, idChef) VALUES (:nom, :description, :idChef)";
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
     * @return Groupe|null $tableau l'objet groupe correspondant
     */
    public function getGroupe(?int $idLeader, ?string $nom, ?string $description): ?Groupe
    {
        $sql = "SELECT * FROM " . PREFIXE_TABLE . "groupe WHERE nom = :nom AND description = :description AND idChef = :idChef";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array(
            ":nom" => $nom,
            ":description" => $description,
            ":idChef" => $idLeader,
        ));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $tableau = $pdoStatement->fetch();
        $tableau = $this->hydrate($tableau);

        return $tableau;
    }

    /**
     * Fonction permettant d'ajouter un utilisateur pour un groupe donné
     * @param int|null $id id du groupe
     * @param int|null $idContact id de la personne que l'on ajoute au groupe
     * @return void
     */
    public function ajouterMembreGroupe(?int $id, ?int $idContact): void
    {
        $sql = "INSERT INTO " . PREFIXE_TABLE . "composer (idGroupe, idUtilisateur) VALUES (:idGroupe, :idUtilisateur)";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array(
            ":idGroupe" => $id,
            ":idUtilisateur" => $idContact,
        ));
    }

    /**
     * Fonction permettant d'envoyer une demande d'ajout à un utilisateur pour un groupe donné
     * @param int|null $id id du groupe
     * @param int|null $idContact id de la personne que l'on ajoute au groupe
     * @return void
     */
    public function DemanderAjoutMembreGroupe(?int $id, ?int $idContact): void
    {
        $sql = "INSERT INTO " . PREFIXE_TABLE . "ajouter (idGroupe, idUtilisateur) VALUES (:idGroupe, :idUtilisateur)";
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
    public function groupeExiste(?string $nom, ?string $description): bool
    {
        $resultat = false;
        $sql = "SELECT * FROM " . PREFIXE_TABLE . "groupe WHERE nom = :nom AND description = :description";
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

    /**
     * Fonction permettant de modifier le nom et la description d'un groupe (pour l'instant)
     * @param int|null $id id du groupe
     * @param string|null $nom nom du groupe
     * @param string|null $description description du groupe
     * @return void
     */
    public function modifierGroupe(?int $id, ?string $nom, ?string $description): void
    {
        $sql = "
        UPDATE " . PREFIXE_TABLE . "groupe 
        SET nom = :nom, description = :description
        WHERE id = :id
        ";

        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array(
            ":id" => $id,
            ":nom" => $nom,
            ":description" => $description,
        ));
    }

    /**
     * Fonction permettant de renvoyer les utilisateurs d'un groupe
     * @param int|null $idGroupe id du groupe
     * @return array|null tableau des utilisateurs
     */

    public function getUsersFromGroup(?int $idGroupe): ?array
    {
        $sql = "SELECT * FROM " . PREFIXE_TABLE . "composer WHERE idGroupe = :idGroupe";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("idGroupe" => $idGroupe));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $result = $pdoStatement->fetchAll();
        return $result;
    }

    /**
     * Fonction permettant de supprimer un utilisateur d'un groupe
     * @param int|null $idGroupe id du groupe
     * @param int|null $idUtilisateur id de l'utilisateur
     * @return void
     */
    public function supprimerMembreGroupe(?int $idGroupe, ?int $idUtilisateur): void
    {
        $sql = "DELETE FROM " . PREFIXE_TABLE . "composer WHERE idGroupe = :idGroupe AND idUtilisateur = :idUtilisateur";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array(
            "idGroupe" => $idGroupe,
            "idUtilisateur" => $idUtilisateur,
        ));
    }
}
