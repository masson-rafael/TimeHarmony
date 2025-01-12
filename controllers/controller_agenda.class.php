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
        $id = $_SESSION['utilisateur']->getId();
        $urlValide = utilitaire::validerURLAgenda($_POST['url'], $tableauErreurs);
        $couleurValide = utilitaire::validerCouleur($_POST['couleur'], $tableauErreurs);
        $nomValide = utilitaire::validerNom($_POST['nom'], $tableauErreurs);

        // Vérifier si tous les champs du formulaire sont remplis
        if ($urlValide && $couleurValide && $nomValide) {
            // Créer une instance de AgendaDao pour interagir avec la base de données
            $manager = new AgendaDao($pdo);

            if($manager->URLEstUnique($_POST['url'], $id)) {
                // Créer une nouvelle instance d'Agenda avec les données du formulaire
                $nouvelAgenda = new Agenda($_POST['url'], $_POST['couleur'], $_POST['nom'], $id);
                // Ajouter l'agenda dans la base de données
                $manager->ajouterAgenda($nouvelAgenda);

                // Retourner un message de succès
                $tableauErreurs[] = "Ajout réussi !";
                $this->lister($tableauErreurs);
            } else {
                // Si l'agenda existe déjà, afficher un message d'erreur
                $tableauErreurs[] = "Agenda avec cette URL existe déjà !";
                $this->lister($tableauErreurs);
            }
        } else {
            // Si le formulaire n'est pas correctement rempli, afficher la vue générique
            $this->lister($tableauErreurs);
        }
    }

    /**
     * Fonction permettant de lister les agendas
     * @return void
     */
    public function lister(?array $tabMessages = null): void {
        $manager = new AgendaDao($this->getPdo());
        $id = $_SESSION['utilisateur']->getId();
        $agendas = $manager->getAgendasUtilisateur($id);
        $template = $this->getTwig()->load('agenda.html.twig');

        //Affichage de la page
        echo $template->render(array(
            'agendas' => $agendas,
            'ajout' => false,
            'message' => $tabMessages,
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
            if($manager->URLEstUnique($_POST['url'], $id)) {
                $manager->modifierAgenda($id, $_POST['url'], $_POST['couleur'], $_POST['nom']);
                // Retourner un message de succès
                $tableauErreurs[] = "Modification réussie !";
                $this->lister($tableauErreurs);
            } else {
                $tableauErreurs[] = "Agenda avec cette URL existe déjà !";
                $this->lister($tableauErreurs); 
            }
        } else {
            // Si le formulaire n'est pas correctement rempli, afficher la vue générique
            $this->lister($tableauErreurs);
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
        $this->lister($messages);
    }
}