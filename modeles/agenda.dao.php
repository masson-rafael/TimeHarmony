<?php

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


    public function find(?int $id): ?Agenda
    {
        $sql = "SELECT * FROM ".PREFIXE_TABLE."agenda WHERE id = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id" => $id));
        
        // On récupère sous forme de tableau associatif
        $result = $pdoStatement->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return null;
        }
        
        // On crée un nouvel objet Agenda avec les données
        $agenda = new Agenda();
        $agenda->setId($result['id']);
        $agenda->setUrl($result['url']);
        $agenda->setCouleur($result['couleur']);
        $agenda->setNom($result['nom']);
        $agenda->setIdUtilisateur($result['IdUtilisateur']);  
        return $agenda;
    }

    public function findURL(?string $url) : ?bool
    {
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

    public function findAll() : ?array{
        $sql="SELECT * FROM ".PREFIXE_TABLE."agenda";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Agenda');
        $agenda = $pdoStatement->fetchAll();
        return $agenda;
    }
    public function ajouterAgenda(Agenda $agenda)
    {
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
    


}