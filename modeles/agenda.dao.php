<?php

/**
 * @todo FAIS TA DOC FELIX (Faite par le magnifique Thibault)
 */

class AgendaDao{
    private ?PDO $pdo;

    public function __construct(?PDO $pdo=null){
        $this->pdo = $pdo;
    }

    public function getPdo(): ?PDO
    {
        return $this->pdo;
    }


    public function setPdo($pdo): void
    {
        $this->pdo = $pdo;
    }

    /**
     * Fonction permettant de retourner un objet Agenda à partir de son id
     * @param int|null $id id de l'agenda
     * @return Agenda|null l'objet Agenda
     */
    public function find(?int $id): ?Agenda {
        $resultat = null;
        $sql = "SELECT * FROM ".PREFIXE_TABLE."agenda WHERE id = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id" => $id));
        
        // On récupère sous forme de tableau associatif
        $result = $pdoStatement->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            // On crée un nouvel objet Agenda avec les données
            $agenda = new Agenda();
            $agenda->setId($result['id']);
            $agenda->setUrl($result['url']);
            $agenda->setCouleur($result['couleur']);
            $agenda->setNom($result['nom']);
            $agenda->setIdUtilisateur($result['IdUtilisateur']); 
            $resultat = $agenda;
        }

        return $resultat;
    }

    /**
     * Fonction permettant de retourner un booléen si l'url d'un agenda existe en bd
     * @param string|null $url url de l'agenda
     * @return bool|null true si l'agenda existe, false sinon
     */
    public function findURL(?string $url) : ?bool {
        $sql="SELECT * FROM ".PREFIXE_TABLE."agenda WHERE url= :url";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("url"=>$url));
        $result = $pdoStatement->fetch(PDO::FETCH_ASSOC);

        $agendaExiste = false;
        if($result){
            $agendaExiste = true;
        }

        return $agendaExiste;
    }

    /**
     * Fonction permettant de retourner tous les agendas d'un utilisateur en particulierde la base de données
     * @param int|null $idUtilisateur l'id de l'utilisateur
     * @return array|null le tableau des agendas
     */
    public function findAllByIdUtilisateur(?int $idUtilisateur): array {
        $sql="SELECT * FROM ".PREFIXE_TABLE."agenda WHERE idUtilisateur= :id";
        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$idUtilisateur));
        $agendas = $pdoStatement->fetchAll(PDO::FETCH_ASSOC);
        $agendas = $this->hydrateAll($agendas);
        return $agendas;
    }

    /**
     * Fonction permettant de retourner tous les agendas de la base de données
     * @return array|null le tableau des agendas
     */
    public function findAll() : ?array{
        $sql="SELECT * FROM ".PREFIXE_TABLE."agenda";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $agenda = $pdoStatement->fetchAll();
        $agenda = $this->hydrateAll($agenda);
        return $agenda;
    }

    /**
     * Fonction permettant d'ajouter un agenda dans la base de données
     * @param Agenda $agenda l'agenda à ajouter
     * @return void
     */
    public function ajouterAgenda(Agenda $agenda): void {
        // Insertion de l'agenda dans la base de données
        $sql = "INSERT INTO " . PREFIXE_TABLE . "agenda (url, couleur, nom, idUtilisateur) VALUES (:url, :couleur, :nom, :idUtilisateur)";
        $pdoStatement = $this->pdo->prepare($sql);
    
        // Exécution de la requête avec les valeurs de l'agenda
        $pdoStatement->execute([
            "url" => $agenda->getUrl(),
            "couleur" => $agenda->getCouleur(),
            "nom" => $agenda->getNom(),
            "idUtilisateur" => $agenda->getIdUtilisateur()
        ]);
    }

    /**
     * Fonction permettant de retourner un objet Agenda à partir d'un tableau associatif
     * @param array|null $tableauAssoc tableau associatif
     * @return Agenda|null l'objet Agenda
     */
    public function hydrate(?array $tableauAssoc): ?Agenda {
        $agenda = new Agenda($tableauAssoc['url'],$tableauAssoc['couleur'],$tableauAssoc['nom'],$tableauAssoc['idUtilisateur'],$tableauAssoc['id']);
        return $agenda;
    }

    /**
     * Fonction permettant de retourner un tableau d'objets Agenda à partir d'un tableau associatif
     * @param array|null $tableau tableau associatif
     * @return array|null le tableau d'objets Agenda
     */
    public function hydrateAll(?array $tableau): ?array {
        $agendas = [];
        foreach($tableau as $tableauAssoc){
            $agenda = $this->hydrate($tableauAssoc);
            $agendas[] = $agenda;
        }
        return $agendas;
    }

    /**
     * Fonction permettant de mofiier un agenda dans la bd
     * @param int|null $id id de l'agenda à modifier
     * @return void
     */
    public function modifierAgenda(?int $id, ?string $URL, ?string $couleur, ?string $nom): void {
        $sql = "UPDATE ".PREFIXE_TABLE."agenda SET url = :url, nom = :nom, couleur = :couleur WHERE id = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("url" => $URL, "nom" => $nom, "couleur" => $couleur, "id" => $id));
    }

    /**
     * Fonction permettant de supprimer un agenda dans la bd
     * @param int|null $id id de l'agenda à supprimer
     * @return void
     */
    public function supprimerAgenda(?int $id): void {
        $sql = "DELETE FROM ".PREFIXE_TABLE."agenda WHERE id = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id" => $id));
    }

    /**
     * Fonction qui retourne les agendas d'un utilisateur dont l'id est passé en parametre
     * @param int|null $id id de l'utilisateur dont on veut les agendas
     * @return array le tableau des agendas renvoyés
     */
    public function getAgendasUtilisateur(?int $id): ?array {
        $sql="SELECT * FROM ".PREFIXE_TABLE."agenda WHERE idUtilisateur = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id" => $id));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $agenda = $pdoStatement->fetchAll();
        $agendas = $this->hydrateAll($agenda);
        return $agenda;
    }
}