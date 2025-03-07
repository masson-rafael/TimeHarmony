<?php

/**
 * @author Félix Autant
 * @brief Controller de la page des agendas
 * @todo Verifier que le undocumented class soit pas à remplir. S'il existe même
 * @version 0.1
 */

/**
 * Undocumented class
 */
class ControllerAgenda extends Controller
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
     * Generer la vue de la page creneauxLibres sans resultats
     *
     * @return void
     */
    public function ajouterAgenda(): void
    {
        $pdo = $this->getPdo(); // Récupérer l'instance PDO

        $tableauErreurs = [];
        $id = $_SESSION['utilisateur'];
        $urlValide = utilitaire::validerURLAgenda($_POST['url'], $tableauErreurs);
        $couleurValide = utilitaire::validerCouleur($_POST['couleur'], $tableauErreurs);
        $nomValide = utilitaire::validerNom($_POST['nom'], $tableauErreurs);

        // Vérifier si tous les champs du formulaire sont remplis
        if ($urlValide && $couleurValide && $nomValide) {
            // Créer une instance de AgendaDao pour interagir avec la base de données
            $manager = new AgendaDao($pdo);
            // var_dump($_SESSION['utilisateur']->getId());
            // var_dump($id);

            if($manager->URLEstUnique($_POST['url'], $id, $_SESSION['utilisateur'])) {
                // Créer une nouvelle instance d'Agenda avec les données du formulaire
                $nouvelAgenda = new Agenda($_POST['url'], $_POST['couleur'], $_POST['nom'], $id);
                // Ajouter l'agenda dans la base de données
                $manager->ajouterAgenda($nouvelAgenda);

                // Retourner un message de succès
                $tableauErreurs[] = "Ajout réussi !";
                $this->lister($tableauErreurs, false);
            } else {
                // Si l'agenda existe déjà, afficher un message d'erreur
                $tableauErreurs[] = "Agenda avec cette URL existe déjà !";
                $this->lister($tableauErreurs, true);
            }
        } else {
            // Si le formulaire n'est pas correctement rempli, afficher la vue générique
            $this->lister($tableauErreurs, true);
        }
    }

    /**
     * Fonction permettant de lister les agendas
     * @param array|null $tabMessages Tableau des messages
     * @param bool|null $contientErreurs true si le tableau contient des erreurs, false sinon
     * @return void
     */
    public function lister(?array $tabMessages = null, ?bool $contientErreurs = false): void {
        $manager = new AgendaDao($this->getPdo());
        $id = $_SESSION['utilisateur'];
        $agendas = $manager->getAgendasUtilisateur($id);
        $template = $this->getTwig()->load('agenda.html.twig');

        //Affichage de la page
        echo $template->render(array(
            'menu' => "agenda",
            'agendas' => $agendas,
            'ajout' => false,
            'message' => $tabMessages,
            'contientErreurs' => $contientErreurs,
        ));
    }

    /**
     * Fonction permmettant de générer la vue d'une page spécifiée en param
     * @param string $page Nom de la page à générer
     * @return void
     */
    public function genererVue(string $page): void {
        //Génération de la vue
        $template = $this->getTwig()->load($page.'.html.twig');
        echo $template->render(array());
    }

    /**
     * Fonction permettant de générer la vue des agendas
     * @param array|null $erreurs Tableau des erreurs
     * @return void
     */
    public function genererVueAgenda(?array $erreurs): void {
        //Génération de la vue agenda
        $template = $this->getTwig()->load('agenda.html.twig');
        echo $template->render(
            array(
                'menu' => "agenda",
                'message' => $erreurs,
                'ajout' => false,
            )
        );
    }

    /**
     * Fonction permettant de générer la vue d'ajout d'un agenda
     * @return void
     */
    public function genererVueAjoutAgenda(): void {
        //Génération de la vue agenda
        $template = $this->getTwig()->load('agenda.html.twig');
        echo $template->render(
            array(
                'menu' => "agenda",
                'ajout' => true,
            )
        );
    }

    /**
     * Fonction permettant de modifier un agenda
     * @return void
     */
    public function modifierAgenda(): void {
        $id = $_GET['id'];

        $pdo = $this->getPdo(); // Récupérer l'instance PDO

        $tableauErreurs = [];
        $urlValide = utilitaire::validerURLAgenda($_POST['url'], $tableauErreurs);
        $couleurValide = utilitaire::validerCouleur($_POST['couleur'], $tableauErreurs);
        $nomValide = utilitaire::validerNomAgenda($_POST['nom'], $tableauErreurs);

        // Vérifier si tous les champs du formulaire sont remplis
        if ($urlValide && $couleurValide && $nomValide) {
            // Créer une instance de AgendaDao pour interagir avec la base de données
            $manager = new AgendaDao($pdo);
            // Ajouter l'agenda dans la base de données
            if($manager->URLEstUnique($_POST['url'], $id, $_SESSION['utilisateur'])) {
                $manager->modifierAgenda($id, $_POST['url'], $_POST['couleur'], $_POST['nom']);
                // Retourner un message de succès
                $tableauErreurs[] = "Modification réussie !";
                $this->lister($tableauErreurs, false);
            } else {
                $tableauErreurs[] = "Agenda avec cette URL existe déjà !";
                $this->lister($tableauErreurs, true); 
            }
        } else {
            // Si le formulaire n'est pas correctement rempli, afficher la vue générique
            $this->lister($tableauErreurs, true);
        }
    }

    /**
     * Fonction permettant de supprimer un agenda
     * 
     * @return void
     */
    public function supprimerAgenda(): void {
        $id = $_GET['id'];
        $manager = new AgendaDao($this->getPdo());
        $manager->supprimerAgenda($id);
        $messages[] = "Suppression réussie !";
        $this->lister($messages, false);
    }
}