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
     * @return void
     */
    public function backup() {
        $date = new DateTime();
        $fichierBackup = "backup/backup_". PREFIXE_TABLE . $date->format('Y-m-d_H-i-s').".sql";
        $commande = "mysqldump --host=". DB_HOST ." --user=".DB_USER." --password=".DB_PASS." ".DB_NAME." > ".$fichierBackup;
        exec($commande . " 2>&1", $output, $result);
    
        if ($result === 0) {
            echo "La base de données " . DB_NAME . " a été sauvegardée avec succès dans le fichier : $fichierBackup";
        } else {
            echo "Erreur lors de la sauvegarde de la base de données.\n";
            echo "Commande : $commande\n";
            echo "Output : " . implode("\n", $output);
        }
    }
    
}