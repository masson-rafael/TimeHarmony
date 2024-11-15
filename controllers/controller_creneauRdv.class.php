<?php


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


    function genererVueRecherche() {
        $contacts = $this->recupererContact(1);
        $groupes = $this->recupererGroupe(1);
        //Génération de la vue
        $template = $this->getTwig()->load('recherche.html.twig');
        echo $template->render(array(
            'contacts' => $contacts,
            'groupes' => $groupes
        ));        

    }

    function recupererContact($idUtilisateur): array {
        $pdo = $this->getPdo();

        $managerUtilisateur = new UtilisateurDao($pdo);
        $contactsId = $managerUtilisateur->findAllContact($idUtilisateur);  
        $contacts=array();     
        foreach ($contactsId as $contact) {
            $contacts[] = $managerUtilisateur->find($contact['idUtilisateur2']);
        }
        return $contacts;
    }

    function recupererGroupe($idUtilisateur): array {
        $pdo = $this->getPdo();

        $managerGroupe = new GroupeDao($pdo);
        $groupesId = $managerGroupe->findAll($idUtilisateur);  
        $groupes=array();     
        foreach ($groupesId as $groupe) {
            $groupes[] = $managerGroupe->find($groupe['idGroupe']);
        }
        return $groupes;
    }
    
    public function obtenir() {
        $pdo = $this->getPdo();
        $manager = new CreneauLibreDao($pdo);
        $creneaux = $manager->findAllAssoc();
        // $controllerAgenda = new ControllerAgenda($this->getTwig(),$this->getLoader());
        $tableau = array();

        foreach ($creneaux as $creneau) {
            // echo $creneau['dateDebut'] . '<br>';
            

        }
        var_dump($creneaux);
        $intervals = [
            ['start' => '2024-11-15 08:00:00', 'end' => '2024-11-15 10:00:00'],
            ['start' => '2024-11-15 09:00:00', 'end' => '2024-11-15 12:00:00'],
            ['start' => '2024-11-15 07:30:00', 'end' => '2024-11-15 09:30:00'],
        ];
        
        $commonStart = max(array_map(fn($interval) => strtotime($interval['start']), $intervals));
        $commonEnd = min(array_map(fn($interval) => strtotime($interval['end']), $intervals));
        
        if ($commonStart < $commonEnd) {
            echo "Intervalle commun : " . date('Y-m-d H:i:s', $commonStart) . " à " . date('Y-m-d H:i:s', $commonEnd);
        } else {
            echo "Aucun intervalle commun trouvé.";
        }


    }
}