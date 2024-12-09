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

        $datesCommunes = array();

        $pdo = $this->getPdo();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            //Mettre dans agendaDAO ou la classe agenda
            $managerCreneau = new CreneauLibreDao($pdo);
            $managerCreneau->supprimerCreneauxLibres();

            extract($_POST, EXTR_OVERWRITE);
            $dateDebPeriode = new DateTime($_POST['debut']);
            $dateFinPeriode = new DateTime($_POST['fin']);
            $dureeMin = $_POST['dureeMin'];

            $managerUtilisateur = new UtilisateurDAO($pdo);

            foreach($_POST['contacts'] as $idUtilisateurCourant) {
                
                $tableauUtilisateur[] = $managerUtilisateur->find($idUtilisateurCourant);
            }

            $tailleTabUser = count($tableauUtilisateur);

            // Lire le fichier JSON
            $jsonFile = 'combinaisons.json'; // Chemin du fichier
            $jsonContent = file_get_contents($jsonFile);

            if ($jsonContent === false) {
                die("Erreur lors de la lecture du fichier JSON");
            }

            // Décoder le JSON en tableau associatif
            $data = json_decode($jsonContent, true); // true pour tableau associatif

            if (json_last_error() !== JSON_ERROR_NONE) {
                die("Erreur de décodage JSON : " . json_last_error_msg());
            }
            
            // Parcourir les combinaisons et afficher les valeurs pour la clé spécifique
            foreach ($data['combinaisons'] as $combinaison) {
                if (array_key_exists($tailleTabUser, $combinaison)) {
                    // Trier les valeurs en ordre décroissant
                    $valeurs = $combinaison[$tailleTabUser];
                    rsort($valeurs);
                    $codesBinaire = afficherValeursAvecTroisUn($valeurs,3,$tailleTabUser);
                }
            }

            
            foreach ($codesBinaire as $codeBinaire) {
                $creneauxByUtilisateur = array();
                // var_dump($codeBinaire);

                // Appeler la fonction pour obtenir les utilisateurs sélectionnés
                $utilisateursSelectionnes = selectionnerUtilisateurs($tableauUtilisateur, $codeBinaire);

                // Afficher les utilisateurs sélectionnés
                // foreach ($utilisateursSelectionnes as $utilisateur) {
                //     echo "Utilisateur sélectionné : " . $utilisateur->getNom() . " " . $utilisateur->getPrenom() . "\n <br>";
                // }
                // echo "<br>";

                // Pour chaque utilisateur, recherche des créneaux libres par agendas
                $assistantRecherche = new Assistant($dateDebPeriode,$dateFinPeriode, $utilisateursSelectionnes);
                foreach ($assistantRecherche->getUtilisateurs() as $utilisateurCourant) {
                    
                    // Voir avec le prof car la methode getAgendas est différentes des getters classiques
                    $utilisateur = new Utilisateur($utilisateurCourant->getId());

                    $agendas = $utilisateur->getAgendas();

                    $allEvents = [];

                    foreach ($agendas as $agenda) {  
                        $urlIcs = $agenda->getUrl();
                    
                        $allEvents = $agenda->recuperationEvenementsAgenda($urlIcs,$debut,$fin,$allEvents);
                    }
                    
                    $mergedEvents = $agenda->mergeAgendas($allEvents);
                    
                    $creneauxByUtilisateur[] = $agenda->rechercheCreneauxLibres($mergedEvents,$_POST['debut'],$_POST['fin'],$pdo);

                }

                // Appeler la fonction pour trouver les dates communes
                $datesCommunes[] = $assistantRecherche->trouverDatesCommunes($creneauxByUtilisateur,$dureeMin);

            }

            // var_dump($datesCommunes);
            // Générer vue
            $this->genererVueCreneaux($datesCommunes);
        }
        else {
            $this->genererVue();
        }     
    }

    public function genererVueCreneaux(?Array $creneaux): void {

        //Génération de la vue
        $template = $this->getTwig()->load('resultat.html.twig');
        // var_dump($creneaux);
        echo $template->render(array(
            'creneauxRDV' => $creneaux
        ));
    }

    public function genererVue(): void {

        //Génération de la vue
        $template = $this->getTwig()->load('index.html.twig');
        echo $template->render(array());
    }
}


function afficherValeursAvecTroisUn($valeurs, $nbDe1, $nbUtilisateurs): array {
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


