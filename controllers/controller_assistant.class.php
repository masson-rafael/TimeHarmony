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


    public function genererVueRecherche(): void {
        // $pdo = $this->getPdo();
        unset($_SESSION['nbUserSelectionné']);
        // Récupération des contacts
        $utilisateur = $_SESSION['utilisateur'];

        $contacts = $utilisateur->getContact($utilisateur->getId());
        $groupes = $utilisateur->getGroupe($utilisateur->getId());

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
            $chronoStartGen = new DateTime();
            $managerCreneau = new CreneauLibreDao($pdo);
            $managerCreneau->supprimerCreneauxLibres();

            extract($_POST, EXTR_OVERWRITE);
            if (isset($_POST['debut']) && isset($_POST['fin']) && isset($_POST['dureeMin']) && isset($_POST['contacts'])) {
                // $_SESSION['dateDebPeriode'] = new DateTime($_POST['debut']);
                // $_SESSION['dateFinPeriode'] = new DateTime($_POST['fin']);
                $_SESSION['dureeMin'] = $_POST['dureeMin'];
                $_SESSION['contacts'] = $_POST['contacts'];
                $_SESSION['debut'] = $_POST['debut'];
                $_SESSION['fin'] = $_POST['fin'];
            }

            $dureeMin = $_SESSION['dureeMin'];
            $contacts = $_SESSION['contacts'];
            $debut = $_SESSION['debut'];
            $fin = $_SESSION['fin'];

            $managerUtilisateur = new UtilisateurDAO($pdo);
            $tableauUtilisateur = [];

            foreach ($contacts as $idUtilisateurCourant) {
                $tableauUtilisateur[] = $managerUtilisateur->find($idUtilisateurCourant);
            }
            $tableauUtilisateur[] = $_SESSION['utilisateur'];

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

            $chronoStart = new DateTime();
            // Génération des dates pour la période
            $dates = $assistantRecherche->genererDates($debut, $fin);
            // $chronoEnd = new DateTime();
            // $chronoInterval = $chronoStart->diff($chronoEnd);
            // $chronoSeconds = $chronoEnd->getTimestamp() - $chronoStart->getTimestamp();
            // echo "Durée genererDates : " . $chronoInterval->format('%s secondes (%H:%I:%S)') . "<br>";
            // echo "Durée totale en secondes genererDates : $chronoSeconds secondes." . "<br>" . "<br>";
            
            // $chronoStart = new DateTime();

            // Initialisation de la matrice
            $matrice = $assistantRecherche->initMatrice($tableauUtilisateur, $dates,$dureeMin);
            // $chronoEnd = new DateTime();
            // $chronoInterval = $chronoStart->diff($chronoEnd);
            // $chronoSeconds = $chronoEnd->getTimestamp() - $chronoStart->getTimestamp();
            // echo "Durée initMatrine : " . $chronoInterval->format('%s secondes (%H:%I:%S)') . "<br>";
            // echo "Durée totale en secondes initMatrice : $chronoSeconds secondes." . "<br>" . "<br>";

            // var_dump($matrice);
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
                $creneauxByUtilisateur = $agenda->rechercheCreneauxLibres($mergedEvents, $debut, $fin, $pdo);

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
            $datesCommunes = $assistantRecherche->getCreneauxCommunsExact($matrice, $_SESSION['nbUserSelectionné']);

            // $chronoEndGen = new DateTime();
            // $chronoInterval = $chronoStartGen->diff($chronoEndGen);
            // $chronoSeconds = $chronoEndGen->getTimestamp() - $chronoStartGen->getTimestamp();
            // echo "Durée totale algo : " . $chronoInterval->format('%s secondes (%H:%I:%S)') . "<br>";
            // echo "Durée totale en secondes totale algo : $chronoSeconds secondes." . "<br>" . "<br>";

            // Générer la vue avec les données structurées
            $this->genererVueCreneaux($datesCommunes);
        } else {
            $this->genererVue();
        }
    }

    /**
     * Fonction qui permet de générer la vue qui contiendra les résultats de la recherche
     * @param array|null $creneaux les creneaux libres communs trouvés grace a la recherche
     * @return void
     */
    public function genererVueCreneaux(?array $creneaux): void {
        $template = $this->getTwig()->load('resultat.html.twig');
        echo $template->render([
            'creneauxCommuns' => $creneaux
        ]);
    }

    /**
     * Fonction permettant de générer la vue par defaut de l'application
     * @return void
     */
    public function genererVue(): void {
        $template = $this->getTwig()->load('index.html.twig');
        echo $template->render(array());
    }
}

