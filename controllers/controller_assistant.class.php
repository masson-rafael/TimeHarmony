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
        $debugMode = false;
        $datesCommunes = [];
        $messagesErreur = [];

        $pdo = $this->getPdo();

        // var_dump(isset($_SESSION['debut']));
        // var_dump(isset($_SESSION['fin']));
        // var_dump(isset($_SESSION['dureeMin']));
        // var_dump(isset($_SESSION['contacts']));
        // var_dump(isset($_SESSION['debutHoraire']));
        // var_dump(isset($_SESSION['finHoraire']));

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

        if (isset($_SESSION['contacts'])) {
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
            } else {
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

            $chronoStartGen = new DateTime();
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
            $_SESSION['finHoraire'] = $finHoraire;
            $_SESSION['debutHoraire'] = $debutHoraire;

            $managerUtilisateur = new UtilisateurDAO($pdo);
            $tableauUtilisateur = [];

            foreach ($contacts as $idUtilisateurCourant) {
                $tableauUtilisateur[] = $managerUtilisateur->find($idUtilisateurCourant);
            }

            $utilisateur = $managerUtilisateur->find($_SESSION['utilisateur']);
            $tableauUtilisateur[] = $utilisateur;

            $contactsPrioritaires = [];
            if (!empty($contactsPrio)) {
                foreach ($contactsPrio as $contact) {
                    $manager = new UtilisateurDAO($pdo);
                    $user = $manager->find($contact);
                    $contactsPrioritaires[] = strtolower($user->getNom());
                }
                $aDesPriorites = true;
            }

            if (empty($contactsPrioritaires)) {
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
                        $tableauUtilisateur[] = $managerUtilisateur->find($idUtilisateurGroupe)->getId();
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

            $debugMode == true ? $chronoStart = new DateTime() : null;

            // Génération des dates pour la période
            $dates = $assistantRecherche->genererDates($debut, $fin);

            if ($debugMode == true) {
                $chronoEnd = new DateTime();
                $chronoInterval = $chronoStart->diff($chronoEnd);
                $chronoSeconds = $chronoEnd->getTimestamp() - $chronoStart->getTimestamp();
                echo "Durée genererDates : " . $chronoInterval->format('%s secondes (%H:%I:%S)') . "<br>";
                echo "Durée totale en secondes genererDates : $chronoSeconds secondes." . "<br>" . "<br>";
                $chronoStart = new DateTime();
            }

            // Initialisation de la matrice
            $matrice = $assistantRecherche->initMatrice($tableauUtilisateur, $dates, $debut, $fin, $debutHoraire, $finHoraire, $dureeMin);
            $debugMode == true ? var_dump($matrice) : null;

            if ($debugMode == true) {
                $chronoEnd = new DateTime();
                $chronoInterval = $chronoStart->diff($chronoEnd);
                $chronoSeconds = $chronoEnd->getTimestamp() - $chronoStart->getTimestamp();
                echo "Durée initMatrine : " . $chronoInterval->format('%s secondes (%H:%I:%S)') . "<br>";
                echo "Durée totale en secondes initMatrice : $chronoSeconds secondes." . "<br>" . "<br>";
            }

            foreach ($assistantRecherche->getUtilisateurs() as $utilisateurCourant) {
                $utilisateur = new Utilisateur($utilisateurCourant->getId(), $utilisateurCourant->getNom());
                $agendas = $utilisateur->getAgendas();
                $allEvents = [];

                $debugMode == true ? $chronoStart = new DateTime() : null;
                foreach ($agendas as $agenda) {
                    $urlIcs = $agenda->getUrl();
                    $allEvents = $agenda->recuperationEvenementsAgenda($urlIcs, $debut, $fin, $allEvents);
                }

                if ($debugMode == true) {
                    $chronoEnd = new DateTime();
                    $chronoInterval = $chronoStart->diff($chronoEnd);
                    $chronoSeconds = $chronoEnd->getTimestamp() - $chronoStart->getTimestamp();
                    echo "Durée recup events agendas : " . $chronoInterval->format('%s secondes (%H:%I:%S)') . "<br>";
                    echo "Durée totale en secondes recup events agendas : $chronoSeconds secondes." . "<br>" . "<br>";
                }

                $mergedEvents = $agenda->mergeAgendas($allEvents);
                $creneauxByUtilisateur = $agenda->recherche('Europe/Paris', $debut, $fin, $mergedEvents);

                $debugMode == true ? $chronoStart = new DateTime() : null;
                foreach ($creneauxByUtilisateur as $key => $creneau) {
                    $dateDebut = $creneau->getDateDebut()->format('Y-m-d H:i:s');
                    $datetime_debut = new DateTime($dateDebut);  // Début du créneau
                    $dateFin = $creneau->getDateFin()->format('Y-m-d H:i:s');
                    $datetime_fin = new DateTime($dateFin);  // Début du créneau
                    $assistantRecherche->remplirCreneau($matrice, $datetime_debut, $datetime_fin, $utilisateurCourant);
                }

                if ($debugMode == true) {
                    $chronoEnd = new DateTime();
                    $chronoInterval = $chronoStart->diff($chronoEnd);
                    $chronoSeconds = $chronoEnd->getTimestamp() - $chronoStart->getTimestamp();
                    echo "Durée remplir creneau : " . $chronoInterval->format('%s secondes (%H:%I:%S)') . "<br>";
                    echo "Durée totale en secondes remplir creneau : $chronoSeconds secondes." . "<br>" . "<br>";
                }
            }

            // Appel de la fonction
            $datesCommunes = $assistantRecherche->getCreneauxCommunsExact($matrice, $_SESSION['nbUserSelectionné'], $debutHoraire, $finHoraire, $debut, $fin, $contactsPrioritaires, $aDesPriorites);

            // exit;
            if ($debugMode == true) {
                $chronoEndGen = new DateTime();
                $chronoInterval = $chronoStartGen->diff($chronoEndGen);
                $chronoSeconds = $chronoEndGen->getTimestamp() - $chronoStartGen->getTimestamp();
                echo "Durée totale algo : " . $chronoInterval->format('%s secondes (%H:%I:%S)') . "<br>";
                echo "Durée totale en secondes totale algo : $chronoSeconds secondes." . "<br>" . "<br>";
            }

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
        $pdo = $this->getPdo();
        $manager = new UtilisateurDAO($pdo);

        foreach ($creneaux as $date => $plagesHoraires) {
            foreach ($plagesHoraires as $plage => $participants) {

                // Convertir la date "DD-MM-YYYY" en "YYYY-MM-DD"
                $dateObj = DateTime::createFromFormat('d-m-Y', $date);
                $dateFormatted = $dateObj->format('Y-m-d'); // Format correct pour la suite

                // Récupérer les heures de début et de fin depuis la chaîne, par ex. "08:00 - 08:30"
                $parts = explode(' - ', $plage);
                list($debut, $fin) = $parts;

                // Créer les objets DateTime en utilisant le format attendu "Y-m-d H:i"
                $startObj = DateTime::createFromFormat('Y-m-d H:i', "$dateFormatted $debut");
                $endObj   = DateTime::createFromFormat('Y-m-d H:i', "$dateFormatted $fin");

                // Formater les dates pour FullCalendar en format ISO
                $start = $startObj->format('Y-m-d\TH:i:s');
                $end   = $endObj->format('Y-m-d\TH:i:s');

                // Récupérer les noms des participants
                foreach ($participants as $id => $dispo) {
                    $user = $manager->find($id);
                    $nom = $user->getNom();
                    $prenom = $user->getPrenom();
                    // Mettre une majuscule à la première lettre du prénom et du nom
                    $participants[$id] = ucfirst($prenom) . ' ' . ucfirst($nom);
                }

                // Ajouter le créneau aux événements
                $evenements[] = [
                    'start' => $start,
                    'end' => $end,
                    'participants' => $participants,
                ];
            }
        }

        $dateDebut = $evenements[0]['start'];

        // Créer les heures de début et de fin à partir des événements
        $heureDebut1 = new DateTime($dateDebut);
        $heureDebut1 = $heureDebut1->format('H:i');
        $heureFin1 = new DateTime($evenements[count($evenements) - 1]['end']);
        $heureFin1 = $heureFin1->format('H:i');

        $heureDebut2 = new DateTime($_SESSION['debutPlageH']);
        $heureFin2 = new DateTime($_SESSION['finPlageH']);
        $heureDebut2 = $heureDebut2->format('H:i');
        $heureFin2 = $heureFin2->format('H:i');

        // Comparer les heures et garder la plus petite pour le début et la plus grande pour la fin
        $heureDebut = ($heureDebut1 < $heureDebut2) ? $heureDebut1 : $heureDebut2;
        $heureFin = ($heureFin1 > $heureFin2) ? $heureFin1 : $heureFin2;

        $template = $this->getTwig()->load('resultat.html.twig');

        echo $template->render([
            'menu' => "recherche",
            'creneauxCommuns' => $creneaux,
            'nbrUtilisateursMin' => $nbrUtilisateursMin,
            'nombreUtilisateursSeclectionnes' => $nombreUtilisateursSeclectionnes,
            'evenements' => $evenements,
            'dateDebut' => $dateDebut,
            'heureDebut' => $heureDebut,
            'heureFin' => $heureFin,
            'page' => 3
        ]);
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
    public function afficherPersonnesObligatoires(?array $tabMessages = null, ?bool $contientErreurs = false): void
    {
        $managerUtilisateur = new UtilisateurDao($this->getPdo());
        $utilisateur = $managerUtilisateur->find($_SESSION['utilisateur']);
        $contacts = $utilisateur->getContact($utilisateur->getId());
        $groupes = $utilisateur->getGroupe($utilisateur->getId());

        // Récupérer les ids des membres des groupes
        $membres = [];
        $pdo = $this->getPdo();
        $manager = new GroupeDao($pdo);

        // Récupérer les membres de chaque groupe
        if (!empty($groupes)) {
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

    public function afficherParametres(?array $tabMessages = null, ?bool $contientErreurs = false): void
    {
        // Récupération des contacts sélectionnés
        if (isset($_POST['contacts'])) {
            $_SESSION['contacts'] = $_POST['contacts'];
        }
    
        // Récupération des contacts dans la table obligatoire
        if (isset($_POST['contactsObligatoires'])) {
            // Initialiser les tableaux pour stocker les contacts présents et obligatoires
            $_SESSION['contactsPresents'] = [];
            $_SESSION['contactsObligatoires'] = [];
            
            // Parcourir tous les contacts de la table obligatoire
            foreach ($_POST['contactsObligatoires'] as $contactId) {
                // Vérifier si le contact est marqué comme obligatoire ou présent
                $statusName = "contactStatus" . $contactId;
                
                if (isset($_POST[$statusName]) && $_POST[$statusName] === 'obligatoire') {
                    // Le contact est marqué comme obligatoire
                    $_SESSION['contactsObligatoires'][] = $contactId;
                } else {
                    // Par défaut, si non marqué comme obligatoire, on le considère comme présent
                    $_SESSION['contactsPresents'][] = $contactId;
                }
            }
        } else {
            // Si aucun contact n'est dans la table, réinitialiser les tableaux
            $_SESSION['contactsPresents'] = [];
            $_SESSION['contactsObligatoires'] = [];
        }
        
        // Conservation du code existant pour les contacts prioritaires
        if (isset($_POST['contactsPrioritaires'])) {
            unset($_SESSION['contactsPrioritaires']);
            $_SESSION['contactsPrioritaires'] = $_POST['contactsPrioritaires'];
        }
    
        // Afficher des informations de débogage si nécessaire
        // var_dump($_SESSION['contactsPresents']);
        // var_dump($_SESSION['contactsObligatoires']);
    
        $template = $this->getTwig()->load('recherche.html.twig');
        echo $template->render(array(
            'page' => 2,
            'message' => $tabMessages,
            'contientErreurs' => $contientErreurs,
            'contactsPresents' => $_SESSION['contactsPresents'] ?? [],
            'contactsObligatoires' => $_SESSION['contactsObligatoires'] ?? []
        ));
    }
}
