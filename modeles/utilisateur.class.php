<?php 

class Utilisateur {
    private int|null $id;
    private string|null $nom;
    private string|null $prenom;
    private string|null $email;
    private string|null $motDePasse;
    private string|null $photoDeProfil;
    private bool|null $estAdmin;

    /* Constructeur par défaut */
    public function __construct() {
        // Initialisation avec des valeurs par défaut
        $this->id = null;
        $this->nom = null;
        $this->prenom = null;
        $this->email = null;
        $this->motDePasse = null;
        $this->photoDeProfil = null;
        $this->estAdmin = false;
    }

    /* Méthode de création avec paramètres */
    public static function createAvecParam(int $id, string $nom, string $prenom, string $email, string $motDePasse, string $photoDeProfil, bool $estAdmin = false): self {
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

    /* ------------- SETTERS ------------- */
    public function getId(): ?int {
        return $this->id;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function getPrenom(): string {
        return $this->prenom;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getMotDePasse(): string {
        return $this->motDePasse;
    }

    public function getPhotoDeProfil(): string|null {
        return $this->photoDeProfil;
    }

    public function getEstAdmin(): bool {
        return $this->estAdmin;
    }

    /* ------------- SETTERS ------------- */
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setNom(string $nom): void {
        $this->nom = $nom;
    }

    public function setPrenom(string $prenom): void {
        $this->prenom = $prenom;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function setMotDePasse(string $motDePasse): void {
        $this->motDePasse = $motDePasse;
    }

    public function setPhotoDeProfil(string|null $photoDeProfil): void {
        $this->photoDeProfil = $photoDeProfil;
    }

    public function setEstAdmin(bool $estAdmin): void {
        $this->estAdmin = $estAdmin;
    }

    public function toString(): string {
        return "Utilisateur : " . $this->id . " " . $this->nom . " " . $this->prenom . " " . $this->email . " " . $this->motDePasse . " " . $this->photoDeProfil . " " . $this->estAdmin;
    }
}