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
     * 
     * @var string|null le dernier fichier de backuo
     */
    private ?string $fichierDernierBackup;

    /**
     * Retourne le dernier fichier de bacup
     * 
     * @return string|null le fichier de backup
     */
    public function getDernierFichierBackup(): ?string {
        return $this->fichierDernierBackup;
    }

    /**
     * Set le dernier fichier de backup
     * 
     * @param string|null $fichier le fichier de backuo
     * @return void
     */
    public function setDernierFichierBackup(?string $fichier): void {
        $this->fichierDernierBackup = $fichier;
    }

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
     * Récupère toutes les tables de la base de données
     * @return array
     */
    private function getTables(): array {
        $stmt = $this->getConnexion()->query("SHOW TABLES");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Effectue un backup incrémental
     * @return void
     */
    public function backupTotale(): void {
        // Générer un nom de fichier unique basé sur la date
        $backupDir = "backup/";
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        $backupFile = $backupDir . date('Y-m-d_H-i-s') . '_' . DB_NAME . '_backup.sql';
        
        // Récupérer la liste des tables
        $tables = $this->getTables();
        
        // Ouvrir le fichier de backup
        $handle = fopen($backupFile, 'w');
        
        // En-tête SQL
        fwrite($handle, "-- Database: " . DB_NAME . "\n");
        fwrite($handle, "-- Backup Date: " . date('Y-m-d H:i:s') . "\n\n");
        
        // Désactiver les contraintes de clés étrangères
        fwrite($handle, "SET FOREIGN_KEY_CHECKS = 0;\n\n");
        
        // Sauvegarder les structures et données de chaque table
        foreach ($tables as $table) {
            // Structure de la table
            $stmt = $this->pdo->query("SHOW CREATE TABLE `$table`");
            $createTable = $stmt->fetch(PDO::FETCH_ASSOC)['Create Table'];
            fwrite($handle, "DROP TABLE IF EXISTS `$table`;\n");
            fwrite($handle, $createTable . ";\n\n");
            
            // Données de la table
            $stmt = $this->pdo->query("SELECT * FROM `$table`");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($rows)) {
                fwrite($handle, "INSERT INTO `$table` VALUES \n");
                $valueStrings = [];
                foreach ($rows as $row) {
                    $rowValues = array_map(function($value) {
                        return $value === null ? 'NULL' : $this->pdo->quote($value);
                    }, $row);
                    $valueStrings[] = '(' . implode(',', $rowValues) . ')';
                }
                fwrite($handle, implode(",\n", $valueStrings) . ";\n\n");
            }
        }
        
        // Réactiver les contraintes de clés étrangères
        fwrite($handle, "SET FOREIGN_KEY_CHECKS = 1;\n");
        fclose($handle);
        $this->setDernierFichierBackup($backupFile);
    }

    /**
     * Restaure une base de données à partir d'un fichier de backup
     * @param string $backupFile Chemin du fichier de backup
     */
    public function restoreDatabase(): void {
        $backupFile = $this->getLatestFile("backup");
        if (!file_exists($backupFile)) {
            throw new Exception("Fichier de backup non trouvé");
        }
        
        // Désactiver les contraintes de clés étrangères
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        
        // Lire et exécuter le fichier SQL
        $sqlContent = file_get_contents($backupFile);
        $sqlStatements = explode(';', $sqlContent);
        
        foreach ($sqlStatements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                try {
                    $this->pdo->exec($statement);
                } catch (PDOException $e) {
                    // Log ou gérer l'erreur selon vos besoins
                    error_log("Erreur lors de la restauration : " . $e->getMessage());
                }
            }
        }
        
        // Réactiver les contraintes de clés étrangères
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    }

    public function getLatestFile(?string $folderPath) {
        // Vérifie si le dossier existe
        if (!is_dir($folderPath)) {
            return "Le dossier spécifié n'existe pas.";
        }
    
        // Récupère tous les fichiers du dossier
        $files = scandir($folderPath);
    
        // Initialise les variables pour suivre le dernier fichier
        $latestFile = null;
        $latestTime = 0;
    
        foreach ($files as $file) {
            $filePath = $folderPath . DIRECTORY_SEPARATOR . $file;
    
            // Ignore les dossiers "." et ".."
            if ($file === "." || $file === "..") {
                continue;
            }
    
            // Vérifie que c'est un fichier
            if (is_file($filePath)) {
                // Récupère le temps de modification
                $fileTime = filemtime($filePath);
    
                // Met à jour si c'est plus récent
                if ($fileTime > $latestTime) {
                    $latestTime = $fileTime;
                    $latestFile = $filePath;
                }
            }
        }
    
        return $latestFile ?: "Aucun fichier trouvé dans le dossier.";
    }
}