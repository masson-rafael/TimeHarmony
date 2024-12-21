<?php

/**
 * @author Rafael Masson
 * @describe Controller de la page des contacts
 * @version 0.2
 */

/**
 * Undocumented class
 */
class ControllerContacts extends Controller
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
     * Fonction appellee par lister() pour recuperer les contacts de l'utilisateur dont on donne l'id en parametre
     * @param int|null $idUtilisateur id de l'utilisateur dont on cherche les contacts
     * @return array tableau des contacts
     */
    function recupererContacts(?int $idUtilisateur): array {
        $pdo = $this->getPdo();

        $managerUtilisateur = new UtilisateurDao($pdo);
        $contactsId = $managerUtilisateur->findAllContact($idUtilisateur);  
        $contacts=array();     
        foreach ($contactsId as $contact) {
            $contacts[] = $managerUtilisateur->find($contact['idUtilisateur2']);
        }
        return $contacts;
    }

    /**
     * Procedure appellee a l'affichage de la page qui affiche tout les contacts de l'utilisateur
     *
     * @return void
     */
    function lister(): void {
        $contacts = $this->recupererContacts($_SESSION['utilisateur']->getId());
        //Génération de la vue
        $template = $this->getTwig()->load('contacts.html.twig');
        echo $template->render(array(
            'menu' => 'contacts',
            'contacts' => $contacts
        ));
    }

    /**
     * Procedure appellee par la corbeille pour supprimer un contact
     *
     * @return void
     */
    function supprimer(): void {
        // Récupération de l'id envoyé en parametre du lien
        $id1 = $_SESSION['utilisateur']->getId();
        $id2 = $_GET['id'];
        $pdo = $this->getPdo();
        $manager = new UtilisateurDao($pdo);
        $manager->supprimerContact($id1,$id2);

        $this->lister();
    }

    /**
     * Procedure appellee par le bouton ajouter un contact sur la page des contacts
     * 
     * @return void
     */
    function afficherUtilisateurs(): void {
        $pdo = $this->getPdo();
        $managerUtilisateur = new UtilisateurDao($pdo);
        $utilisateursPasContacts = $managerUtilisateur->recupererIdsUtilisateursPasContacts($_SESSION['utilisateur']->getId());
        $utilisateurs = $managerUtilisateur->hydrateAll($utilisateursPasContacts);
        //Génération de la vue
        $template = $this->getTwig()->load('contacts.html.twig');
        echo $template->render(array(
            'menu' => 'contacts',
            'utilisateurs' => $utilisateurs
        ));
    }

    /**
     * Procedure appellee par l'appui de "ajouter" dans la fenetre modale d'ajout un utilisateur en (demande de) contact
     *
     * @return void
     */
    function ajouter(): void {
        // Récupération de l'id envoyé en parametre du lien
        $id1 = $_SESSION['utilisateur']->getId();
        $id2 = $_GET['id'];
        $pdo = $this->getPdo();
        $manager = new UtilisateurDao($pdo);
        $manager->ajouterDemandeContact($id1,$id2);

        $this->lister();
    }

    /**
     * Fonction permettant d'afficher le twig correspondant à la page des notifications
     * @return void
     */
    public function afficherPageNotifications(): void {
        /**
         * Step 1 : Appel de la fonction qui trouve ET RENVOIE les contacts que j'ai envoyé
         * Step 2 : Appel de la fonction qui trouve ET RENVOIE les demandes de contact d'autres utilisateurs
         * Step 3 : Affichage du twig AVEC les 2 renvois
         */
        $mesDemandes = $this->getMesDemandesEnvoyees();
        $demandesRecues = $this->getMesDemandesRecues();

        $template = $this->getTwig()->load('notifications.html.twig');
        echo $template->render(array(
            'demandesEnvoyees' => $mesDemandes,
            'demandesRecues' => $demandesRecues
        ));
    }

    /**
     * Fonction dont le but est de renvoyer le tableau contenant la liste de mes demandes de contact
     * @return array|null $tabDemandes tableau des utilisateurs à qui j'ai envoyé une demande
     */
    public function getMesDemandesEnvoyees(): ?array {
        $tabDemandes = [];

        //Faux car création lors du DAO
        return $tabDemandes;
    }

    /**
     * Fonction dont le but est de renvoyer le tableau contenant la liste des utilisateurs qu'on m'ont demandé en contact
     * @return array|null $tabDemandesPourMoi tableau des utilisateurs qui m'ont demandés en contact
     */
    public function getMesDemandesRecues(): ?array {
        $tabDemandesPourMoi = [];

        //Faux car création lors du DAO
        return $tabDemandesPourMoi;
    }
}