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
     * @param string|null $debut date de debut de la recherche (1er jour de recherche)
     * @param string|null $fin Date de fin de la recherche (dernier jour de recherche)
     * @param string|null $debutH heure de debut de la recherche
     * @param string|null $finH heure de fin de la recherche
     * @param string|null $duration durée des créneaux communs demandés
     * @return array
     */
    function initMatrice(?array $utilisateurs, ?array $dates, ?string $debut, ?string $fin, ?string $debutH, ?string $finH, ?string $duration = "00:30"): array
{
    $matrice = [];
    list($durationHours, $durationMinutes) = explode(':', $duration);
    $durationInterval = $durationHours * 60 + $durationMinutes; // En minutes

    // Formatage des heures limites
    $debutFormate = new DateTime($debut);
    $finFormate = new DateTime($fin);
    $deb = $debutFormate->format('H:i');
    $fin = $finFormate->format('H:i');
    
    foreach ($dates as $date) {
        $matrice[$date] = [];
        
        // Déterminer l'heure de début pour cette date
        $heureDebutJournée = new DateTime("$date $debutH");
        $heureFinJournée = new DateTime("$date $finH");
        
        // Pour le premier jour, vérifier si deb est après debutH
        if ($date === $dates[0]) {
            if ($deb > $debutH) {
                // Si deb est après debutH, utiliser deb
                $heureDebutJournée = new DateTime("$date $deb");
            } else if ($deb < $debutH) {
                // Si deb est avant debutH, utiliser deb
                $heureDebutJournée = new DateTime("$date $deb");
            }
        }
        var_dump($heureDebutJournée);
        
        // Pour le dernier jour, vérifier si fin est avant finH
        $estDernierJour = ($date === end($dates));
        $heureFinDernierJR = clone $heureFinJournée;
        var_dump($estDernierJour);
        if ($estDernierJour) {
            var_dump($fin);
            var_dump($finH);
            if ($fin < $finH) {
                // Si fin est avant finH, utiliser fin
                $heureFinDernierJR = new DateTime("$date $fin");
            } else if ($fin > $finH) {
                // Si fin est après finH, utiliser fin
                $heureFinDernierJR = new DateTime("$date $fin");
            }
        }
        var_dump($heureFinDernierJR);
        
        // Générer les créneaux
        while ($heureDebutJournée < $heureFinDernierJR) {
            $start = $heureDebutJournée->format('H:i');
            
            // Calculer l'heure de fin du créneau
            $endTime = clone $heureDebutJournée;
            $endTime->add(new DateInterval("PT{$durationInterval}M"));
            
            // Si la fin du créneau dépasse la fin de journée, arrêter
            if ($endTime > $heureFinDernierJR) {
                break;
            }
            
            $end = $endTime->format('H:i');
            $key = "$start - $end";
            
            // Initialiser les utilisateurs dans ce créneau
            $matrice[$date][$key] = array_fill_keys(array_map(function ($user) {
                return $user->getId();
            }, $utilisateurs), 0);
            
            // Avancer au prochain créneau (incrément de 5 minutes)
            $heureDebutJournée->add(new DateInterval('PT5M'));
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
                    $matrice[$date][$interval][$utilisateur->getId()] = 1;
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
    function getCreneauxCommunsExact(array $matrice, int $nb_utilisateurs_exact, string $debutHoraire, string $finHoraire, string $debut, string $fin, ?array $idUtilisateursPrioritaires, ?bool $aDesUsersPrioritaires): array
    {
        $resultat = [];
        $dateDebutRecherche = new DateTime($debut);
        $dateFinRecherche = new DateTime($fin);

        // Extraction de l'heure et des minutes
        // Créer une copie de $dateDebutRecherche pour ne pas modifier l'original
        $dateDebut = clone $dateDebutRecherche;  // Copie de l'objet

        // Modification de l'heure avec l'horaire de début
        list($hour, $minute) = explode(":", $debutHoraire);
        $dateDebut->setTime($hour, $minute);

        // Créer une nouvelle copie pour $dateFin, pour modifier l'heure de fin sans toucher à $dateDebut
        $dateFin = clone $dateDebutRecherche;  // Copie de l'objet

        // Modification de l'heure avec l'horaire de fin
        list($hour, $minute) = explode(":", $finHoraire);
        $dateFin->setTime($hour, $minute);

        if($aDesUsersPrioritaires) {
            $tabUsersPrioritaires = [];
            foreach ($idUtilisateursPrioritaires as $idUserPrio) {
                $tabUsersPrioritaires[] = $idUserPrio;
            }

            foreach ($matrice as $date => $creneaux) {
                foreach ($creneaux as $plage => $users) {
                    foreach ($tabUsersPrioritaires as $idUtilisateursPrioritaires) {
                        if($users[$idUtilisateursPrioritaires] === 1) {
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
                            if ($heureDebutObj >= $dateDebut && $heureFinObj <= $dateFin && $heureFinObj <= $dateFinRecherche) {

                                // Compter le nombre d'utilisateurs disponibles dans ce créneau
                                $count_disponibles = count(array_filter($users, fn($dispo) => $dispo === 1));

                                // Vérifier si le nombre d'utilisateurs correspond exactement au critère
                                if ($count_disponibles === $nb_utilisateurs_exact) {
                                    $resultat[$date][$plage] = $users;
                                }
                            }
                        }
                    }
                }
            }
        } else {
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
                    if ($heureDebutObj >= $dateDebut && $heureFinObj <= $dateFin && $heureFinObj <= $dateFinRecherche) {

                        // Compter le nombre d'utilisateurs disponibles dans ce créneau
                        $count_disponibles = count(array_filter($users, fn($dispo) => $dispo === 1));

                        // Vérifier si le nombre d'utilisateurs correspond exactement au critère
                        if ($count_disponibles === $nb_utilisateurs_exact) {
                            $resultat[$date][$plage] = $users;
                        }
                    }
                }
            }
        }

        return $resultat;
    }

    // function getCreneauxCommunsExact(array $matrice, int $nb_utilisateurs_exact, string $debutHoraire, string $finHoraire, string $debut, string $fin): array
    // {
    //     $resultat = [];

    //     $dateClotureRecherche = new DateTime("$fin");
    //     // var_dump($dateClotureRecherche);

    //     // Convertir les dates et heures de début et de fin en objets DateTime
    //     $dateDebut = new DateTime(" $debut $debutHoraire");
    //     $dateFin = new DateTime("$debut $finHoraire");

    //     foreach ($matrice as $date => $creneaux) {

    //         foreach ($creneaux as $plage => $users) {
    //             // Extraire l'heure de début et de fin de la plage horaire
    //             [$heureDebut, $heureFin] = explode(' - ', $plage);

    //             // Créer des objets DateTime pour les heures de début et de fin
    //             $heureDebutObj = new DateTime($date . ' ' . $heureDebut);
    //             $heureFinObj = new DateTime($date . ' ' . $heureFin);

    //             if ($dateDebut > $dateFin) {
    //                 $dateFin->modify('+1 day');
    //             }

    //             if ($heureFinObj > $dateFin) {
    //                 $dateDebut->modify('+1 day');
    //                 $dateFin->modify('+1 day');
    //             }

    //             // Gérer les plages qui chevauchent minuit
    //             // Si l'heure de fin est avant l'heure de début, cela signifie que la plage va jusqu'au jour suivant
    //             if ($heureFinObj <= $heureDebutObj) {
    //                 $heureFinObj->modify('+1 day');
    //             }


    //             // Vérifier si la plage horaire est dans l'intervalle souhaité
    //             if ($heureDebutObj >= $dateDebut && $heureFinObj <= $dateFin && $heureFinObj <= $dateClotureRecherche) {

    //                 // Compter le nombre d'utilisateurs disponibles dans ce créneau
    //                 $count_disponibles = count(array_filter($users, fn($dispo) => $dispo === 1));

    //                 // Vérifier si le nombre d'utilisateurs correspond exactement au critère
    //                 if ($count_disponibles === $nb_utilisateurs_exact) {                
    //                     $resultat[$date][$plage] = $users;
    //                 }
    //             }
    //         }
    //     }

    //     return $resultat;
    // }




}

