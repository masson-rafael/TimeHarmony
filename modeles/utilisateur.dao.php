<?php
/**
 * @author Thibault Latxague et Rafael Masson
 * @describe Classe des utilisateurs (DAO)
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
        if ($this->getPdo() === null) {
            throw new Exception('Connexion PDO non initialisée.');
        }        
        $pdoStatement = $this->getPdo()->prepare($sql);
        $pdoStatement->execute(array("id" => $id));
        
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Utilisateur');
        $utilisateur = $pdoStatement->fetch();
        
        if (!$utilisateur) {
            return null;
        }
        
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

    public function findAllContact(?int $id): array {
        // $sql="SELECT idUtilisateur2 FROM ".PREFIXE_TABLE."contacter WHERE idUtilisateur1= :id";
        // $pdoStatement = $this->pdo->prepare($sql);
        // // Ajout des parametres
        // $pdoStatement->execute(array("id"=>$id));
        // $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        // $result = $pdoStatement->fetchAll();

        $sql="SELECT * FROM timeharmony_utilisateur INNER JOIN timeharmony_contacter ON id = idUtilisateur2 WHERE idUtilisateur1 = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        // Ajout des parametres
        $pdoStatement->execute(array("id"=>$id));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $result = $pdoStatement->fetchAll();

        

        return $result;
    }

    public function getUserMail(?string $mail) : ?Utilisateur {
        $sql="SELECT * FROM ".PREFIXE_TABLE."utilisateur WHERE email= :email";
        $pdoStatement = $this->pdo->prepare($sql);
        // Ajout des parametres
        $pdoStatement->execute(array("email"=>$mail));
        $result = $pdoStatement->fetch(PDO::FETCH_ASSOC);
        $utilisateur = Utilisateur::createAvecParam($result['id'], $result['nom'], $result['prenom'], $result['email'], $result['motDePasse'], $result['photoDeProfil'], $result['estAdmin']);
        return $utilisateur;
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

    /**
     * set a hydrater le tableau associatif
     *
     * @param array|null $tableauAssoc tableau associatif
     * @return CreneauLibre|null créneau libre
     */
    public function hydrate(?array $tableauAssoc): ?Utilisateur {
        $utilisateur = new Utilisateur();
        $utilisateur->setId($tableauAssoc['id']);
        $utilisateur->setNom($tableauAssoc['nom']);
        $utilisateur->setPrenom($tableauAssoc['prenom']);
        $utilisateur->setEmail($tableauAssoc['email']);
        $utilisateur->setMotDePasse($tableauAssoc['motDePasse']);
        $utilisateur->setPhotoDeProfil($tableauAssoc['photoDeProfil']);
        $utilisateur->setEstAdmin($tableauAssoc['estAdmin']);
        return $utilisateur;
    }

    /**
     * set a hydrater tous les créneaux libres
     *
     * @param array|null $tableau tableau associatif
     * @return array|null tableau des créneaux libres
     */
    public function hydrateAll(?array $tableau): ?array{
        $utilisateurs = [];
        foreach($tableau as $tableauAssoc){
            $utilisateur = $this->hydrate($tableauAssoc);
            $utilisateurs[] = $utilisateur;
        }
        return $utilisateurs;
    }
     /** Suppression de l'utilisateur dans la BD
     * 
     * @param integer|null $id de l'utilisateur
     * @return void
     */
   public function supprimerUtilisateur(?int $id){
        $sql = "DELETE FROM ".PREFIXE_TABLE."utilisateur WHERE id = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id" => $id));
    }

    public function modifierUtilisateur(?int $id, ?string $nom, ?string $prenom, ?bool $estAdmin){
        $sql = "UPDATE ".PREFIXE_TABLE."utilisateur SET nom = :nom, prenom = :prenom, estAdmin = :estAdmin WHERE id = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array(
            "nom" => $nom,
            "prenom" => $prenom,
            "estAdmin" => $estAdmin,
            "id" => $id
        ));
    }

      /**
   * Met à jour le chemin de la photo de profil de l'utilisateur dans la base de données.
   *
   * @param int $id L'identifiant de l'utilisateur.
   * @param string $cheminPhoto Le chemin de la nouvelle photo de profil.
   */
  public function modifierPhotoProfil($id, $cheminPhoto) {
    $sql = "UPDATE ".PREFIXE_TABLE."utilisateur SET photoDeProfil = :photoDeProfil WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(array('photoDeProfil' => $cheminPhoto, 'id' => $id));
}

}