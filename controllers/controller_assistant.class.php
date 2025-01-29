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

        $utilisateur = $_SESSION['utilisateur'];

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
            $_SESSION['debutHoraire'] = $_POST['debutHoraire'];
            $_SESSION['finHoraire'] = $_POST['finHoraire'];
        }

        $valideDuree = Utilitaire::validerDuree($_POST['debut'], $_POST['fin'], $messagesErreur);
        $dureeMinValide = Utilitaire::validerDureeMinimale($_POST['dureeMin'], $messagesErreur);
        @$contactsValide = Utilitaire::validerContacts($_POST['contacts'], $messagesErreur);
        $plageHoraireValide= Utilitaire::validerPlageHoraire($_POST['debutHoraire'],$_POST['finHoraire'], $messagesErreur);


        if ($valideDuree && $dureeMinValide && $contactsValide && $plageHoraireValide) {
            if (!isset($_SESSION['debut']) || !isset($_SESSION['fin']) || !isset($_SESSION['dureeMin']) || !isset($_SESSION['contacts']) || !isset($_SESSION['debutHoraire']) || !isset($_SESSION['finHoraire'])) {
                $_SESSION['debut'] = $_POST['debut'];
                $_SESSION['fin'] = $_POST['fin'];
                $_SESSION['dureeMin'] = $_POST['dureeMin'];
                $_SESSION['contacts'] = $_POST['contacts'];
                $_SESSION['debutHoraire'] = $_POST['debutHoraire'];
                $_SESSION['finHoraire'] = $_POST['finHoraire'];
            }

            // $chronoStartGen = new DateTime();
            $managerCreneau = new CreneauLibreDao($pdo);
            $managerCreneau->supprimerCreneauxLibres();

            extract($_POST, EXTR_OVERWRITE);
            if (isset($_POST['debut']) && isset($_POST['fin']) && isset($_POST['dureeMin']) && isset($_POST['contacts']) && isset($_POST['debutHoraire']) && isset($_POST['finHoraire'])) {
                $_SESSION['dureeMin'] = $_POST['dureeMin'];
                $_SESSION['contacts'] = $_POST['contacts'];
                $_SESSION['debut'] = $_POST['debut'];
                $_SESSION['fin'] = $_POST['fin'];
                $_SESSION['debutHoraire'] = $_POST['debutHoraire'];
                $_SESSION['finHoraire'] = $_POST['finHoraire'];
                // $_SESSION['nbUserSelectionné'] = sizeof($_POST['contacts']);
            }

            $dureeMin = $_SESSION['dureeMin'];
            $contacts = $_SESSION['contacts'];
            $debut = $_SESSION['debut'];
            $fin = $_SESSION['fin'];
            $debutHoraire = $_SESSION['debutHoraire'];
            $finHoraire = $_SESSION['finHoraire'];

            $managerUtilisateur = new UtilisateurDAO($pdo);
            $tableauUtilisateur = [];

            foreach ($contacts as $idUtilisateurCourant) {
                $tableauUtilisateur[] = $managerUtilisateur->find($idUtilisateurCourant);
            }
            $tableauUtilisateur[] = $_SESSION['utilisateur'];

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
                    // var_dump($urlIcs);
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
            $datesCommunes = $assistantRecherche->getCreneauxCommunsExact($matrice, $_SESSION['nbUserSelectionné'],$debutHoraire,$finHoraire,$debut,$fin);
            // $chronoEndGen = new DateTime();
            // $chronoInterval = $chronoStartGen->diff($chronoEndGen);
            // $chronoSeconds = $chronoEndGen->getTimestamp() - $chronoStartGen->getTimestamp();
            // echo "Durée totale algo : " . $chronoInterval->format('%s secondes (%H:%I:%S)') . "<br>";
            // echo "Durée totale en secondes totale algo : $chronoSeconds secondes." . "<br>" . "<br>";

            // Générer la vue avec les données structurées
            $tailleContacts = sizeof($tableauUtilisateur);
            $nombreUtilisateursSeclectionnes = $_SESSION['nbUserSelectionné'];
            $nbrUtilisateursMin = ceil($tailleContacts / 2);
            $this->genererVueCreneaux($datesCommunes, $nbrUtilisateursMin,$nombreUtilisateursSeclectionnes );
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
        $template = $this->getTwig()->load('resultat.html.twig');
        echo $template->render([
            'menu' => "recherche",
            'creneauxCommuns' => $creneaux,
            'nbrUtilisateursMin' => $nbrUtilisateursMin,
            'nombreUtilisateursSeclectionnes' => $nombreUtilisateursSeclectionnes
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
}
