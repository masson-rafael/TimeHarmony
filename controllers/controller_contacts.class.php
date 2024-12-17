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
     * Fonction appellee par lister() pour recuperer les contacts de l'utilisateur
     * 
     * @return array tableau des contacts
     */
    function recupererContacts($idUtilisateur): array {
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
     * Fonction appellee a l'affichage de la page qui affiche tout les contacts de l'utilisateur
     *
     * @return void
     */
    function lister() {
        $contacts = $this->recupererContacts($_SESSION['utilisateur']->getId());
        //Génération de la vue
        $template = $this->getTwig()->load('contacts.html.twig');
        echo $template->render(array(
            'menu' => 'contacts',
            'contacts' => $contacts
        ));
    }

    /**
     * Fonction appellee par la corbeille pour supprimer un contact
     *
     * @return void
     */
    function supprimer() {
        // Récupération de l'id envoyé en parametre du lien
        $id1 = $_SESSION['utilisateur']->getId();
        $id2 = $_GET['id'];
        $pdo = $this->getPdo();
        $manager = new UtilisateurDao($pdo);
        $manager->supprimerContact($id1,$id2);

        $this->lister();
    }

    /**
     * Fonction appellee par le bouton ajouter un contact sur la page des contacts
     * 
     * @return void
     */
    function afficherUtilisateurs(){
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
     * Fonction appellee par l'appui de "ajouter" dans la fenetre modale d'ajout un utilisateur en contact
     *
     * @return void
     */
    function ajouter() {
        // Récupération de l'id envoyé en parametre du lien
        $id1 = $_SESSION['utilisateur']->getId();
        $id2 = $_GET['id'];
        $pdo = $this->getPdo();
        $manager = new UtilisateurDao($pdo);
        $manager->ajouterContact($id1,$id2);

        $this->lister();
    }
}