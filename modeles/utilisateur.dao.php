<?php
/**
 * @author Thibault Latxague et Rafael Masson
 * @describe Controller de la page de recherche de créneaux libres
 * @version 0.1
 */

class UtilisateurDao{
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
     * Set la valeur du pdo
     *
     * @param PDO|null $pdo
     * @return void
     */
    public function setPdo(?PDO $pdo): void {
        $this->pdo = $pdo;
    }

    /**
     * Trouver un utilisateur par son id
     *
     * @param integer|null $id de l'ustilisateur
     * @return Utilisateur|null objet utiisateur
     */
    public function find(?int $id): ?Utilisateur {
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

    /**
     * Recupere un booleen si le mail existe dans la BD
     * 
     * @param string|null $mail de l'utilisateur
     * @return boolean|null si l'utilisateur existe
     */
    public function findMail(?string $mail) : ?bool {
        $sql="SELECT * FROM ".PREFIXE_TABLE."utilisateur WHERE email= :email";
        $pdoStatement = $this->pdo->prepare($sql);
        // Ajout des parametres
        $pdoStatement->execute(array("email"=>$mail));
        $result = $pdoStatement->fetch(PDO::FETCH_ASSOC);

        $utilisateurExiste = false;
        if($result){
            $utilisateurExiste = true;
        }

        return $utilisateurExiste;
    }

    /**
     * Renvoie tous les utilisateurs
     *
     * @return array|null tableau d'utilisateurs
     */
    public function findAll() : ?array{
        $sql="SELECT * FROM ".PREFIXE_TABLE."utilisateur";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Utilisateur');
        $utilisateur = $pdoStatement->fetchAll();
        return $utilisateur;
    }

    /**
     * Requete d'insertion d'un utilisateur dans la BD
     *
     * @param Utilisateur $utilisateur que l'on veut ajouter
     * @return void
     */
    public function ajouterUtilisateur(Utilisateur $utilisateur){
        //Insertion d'un utilisateur classique
        $sql = "INSERT INTO ".PREFIXE_TABLE."utilisateur (nom, prenom, email, motDePasse, photoDeProfil, estAdmin) VALUES (:nom, :prenom, :email, :motDePasse, :photoDeProfil, :estAdmin)";
        $pdoStatement = $this->pdo->prepare($sql);
        // Parametrage de la requete
        $pdoStatement->execute(array(
            "nom" => $utilisateur->getNom(),
            "prenom" => $utilisateur->getPrenom(),
            "email" => $utilisateur->getEmail(),
            "motDePasse" => $utilisateur->getMotDePasse(),
            "photoDeProfil" => $utilisateur->getPhotoDeProfil(),
            "estAdmin" => $utilisateur->getEstAdmin()
        ));
    }

    /**
     * Verifie que la connexion s'est bien passée
     *
     * @param string|null $mail de l'utilisateur
     * @return array|null tableau contenant un booleen et soit null soit le mot de passe crypté
     */
    public function connexionReussie(?string $mail) : ?array {
        $sql = "SELECT motDePasse FROM ".PREFIXE_TABLE."utilisateur WHERE email = :email";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("email" => $mail));

        if($pdoStatement->rowCount() == 0){
            return [false, null];
        }

        $motDePasse = $pdoStatement->fetch();
        return [true, $motDePasse[0]];
    }
}