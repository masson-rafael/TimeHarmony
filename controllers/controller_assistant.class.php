<?php
use ICal\ICal;


/**
 * @author Félix Autant
 * @describe Controller de la page de recherche de créneaux libres
 * @todo Verifier que le undocumented class soit pas à remplir. S'il existe même
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


    public function genererVueRecherche(): void
    {
        $pdo = $this->getPdo();
        unset($_SESSION['nbUserSelectionné']);
        // Récupération des contacts
        $utilisateur = new Utilisateur(1);

        $contacts = $utilisateur->getContact($pdo, $utilisateur->getId());

        $groupes = $utilisateur->getGroupe($pdo, $utilisateur->getId());

        //Génération de la vue
        $template = $this->getTwig()->load('recherche.html.twig');
        echo $template->render(array(
            'contacts' => $contacts,
            'groupes' => $groupes
        ));

    }

    public function obtenir(): void
    {
        $datesCommunes = [];

        $pdo = $this->getPdo();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $managerCreneau = new CreneauLibreDao($pdo);
            $managerCreneau->supprimerCreneauxLibres();

            extract($_POST, EXTR_OVERWRITE);
            if (isset($_POST['debut']) && isset($_POST['fin']) && isset($_POST['dureeMin']) && isset($_POST['contacts'])) {
                $_SESSION['dateDebPeriode'] = new DateTime($_POST['debut']);
                $_SESSION['dateFinPeriode'] = new DateTime($_POST['fin']);
                $_SESSION['dureeMin'] = $_POST['dureeMin'];
                $_SESSION['contacts'] = $_POST['contacts'];

                //A supprimer
                $_SESSION['debut'] = $_POST['debut'];
                $_SESSION['fin'] = $_POST['fin'];
            }

            $dateDebPeriode = $_SESSION['dateDebPeriode'];
            $dateFinPeriode = $_SESSION['dateFinPeriode'];
            $dureeMin = $_SESSION['dureeMin'];
            $contacts = $_SESSION['contacts'];

            //A supprimer
            $debut = $_SESSION['debut'];
            $fin = $_SESSION['fin'];



            // var_dump($dateDebPeriode);


            $managerUtilisateur = new UtilisateurDAO($pdo);
            $tableauUtilisateur = [];

            foreach ($contacts as $idUtilisateurCourant) {
                $tableauUtilisateur[] = $managerUtilisateur->find($idUtilisateurCourant);
            }

            // var_dump($tableauUtilisateur);

            $tailleTabUser = count($tableauUtilisateur);

            // Initialisez la session pour stocker la variable
            // session_start();
            if (!isset($_SESSION['nbUserSelectionné'])) {
                $_SESSION['nbUserSelectionné'] = $tailleTabUser; // Valeur initiale
            }

            // Gérer les actions des boutons
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['increment'])) {
                    $_SESSION['nbUserSelectionné']++;
                } elseif (isset($_POST['decrement'])) {
                    $_SESSION['nbUserSelectionné']--;
                }
            }

            var_dump($_SESSION['nbUserSelectionné']);

            $utilisateurs = $tableauUtilisateur;

            // Génération des dates pour la période
            $dates = genererDates($dateDebPeriode, $dateFinPeriode);

            // Initialisation de la matrice
            $matrice = initMatrice($utilisateurs, $dates);

            //------------------------------------------REMPLISSAGE DES MATRICES-------------------------------------------------------------------            

            // if (empty($creneauxByUtilisateur)) {
            $assistantRecherche = new Assistant($dateDebPeriode, $dateFinPeriode, $tableauUtilisateur);
            foreach ($assistantRecherche->getUtilisateurs() as $utilisateurCourant) {
                $utilisateur = new Utilisateur($utilisateurCourant->getId(), $utilisateurCourant->getNom());

                $agendas = $utilisateur->getAgendas();
                $allEvents = [];

                foreach ($agendas as $agenda) {
                    $urlIcs = $agenda->getUrl();
                    // var_dump($urlIcs);
                    $allEvents = $agenda->recuperationEvenementsAgenda($urlIcs, $debut, $fin, $allEvents);
                }

                $mergedEvents = $agenda->mergeAgendas($allEvents);
                $creneauxByUtilisateur = $agenda->rechercheCreneauxLibres($mergedEvents, $debut, $fin, $pdo);

                foreach ($creneauxByUtilisateur as $key => $creneau) {
                    $dateDebut = $creneau->getDateDebut()->format('Y-m-d H:i:s');
                    $datetime_debut = new DateTime($dateDebut);  // Début du créneau
                    $dateFin = $creneau->getDateFin()->format('Y-m-d H:i:s');
                    $datetime_fin = new DateTime($dateFin);  // Début du créneau

                    remplirCreneau($matrice, $datetime_debut, $datetime_fin, $utilisateurCourant);
                }
            }


            // Appel de la fonction
            $nbUtilisateursMinimum = 3; // Modifier pour changer le critère
            $datesCommunes = getCreneauxCommunsExact($matrice, $nbUtilisateursMinimum);

            var_dump($datesCommunes);


            // Générer la vue avec les données structurées
            $this->genererVueCreneaux($datesCommunes);
        } else {
            $this->genererVue();
        }
    }

    public function genererVueCreneaux(?array $creneaux): void
    {
        $template = $this->getTwig()->load('resultat.html.twig');
        echo $template->render([
            'creneauxRDV' => $creneaux
        ]);
    }


    public function genererVue(): void
    {

        //Génération de la vue
        $template = $this->getTwig()->load('index.html.twig');
        echo $template->render(array());
    }
}


// Fonction pour sélectionner les utilisateurs en fonction du code binaire
function selectionnerUtilisateurs($utilisateurs, $codeBinaire)
{
    $utilisateursSelectionnes = [];

    // Parcours du code binaire
    for ($i = 0; $i < strlen($codeBinaire); $i++) {
        // Si le bit est '1', on sélectionne l'utilisateur correspondant
        if ($codeBinaire[$i] == '1') {
            $utilisateursSelectionnes[] = $utilisateurs[$i];
        }
    }

    return $utilisateursSelectionnes;
}


// Fonction pour initialiser la matrice
function initMatrice($utilisateurs, $dates)
{
    $matrice = [];
    foreach ($dates as $date) {
        $matrice[$date] = [];
        $time = new DateTime("$date 00:00");
        for ($i = 0; $i < 288; $i++) { // 288 créneaux de 5 minutes sur une journée
            $start = $time->format('H:i');
            $end = $time->add(new DateInterval('PT30M'))->format('H:i');
            $time->sub(new DateInterval('PT25M')); // Décalage de 5 minutes pour le prochain créneau
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
function getCreneauxCommunsExact(array $matrice, int $nb_utilisateurs_exact): array {
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