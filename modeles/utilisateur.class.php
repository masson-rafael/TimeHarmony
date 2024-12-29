<?php 
/**
 * @author Thibault Latxague
 * @describe classe Utilisateur
 * @version 0.1
 */

class Utilisateur {
    /**
     *
     * @var integer|null id de l'utilisateur
     */
    private int|null $id;
    /**
     *
     * @var string|null nom de l'utilisateur
     */
    private string|null $nom;
    /**
     *
     * @var string|null prenom de l'utilisateur
     */
    private string|null $prenom;
    /**
     *
     * @var string|null email de l'utilisateur
     */
    private string|null $email;
    /**
     * 
     * @var string|null mot de passe de l'utilisateur
     */
    private string|null $motDePasse;
    /**
     *
     * @var string|null photo de profil de l'utilisateur
     */
    private string|null $photoDeProfil;
    /**
     *
     * @var boolean|null est admin de l'utilisateur
     */
    private bool|null $estAdmin;
    /**
     * 
     * @var integer|null nombre de tentatives échouées de connexion
     */
    private int|null $tentativesEchouees;
    /**
     * 
     * @var DateTime|null date du dernier échec de connexion
     */
    private DateTime|null $dateDernierEchecConnexion;
    /**
     * 
     * @var string|null statut du compte de l'utilisateur
     */
    private string|null $statutCompte;

    /**
     * Constructeur par défaut
     */
    public function __construct(int $id = null, string $nom = null, string $prenom = null, string $email = null, string $motDePasse = null, string $photoDeProfil = null, bool $estAdmin = false) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->motDePasse = $motDePasse;
        $this->photoDeProfil = $photoDeProfil;
        $this->estAdmin = $estAdmin;
    }

    /**
     * Constructeur avec paramètres
     *
     * @param integer|null $id de l'utilisateur
     * @param string $nom de l'utilisateur
     * @param string $prenom de l'utilisateur
     * @param string $email de l'utilisateur
     * @param string $motDePasse de l'utilisateur
     * @param string $photoDeProfil de l'utilisateur
     * @param boolean $estAdmin de l'utilisateur et valeur par défaut à false
     * @return self instance de la classe
     */
    public static function createAvecParam(int $id = null, string $nom, string $prenom, string $email, string $motDePasse, string $photoDeProfil, bool $estAdmin = false): self {
        $instance = new self();
        $instance->id = $id;
        $instance->nom = $nom;
        $instance->prenom = $prenom;
        $instance->email = $email;
        $instance->motDePasse = $motDePasse;
        $instance->photoDeProfil = $photoDeProfil;
        $instance->estAdmin = $estAdmin;
        return $instance;
    }

    /**
     * Création d'un utilisateur à partir d'un autre utilisateur
     *
     * @param Utilisateur $utilisateur à copier
     * @return self instance de la classe
     */
    public static function createWithCopy(Utilisateur $utilisateur): self {
        $instance = new self();
        $instance->id = $utilisateur->id;
        $instance->nom = $utilisateur->nom;
        $instance->prenom = $utilisateur->prenom;
        $instance->email = $utilisateur->email;
        $instance->motDePasse = $utilisateur->motDePasse;
        $instance->photoDeProfil = $utilisateur->photoDeProfil;
        $instance->estAdmin = $utilisateur->estAdmin;
        return $instance;
    }

    /**
     * Get l'id de l'utilisateur
     *
     * @return integer|null id de l'utilisateur
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * Get le nom de l'utilisateur
     *
     * @return string nom de l'utilisateur
     */
    public function getNom(): string {
        return $this->nom;
    }

    /**
     * Get le prénom de l'utilisateur
     *
     * @return string prénom de l'utilisateur
     */
    public function getPrenom(): string {
        return $this->prenom;
    }

    /**
     * Get l'email de l'utilisateur
     *
     * @return string email de l'utilisateur
     */
    public function getEmail(): string {
        return $this->email;
    }

    /**
     * Get le mot de passe de l'utilisateur
     *
     * @return string mot de passe de l'utilisateur
     */
    public function getMotDePasse(): string {
        return $this->motDePasse;
    }

    /**
     * Get la photo de profil de l'utilisateur
     *
     * @return string|null photo de profil de l'utilisateur
     */
    public function getPhotoDeProfil(): string|null {
        return $this->photoDeProfil;
    }

    /**
     * Get si l'utilisateur est admin
     *
     * @return boolean est admin de l'utilisateur
     */
    public function getEstAdmin(): bool {
        return $this->estAdmin;
    }

    /**
     * Get le nombre de tentatives échouées de connexion
     *
     * @return integer|null nombre de tentatives échouées de connexion
     */
    public function getTentativesEchouees(): ?int {
        return $this->tentativesEchouees;
    }

    /**
     * Get la date du dernier échec de connexion
     *
     * @return DateTime|null date du dernier échec de connexion
     */
    public function getDateDernierEchecConnexion(): ?DateTime {
        return $this->dateDernierEchecConnexion;
    }

    /**
     * Get le statut du compte de l'utilisateur
     *
     * @return string|null statut du compte de l'utilisateur
     */
    public function getStatutCompte(): ?string {
        return $this->statutCompte;
    }

    /**
     * Set l'id de l'utilisateur
     *
     * @param integer $id de l'utilisateur
     * @return void
     */
    public function setId(int $id): void {
        $this->id = $id;
    }

    /**
     * Set le nom de l'utilisateur
     *
     * @param string $nom de l'utilisateur
     * @return void
     */
    public function setNom(string $nom): void {
        $this->nom = $nom;
    }

    /**
     * Set le prénom de l'utilisateur
     *
     * @param string $prenom de l'utilisateur
     * @return void
     */
    public function setPrenom(string $prenom): void {
        $this->prenom = $prenom;
    }

    /**
     * Set l'email de l'utilisateur
     *
     * @param string $email de l'utilisateur
     * @return void
     */
    public function setEmail(string $email): void {
        $this->email = $email;
    }

    /**
     * Set le mot de passe de l'utilisateur
     *
     * @param string $motDePasse de l'utilisateur
     * @return void
     */
    public function setMotDePasse(string $motDePasse): void {
        $this->motDePasse = $motDePasse;
    }

/**
   * Définit une nouvelle photo de profil pour l'utilisateur.
   *
   * @param string $photoDeProfil Le chemin de la photo de profil.
   */
    public function setPhotoDeProfil(string $photoDeProfil) {
    $this->photoDeProfil = $photoDeProfil;
}
    /**
     * Set si l'utilisateur est adminé
     *
     * @param boolean $estAdmin de l'utilisateur
     * @return void
     */
    public function setEstAdmin(bool $estAdmin): void {
        $this->estAdmin = $estAdmin;
    }

    /**
     * Set le nombre de tentatives échouées de connexion
     *
     * @param integer $tentativesEchouees de l'utilisateur
     * @return void
     */
    public function setTentativesEchouees(int $tentativesEchouees): void {
        $this->tentativesEchouees = $tentativesEchouees;
    }

    /**
     * Set la date du dernier échec de connexion
     *
     * @param DateTime|null $dateDernierEchecConnexion de l'utilisateur
     * @return void
     */
    public function setDateDernierEchecConnexion(?DateTime $dateDernierEchecConnexion): void {
        $this->dateDernierEchecConnexion = $dateDernierEchecConnexion;
    }

    /**
     * Set le statut du compte de l'utilisateur
     *
     * @param string $statutCompte de l'utilisateur
     * @return void
     */
    public function setStatutCompte(string $statutCompte): void {
        $this->statutCompte = $statutCompte;
    }

    /**
     * ToString permettant d'afficher les paramtres de l'utilisateur
     *
     * @return string chaine de caractères a afficher
     */
    public function toString(): string {
        return "Utilisateur : " . $this->id . " " . $this->nom . " " . $this->prenom . " " . $this->email . " " . $this->motDePasse . " " . $this->photoDeProfil . " " . $this->estAdmin;
    }

    public function getContact(?PDO $pdo, ?int $idUtilisateur): ?array {
        $managerUtilisateur = new UtilisateurDao($pdo);
        $tableau = $managerUtilisateur->findAllContact($idUtilisateur);  
        $contacts = $managerUtilisateur->hydrateAll($tableau);
        return $contacts;
    }

    public function getGroupe(?PDO $pdo, ?int $idUtilisateur): ?array {
        // Récupération des groupes
        $managerGroupe = new GroupeDao($pdo);

        $tableau = $managerGroupe->findAll($idUtilisateur);
        $groupes = $managerGroupe->hydrateAll($tableau);

        return $groupes;
    }

    public function getAgendas(): ?array {
        $db = Bd::getInstance();
        $pdo = $db->getConnexion();

        $managerAgenda = new AgendaDao();
        $tableau = $managerAgenda->findAllByIdUtilisateur($this->getId(),$pdo);
        $agendas = $managerAgenda->hydrateAll($tableau);
        return $agendas;
    }

    /**
     * Fonction qui augmente le nombre de tentatives échouées de connexion
     * Si le nombre de tentatives échouées est supérieur à MAX_CONNEXION_ECHOUEES, le statut du compte est bloqué
     * @return void
     */
    public function gererEchecConnexion(): void {
        $this->setTentativesEchouees($this->getTentativesEchouees() + 1);

        if($this->getTentativesEchouees() >= MAX_CONNEXION_ECHOUEES) {
            $this->setDateDernierEchecConnexion(new DateTime());
            $this->setStatutCompte("bloque");
        }
    }

    /**
     * Fonction qui réactive le compte si le délai d'attente est écoulé
     * @return void
     */
    public function reactiverCompte(): void {
        if($this->delaiAttenteEstEcoulé()) {
            $this->setStatutCompte("actif");
            $this->reinitialiserTentativesConnexion();
        }
        //$this->reinitialiserTentativesConnexion();
        if($this->getTentativesEchouees() < MAX_CONNEXION_ECHOUEES) {
            $this->setDateDernierEchecConnexion(null);
        }
    }

    /**
     * Fonction qui réiniialise le nombre de tentatives échouées de connexion
     * @return void
     */
    public function reinitialiserTentativesConnexion(): void {
        $this->setTentativesEchouees(0);
    }

    /**
     * Fonction qui retourne vrai si le délai d'attente est écoulé, faux sinon
     * @return bool|null vrai si le délai d'attente est écoulé, faux sinon
     */
    public function delaiAttenteEstEcoulé(): ?bool {
        $delaisEcoule = false;

        if($this->tempsRestantAvantReactivationCompte() < 0) {
            $delaisEcoule = true;
            $this->setDateDernierEchecConnexion(null);
            $this->reinitialiserTentativesConnexion();
        }

        return $delaisEcoule;
    }

    /**
     * Fonction qui retourne le temps restant avant la réactivation du compte
     * @return DateTime temps restant avant réactivation du compte
     */
    public function tempsRestantAvantReactivationCompte(): int {
        $dateActuelle = new DateTime();
        if ($this->getDateDernierEchecConnexion() == null) {
            $this->setDateDernierEchecConnexion(new DateTime());
        }
        return DELAI_ATTENTE_CONNEXION - ($dateActuelle->getTimestamp() - $this->getDateDernierEchecConnexion()->getTimestamp());
    }
}