<?php
/**
 * @author Félix AUTANT
 * @describe Classe des agendas
 * @version 0.1
 * @todo FAIS TA DOC FELIX
 */

class Assistant
{

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebPeriode(): ?int
    {
        return $this->dateDebPeriode;
    }

    public function getDateFinPeriode(): ?int
    {
        return $this->dateFinPeriode;
    }

    public function getUtilisateurs(): ?array
    {
        return $this->utilisateurs;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setDateDebPeriode(int $dateDebPeriode): void
    {
        $this->id = $dateDebPeriode;
    }

    public function setDateFinPeriode(int $dateFinPeriode): void
    {
        $this->id = $dateFinPeriode;
    }

    public function setUtilisateurs(array $utilisateurs): void
    {
        $this->utilisateurs = $utilisateurs;
    }

    // // Fonction pour vérifier les chevauchements d'événements
    // public function trouverDatesCommunes(array $creneaux, string $dureeMin): array
    // {
    //     $datesCommunes = [];

    //     // Convertir la durée minimale (format "H:i") en minutes
    //     list($heures, $minutes) = explode(':', $dureeMin);
    //     $dureeMinMinutes = ($heures * 60) + $minutes;

    //     // Obtenir toutes les plages horaires pour chaque sous-tableau
    //     $groupesCreneaux = array_map(function ($groupe) {
    //         return array_map(function ($creneau) {
    //             return [
    //                 'debut' => $creneau->getDateDebut(),
    //                 'fin' => $creneau->getDateFin(),
    //             ];
    //         }, $groupe);
    //     }, $creneaux);

    //     // Trouver l'intersection globale des plages horaires
    //     $intersection = $this->intersectionGlobale($groupesCreneaux);

    //     // Filtrer les intersections en fonction de la durée minimale
    //     foreach ($intersection as $plage) {
    //         $interval = $plage['debut']->diff($plage['fin']);
    //         $dureeMinutes = ($interval->h * 60) + $interval->i;

    //         if ($dureeMinutes >= $dureeMinMinutes) {
    //             $datesCommunes[] = [
    //                 'debut' => $plage['debut']->format('Y-m-d H:i:s'),
    //                 'fin' => $plage['fin']->format('Y-m-d H:i:s'),
    //                 'duree' => "{$interval->h}h {$interval->i}m"
    //             ];
    //         }
    //     }

    //     return $datesCommunes;
    // }

    // private function intersectionGlobale(array $groupesCreneaux): array
    // {
    //     // Initialiser l'intersection avec le premier groupe
    //     $intersection = $groupesCreneaux[0];

    //     // Calculer l'intersection progressive avec chaque groupe
    //     for ($i = 1; $i < count($groupesCreneaux); $i++) {
    //         $intersection = $this->intersecterDeuxGroupes($intersection, $groupesCreneaux[$i]);
    //     }

    //     return $intersection;
    // }

    // private function intersecterDeuxGroupes(array $groupe1, array $groupe2): array
    // {
    //     $resultat = [];

    //     foreach ($groupe1 as $plage1) {
    //         foreach ($groupe2 as $plage2) {
    //             $debutMax = max($plage1['debut'], $plage2['debut']);
    //             $finMin = min($plage1['fin'], $plage2['fin']);

    //             if ($debutMax < $finMin) {
    //                 $resultat[] = [
    //                     'debut' => $debutMax,
    //                     'fin' => $finMin,
    //                 ];
    //             }
    //         }
    //     }

    //     return $resultat;
    // }

    // public function combinerCreneaux($data): array
    // {

    //     // Étape 1 : Extraire et convertir les dates en timestamps
    //     $intervals = array_map(function ($item) {
    //         return [
    //             'start' => strtotime($item['dateDebut']),
    //             'end' => strtotime($item['dateFin'])
    //         ];
    //     }, $data);

    //     // Étape 2 : Trier les créneaux par date de début
    //     usort($intervals, function ($a, $b) {
    //         return $a['start'] - $b['start'];
    //     });

    //     // Étape 3 : Fusionner les créneaux
    //     $mergedIntervals = [];
    //     foreach ($intervals as $interval) {
    //         if (empty($mergedIntervals)) {
    //             $mergedIntervals[] = $interval;
    //         } else {
    //             $lastInterval = &$mergedIntervals[count($mergedIntervals) - 1];
    //             if ($interval['start'] <= $lastInterval['end']) {
    //                 // Étendre l'heure de fin si chevauchement
    //                 $lastInterval['end'] = max($lastInterval['end'], $interval['end']);
    //             } else {
    //                 $mergedIntervals[] = $interval;
    //             }
    //         }
    //     }

    //     // Étape 4 : Convertir les timestamps en format lisible
    //     $results = array_map(function ($interval) {
    //         return [
    //             'dateDebut' => date('Y-m-d H:i:s', $interval['start']),
    //             'dateFin' => date('Y-m-d H:i:s', $interval['end'])
    //         ];
    //     }, $mergedIntervals);

    //     return $results;


    function initMatrice($utilisateurs, $dates, $durationHours = 0, $durationMinutes = 30)
    {
        $matrice = [];
        foreach ($dates as $date) {
            $matrice[$date] = [];
            $time = new DateTime("$date 00:00");
            $durationInterval = $durationHours * 60 + $durationMinutes; // Convertir la durée en minutes
            $totalSlots = floor((24 * 60) / 5); // Total de créneaux possibles en une journée (intervalles de 5 minutes)

            for ($i = 0; $i < $totalSlots; $i++) {
                $start = $time->format('H:i');
                $end = $time->add(new DateInterval("PT{$durationInterval}M"))->format('H:i');
                $time->sub(new DateInterval('PT' . ($durationInterval - 5) . 'M')); // Ajustement pour le prochain créneau
                $key = "$start - $end";

                // Initialisation des utilisateurs dans chaque créneau
                $matrice[$date][$key] = array_fill_keys(array_map(function ($user) {
                    return $user->getNom();
                }, $utilisateurs), 0);
            }
        }
        return $matrice;
    }

    // Fonction pour générer les dates entre deux datetime
    function genererDates($debut, $fin)
    {
        $dates = [];
        $interval = new DateInterval('P1D');
        $periode = new DatePeriod($debut, $interval, $fin->add($interval));
        foreach ($periode as $date) {
            $dates[] = $date->format('Y-m-d');
        }
        return $dates;
    }

    // Fonction pour remplir la matrice avec un créneau
    function remplirCreneau(&$matrice, $datetime_debut, $datetime_fin, $utilisateur)
    {
        foreach ($matrice as $date => $creneaux) {
            foreach ($creneaux as $interval => $users) {
                // Séparer l'intervalle en début et fin
                list($start, $end) = explode(' - ', $interval);

                // Créer les objets DateTime pour la comparaison
                $start_datetime = new DateTime("$date $start");
                $end_datetime = new DateTime("$date $end");

                // Vérifier si la date et l'heure à comparer sont dans l'intervalle
                if ($datetime_debut <= $start_datetime && $datetime_fin >= $end_datetime) {
                    $matrice[$date][$interval][$utilisateur->getNom()] = 1; // Marque la disponibilité
                }
            }
        }

    }
    
    function getCreneauxCommunsExact(array $matrice, int $nb_utilisateurs_exact): array
    {
        $resultat = [];
    
        foreach ($matrice as $date => $creneaux) {
            foreach ($creneaux as $plage => $users) {
                // Compter le nombre d'utilisateurs disponibles dans ce créneau
                $count_disponibles = count(array_filter($users, fn($dispo) => $dispo === 1));
    
                // Vérifier si le nombre d'utilisateurs correspond exactement au critère
                if ($count_disponibles === $nb_utilisateurs_exact) {
                    $resultat[$date][$plage] = $users;
                }
            }
        }
    
        return $resultat;
    }


}

