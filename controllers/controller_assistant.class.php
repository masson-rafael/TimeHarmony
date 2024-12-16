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

            //--------------------------------------CREATION DES MATRICES ------------------------------------------------------------------------//

            // Définir les datetimes de début et de fin
            // $dateDebut = new DateTime("2024-10-21 00:00:00");
            // $dateFin = new DateTime("2024-10-27 18:00:00");

            // // Création des créneaux horaires basés sur les heures de $dateDebut et $dateFin
            // $horaires = [];
            // $heureDebut = clone $dateDebut;
            // $heureFin = clone $dateFin;
            // $intervalHoraire = new DateInterval('PT5M'); // Intervalles de 5 minutes
            // $dureeCreneau = new DateInterval('PT30M'); // Durée de chaque créneau

            // while ($heureDebut < $heureFin) {
            //     $debutCreneau = $heureDebut->format('H:i');
            //     $finCreneau = $heureDebut->add($dureeCreneau)->format('H:i');
            //     $horaires[] = "$debutCreneau - $finCreneau";
            //     $heureDebut->sub($dureeCreneau)->add($intervalHoraire); // Revenir au début et avancer de 5 minutes
            // }

            // // Initialisation de la matrice pour chaque jour
            // $matrices = [];

            // $interval = new DateInterval('P1D');
            // $period = new DatePeriod(new DateTime($dateDebut->format('Y-m-d 00:00:00')), $interval, new DateTime($dateFin->format('Y-m-d 00:00:00')));

            // foreach ($period as $date) {
            //     $jour = $date->format('Y-m-d');
            //     $matriceJour = [];
            //     foreach ($horaires as $horaire) {
            //         $ligne = [];
            //         foreach ($tableauUtilisateur as $utilisateur) {
            //             $ligne[$utilisateur->getNom()] = 0; // Valeur par défaut
            //         }
            //         $matriceJour[$horaire] = $ligne;
            //     }
            //     $matrices[$jour] = $matriceJour;
            // }

            // var_dump($matrices);

            //------------------------------------------REMPLISSAGE DES MATRICES-------------------------------------------------------------------

            if (empty($creneauxByUtilisateur)) {
                $assistantRecherche = new Assistant($dateDebPeriode, $dateFinPeriode, $tableauUtilisateur);
                foreach ($assistantRecherche->getUtilisateurs() as $utilisateurCourant) {
                    $utilisateur = new Utilisateur($utilisateurCourant->getId());
                    $agendas = $utilisateur->getAgendas();
                    $allEvents = [];

                    foreach ($agendas as $agenda) {
                        $urlIcs = $agenda->getUrl();
                        $allEvents = $agenda->recuperationEvenementsAgenda($urlIcs, $debut, $fin, $allEvents);
                    }

                    $mergedEvents = $agenda->mergeAgendas($allEvents);
                    $creneauxByUtilisateur = $agenda->rechercheCreneauxLibres($mergedEvents, $debut, $fin, $pdo);
                    // var_dump($creneauxByUtilisateur);
                }
            }

            //-------------------------------------------------------------------------------------------------------------------



            // Liste des personnes
            $personnes = ['masson', 'keita', 'autant', 'etcheverry'];

            // Fonction pour initialiser la matrice


            // Génération des dates pour la période
            $dates = genererDates(new DateTime('2024-10-21'), new DateTime('2024-10-22'));

            // Initialisation de la matrice
            $matrice = initMatrice($personnes, $dates);

            // Définir un créneau pour l'utilisateur
            $datetime_debut = new DateTime('2024-10-21 09:00');  // Début du créneau
            $datetime_fin = new DateTime('2024-10-21 10:30');   // Fin du créneau
            $personne = 'masson';  // Utilisateur à remplir

            // Remplir la matrice avec ce créneau
            remplirCreneau($matrice, $datetime_debut, $datetime_fin, $personne);

            // Affichage pour vérification
            // echo "<pre>";
            // print_r($matrice);
            // echo "</pre>";

            var_dump($matrice);



            //---------------------------------------------------------------------------------------------------------------------------------
            // //Sélectionner les utilisateurs en fonction d'un code binaire (par exemple : 1101 -> utilisateur 1, 2 et 4 sélectionné)
            // $utilisateursSelectionnes = selectionnerUtilisateurs($tableauUtilisateur, $codeBinaire);

            //     $datesTrouvees = $assistantRecherche->trouverDatesCommunes($associatedData, $dureeMin);

            //     foreach ($datesTrouvees as $date) {
            //         $nombreUtilisateursConcernes = count($utilisateursSelectionnes);
            //         $pourcentageParticipation = ($nombreUtilisateursConcernes / $tailleTabUser) * 100;

            //         $datesCommunes[] = [
            //             'date' => $date, // Date commune
            //             'utilisateurs' => array_map(function ($utilisateur) {
            //                 return [
            //                     'id' => $utilisateur->getId(),
            //                     'nom' => $utilisateur->getNom(),
            //                     'prenom' => $utilisateur->getPrenom()
            //                 ];
            //             }, $utilisateursSelectionnes), // Liste des utilisateurs concernés
            //             'pourcentage' => round($pourcentageParticipation, 2) // Arrondi à 2 décimales
            //         ];
            //     }
            // //  var_dump($datesCommunes);
            // // Générer la vue avec les données structurées
            // $this->genererVueCreneaux($datesCommunes);
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


function recupererValeursDeCombinaison($valeurs, $nbDe1, $nbUtilisateurs): array
{
    $tabDeBinaire = array();
    foreach ($valeurs as $valeur) {
        // Convertir la valeur en binaire
        $binaire = decbin($valeur);

        // Ajouter des zéros à gauche pour avoir une longueur de 4 bits
        $binaire = str_pad($binaire, $nbUtilisateurs, '0', STR_PAD_LEFT);

        // Compter le nombre de '1' dans la représentation binaire
        $nbUn = substr_count($binaire, '1');

        // Si le nombre de '1' est exactement 3, ajouter à la liste
        if ($nbUn == $nbDe1) {
            $tabDeBinaire[] = $binaire;
        }
    }
    return $tabDeBinaire;
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


function initMatrice($personnes, $dates)
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
            $matrice[$date][$key] = array_fill_keys($personnes, 0);
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
function remplirCreneau(&$matrice, $datetime_debut, $datetime_fin, $personne)
{
    $debut_date = $datetime_debut->format('Y-m-d');
    $fin_date = $datetime_fin->format('Y-m-d');
    $start_time = $datetime_debut->format('H:i');
    $end_time = $datetime_fin->format('H:i');

    foreach ($matrice as $date => $creneaux) {
        foreach ($creneaux as $interval => $values) {
            list($start, $end) = explode(' - ', $interval);
            if (
                ($date == $debut_date && $start >= $start_time) &&
                ($date == $fin_date && $end <= $end_time)
            ) {
                $matrice[$date][$interval][$personne] = 1; // Marque la disponibilité
            }
        }
    }
}