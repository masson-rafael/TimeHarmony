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
    public function trouverDatesCommunes(array $creneaux, string $dureeMin): array
    {
        $datesCommunes = [];
    
        // Convertir la durée minimale (format "H:i") en minutes
        list($heures, $minutes) = explode(':', $dureeMin);
        $dureeMinMinutes = ($heures * 60) + $minutes;
    
        // Obtenir toutes les plages horaires pour chaque sous-tableau
        $groupesCreneaux = array_map(function ($groupe) {
            return array_map(function ($creneau) {
                return [
                    'debut' => $creneau->getDateDebut(),
                    'fin' => $creneau->getDateFin(),
                ];
            }, $groupe);
        }, $creneaux);
    
        // Trouver l'intersection globale des plages horaires
        $intersection = $this->intersectionGlobale($groupesCreneaux);
    
        // Filtrer les intersections en fonction de la durée minimale
        foreach ($intersection as $plage) {
            $interval = $plage['debut']->diff($plage['fin']);
            $dureeMinutes = ($interval->h * 60) + $interval->i;
    
            if ($dureeMinutes >= $dureeMinMinutes) {
                $datesCommunes[] = [
                    'debut' => $plage['debut']->format('Y-m-d H:i:s'),
                    'fin' => $plage['fin']->format('Y-m-d H:i:s'),
                    'duree' => "{$interval->h}h {$interval->i}m"
                ];
            }
        }
    
        return $datesCommunes;
    }
    
    private function intersectionGlobale(array $groupesCreneaux): array
    {
        // Initialiser l'intersection avec le premier groupe
        $intersection = $groupesCreneaux[0];
    
        // Calculer l'intersection progressive avec chaque groupe
        for ($i = 1; $i < count($groupesCreneaux); $i++) {
            $intersection = $this->intersecterDeuxGroupes($intersection, $groupesCreneaux[$i]);
        }
    
        return $intersection;
    }
    
    private function intersecterDeuxGroupes(array $groupe1, array $groupe2): array
    {
        $resultat = [];
    
        foreach ($groupe1 as $plage1) {
            foreach ($groupe2 as $plage2) {
                $debutMax = max($plage1['debut'], $plage2['debut']);
                $finMin = min($plage1['fin'], $plage2['fin']);
    
                if ($debutMax < $finMin) {
                    $resultat[] = [
                        'debut' => $debutMax,
                        'fin' => $finMin,
                    ];
                }
            }
        }
    
        return $resultat;
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

