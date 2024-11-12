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
        $utilisateur = new Utilisateur($result['id'], $result['nom'], $result['prenom'], $result['email'], $result['motDePasse'], $result['photoDeProfil'], $result['estAdmin']);
        
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
        //Insertion d'un utilisateur classique
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

    public function connexionReussie(?string $mail) : ?array {
        $sql = "SELECT motDePasse FROM ".PREFIXE_TABLE."utilisateur WHERE email = :email";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("email" => $mail));
        //$pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Utilisateur');

        if($pdoStatement->rowCount() == 0){
            return [false, null];
        }

        $motDePasse = $pdoStatement->fetch();
        return [true, $motDePasse[0]];
    }
}