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
class ControllerCreneauRdv extends Controller
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
        $contacts = $this->recupererContact(1);
        $groupes = $this->recupererGroupe(1);
        //Génération de la vue
        $template = $this->getTwig()->load('recherche.html.twig');
        echo $template->render(array(
            'contacts' => $contacts,
            'groupes' => $groupes
        ));        

    }

    public function recupererContact($idUtilisateur): array {
        $pdo = $this->getPdo();

        $managerUtilisateur = new UtilisateurDao($pdo);
        $contactsId = $managerUtilisateur->findAllContact($idUtilisateur);  
        $contacts=array();     
        foreach ($contactsId as $contact) {
            $contacts[] = $managerUtilisateur->find($contact['idUtilisateur2']);
        }
        return $contacts;
    }

    public function recupererGroupe($idUtilisateur): array {
        $pdo = $this->getPdo();

        $managerGroupe = new GroupeDao($pdo);
        $groupesId = $managerGroupe->findAll($idUtilisateur);  
        $groupes=array();     
        foreach ($groupesId as $groupe) {
            $groupes[] = $managerGroupe->find($groupe['idGroupe']);
        }
        return $groupes;
    }
    
    public function obtenir(): void {
        $pdo = $this->getPdo();
        $idAgenda=0;
        // if (isset($_POST['urlIcs']) && !empty($_POST['urlIcs'])) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            extract($_POST, EXTR_OVERWRITE);

            var_dump($_POST['contacts']);
            $managerCreneau = new CreneauLibreDao($pdo);
            $managerCreneau->supprimerCreneauxLibres();

            
            $controllerAgenda = new ControllerAgenda($this->getTwig(),$this->getLoader());
            $agendas = array();
            foreach ($_POST['contacts'] as $id) {
                $managerAgenda = new AgendaDao();
                $tableau = $managerAgenda->findAllByIdUtilisateur($id,$pdo);
                $agendas = $managerAgenda->hydrateAll($tableau);
                // var_dump($agendas);

                echo $id . '<br>' ;
                // $idAgenda++;
                $controllerAgenda->obtenir($id, $debut, $fin);
               
            }
            
            
            
            $creneaux = $managerCreneau->findAllAssoc();
            // var_dump($creneaux);
            

            // Appeler la fonction pour trouver les dates communes
            $datesCommunes = $this->trouverDatesCommunes($creneaux);
        

            // var_dump($datesCommunes);
            
            // Générer vue
            $this->genererVueCreneaux($datesCommunes);
        }
        else {
            $this->genererVue();
        }


        // // Afficher les résultats
        // echo "Dates communes :\n";
        // foreach ($datesCommunes as $dateCommune) {
        //     echo "De : " . $dateCommune['debut'] . " à " . $dateCommune['fin'] . "<br>";
        // }        
    }

    // Fonction pour vérifier les chevauchements d'événements
    public function trouverDatesCommunes($creneaux): array {
        $datesCommunes = [];
    
        // Convertir chaque date en objet DateTime
        for ($i = 0; $i < count($creneaux); $i++) {
            for ($j = $i + 1; $j < count($creneaux); $j++) {
                $debut1 = new DateTime($creneaux[$i]['dateDebut']);
                $fin1 = new DateTime($creneaux[$i]['dateFin']);
                $debut2 = new DateTime($creneaux[$j]['dateDebut']);
                $fin2 = new DateTime($creneaux[$j]['dateFin']);
    
                // Vérifier s'il y a un chevauchement
                if ($debut1 < $fin2 && $debut2 < $fin1) {
                    // Calculer l'intersection des deux plages de dates
                    $chevauchementDebut = max($debut1, $debut2);
                    $chevauchementFin = min($fin1, $fin2);
    
                    // Ajouter l'intervalle d'intersection au tableau
                    $datesCommunes[] = [
                        'debut' => $chevauchementDebut->format('Y-m-d H:i:s'),
                        'fin' => $chevauchementFin->format('Y-m-d H:i:s'),
                    ];
                }
            }
        }
        return $datesCommunes;
    }

    public function genererVueCreneaux(Array $creneaux): void {

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