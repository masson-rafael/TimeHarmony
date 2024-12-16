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

    public function obtenir(): void {
    $datesCommunes = [];

    $pdo = $this->getPdo();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $managerCreneau = new CreneauLibreDao($pdo);
        $managerCreneau->supprimerCreneauxLibres();

        extract($_POST, EXTR_OVERWRITE);
        if (isset($_POST['debut']) && isset($_POST['fin']) && isset($_POST['dureeMin']) && isset($_POST['contacts'])){
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
        $debut =  $_SESSION['debut'];
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

        // Lire et décoder le fichier JSON
        $jsonFile = 'combinaisons.json';
        $jsonContent = file_get_contents($jsonFile);
        $data = json_decode($jsonContent, true);

        // Récupérer les codes binaires associés aux valeurs du tableau de combinaison 
        foreach ($data['combinaisons'] as $combinaison) {
            if (array_key_exists($tailleTabUser, $combinaison)) {
                $valeurs = $combinaison[$tailleTabUser];
                rsort($valeurs);
                $codesBinaire = recupererValeursDeCombinaison($valeurs, $_SESSION['nbUserSelectionné'], $tailleTabUser);
            }
        }

        // $nbUtilisateursEnBinaire = str_repeat('1', $tailleTabUser);

        // //Sélectionner les utilisateurs en fonction d'un code binaire (par exemple : 1101 -> utilisateur 1, 2 et 4 sélectionné)
        // $utilisateursSelectionnes = selectionnerUtilisateurs($tableauUtilisateur, $nbUtilisateursEnBinaire);
        // var_dump($utilisateursSelectionnes);

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
                $creneauxByUtilisateur[] = $agenda->rechercheCreneauxLibres($mergedEvents, $debut, $fin, $pdo);
            }
        }
        // $creneauxByUtilisateur = [];
        // var_dump($creneauxByUtilisateur);

        
        
        // var_dump($creneauxByUtilisateur);
        // Recherche
        foreach ($codesBinaire as $codeBinaire) {
            
            // Convertir le code binaire en tableau d'indices actifs
            $activeKeys = [];
            for ($i = 0; $i < strlen($codeBinaire); $i++) {
                if ($codeBinaire[$i] === '1') {
                    $activeKeys[] = $i;
                }
            }

            // var_dump($activeKeys);

            // Récupérer les données associées aux clés actives
            $creneauxUtilisateurs = [];
            foreach ($activeKeys as $key) {
                // var_dump($key);
                if (isset($creneauxByUtilisateur[$key])) {
                    // var_dump($creneauxByUtilisateur[$key]);
                    $associatedData[$key] = $creneauxByUtilisateur[$key];
                }
            }

            // var_dump($associatedData);

            //Sélectionner les utilisateurs en fonction d'un code binaire (par exemple : 1101 -> utilisateur 1, 2 et 4 sélectionné)
            $utilisateursSelectionnes = selectionnerUtilisateurs($tableauUtilisateur, $codeBinaire);

            // $assistantRecherche = new Assistant($dateDebPeriode, $dateFinPeriode, $utilisateursSelectionnes);
            // foreach ($assistantRecherche->getUtilisateurs() as $utilisateurCourant) {
            //     $utilisateur = new Utilisateur($utilisateurCourant->getId());
            //     $agendas = $utilisateur->getAgendas();
            //     $allEvents = [];

            //     foreach ($agendas as $agenda) {
            //         $urlIcs = $agenda->getUrl();
            //         $allEvents = $agenda->recuperationEvenementsAgenda($urlIcs, $debut, $fin, $allEvents);
            //     }

            //     $mergedEvents = $agenda->mergeAgendas($allEvents);
            //     $creneauxByUtilisateur[] = $agenda->rechercheCreneauxLibres($mergedEvents, $debut, $fin, $pdo);
            //     // var_dump($creneauxByUtilisateur);
            // }

            $datesTrouvees = $assistantRecherche->trouverDatesCommunes($associatedData, $dureeMin);
            
            foreach ($datesTrouvees as $date) {
                $nombreUtilisateursConcernes = count($utilisateursSelectionnes);
                $pourcentageParticipation = ($nombreUtilisateursConcernes / $tailleTabUser) * 100;
            
                $datesCommunes[] = [
                    'date' => $date, // Date commune
                    'utilisateurs' => array_map(function ($utilisateur) {
                        return [
                            'id' => $utilisateur->getId(),
                            'nom' => $utilisateur->getNom(),
                            'prenom' => $utilisateur->getPrenom()
                        ];
                    }, $utilisateursSelectionnes), // Liste des utilisateurs concernés
                    'pourcentage' => round($pourcentageParticipation, 2) // Arrondi à 2 décimales
                ];
            }
        }
        //  var_dump($datesCommunes);
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


    public function genererVue(): void {

        //Génération de la vue
        $template = $this->getTwig()->load('index.html.twig');
        echo $template->render(array());
    }
}


function recupererValeursDeCombinaison($valeurs, $nbDe1, $nbUtilisateurs): array {
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
function selectionnerUtilisateurs($utilisateurs, $codeBinaire) {
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


