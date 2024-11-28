<?php
/**
 * @author Thibault Latxague
 * @describe Classe des agendas
 * @version 0.1
 */

class Assistant {

    private int|null $id;
    private DateTime|null $dateDebPeriode;
    private DateTime|null $dateFinPeriode;
    private array|null $utilisateurs;

    public function __construct(?DateTime $dateDebPeriode, ?DateTime $dateFinPeriode, ?array $utilisateurs)
    {
        $this->dateDebPeriode = $dateDebPeriode; 
        $this->dateFinPeriode = $dateFinPeriode;
        $this->utilisateurs = $utilisateurs;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getDateDebPeriode(): ?int {
        return $this->dateDebPeriode;
    }

    public function getDateFinPeriode(): ?int {
        return $this->dateFinPeriode;
    }

    public function getUtilisateurs(): ?array {
        return $this->utilisateurs;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setDateDebPeriode(int $dateDebPeriode): void {
        $this->id = $dateDebPeriode;
    }

    public function setDateFinPeriode(int $dateFinPeriode): void {
        $this->id = $dateFinPeriode;
    }

    public function setUtilisateurs(array $utilisateurs): void {
        $this->utilisateurs = $utilisateurs;
    }

    // Fonction pour vérifier les chevauchements d'événements
    public function trouverDatesCommunes($creneaux): array {
        $datesCommunes = [];
        
        // Fusionner les sous-tableaux en un seul tableau plat
        $creneauxPlats = array_merge(...$creneaux);
        
        // Trouver les intersections des créneaux
        for ($i = 0; $i < count($creneauxPlats); $i++) {
            for ($j = $i + 1; $j < count($creneauxPlats); $j++) {
                // Accéder directement aux propriétés de l'objet
                $debut1 = $creneauxPlats[$i]->getDateDebut();
                $fin1 = $creneauxPlats[$i]->getDateFin();
                $debut2 = $creneauxPlats[$j]->getDateDebut();
                $fin2 = $creneauxPlats[$j]->getDateFin();
    
                // Vérifier s'il y a un chevauchement
                if ($debut1 < $fin2 && $debut2 < $fin1) {
                    // Calculer l'intersection des deux plages de dates
                    $chevauchementDebut = max($debut1, $debut2);
                    $chevauchementFin = min($fin1, $fin2);
    
                    // Ajouter l'intervalle d'intersection au tableau
                    $datesCommunes[] = [
                        'debut' => $chevauchementDebut->format('Y-m-d H:i:s'),
                        'fin' => $chevauchementFin->format('Y-m-d H:i:s'),
                    ];
                }
            }
        }
    
        return $datesCommunes;
    }
    
    

    public function combinerCreneaux($data): array {

        // Étape 1 : Extraire et convertir les dates en timestamps
        $intervals = array_map(function($item) {
            return [
                'start' => strtotime($item['dateDebut']),
                'end' => strtotime($item['dateFin'])
            ];
        }, $data);

        // Étape 2 : Trier les créneaux par date de début
        usort($intervals, function($a, $b) {
            return $a['start'] - $b['start'];
        });

        // Étape 3 : Fusionner les créneaux
        $mergedIntervals = [];
        foreach ($intervals as $interval) {
            if (empty($mergedIntervals)) {
                $mergedIntervals[] = $interval;
            } else {
                $lastInterval = &$mergedIntervals[count($mergedIntervals) - 1];
                if ($interval['start'] <= $lastInterval['end']) {
                    // Étendre l'heure de fin si chevauchement
                    $lastInterval['end'] = max($lastInterval['end'], $interval['end']);
                } else {
                    $mergedIntervals[] = $interval;
                }
            }
        }

        // Étape 4 : Convertir les timestamps en format lisible
        $results = array_map(function($interval) {
            return [
                'dateDebut' => date('Y-m-d H:i:s', $interval['start']),
                'dateFin' => date('Y-m-d H:i:s', $interval['end'])
            ];
        }, $mergedIntervals);

        return $results;
    }

}