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
        $pdo = $this->getPdo();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            //Mettre dans agendaDAO ou la classe agenda
            $managerCreneau = new CreneauLibreDao($pdo);
            $managerCreneau->supprimerCreneauxLibres();

            extract($_POST, EXTR_OVERWRITE);

            $managerUtilisateur = new UtilisateurDAO($pdo);
            // On cree un objet utilisateur par id a la suite de l'envoi du formulaire
            foreach($_POST['contacts'] as $idUtilisateurCourant) {
                
                $tableauUtilisateur[] = $managerUtilisateur->find($idUtilisateurCourant);
            }

            $creneaux = array();
            // Pour chaque utilisateur, recherche des créneaux libres par agendas
            $assistantRecherche = new Assistant(new DateTime($_POST['debut']),new DateTime($_POST['fin']), $tableauUtilisateur);
            foreach ($assistantRecherche->getUtilisateurs() as $utilisateurCourant) {
                

                // Voir avec le prof car la methode getAgendas est différentes des getters classiques
                $utilisateur = new Utilisateur($utilisateurCourant->getId());
                $agendas = $utilisateur->getAgendas();

                $allEvents = [];

                foreach ($agendas as $agenda) {  

                    $urlIcs = $agenda->getUrl();
                    // var_dump($urlIcs);
                    $idAgenda = $agenda->getId();
                    

                    try {
                        $ical = new ICal($urlIcs);
                        $events = $ical->eventsFromRange($_POST['debut'],$_POST['fin']);
                        
                        // var_dump($events);
                        // Récupérer les événements (sans plage => tout)
                        $allEvents = array_merge($allEvents, $events);
                    } catch (\Exception $e) {
                        echo "Erreur de lecture de l'agenda : {$e->getMessage()}\n";
                    }

                    // $agenda->rechercheCreneauxLibres($idAgenda,$urlIcs,$_POST['debut'],$_POST['fin'],$pdo);
                }
               
                // var_dump($allEvents);

                // var_dump($evenementsFusionnes);

                // $agenda->rechercheCreneauxLibres($idAgenda,$urlIcs,$_POST['debut'],$_POST['fin'],$pdo);
                $creneauxByUtilisateur[] = $agenda->rechercheCreneauxLibres($allEvents,$_POST['debut'],$_POST['fin'],$pdo);

                var_dump($creneauxByUtilisateur);

                // $creneauxByUtilisateur = $managerCreneau->findAllByIdUtilisateur($utilisateur->getId());
                // $creneaux[] = $assistantRecherche->combinerCreneaux($creneauxByUtilisateur);

                // Afficher les résultats
                // echo "Créneaux sans chevauchement :\n";
                // foreach ($creneaux as $interval) {
                //     echo $interval['start'] . " - " . $interval['end'] . "\n <br>";
                // }
                // var_dump($creneaux);
            }
            // $creneaux = $managerCreneau->findAllAssoc();
            // var_dump($creneauxByUtilisateur);
            // Appeler la fonction pour trouver les dates communes
            $datesCommunes = $assistantRecherche->trouverDatesCommunes($creneauxByUtilisateur);
            
            
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