<?php

class CreneauLibreDao{
    private ?PDO $pdo;

    public function __construct(?PDO $pdo=null){
        $this->pdo = $pdo;
    }

    public function getPdo(): ?PDO
    {
        return $this->pdo;
    }


    public function setPdo($pdo): void
    {
        $this->pdo = $pdo;
    }

    public function supprimerCreneauxLibres(): void {
        $sql = "DELETE FROM ".PREFIXE_TABLE."creneaulibre";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
    }

    public function ajouterCreneauLibre(CreneauLibre $creneauLibre): void{

        // Préparation de la requête SQL
        $sql = "INSERT INTO ".PREFIXE_TABLE."creneaulibre (dateDebut, dateFin, idAgenda) VALUES (:dateDebut, :dateFin, :idAgenda)";
        $pdoStatement = $this->pdo->prepare($sql);

        // Exécution de la requête avec les valeurs formatées
        $pdoStatement->execute(array(
            "dateDebut" => $creneauLibre->getDateDebut()->format('Y-m-d H:i:s'),
            "dateFin" => $creneauLibre->getDateFin()->format('Y-m-d H:i:s'),
            "idAgenda" => $creneauLibre->getIdAgenda()  // Si nécessaire
        ));
    }
    
    public function findAllAssoc(): ?array {
        $sql="SELECT * FROM ".PREFIXE_TABLE."creneaulibre";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $creneauxLibres = $pdoStatement->fetchAll();

        //var_dump( $creneauxLibres);
        return $creneauxLibres;
    }
    public function hydrate($tableauAssoc): ?CreneauLibre {
        //Conversion de String en DateTime
        $dateDebut = new DateTime($tableauAssoc['dateDebut']);
        $dateFin = new DateTime($tableauAssoc['dateFin']);
        
        $creneau = new CreneauLibre($tableauAssoc['id'], $dateDebut,$dateFin,$tableauAssoc['idAgenda']);

        return $creneau;
    }

    public function hydrateAll($tableau): ?array{
        $creneaux = [];
        foreach($tableau as $tableauAssoc){
            $creneau = $this->hydrate($tableauAssoc);
            $creneaux[] = $creneau;
        }
        return $creneaux;
    }
}