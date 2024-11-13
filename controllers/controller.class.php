<?php
/**
 * @author Félix Autant
 * @describe controller
 * @version 0.1
 */

class Controller{
    /**
     *
     * @var PDO pdo
     */
    private PDO $pdo;
    /**
     *
     * @var \Twig\Loader\FilesystemLoader loader
     */
    private \Twig\Loader\FilesystemLoader $loader;
    /**
     *
     * @var \Twig\Environment twig
     */
    private \Twig\Environment $twig;
    /**
     *
     * @var array|null methode get
     */
    private ?array $get = null;
    /**
     *
     * @var array|null methode post
     */
    private ?array $post =null;

    /**
     * Constructeur par défaut
     *
     * @param \Twig\Environment $twig Environnement twig
     * @param \Twig\Loader\FilesystemLoader $loader Loader de fichier
     */
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader) {
        $db = Bd::getInstance();
        $this->pdo = $db->getConnexion();

        $this->loader = $loader;    
        $this->twig = $twig;

        if (isset($_GET) && !empty($_GET)){
            $this->get = $_GET;
        }
        if (isset($_POST) && !empty($_POST)){
            $this->post = $_POST;
        }
    }

    /**
     * Appel d'une méthode	
     *
     * @param string $methode nom de la méthode à appeler
     * @throws Exception si la méthode n'existe pas dans la classe
     * @return mixed methode de la classe dont on appelle la methode
     */
    public function call(string $methode): mixed{
        if (!method_exists($this, $methode)){
            throw new Exception("La méthode $methode n'existe pas dans le controller ". __CLASS__ ); 
        }
        return $this->$methode();
    }

    /**
     * Get la valeur du pdo
     *
     * @return PDO|null le pdo
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
    public function setPdo(?PDO $pdo):void {
        $this->pdo = $pdo;
    }

    /**
     * Get la valeur du loader
     *
     * @return \Twig\Loader\FilesystemLoader le loader
     */
    public function getLoader(): \Twig\Loader\FilesystemLoader {
        return $this->loader;
    }

    /**
     * Set la valeur du loader
     *
     * @param \Twig\Loader\FilesystemLoader $loader Loader de fichier
     * @return void
     */
    public function setLoader(\Twig\Loader\FilesystemLoader $loader) :void {
        $this->loader = $loader;
    }

    /**
     * Get la valeur du twig
     *
     * @return \Twig\Environment le twig
     */
    public function getTwig(): \Twig\Environment {
        return $this->twig;
    }

    /**
     * Set la valeur du twig
     *
     * @param \Twig\Environment $twig Environnement twig
     * @return void
     */
    public function setTwig(\Twig\Environment $twig): void {
        $this->twig = $twig;
    }

    /**
     * get la valeur de get
     *
     * @return array|null tableau de valeurs récupérées en get
     */
    public function getGet(): ?array {
        return $this->get;
    }

    /**
     * Set la valeur de get
     *
     * @param array|null $get tableau de vakeurs à mettre dans get
     * @return void
     */
    public function setGet(?array $get): void {
        $this->get = $get;
    }

    /**
     * Get la valeur de post
     *
     * @return array|null tableau de valeurs récupérées en post
     */
    public function getPost(): ?array {
        return $this->post;
    }

    /**
     * Set la valeur de post
     *
     * @param array|null $post tableau de valeurs à mettre dans post
     * @return void
     */
    public function setPost(?array $post): void {
        $this->post = $post;
    }
}




