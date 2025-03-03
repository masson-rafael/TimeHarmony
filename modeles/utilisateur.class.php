<?php 
/**
 * @author Thibault Latxague
 * @brief classe Utilisateur
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
     * 
     * @var string|null le token de réinitialisation
     */
    private string|null $tokenReinitialisation;
    /**
     * 
     * @var DateTime|null la date d'expiration du token genere
     */
    private DateTime|null $dateExpirationToken;
    /**
     * 
     * @var string|null le token d'activation du compte
     */
    private string|null $tokenActivationCompte;
    /**
     * 
     * @var DateTime|null la date d'expiration du token d'activation du compte
     */
    private DateTime|null $dateExpirationTokenActivationCompte;
    /**
     * 
     * @var int|null le nombre de demandes reçues
     */
    private int|null $nombreDemandesEnCours;
    /**
     * 
     * @var DateTime|null la date de la dernière connexion
     */
    private DateTime|null $dateDerniereConnexion;

    /**
     * Constructeur par défaut
     */
    public function __construct(int $id = null, string $nom = null, string $prenom = null, string $email = null, string $motDePasse = null, string $photoDeProfil = null, bool $estAdmin = false, ?DateTime $dateDerniereConnexion = new DateTime()) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->motDePasse = $motDePasse;
        $this->photoDeProfil = $photoDeProfil;
        $this->estAdmin = $estAdmin;
        $this->nombreDemandesEnCours = $this->getDemandes();
        $this->dateDerniereConnexion = $dateDerniereConnexion;
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
        //$instance->nombreDemandesEnCours = $this->getDemandes();
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
        $instance->nombreDemandesEnCours = $utilisateur->nombreDemandesEnCours;
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
     * Get le token de réinitialisation
     * 
     * @return string|null le token de réinitialisation
     */
    public function getTokenReinitialisation(): ?string {
        return $this->tokenReinitialisation;
    }

    /**
     * Get la date de réinitialisation du token
     * 
     * @return DateTime|null date de réinitialisation du token
     */
    public function getDateExpirationToken(): ?DateTime {
        return $this->dateExpirationToken;
    }

    /**
     * Get le token d'activation du compte
     *
     * @return string|null le token d'activation du compte
     */
    public function getTokenActivationCompte(): ?string {
        return $this->tokenActivationCompte;
    }

    /**
     * Get la date d'expiration du token d'activation du compte
     *
     * @return DateTime|null la date d'expiration du token d'activation du compte
     */
    public function getDateExpirationTokenActivationCompte(): ?DateTime {
        return $this->dateExpirationTokenActivationCompte;
    }

    /**
     * Get le nombre de demandes reçues
     *
     * @return int|null Le nombre de demandes en cours
     */
    public function getNombreDemandesEnCours(): ?int {
        return $this->nombreDemandesEnCours;
    }

    /**
     * Get la date de la dernière connexion
     *
     * @return DateTime|null La date de la dernière connexion
     */
    public function getDateDerniereConnexion(): ?DateTime {
        return $this->dateDerniereConnexion;
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
     * Set le token de reinitialisation
     * 
     * @param string $token le token de reinit
     * @return void
     */
    public function setTokenReinitialisation(?string $token): void {
        $this->tokenReinitialisation = $token;
    }
    

    /**
     * Set la date d'expiration du token
     * 
     * @param DateTime|null $date la date d'expritation du token
     * @return void
     */
    public function setDateExpirationToken(?DateTime $date): void {
        $this->dateExpirationToken = $date;
    }

    /**
     * Set le token d'activation du compte
     *
     * @param string|null $tokenActivationCompte le token d'activation du compte
     * @return void
     */
    public function setTokenActivationCompte(?string $tokenActivationCompte): void {
        $this->tokenActivationCompte = $tokenActivationCompte;
    }

    /**
     * Set la date d'expiration du token d'activation du compte
     *
     * @param DateTime|null $dateExpirationTokenActivationCompte la date d'expiration du token d'activation du compte
     * @return void
     */
    public function setDateExpirationTokenActivationCompte(?DateTime $dateExpirationTokenActivationCompte): void {
        $this->dateExpirationTokenActivationCompte = $dateExpirationTokenActivationCompte;
    }

    /**
     * Set le nombre de demandes reçues
     *
     * @param int|null $nombreDemandesEnCours Le nombre de demandes en cours à définir
     * @return self
     */
    public function setNombreDemandesEnCours(?int $nombreDemandesEnCours): self {
        $this->nombreDemandesEnCours = $nombreDemandesEnCours;
        return $this;
    }

    /**
     * Set la date de la dernière connexion
     *
     * @param DateTime|null $dateDerniereConnexion La date de la dernière connexion
     * @return void
     */
    public function setDateDerniereConnexion(?DateTime $dateDerniereConnexion): void {
        $this->dateDerniereConnexion = $dateDerniereConnexion;
    }

    /**
     * ToString permettant d'afficher les paramtres de l'utilisateur
     *
     * @return string chaine de caractères a afficher
     */
    public function toString(): string {
        return "Utilisateur : " . $this->id . " " . $this->nom . " " . $this->prenom . " " . $this->email . " " . $this->motDePasse . " " . $this->photoDeProfil . " " . $this->estAdmin;
    }

    /**
     * Fonction permettant de retourner un tableau de contacts associés à un utilisateur.
     * 
     * @param int|null $idUtilisateur L'identifiant de l'utilisateur dont on veut récupérer les contacts.
     * @return array|null Le tableau des contacts, ou null si aucun contact n'est trouvé.
     */
    public function getContact(?int $idUtilisateur): ?array {
        $db = Bd::getInstance();
        $pdo = $db->getConnexion();

        $managerUtilisateur = new UtilisateurDao($pdo);
        $contacts = $managerUtilisateur->findAllContact($idUtilisateur);  
        return $contacts;
    }

    /**
     * Fonction permettant de retourner un tableau de groupes associés à un utilisateur.
     * 
     * @param int|null $idUtilisateur L'identifiant de l'utilisateur dont on veut récupérer les groupes.
     * @return array|null Le tableau des groupes, ou null si aucun groupe n'est trouvé.
     */
    public function getGroupe(?int $idUtilisateur): ?array {
        $db = Bd::getInstance();
        $pdo = $db->getConnexion();

        // Récupération des groupes
        $managerGroupe = new GroupeDao($pdo);
        $groupes = $managerGroupe->findAll($idUtilisateur);
        return $groupes;
    }

    /**
     * Fonction permettant de retourner un tableau des agendas associés à l'utilisateur actuel.
     * 
     * @return array|null Le tableau des agendas, ou null si aucun agenda n'est trouvé.
     */
    public function getAgendas(): ?array {
        $db = Bd::getInstance();
        $pdo = $db->getConnexion();

        $managerAgenda = new AgendaDao($pdo);
        $agendas = $managerAgenda->findAllByIdUtilisateur($this->getId());
        return $agendas;
    }

    /**
     * Fonction qui retourne le nombre de demandes que l'utilisateur a en cours
     * 
     * @return int|null le nombre de demandes
     */
    public function getDemandes(): ?int {
        $nombreDemandes = 0;
        $db = Bd::getInstance();
        $pdo = $db->getConnexion();  
        
        $managerUtilisateur = new UtilisateurDAO($pdo);
        $nombreDemandes = $managerUtilisateur->getNombreDemandesDeContact($this->getId());
        $this->setNombreDemandesEnCours($nombreDemandes);
        return $nombreDemandes;
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
     * @return int temps restant avant réactivation du compte
     */
    public function tempsRestantAvantReactivationCompte(): int {
        $dateActuelle = new DateTime();
        if ($this->getDateDernierEchecConnexion() == null) {
            $this->setDateDernierEchecConnexion(new DateTime());
        }
        return DELAI_ATTENTE_CONNEXION - ($dateActuelle->getTimestamp() - $this->getDateDernierEchecConnexion()->getTimestamp());
    }

    /**
     * Fonction qui génère et retourne le token créé + son temps d'expiration
     * 
     * @return string|null le token généré
     */
    public function genererTokenReinitialisation(): ?string {
        $this->setTokenReinitialisation(bin2hex(random_bytes(32)));
        $this->setDateExpirationToken(new DateTime(date('Y-m-d H:i:s', strtotime('+1 hour'))));
        return $this->getTokenReinitialisation();
    }

    /**
     * Fonction qui génère et retourne le token d'activation du compte
     * 
     * @return string|null le token généré
     */
    public function genererTokenActivationCompte(): ?string {
        $this->setTokenActivationCompte(bin2hex(random_bytes(32)));
        $this->setDateExpirationTokenActivationCompte(new DateTime(date('Y-m-d H:i:s', strtotime('+1 hour'))));
        return $this->getTokenActivationCompte();
    }
    
    /**
     * Fonction qui verifie que le token est bien valide
     * 
     * @param string|null $token le token de l'utilisateur
     * @return bool si le token est valide ou non
     */
    public function estTokenValide(?string $token): bool {
        return $this->getTokenReinitialisation() === $token && $this->getDateExpirationToken() > time();
    }

    /**
     * Fonction qui permet de supprimer les anciennes photos de l'utilisateur 
     * pour éviter une surcharge de la taille du projet
     *
     * @return void
     */
    public function supprimerAnciennesPhotos(): void {
        $dossier = 'image/photo_user';

        // Parcours tous les fichiers du dossier
        $fichiers = scandir($dossier);
        $pregMatch = '/^profil_'.$this->getId().'_/';
        foreach ($fichiers as $fichier) {
            $cheminFichier = $dossier . '/' . $fichier;

            // Vérifie si c'est un fichier et s'il commence par "profil_"
            if (is_file($cheminFichier) && preg_match($pregMatch, $fichier)) {
                unlink($cheminFichier);
            }
        }
    }
}