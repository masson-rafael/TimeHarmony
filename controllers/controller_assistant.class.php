<?php

use ICal\ICal;


/**
 * @author Félix Autant
 * @brief Controller de la page de l'assistant qui recherche les crébneaux libres
 * @todo Verifier que le undocumented class soit pas à remplir. S'il existe mêmegit
 * @version 0.1
 */

/**
 * Undocumented class
 */
class ControllerAssistant extends Controller
{
    /**
     * Constructeur par défaut
     *
     * @param \Twig\Environment $twig Environnement twig
     * @param \Twig\Loader\FilesystemLoader $loader Loader de fichier
     */

    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }

    /**
     * Fonction qui permet de générer la vue qui contiendra les paramètres de la recherche
     * @return void
     */
    public function genererVueRecherche(?array $tabMessages = null, ?bool $contientErreurs = false): void
    {

        // vide la variable de session nbUserSelectionné
        unset($_SESSION['nbUserSelectionné']);
        unset($_SESSION['contacts']);
        unset($_SESSION['debut']);
        unset($_SESSION['fin']);
        unset($_SESSION['groupes']);
        unset($_POST['increment']);
        unset($_POST['decrement']);
        unset($_POST['debutPlageH']);
        unset($_POST['finPlageH']);

        // $utilisateur = $_SESSION['utilisateur'];

        // var_dump($utilisateur);
        $pdo = $this->getPdo();
        $managerUtilisateur = new UtilisateurDao($pdo);
        $utilisateur = $managerUtilisateur->find($_SESSION['utilisateur']);
        

        $contacts = $utilisateur->getContact($utilisateur->getId());
        $groupes = $utilisateur->getGroupe($utilisateur->getId());

        // Récupérer les ids des membres des groupes
        $membres = [];
        $pdo = $this->getPdo();
        $manager = new GroupeDao($pdo);

        // Récupérer les membres de chaque groupe
        foreach ($groupes as $groupe) {
            $membresGroupe = $manager->getUsersFromGroup($groupe->getId());

            // Stocker les IDs des membres dans un tableau
            $membres[$groupe->getId()] = [];
            foreach ($membresGroupe as $membre) {
                $membres[$groupe->getId()][] = $membre['idUtilisateur'];
            }
        }

        //Génération de la vue
        $template = $this->getTwig()->load('recherche.html.twig');
        echo $template->render(array(
            'menu' => "recherche",
            'contacts' => $contacts,
            'groupes' => $groupes,
            'message' => $tabMessages,
            'membres' => $membres,
            'contientErreurs' => $contientErreurs
        ));
    }

    /**
     * Fonction de faire la recherche et d'obtenir les créneaux communs disponibles
     * @return void
     */
    public function obtenir(): void
    {
        $datesCommunes = [];
        $messagesErreur = [];

        $pdo = $this->getPdo();

        if (isset($_SESSION['debut']) && isset($_SESSION['fin']) && isset($_SESSION['dureeMin']) && isset($_SESSION['contacts']) && isset($_SESSION['debutHoraire']) && isset($_SESSION['finHoraire'])) {
            $_POST['debut'] = $_SESSION['debut'];
            $_POST['fin'] = $_SESSION['fin'];
            $_POST['dureeMin'] = $_SESSION['dureeMin'];
            $_POST['contacts'] = $_SESSION['contacts'];
            $_POST['debutPlageH'] = $_SESSION['debutPlageH'];
            $_POST['finPlageH'] = $_SESSION['finPlageH'];
        }
        // if(isset($_SESSION['contactsPrioritaires'])) {
        //     $_POST['contactsPrioritaires'] = $_SESSION['contactsPrioritaires'];
        // }

        // var_dump($_POST);

        if(isset($_SESSION['contacts'])) {
            $_POST['contacts'] = $_SESSION['contacts'];
        }

        $valideDuree = Utilitaire::validerDuree($_POST['debut'], $_POST['fin'], $messagesErreur);
        $dureeMinValide = Utilitaire::validerDureeMinimale($_POST['dureeMin'], $messagesErreur);
        @$contactsPrioritairesValide = Utilitaire::validerContacts($_POST['contactsPrioritaires'], $messagesErreur);
        @$contactsValide = Utilitaire::validerContacts($_POST['contacts'], $messagesErreur);
        $plageHoraireValide = Utilitaire::validerPlageHoraire($_POST['debutPlageH'], $_POST['finPlageH'], $messagesErreur);

        // var_dump($_POST['contacts']);
        // var_dump($valideDuree);
        // var_dump($dureeMinValide);
        // var_dump($contactsValide);
        // var_dump($plageHoraireValide);

        if ($valideDuree && $dureeMinValide && $contactsValide && $plageHoraireValide) {
            //var_dump("CHAMPS VALIDES");
            if (!isset($_SESSION['debut']) || !isset($_SESSION['fin']) || !isset($_SESSION['dureeMin']) || !isset($_SESSION['contacts']) || !isset($_SESSION['debutPlageH']) || !isset($_SESSION['finPlageH'])) {
                $_SESSION['debut'] = $_POST['debut'];
                $_SESSION['fin'] = $_POST['fin'];
                $_SESSION['dureeMin'] = $_POST['dureeMin'];
                $_SESSION['contacts'] = $_POST['contacts'];
                $_SESSION['debutPlageH'] = $_POST['debutPlageH'];
                $_SESSION['finPlageH'] = $_POST['finPlageH'];
            }

            // if ($contactsPrioritairesValide) {
            //     if (!isset($_SESSION['contactsPrioritaires'])) {
            //         $_SESSION['contactsPrioritaires'] = $_POST['contactsPrioritaires'];
            //     }
            // }

            // $chronoStartGen = new DateTime();
            $managerCreneau = new CreneauLibreDao($pdo);
            $managerCreneau->supprimerCreneauxLibres();

            extract($_POST, EXTR_OVERWRITE);

            if (isset($_POST['debut']) && isset($_POST['fin']) && isset($_POST['dureeMin']) && isset($_POST['debutPlageH']) && isset($_POST['finPlageH'])) {
                $_SESSION['dureeMin'] = $_POST['dureeMin'];
                $_SESSION['debut'] = $_POST['debut'];
                $_SESSION['fin'] = $_POST['fin'];
                $_SESSION['debutPlageH'] = $_POST['debutPlageH'];
                $_SESSION['finPlageH'] = $_POST['finPlageH'];
                //var_dump("STOP");
            }

            $dureeMin = $_SESSION['dureeMin'];
            $contacts = $_SESSION['contacts'];
            // A FAIRE PLUS TARD : Faire une vérification des contacts prioritaires vidés
            @$contactsPrio = $_SESSION['contactsPrioritaires'];
            $debut = $_SESSION['debut'];
            $fin = $_SESSION['fin'];
            $debutHoraire = $_SESSION['debutPlageH'];
            $finHoraire = $_SESSION['finPlageH'];

            $managerUtilisateur = new UtilisateurDAO($pdo);
            $tableauUtilisateur = [];

            foreach ($contacts as $idUtilisateurCourant) {
                $tableauUtilisateur[] = $managerUtilisateur->find($idUtilisateurCourant);
            }

            $utilisateur = $managerUtilisateur->find($_SESSION['utilisateur']);
            $tableauUtilisateur[] = $utilisateur;

            $contactsPrioritaires = [];
            if(!empty($contactsPrio)) {
                foreach ($contactsPrio as $contact) {
                    $manager = new UtilisateurDAO($pdo);
                    $user = $manager->find($contact);
                    $contactsPrioritaires[] = strtolower($user->getNom());
                }
                $aDesPriorites = true;
            }

            if(empty($contactsPrioritaires)) {
                $aDesPriorites = false;
            }

            if (isset($_POST['groupes'])) {
                $managerGroupe = new GroupeDao($pdo);
                foreach ($groupes as $idGroupe) {
                    $tableauUtilisateurGroupe[] = $managerGroupe->getUsersFromGroup($idGroupe);
                }
                $idUtilisateurs = array_column($tableauUtilisateurGroupe[0], 'idUtilisateur');

                foreach ($idUtilisateurs as $idUtilisateurGroupe) {
                    $verif = false;
                    foreach ($tableauUtilisateur as $utilisateur) {
                        if ($idUtilisateurGroupe === $utilisateur->getId()) {
                            $verif = true;
                        }
                    }
                    if ($verif === false) {
                        $tableauUtilisateur[] = $managerUtilisateur->find($idUtilisateurGroupe);
                        $_SESSION['contacts'][] = $idUtilisateurGroupe;
                    }
                }
            }

            $tailleTabUser = count($tableauUtilisateur);

            // Initialisez la session pour stocker la variable
            if (!isset($_SESSION['nbUserSelectionné'])) {
                $_SESSION['nbUserSelectionné'] = $tailleTabUser;
            }

            // Gérer les actions des boutons
            /**
             * @todo formulaires POST
             */
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['increment'])) {
                    $_SESSION['nbUserSelectionné']++;
                } elseif (isset($_POST['decrement'])) {
                    $_SESSION['nbUserSelectionné']--;
                }
            }

            // var_dump($debut);
            // var_dump($fin);
            // var_dump($tableauUtilisateur);
            $assistantRecherche = new Assistant(new Datetime($debut), new Datetime($fin), $tableauUtilisateur);

            // $chronoStart = new DateTime();

            // Génération des dates pour la période
            $dates = $assistantRecherche->genererDates($debut, $fin);

            // $chronoEnd = new DateTime();
            // $chronoInterval = $chronoStart->diff($chronoEnd);
            // $chronoSeconds = $chronoEnd->getTimestamp() - $chronoStart->getTimestamp();
            // echo "Durée genererDates : " . $chronoInterval->format('%s secondes (%H:%I:%S)') . "<br>";
            // echo "Durée totale en secondes genererDates : $chronoSeconds secondes." . "<br>" . "<br>";

            // $chronoStart = new DateTime();

            // Initialisation de la matrice
            $matrice = $assistantRecherche->initMatrice($tableauUtilisateur, $dates, $dureeMin);

            // $chronoEnd = new DateTime();
            // $chronoInterval = $chronoStart->diff($chronoEnd);
            // $chronoSeconds = $chronoEnd->getTimestamp() - $chronoStart->getTimestamp();
            // echo "Durée initMatrine : " . $chronoInterval->format('%s secondes (%H:%I:%S)') . "<br>";
            // echo "Durée totale en secondes initMatrice : $chronoSeconds secondes." . "<br>" . "<br>";

            foreach ($assistantRecherche->getUtilisateurs() as $utilisateurCourant) {
                $utilisateur = new Utilisateur($utilisateurCourant->getId(), $utilisateurCourant->getNom());
                $agendas = $utilisateur->getAgendas();
                $allEvents = [];

                // $chronoStart = new DateTime();
                foreach ($agendas as $agenda) {
                    $urlIcs = $agenda->getUrl();
                    $allEvents = $agenda->recuperationEvenementsAgenda($urlIcs, $debut, $fin, $allEvents);
                }
                // $chronoEnd = new DateTime();
                // $chronoInterval = $chronoStart->diff($chronoEnd);
                // $chronoSeconds = $chronoEnd->getTimestamp() - $chronoStart->getTimestamp();
                // echo "Durée recup events agendas : " . $chronoInterval->format('%s secondes (%H:%I:%S)') . "<br>";
                // echo "Durée totale en secondes recup events agendas : $chronoSeconds secondes." . "<br>" . "<br>";

                $mergedEvents = $agenda->mergeAgendas($allEvents);
                $creneauxByUtilisateur = $agenda->recherche('Europe/Paris', $debut, $fin, $mergedEvents);

                // $chronoStart = new DateTime();
                foreach ($creneauxByUtilisateur as $key => $creneau) {
                    $dateDebut = $creneau->getDateDebut()->format('Y-m-d H:i:s');
                    $datetime_debut = new DateTime($dateDebut);  // Début du créneau
                    $dateFin = $creneau->getDateFin()->format('Y-m-d H:i:s');
                    $datetime_fin = new DateTime($dateFin);  // Début du créneau
                    $assistantRecherche->remplirCreneau($matrice, $datetime_debut, $datetime_fin, $utilisateurCourant);
                }

                // $chronoEnd = new DateTime();
                // $chronoInterval = $chronoStart->diff($chronoEnd);
                // $chronoSeconds = $chronoEnd->getTimestamp() - $chronoStart->getTimestamp();
                // echo "Durée remplir creneau : " . $chronoInterval->format('%s secondes (%H:%I:%S)') . "<br>";
                // echo "Durée totale en secondes remplir creneau : $chronoSeconds secondes." . "<br>" . "<br>";
            }

            // Appel de la fonction
            $datesCommunes = $assistantRecherche->getCreneauxCommunsExact($matrice, $_SESSION['nbUserSelectionné'], $debutHoraire, $finHoraire, $debut, $fin, $contactsPrioritaires, $aDesPriorites);
            // exit;
            // $chronoEndGen = new DateTime();
            // $chronoInterval = $chronoStartGen->diff($chronoEndGen);
            // $chronoSeconds = $chronoEndGen->getTimestamp() - $chronoStartGen->getTimestamp();
            // echo "Durée totale algo : " . $chronoInterval->format('%s secondes (%H:%I:%S)') . "<br>";
            // echo "Durée totale en secondes totale algo : $chronoSeconds secondes." . "<br>" . "<br>";

            // Générer la vue avec les données structurées
            $tailleContacts = sizeof($tableauUtilisateur);
            $nombreUtilisateursSeclectionnes = $_SESSION['nbUserSelectionné'];
            $nbrUtilisateursMin = ceil($tailleContacts / 2);

            // //Convertion date américaine en française
            // foreach ($datesCommunes as $date => $plagesHoraires) {
            //     // Transformer la date en format français
            //     $dateFr = DateTime::createFromFormat('Y-m-d', $date)->format('d/m/Y');

            //     // Copier les plages horaires sous la nouvelle clé formatée
            //     $datesCommunesFrancaise[$dateFr] = $plagesHoraires;
            // }

            // Compter le nombre total de créneaux disponibles
            $nombreCreneauxDisponibles = 0;

            foreach ($datesCommunes as $dateFr => $plagesHoraires) {
                // Transformer "30/01/2025" en objet DateTime
                $dateObj = DateTime::createFromFormat('Y-m-d', $dateFr);

                // Vérifier si la conversion a réussi
                if ($dateObj !== false) {
                    // Stocker la date sous forme d'objet DateTime avec format 'Y-m-d'
                    $datesCommunesFrancaise[$dateObj->format('d-m-Y')] = $plagesHoraires;
                } else {
                    // Gérer l'erreur si la date ne peut pas être parsée
                    echo "Erreur : la date '$dateFr' n'est pas valide.\n";
                }

                $nombreCreneauxDisponibles += count($plagesHoraires);
            }

            //var_dump($nombreCreneauxDisponibles);
            // var_dump($datesCommunesFrancaise);

            $this->genererVueCreneaux($datesCommunesFrancaise, $nbrUtilisateursMin, $nombreUtilisateursSeclectionnes);
        } else {
            $this->genererVueRecherche($messagesErreur, true);
        }
    }


    /**
     * Fonction qui permet de générer la vue qui contiendra les résultats de la recherche
     * @param array|null $creneaux les creneaux libres communs trouvés grace a la recherche
     * @param int|null $nbrUtilisateursMin le nombre minimum de personnes concernés par la recherche
     * @param int|null $nombreUtilisateursSelectionnes le nombre de personnes selectionnés par la recherche
     * @return void
     */
    public function genererVueCreneaux(?array $creneaux, ?int $nbrUtilisateursMin, ?int $nombreUtilisateursSeclectionnes): void
    {
        // Formater les créneaux pour le calendrier FullCalendar
        $evenements = [];

        foreach ($creneaux as $date => $plagesHoraires) {
            $dateObj = DateTime::createFromFormat('d-m-Y', $date);
            if (!$dateObj) continue;
            
            // Tri des plages par heure de début
            uksort($plagesHoraires, function($a, $b) {
                $startA = explode(' - ', $a)[0];
                $startB = explode(' - ', $b)[0];
                return strtotime($startA) <=> strtotime($startB);
            });
    
            $mergedEvent = null;
            
            foreach ($plagesHoraires as $plage => $participants) {
                // Récupérer les dates de début et de fin
                list($debut, $fin) = explode(' - ', $plage);
                $start = DateTime::createFromFormat('d-m-Y H:i', "$date $debut");
                $dateDebut = $start; // Date initiale de la recherche
                $end = DateTime::createFromFormat('d-m-Y H:i', "$date $fin");
                
                // Vérifier si on doit fusionner les événements (interval de 30 minutes)
                if (!$mergedEvent) {
                    // Premier événement
                    $mergedEvent = [
                        'start' => $start,
                        'end' => $end,
                    ];
                } 
                else {
                    $diffSeconds = $start->getTimestamp() - $mergedEvent['end']->getTimestamp();
                    if ($diffSeconds <= 30 * 60) { // 30 minutes = 1800 secondes
                        // Si les événements se chevauchent ou si l'écart est inférieur ou égal à 30 minutes, on fusionne
                        if ($end > $mergedEvent['end']) {
                            $mergedEvent['end'] = $end;
                        }
                    } else {
                        // Sinon, on ajoute l'événement fusionné et on réinitialise
                        $evenements[] = $this->createEventObject($mergedEvent);
                        $mergedEvent = [
                            'start' => $start,
                            'end' => $end,
                        ];
                    }
                }
            }
            
            // Ajouter le dernier événement fusionné
            if ($mergedEvent) {
                $evenements[] = $this->createEventObject($mergedEvent);
            }
        }

        $template = $this->getTwig()->load('resultat.html.twig');

        echo $template->render([
            'menu' => "recherche",
            'creneauxCommuns' => $creneaux,
            'nbrUtilisateursMin' => $nbrUtilisateursMin,
            'nombreUtilisateursSeclectionnes' => $nombreUtilisateursSeclectionnes,
            'evenements' => $evenements,
            'dateDebut' => $dateDebut,
        ]);
    }

    /**
    * Créer un objet événement pour FullCalendar
    * @param array $mergedEvent
    * @return array
    */
    private function createEventObject(array $mergedEvent): array
    {
        return [
            'title' => 'Créneau disponible',
            'start' => $mergedEvent['start']->format('Y-m-d\TH:i:s'),
            'end' => $mergedEvent['end']->format('Y-m-d\TH:i:s'),
        ];
    }

    /**
     * Fonction permettant de générer la vue par defaut de l'application
     * @return void
     */
    public function genererVue(): void
    {
        $template = $this->getTwig()->load('index.html.twig');
        echo $template->render(array());
    }

        /**
     * Fonction qui permet d'afficher la premiere page de la recherche : personnes obligatoires
     * 
     * @param array|null $tabMessages tableau contenant les messages d'erreurs
     * @param bool|null $contientErreurs booleen indiquant si la page contient des erreurs
     * @return void
     */
    public function afficherPersonnesObligatoires(?array $tabMessages = null, ?bool $contientErreurs = false): void {
        $utilisateur = $_SESSION['utilisateur'];
        $contacts = $utilisateur->getContact($utilisateur->getId());
        $groupes = $utilisateur->getGroupe($utilisateur->getId());

        // Récupérer les ids des membres des groupes
        $membres = [];
        $pdo = $this->getPdo();
        $manager = new GroupeDao($pdo);

        // Récupérer les membres de chaque groupe
        if(!empty($groupes)) {
            foreach ($groupes as $groupe) {
                $membresGroupe = $manager->getUsersFromGroup($groupe->getId());

                // Stocker les IDs des membres dans un tableau
                $membres[$groupe->getId()] = [];
                foreach ($membresGroupe as $membre) {
                    $membres[$groupe->getId()][] = $membre['idUtilisateur'];
                }
            }
        } else {
            $manager = new UtilisateurDAO($pdo);
            $contacts = $manager->findAllContact($utilisateur->getId());
            foreach ($contacts as $contact) {
                $membres[] = $contact->getId();
            }
        }

        $template = $this->getTwig()->load('recherche.html.twig');
        echo $template->render(array(
            'page' => 1,
            'contacts' => $contacts,
            'groupes' => $groupes,
            'message' => $tabMessages,
            'membres' => $membres,
            'contientErreurs' => $contientErreurs
        ));
    }

    public function afficherParametres(?array $tabMessages = null, ?bool $contientErreurs = false): void {
        if(isset($_POST['contacts'])) {
            $_SESSION['contacts'] = $_POST['contacts'];
        }

        if(isset($_POST['contactsPrioritaires'])) {
            unset($_SESSION['contactsPrioritaires']);
            $_SESSION['contactsPrioritaires'] = $_POST['contactsPrioritaires'];
        }

        $template = $this->getTwig()->load('recherche.html.twig');
        echo $template->render(array(
            'page' => 2,
            'message' => $tabMessages,
            'contientErreurs' => $contientErreurs
        ));
    }
}
