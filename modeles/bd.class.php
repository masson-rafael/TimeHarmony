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
    public function backup(?array &$messageErreur) : void
    {
        $messageErreur = [];
        $date = new DateTime(); // Initialiser la date actuelle
        $pdo = $this->getConnexion(); // Obtenir la connexion à la base de données
        $backupDir = 'backup'; // Définir le répertoire de sauvegarde
    
        // Création du dossier de backup s'il n'existe pas
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0777, true); // Créer le dossier avec les permissions appropriées
        }
    
        $backupFile = $backupDir . '/backup_' . DB_NAME . '_' . $date->format('Y-m-d_H-i-s') . '.sql'; // Construire le nom du fichier de sauvegarde
    
        try {
            $fileHandle = fopen($backupFile, 'w'); // Ouvrir le fichier de sauvegarde en écriture
    
            // En-tête du fichier SQL
            fwrite($fileHandle, "-- Backup de la base " . DB_NAME . "\n"); // Écrire le nom de la base
            fwrite($fileHandle, "-- Date: " . $date->format('Y-m-d H:i:s') . "\n\n"); // Écrire la date du backup
    
            // Récupérer la liste des tables
            $tablesQuery = $pdo->query("SHOW TABLES"); // Obtenir toutes les tables de la base
            $tables = $tablesQuery->fetchAll(PDO::FETCH_COLUMN); // Extraire les noms des tables
    
            foreach ($tables as $table) {
                // Exporter la structure de la table
                $createTableStmt = $pdo->prepare("SHOW CREATE TABLE `" . $table . "`"); // Préparer la requête pour obtenir la structure de la table
                $createTableStmt->execute(); // Exécuter la requête
                $createTableResult = $createTableStmt->fetch(PDO::FETCH_ASSOC); // Récupérer le résultat
    
                fwrite($fileHandle, "\n\n-- Structure de la table `$table`\n\n"); // Ajouter un commentaire sur la structure de la table
                fwrite($fileHandle, $createTableResult['Create Table'] . ";\n\n"); // Écrire la commande CREATE TABLE
    
                // Exporter les données de la table
                fwrite($fileHandle, "-- Données de la table `$table`\n\n"); // Ajouter un commentaire sur les données de la table
    
                $selectStmt = $pdo->prepare("SELECT * FROM `" . $table . "`"); // Préparer la requête pour sélectionner toutes les données
                $selectStmt->execute(); // Exécuter la requête
    
                while ($row = $selectStmt->fetch(PDO::FETCH_ASSOC)) { // Parcourir chaque ligne de la table
                    $columns = array_map(function ($value) use ($pdo) { // Mapper les valeurs pour les formater
                        if ($value === null) { // Vérifier si la valeur est NULL
                            return 'NULL'; // Retourner NULL pour les valeurs nulles
                        }
                        return $pdo->quote($value); // Échapper les valeurs avec quote
                    }, array_values($row)); // Obtenir les valeurs de la ligne
    
                    $columnString = implode(', ', $columns); // Construire une chaîne avec les valeurs séparées par des virgules
                    fwrite($fileHandle, "INSERT INTO `$table` VALUES ($columnString);\n"); // Écrire la commande INSERT INTO
                }
            }
    
            fclose($fileHandle); // Fermer le fichier de sauvegarde
            $messageErreur[] = "Backup réalisé avec succès : $backupFile"; // Message de succès
    
        } catch (Exception $e) {
            if (isset($fileHandle)) {
                fclose($fileHandle); // Fermer le fichier en cas d'erreur
            }
            $messageErreur[] = "Erreur lors du backup : " . $e->getMessage(); // Afficher le message d'erreur
        }
    }
}