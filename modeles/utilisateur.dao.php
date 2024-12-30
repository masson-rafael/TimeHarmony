<?php

/**
 * @author Thibault Latxague et Rafael Masson
 * @describe Classe des utilisateurs (DAO)
 * @version 0.2
 */

class UtilisateurDao
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
     * Set la valeur du pdo
     *
     * @param PDO|null $pdo
     * @return void
     */
    public function setPdo(?PDO $pdo): void
    {
        $this->pdo = $pdo;
    }

    /**
     * Trouver un utilisateur par son id
     *
     * @param integer|null $id de l'ustilisateur
     * @return Utilisateur|null objet utiisateur
     */
    public function find(?int $id): ?Utilisateur {
        $resultat = null;
        $sql = "SELECT * FROM ".PREFIXE_TABLE."utilisateur WHERE id = :id";
        if ($this->getPdo() === null) {
            throw new Exception('Connexion PDO non initialisée.');
        }        
        $pdoStatement = $this->getPdo()->prepare($sql);
        $pdoStatement->execute(array("id" => $id));
        $utilisateur = $pdoStatement->fetch(PDO::FETCH_ASSOC);

        if ($utilisateur) {
            $resultat = $this->hydrate($utilisateur);
        }

        return $resultat;
    }

    /**
     * Recupere un booleen si le mail existe dans la BD
     * 
     * @param string|null $mail de l'utilisateur
     * @return boolean|null si l'utilisateur existe
     */
    public function findMail(?string $mail): ?bool {
        $sql = "SELECT * FROM " . PREFIXE_TABLE . "utilisateur WHERE email= :email";
        $pdoStatement = $this->pdo->prepare($sql);
        // Ajout des parametres
        $pdoStatement->execute(array("email" => $mail));
        $result = $pdoStatement->fetch(PDO::FETCH_ASSOC);
        $utilisateurExiste = false;

        if ($result) {
            $utilisateurExiste = true;
        }

        return $utilisateurExiste;
    }

    /**
     * Récupère une liste d'utilisateurs qui sont des contacts de l'utilisateur dont l'id est passé en param
     * @param int|null $id L'identifiant de l'utilisateur.
     * @return array|null Un tableau d'utilisateurs.
     */
    public function findAllContact(?int $id): array {
        $sql="SELECT * FROM timeharmony_utilisateur INNER JOIN timeharmony_contacter ON id = idUtilisateur2 WHERE idUtilisateur1 = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id" => $id));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $result = $pdoStatement->fetchAll();
        $result = $this->hydrateAll($result);
        return $result;
    }

    /**
     * Fonction qui recupere les ids de tout les utilisateurs qui ne sont pas en contact avec l'utilisateur $id
     * (et qui n'ont pas de demande de contact avec lui en cours)
     * 
     * @param int $id utilisateur dont on cherche toutes les personnes potentiellement contactables
     * @return array tableau d'identifiants utilisateurs
     */
    public function recupererIdsUtilisateursPasContacts(?int $id): array {
        $sql="SELECT * FROM ".PREFIXE_TABLE."utilisateur u WHERE u.id != :id AND u.id NOT IN ( 
        SELECT idUtilisateur2 FROM ".PREFIXE_TABLE."contacter WHERE idUtilisateur1 = :id UNION
        SELECT idUtilisateur1 FROM ".PREFIXE_TABLE."contacter WHERE idUtilisateur2 = :id UNION
        SELECT idUtilisateur2 FROM ".PREFIXE_TABLE."demander WHERE idUtilisateur1 = :id UNION
        SELECT idUtilisateur1 FROM ".PREFIXE_TABLE."demander WHERE idUtilisateur2 = :id)";
        $pdoStatement = $this->pdo->prepare($sql);
        // Ajout des parametres
        $pdoStatement->execute(array("id"=>$id));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $result = $pdoStatement->fetchAll();

        return $result;
    }

    /**
     * Recupere un objet utilisateur à partir de son mail
     * @param string|null $mail de l'utilisateur
     * @return Utilisateur|null utilisateur
     */
    public function getUserMail(?string $mail): ?Utilisateur {
        $sql = "SELECT * FROM " . PREFIXE_TABLE . "utilisateur WHERE email= :email";
        $pdoStatement = $this->pdo->prepare($sql);
        // Ajout des parametres
        $pdoStatement->execute(array("email" => $mail));
        $result = $pdoStatement->fetch(PDO::FETCH_ASSOC);
        $utilisateur = $this->hydrate($result);
        return $utilisateur;
    }

    /**
     * Renvoie tous les utilisateurs
     *
     * @return array|null tableau d'objets utilisateurs
     */
    public function findAll(): ?array {
        $sql = "SELECT * FROM " . PREFIXE_TABLE . "utilisateur";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $utilisateur = $pdoStatement->fetchAll(PDO::FETCH_ASSOC);
        $utilisateur = $this->hydrateAll($utilisateur);
        return $utilisateur;
    }

    /**
     * Requete d'insertion d'un utilisateur dans la BD
     *
     * @param Utilisateur $utilisateur que l'on veut ajouter
     * @return void
     */
    public function ajouterUtilisateur(Utilisateur $utilisateur): void {
        //Insertion d'un utilisateur classique
        $sql = "INSERT INTO " . PREFIXE_TABLE . "utilisateur (nom, prenom, email, motDePasse, photoDeProfil, estAdmin) VALUES (:nom, :prenom, :email, :motDePasse, :photoDeProfil, :estAdmin)";
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
    public function connexionReussie(?string $mail): ?array {
        $resultat = [false, null];
        $sql = "SELECT motDePasse FROM " . PREFIXE_TABLE . "utilisateur WHERE email = :email";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("email" => $mail));

        if ($pdoStatement->rowCount() != 0) {
            $resultat = [true, $pdoStatement->fetch()[0]];
        }

        return $resultat;
    }

    /**
     * set a hydrater le tableau associatif
     *
     * @param array|null $tableauAssoc tableau associatif
     * @return Utilisateur|null $utilisateur objet utilisateur
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
        $utilisateur->setTentativesEchouees($tableauAssoc['tentativesEchouees']);
        $tableauAssoc['dateDernierEchecConnexion'] == null ? $utilisateur->setDateDernierEchecConnexion(null) : $utilisateur->setDateDernierEchecConnexion(new DateTime($tableauAssoc['dateDernierEchecConnexion']));
        $utilisateur->setTokenReinitialisation($tableauAssoc['token']);
        $tableauAssoc['dateExpirationToken'] == null ? $utilisateur->setDateExpirationToken(null) : $utilisateur->setDateExpirationToken(new DateTime($tableauAssoc['dateExpirationToken']));
        $utilisateur->setStatutCompte($tableauAssoc['statutCompte']);
        return $utilisateur;
    }

    /**
     * Fonction qui sert a hydrater tous les utilisateurs
     *
     * @param array|null $tableau tableau associatif
     * @return array|null tableau des utilisateurs
     */
    public function hydrateAll(?array $tableau): ?array {
        $utilisateurs = [];
        foreach ($tableau as $tableauAssoc) {
            $utilisateur = $this->hydrate($tableauAssoc);
            $utilisateurs[] = $utilisateur;
        }
        return $utilisateurs;
    }

    /**
     * Procedure qui ajoute une demande de contact en base de donnees
     * 
     * @param int $id1 identifiant de l'utilisateur qui fait la demande
     * @param int $id2 identifiant de l'utilisateur qui recoit la demande
     * @return void
     */
    public function ajouterDemandeContact(?int $id1, ?int $id2): void {
        $sql = "INSERT INTO ". PREFIXE_TABLE ."demander (idUtilisateur1, idUtilisateur2) VALUES (:id1, :id2)";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id1" => $id1, "id2" => $id2));
    }

    /**
     * Procedure qui supprime un contact entre 2 utilisateurs en base de donnees
     * 
     * @param int $id1 identifiant de l'utilisateur 1
     * @param int $id2 identifiant de l'utilisateur 2
     * @return void
     */
    public function supprimerContact(?int $id1, ?int $id2): void {
        $sql = "DELETE FROM ".PREFIXE_TABLE."contacter WHERE idUtilisateur1= :id1 AND idUtilisateur2= :id2";
        $pdoStatement = $this->pdo->prepare($sql);
        // Ajout des parametres
        $pdoStatement->execute(array("id1" => $id1, "id2" => $id2));

        $sql = "DELETE FROM ".PREFIXE_TABLE."contacter WHERE idUtilisateur1= :id2 AND idUtilisateur2= :id1";
        $pdoStatement = $this->pdo->prepare($sql);
        // Ajout des parametres
        $pdoStatement->execute(array("id1" => $id1, "id2" => $id2));
    }

    /** Suppression de l'utilisateur dans la BD
     * 
     * @param integer|null $id de l'utilisateur
     * @return void
     */
    public function supprimerUtilisateur(?int $id): void {
        $sql = "DELETE FROM " . PREFIXE_TABLE . "utilisateur WHERE id = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id" => $id));
    }

    /**
     * Modifie un utilisateur dans la BD
     * 
     * @param integer|null $id de l'utilisateur 
     * @param string|null $nom de l'utilisateur 
     * @param string|null $prenom de l'utilisateur
     * @param string|null $email de l'utilisateur
     * @param boolean|null $estAdmin de l'utilisateur
     * @param string|null $photoDeProfil de l'utilisateur
     * @return void
     */
    public function modifierUtilisateur(?int $id, ?string $nom, ?string $prenom, ?string $email, ?bool $estAdmin, ?string $photoDeProfil): void {
        $sql = "UPDATE ".PREFIXE_TABLE."utilisateur SET nom = :nom, prenom = :prenom, email = :email, estAdmin = :estAdmin, photoDeProfil = :pdp WHERE id = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $estAdmin == false ? $estAdmin = 0 : $estAdmin = 1;
        $pdoStatement->execute(array(
            "nom" => $nom,
            "prenom" => $prenom,
            "email" => $email,
            "estAdmin" => $estAdmin,
            "id" => $id,
            "pdp" => $photoDeProfil
        ));
    }

    /**
     * Met à jour le chemin de la photo de profil de l'utilisateur dans la base de données.
     *
     * @param int $id L'identifiant de l'utilisateur.
     * @param string $cheminPhoto Le chemin de la nouvelle photo de profil.
     * @return void
     */
    public function modifierPhotoProfil(?int $id, ?string $cheminPhoto): void {
        $sql = "UPDATE ".PREFIXE_TABLE."utilisateur SET photoDeProfil = :photoDeProfil WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array('photoDeProfil' => $cheminPhoto, 'id' => $id));
    }

    /**
     * Reinitialise le mot de passe de l'utilisateur
     * 
     * @param integer|null $id de l'utilisateur
     * @param string|null $mdp de l'utilisateur
     * @return void
     */
    public function reinitialiserMotDePasse(?int $id, ?string $mdp): void {
        $sql = "UPDATE ".PREFIXE_TABLE."utilisateur SET motDePasse = :mdp WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array('mdp' => $mdp, 'id' => $id));
    }

    /**
     * Recupere l'id de l'utilisateur à partir de son mail
     * @param string|null $mail de l'utilisateur
     * @return integer|null id de l'utilisateur
     */
    public function getIdFromMail(?string $mail) : ?int {
        $sql = "SELECT id FROM ".PREFIXE_TABLE."utilisateur WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array('email' => $mail));
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['id'];
    }

    /**
     * Fonction qui renvoie un tableau d'utilisateur qui sont des admins
     * @return array|null liste des admins
     */
    public function getAdministrateurs(): array {
        $sql = "SELECT * FROM ".PREFIXE_TABLE."utilisateur WHERE estAdmin = 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = $this->hydrateAll($result);
        return $result;
    }

    /**
     * Fonction permettant de renvoyer les demandes envoyées par l'utilisateur dont l'id est donné en paramètre
     * @param int|null $id id de l'utilisateur dont on veut les demandes envoyées
     * @return array|null Liste des demandes ou null si aucune demande n'existe
     */
    public function getDemandesEnvoyees(?int $id): ?array {
        $sql = "SELECT U.*
                FROM " . PREFIXE_TABLE . "utilisateur U
                JOIN " . PREFIXE_TABLE . "demander D ON D.idUtilisateur2 = U.id
                WHERE D.idUtilisateur1 = :idDemandeur";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idDemandeur' => $id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $result ?: null;
    }

    /**
     * Fonction permettant de renvoyer les demandes recues par l'utilisateur dont l'id est donné en parametre
     * @param int|null $id id de l'utilisateur dont on veut les demandes recues
     */
    public function getDemandesRecues(?int $id): ?array {
        $sql = "SELECT U.*
        FROM " . PREFIXE_TABLE . "utilisateur U
        JOIN " . PREFIXE_TABLE . "demander D ON D.idUtilisateur1 = U.id
        WHERE D.idUtilisateur2 = :idDemandeur";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idDemandeur' => $id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    /**
     * Supprime une demande envoyée par un utilisateur à un autre utilisateur.
     *
     * @param int|null $idEnvoyeur ID de l'utilisateur ayant envoyé la demande.
     * @param int|null $idReceveur ID de l'utilisateur ayant reçu la demande.
     * @return void
     */
    public function supprimerDemandeEnvoyee(?int $idEnvoyeur, ?int $idReceveur): void {
        $sql = "DELETE FROM ".PREFIXE_TABLE."demander WHERE idUtilisateur1 = :id1 AND idUtilisateur2= :id2";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array('id1' => $idEnvoyeur, 'id2' => $idReceveur));
    }

    /**
     * Refuse une demande reçue par un utilisateur.
     *
     * @param int|null $idReceveur ID de l'utilisateur ayant reçu la demande.
     * @param int|null $idDemandeur ID de l'utilisateur ayant envoyé la demande.
     * @return void
     */
    public function refuserDemande(?int $idReceveur, ?int $idDemandeur): void {	
        $sql = "DELETE FROM ".PREFIXE_TABLE."demander WHERE idUtilisateur1 = :id1 AND idUtilisateur2= :id2";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array('id1' => $idDemandeur, 'id2' => $idReceveur));
    }

    /**
     * Accepte une demande reçue par un utilisateur.
     *
     * @param int|null $idReceveur ID de l'utilisateur ayant reçu la demande.
     * @param int|null $idDemandeur ID de l'utilisateur ayant envoyé la demande.
     * @return void
     */
    public function accepterDemande(?int $idReceveur, ?int $idDemandeur): void {	
        /**
         * Etape 1 : Ajout dans la table contacter la relation ($idReceveur, $idDemandeur)
         * Etape 2 : Ajout dans la table contacter la relation ($idDemandeur, $idReceveur)
         * Etape 3 : Supprimer dans la table demander la relation ($idDemandeur, $idReceveur)
         */
        $sql = "INSERT INTO ".PREFIXE_TABLE."contacter (idUtilisateur1, idUtilisateur2) VALUES (:id1, :id2)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array('id1' => $idReceveur, 'id2' => $idDemandeur));

        $sql = "INSERT INTO ".PREFIXE_TABLE."contacter (idUtilisateur1, idUtilisateur2) VALUES (:id1, :id2)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array('id1' => $idDemandeur, 'id2' => $idReceveur));

        $sql = "DELETE FROM ".PREFIXE_TABLE."demander WHERE idUtilisateur1 = :id1 AND idUtilisateur2= :id2";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array('id1' => $idDemandeur, 'id2' => $idReceveur));
    }

    /**
     * Fonction permettant de récupérer un objet utilisateur dont l'email correspond à celui passé en paramètre
     * @param string|null $email email de l'utilisateur
     * @return Utilisateur objet utilisateur
     */
    public function getObjetUtilisateur(?string $email): ?Utilisateur {
        $utilisateur = null;
        $sql = "SELECT * FROM " . PREFIXE_TABLE . "utilisateur WHERE email = :email";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("email" => $email));
        $result = $pdoStatement->fetch(PDO::FETCH_ASSOC);
        if($result != false) {
            $utilisateur = $this->hydrate($result);
        }
        return $utilisateur;
    }

    /**
     * Fonction permettant de mettre à jour tous les champs d'un utilisateur
     * @param Utilisateur|null $utilisateur utilisateur à mettre à jour
     * @return void
     */
    public function miseAJourUtilisateur(?Utilisateur $utilisateur): void {
        $date = null;
        $dateToken = null;
        $sql = "UPDATE " . PREFIXE_TABLE . "utilisateur SET 
            nom = :nom,
            prenom = :prenom,
            email = :email,
            motDePasse = :motDePasse,
            photoDeProfil = :photoDeProfil,
            estAdmin = :estAdmin,
            tentativesEchouees = :tentativesEchouees,
            dateDernierEchecConnexion = :dateDernierEchecConnexion,
            statutCompte = :statutCompte,
            token = :token,
            dateExpirationToken = :dateExpiraiton
            WHERE id = :id";
        $pdoStatement = $this->pdo->prepare($sql);

        if(!is_null($utilisateur->getDateDernierEchecConnexion())) {
            $date = $utilisateur->getDateDernierEchecConnexion()->format('Y-m-d H:i:s');
        }

        if(!is_null($utilisateur->getDateExpirationToken())) {
            $dateToken = $utilisateur->getDateExpirationToken()->format('Y-m-d H:i:s');
        }

        $pdoStatement->execute(array(
            "nom" => $utilisateur->getNom(),
            "prenom" => $utilisateur->getPrenom(),
            "email" => $utilisateur->getEmail(),
            "motDePasse" => $utilisateur->getMotDePasse(),
            "photoDeProfil" => $utilisateur->getPhotoDeProfil(),
            "estAdmin" => $utilisateur->getEstAdmin(),
            "tentativesEchouees" => $utilisateur->getTentativesEchouees(),
            "dateDernierEchecConnexion" => $date,
            "statutCompte" => $utilisateur->getStatutCompte(),
            "token" => $utilisateur->getTokenReinitialisation(),
            "dateExpiraiton" => $dateToken,
            "id" => $utilisateur->getId()
        ));
    }
}


