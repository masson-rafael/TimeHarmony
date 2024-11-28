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
     * Set la photo de profil de l'utilisateur
     *
     * @param string|null $photoDeProfil de l'utilisateur
     * @return void
     */
    public function setPhotoDeProfil(string|null $photoDeProfil): void {
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
     * ToString permettant d'afficher les paramtres de l'utilisateur
     *
     * @return string chaine de caractères a afficher
     */
    public function toString(): string {
        return "Utilisateur : " . $this->id . " " . $this->nom . " " . $this->prenom . " " . $this->email . " " . $this->motDePasse . " " . $this->photoDeProfil . " " . $this->estAdmin;
    }

    public function getContact($pdo, $idUtilisateur): array|null {
    $managerUtilisateur = new UtilisateurDao($pdo);
    $tableau = $managerUtilisateur->findAllContact($idUtilisateur);  
    $contacts = $managerUtilisateur->hydrateAll($tableau);
    return $contacts;
    }

    public function getGroupe($pdo,$idUtilisateur): array|null {
        // Récupération des groupes
        $managerGroupe = new GroupeDao($pdo);

        $tableau = $managerGroupe->findAll($idUtilisateur);
        $groupes = $managerGroupe->hydrateAll($tableau);

        return $groupes;
    }

    public function getAgendas(): array|null {
        $db = Bd::getInstance();
        $pdo = $db->getConnexion();

        $managerAgenda = new AgendaDao();
        $tableau = $managerAgenda->findAllByIdUtilisateur($this->getId(),$pdo);
        $agendas = $managerAgenda->hydrateAll($tableau);
        return $agendas;
    }
}