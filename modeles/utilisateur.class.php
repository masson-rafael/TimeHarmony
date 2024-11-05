<?php 

class Utilisateur {
    private int|null $id;
    private string|null $nom;
    private string|null $prenom;
    private string|null $email;
    private string|null $motDePasse;
    private string|null $photoDeProfil;
    private bool|null $estAdmin;
    //private array|null $agendas;        -> nécessaire ?

    /* ------------- CONSTRUCTEUR ------------- */
    public function __construct(int $id, string $nom, string $prenom, string $email, string $motDePasse, string|null $photoDeProfil, bool $estAdmin = false) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->motDePasse = $motDePasse;
        $this->photoDeProfil = $photoDeProfil;
        $this->estAdmin = $estAdmin;
    }

    /* ------------- SETTERS ------------- */
    public function getId(): int {
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
}