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

    function initMatrice($utilisateurs, $dates, $duration = "00:30")
    {
        $matrice = [];
        list($durationHours, $durationMinutes) = explode(':', $duration); // Extraire les heures et minutes depuis la chaîne
        $durationInterval = $durationHours * 60 + $durationMinutes; // Convertir la durée en minutes

        foreach ($dates as $date) {
            $matrice[$date] = [];
            $time = new DateTime("$date 00:00");
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
        $debut = new DateTime($debut);
        $fin = new DateTime($fin);
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

