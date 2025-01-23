<?php
/**
 * @author Félix Autant
 * @brief classe de la base de données
 * @version 0.1
 * @todo faire methode __clone
 */

class Bd{
    /**
     *
     * @var Bd|null instance de la base de données
     */
    private static ?Bd $instance = null;
    /**
     *
     * @var PDO|null notre pdo
     */
    private ?PDO $pdo;

    /**
     * Notre constructeur par défaut
     * 
     * @throws PDOException si la connexion à la base de données échoue
     */
    private function __construct() {
        try {
            $this->pdo = new PDO('mysql:host='. DB_HOST . ';dbname='. DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e){
            die('Connexion à la base de données échouée : ' . $e->getMessage());
        }
    }

    /**
     * Get l'instance de la base de données
     *
     * @return Bd instance de la base de données
     */
    public static function getInstance(): Bd{
        if (self::$instance == null){
            self::$instance = new Bd();
        }
        return self::$instance;
    }

    /**
     * Get la connexion
     *
     * @return PDO notre connexion au pdo
     */
    public function getConnexion(): PDO{
        return $this->pdo;
    }

    /**
     * Empeche de cloner l'objet
     *
     * @return void
     */
    private function __clone(){
    }

    /**
     * Empecher la dématérialisation
     * 
     * @throws Exception si on tente de déserialiser un singleton
     */
    public function __wakeup(){
        throw new Exception("Un singleton ne doit pas être deserialisé");
    }

    /**
     * Faire une backup de toute la base de données
     *
     * @param array|null $messageErreur le tableau contenant les messages d'erreur
     * @return void
     */
    public function backup() : void
    {
        $date = new DateTime(); // Initialiser la date actuelle
        echo "Debut backup";
        $backupDir = 'backup'; // Définir le répertoire de sauvegarde
    
        // Création du dossier de backup s'il n'existe pas
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0777, true); // Créer le dossier avec les permissions appropriées
        }

        $backupFile = $backupDir . '/backup_' . DB_NAME . '_' . $date->format('Y-m-d_H-i-s') . '.sql'; // Construire le nom du fichier de sauvegarde

        if(DB_HOST == "localhost") {
            $path_mysqldump = "C:/wamp64/bin/mysql/mysql8.3.0/bin/mysqldump";
            $commande = $path_mysqldump . " -u " . DB_USER . " --databases " . DB_NAME . " --routines --events --triggers > " . $backupFile;
        } else {
            $path_mysqldump = "DB_MYSQLDUMP_PATH";
            $commande = $path_mysqldump . " -u " . DB_USER . " -p " . DB_PASS . " --databases " . DB_NAME . " --routines --events --triggers > " . $backupFile;
        }
    
        $output = null;
        $returnVar = null;
        exec($commande, $output, $returnVar);
    }
}