<?php
/**
 * @author Félix AUTANT
 * @brief Classe de l'assistant de recherche
 * @version 0.1
 */

class Assistant
{

    /**
     *
     * @var integer|null id de l'assistant
     */
    private int|null $id;

    /**
     *
     * @var DateTime|null date du début de la recherche
     */
    private DateTime|null $dateDebPeriode;

    /**
     *
     * @var DateTime|null date de la fin de la recherche
     */
    private DateTime|null $dateFinPeriode;

    /**
     *
     * @var array|null tableau des utilisateurs
     */
    private array|null $utilisateurs;


    /**
     * Constructeur par défaut
     *
     * @param DateTime $dateDebPeriode de la recherche
     * @param DateTime $dateFinPeriode de la recherche
     * @param array $utilisateurs concernés par la recherche
     */
    public function __construct(?DateTime $dateDebPeriode, ?DateTime $dateFinPeriode, ?array $utilisateurs)
    {
        $this->dateDebPeriode = $dateDebPeriode;
        $this->dateFinPeriode = $dateFinPeriode;
        $this->utilisateurs = $utilisateurs;
    }

    /**
     * Get l'id de l'assistant
     *
     * @return integer $id de l'assistant
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get la date du début de la période de recherche
     *
     * @return DateTime $dateDebutPeriode de la recherche
     */
    public function getDateDebPeriode(): ?DateTime
    {
        return $this->dateDebPeriode;
    }

    /**
     * Get la date de fin de la période de recherche
     *
     * @return DateTime $dateFinPeriode de la recherche
     */
    public function getDateFinPeriode(): ?DateTime
    {
        return $this->dateFinPeriode;
    }

    /**
     * Get les utilisateurs concernés par la recherche
     *
     * @return array $utilisateurs
     */
    public function getUtilisateurs(): ?array
    {
        return $this->utilisateurs;
    }

    /**
     * Set l'id de l'assistant
     *
     * @param integer $id de l'assitant
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Set la date de début de la période de recherche
     *
     * @param DateTime $dateDebPeriode de la recherche
     * @return void
     */
    public function setDateDebPeriode(int $dateDebPeriode): void
    {
        $this->id = $dateDebPeriode;
    }

    /**
     * Set la date de fin de la période de recherche
     *
     * @param DateTime $dateFinPeriode de la recherche
     * @return void
     */
    public function setDateFinPeriode(int $dateFinPeriode): void
    {
        $this->id = $dateFinPeriode;
    }

    /**
     * Set les utilisateurs concernés par la recherche
     *
     * @param array $utilisateurs de la recherche
     * @return void
     */
    public function setUtilisateurs(array $utilisateurs): void
    {
        $this->utilisateurs = $utilisateurs;
    }

    /**
     * Fonction de génération de la matrice qui contriendra créneaux libres des utilisateurs
     *
     * @param array|null $utilisateurs concernés par la recherche d'un créneau commun
     * @param array|null $dates concernés par la recherche
     * @param string|null $duration durée des créneaux communs demandés
     * @return array
     */
    function initMatrice($utilisateurs, $dates, $duration = "00:30"): array
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


    /**
     * Fonction pour générer les dates entre deux datetime
     *
     * @param string|null $debut date de debut de la recherche
     * @param string|null $fin Date de fin de la recherche
     * @return array
     */
    function genererDates($debut, $fin): array
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

    /**
     * Fonction pour remplir la matrice avec un créneau
     *
     * @param array|null $matrice 
     * @param string|null $debut date de debut de la recherche
     * @param string|null $fin Date de fin de la recherche
     * @return void
     */
    function remplirCreneau(&$matrice, $debutCreneau, $finCreneau, $utilisateur)
    {
        foreach ($matrice as $date => $creneaux) {
            foreach ($creneaux as $interval => $users) {
                // Séparer l'intervalle en début et fin
                list($start, $end) = explode(' - ', $interval);

                // Créer les objets DateTime pour la comparaison
                $debutPlage = new DateTime("$date $start");
                $finPlage = new DateTime("$date $end");

                // Vérifier si la date et l'heure à comparer sont dans l'intervalle
                if ($debutCreneau <= $debutPlage && $finCreneau >= $finPlage) {
                    $matrice[$date][$interval][$utilisateur->getNom()] = 1;
                }
            }
        }

    }

    /**
     * Fonction pour récupérer les créneaux communs en fonction d'un nombre d'utilisateur
     *
     * @param array|null $matrice 
     * @param int|null $nb_utilisateurs_exact concernés par la recherche
     * @param string|null $debutHoraire : début de la plage horaire
     * @param string|null $finHoraire : fin de la plage horaire
     * @return array
     */
    function getCreneauxCommunsExact(array $matrice, int $nb_utilisateurs_exact, string $debutHoraire, string $finHoraire, string $debut, string $fin): array
    {
        $resultat = [];

        $dateClotureRecherche = new DateTime("$fin $finHoraire");

        // Convertir les dates et heures de début et de fin en objets DateTime
        $dateDebut = new DateTime(" $debut $debutHoraire");
        $dateFin = new DateTime("$debut $finHoraire");

        foreach ($matrice as $date => $creneaux) {

            foreach ($creneaux as $plage => $users) {
                // Extraire l'heure de début et de fin de la plage horaire
                [$heureDebut, $heureFin] = explode(' - ', $plage);

                // Créer des objets DateTime pour les heures de début et de fin
                $heureDebutObj = new DateTime($date . ' ' . $heureDebut);
                $heureFinObj = new DateTime($date . ' ' . $heureFin);

                if ($dateDebut > $dateFin) {
                    $dateFin->modify('+1 day');
                }

                if ($heureFinObj > $dateFin) {
                    $dateDebut->modify('+1 day');
                    $dateFin->modify('+1 day');
                }

                // Gérer les plages qui chevauchent minuit
                // Si l'heure de fin est avant l'heure de début, cela signifie que la plage va jusqu'au jour suivant
                if ($heureFinObj <= $heureDebutObj) {
                    $heureFinObj->modify('+1 day');
                }


                // Vérifier si la plage horaire est dans l'intervalle souhaité
                if ($heureDebutObj >= $dateDebut && $heureFinObj <= $dateFin && $heureFinObj <= $dateClotureRecherche) {

                    // Compter le nombre d'utilisateurs disponibles dans ce créneau
                    $count_disponibles = count(array_filter($users, fn($dispo) => $dispo === 1));
                    
                    // Vérifier si le nombre d'utilisateurs correspond exactement au critère
                    if ($count_disponibles === $nb_utilisateurs_exact) {                
                        $resultat[$date][$plage] = $users;
                    }
                }
            }
        }

        return $resultat;
    }




}

