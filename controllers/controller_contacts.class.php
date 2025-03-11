<?php

/**
 * @author Rafael Masson
 * @brief Controller de la page des contacts
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
        $contacts = $managerUtilisateur->findAllContact($idUtilisateur);  
        return $contacts;
    }

    /**
     * Procedure appellee a l'affichage de la page qui affiche tout les contacts de l'utilisateur
     *
     * @param array|null $tableauMessages tableau des messages à afficher
     * @param bool|null $contientErreurs true si le tableau contient des erreurs, false sinon
     * @return void
     */
    function lister(?array $tableauMessages = null, ?bool $contientErreurs = false): void {
        $contacts = $this->recupererContacts($_SESSION['utilisateur']);
        //Génération de la vue
        $template = $this->getTwig()->load('contacts.html.twig');
        echo $template->render(array(
            'menu' => 'contacts',
            'contacts' => $contacts,
            'message' => $tableauMessages,
            'contientErreurs' => $contientErreurs
        ));
    }

    /**
     * Procedure appellee par la corbeille pour supprimer un contact
     *
     * @return void
     */
    function supprimer(): void {
        // Récupération de l'id envoyé en parametre du lien
        $id1 = $_SESSION['utilisateur'];
        $id2 = $_GET['id'];
        $pdo = $this->getPdo();
        $manager = new UtilisateurDao($pdo);
        $userSupprime = $manager->find($id2);
        $manager->supprimerContact($id1,$id2);
        $tableauMessages[] = "Contact " . $userSupprime->getNom() . " " . $userSupprime->getPrenom() . " supprimé avec succès !";
        $this->lister($tableauMessages, false);
    }

    /**
     * Procedure appellee par le bouton ajouter un contact sur la page des contacts
     * 
     * @return void
     */
    function afficherUtilisateurs(?array $tableauMessages = null, ?bool $contientErreurs = false): void {
        $pdo = $this->getPdo();
        $managerUtilisateur = new UtilisateurDao($pdo);
        $utilisateursPasContacts = $managerUtilisateur->recupererIdsUtilisateursPasContacts($_SESSION['utilisateur']);
        $utilisateurs = $managerUtilisateur->hydrateAll($utilisateursPasContacts);
        //Génération de la vue
        $template = $this->getTwig()->load('contacts.html.twig');
        echo $template->render(array(
            'menu' => 'contacts',
            'utilisateurs' => $utilisateurs,
            'message' => $tableauMessages,
            'contientErreurs' => $contientErreurs
        ));
    }

    /**
     * Procedure appellee par l'appui de "ajouter" dans la fenetre modale d'ajout un utilisateur en (demande de) contact
     *
     * @return void
     */
    function ajouter(): void {
        // Récupération de l'id envoyé en parametre du lien
        $id1 = $_SESSION['utilisateur'];
        $id2 = $_GET['id'];
        $pdo = $this->getPdo();
        $manager = new UtilisateurDao($pdo);
        $manager->ajouterDemandeContact($id1,$id2);
        $utilisateurDemandeEnContact = $manager->find($id2);
        $tableauMessages[] = "Contact " . $utilisateurDemandeEnContact->getNom() . " " . $utilisateurDemandeEnContact->getPrenom() . " ajouté avec succès !";
        $this->afficherUtilisateurs($tableauMessages, false);
    }
}