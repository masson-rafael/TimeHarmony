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
        $sql = "SELECT * FROM ".PREFIXE_TABLE."utilisateur WHERE id = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id" => $id));
        
        // On récupère sous forme de tableau associatif
        $result = $pdoStatement->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return null;
        }
        
        // On crée un nouvel objet Utilisateur avec les données
        $utilisateur = new Utilisateur();
        $utilisateur->setId($result['id']);
        $utilisateur->setNom($result['nom']);
        $utilisateur->setPrenom($result['prenom']);
        $utilisateur->setEmail($result['email']);
        $utilisateur->setMotDePasse($result['motDePasse']);
        $utilisateur->setPhotoDeProfil($result['photoDeProfil']);
        $utilisateur->setEstAdmin($result['estAdmin']);
        
        return $utilisateur;
    }

    public function findMail(?string $mail) : ?bool
    {
        $sql="SELECT * FROM ".PREFIXE_TABLE."utilisateur WHERE email= :email";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("email"=>$mail));
        $result = $pdoStatement->fetch(PDO::FETCH_ASSOC);

        $utilisateurExiste = false;
        if($result){
            $utilisateurExiste = true;
        }

        return $utilisateurExiste;
    }

    public function findAll() : ?array{
        $sql="SELECT * FROM ".PREFIXE_TABLE."utilisateur";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Utilisateur');
        $utilisateur = $pdoStatement->fetchAll();
        return $utilisateur;
    }

    public function ajouterUtilisateur(Utilisateur $utilisateur){
        $sql = "INSERT INTO ".PREFIXE_TABLE."utilisateur (nom, prenom, email, motDePasse, photoDeProfil, estAdmin) VALUES (:nom, :prenom, :email, :motDePasse, :photoDeProfil, :estAdmin)";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array(
            "nom" => $utilisateur->getNom(),
            "prenom" => $utilisateur->getPrenom(),
            "email" => $utilisateur->getEmail(),
            "motDePasse" => $utilisateur->getMotDePasse(),
            "photoDeProfil" => $utilisateur->getPhotoDeProfil(),
            "estAdmin" => $utilisateur->getEstAdmin()
        ));
    }
}