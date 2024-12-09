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
    public function __construct() {
        $this->id = null;
        $this->nom = null;
        $this->prenom = null;
        $this->email = null;
        $this->motDePasse = null;
        $this->photoDeProfil = null;
        $this->estAdmin = false;
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
  public function setPhotoDeProfil($photoDeProfil) {
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
}