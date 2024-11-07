<?php

class UtilisateurDao{
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


    public function find(?int $id): ?Utilisateur
    {
        $sql="SELECT * FROM ".PREFIXE_TABLE."utilisateur WHERE id= :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$id));
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Utilisateur');
        $utilisateur = $pdoStatement->fetch();
        return $utilisateur;
    }

    public function findAll(){
        $sql="SELECT * FROM ".PREFIXE_TABLE."utilisateur";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Utilisateur');
        $utilisateur = $pdoStatement->fetchAll();
        return $utilisateur;
    }
}