<?php

/**
 * @author Rafael Masson
 * @describe Controller de la page des contacts
 * @version 0.1
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

    function lister() {
        $contacts = $this->recupererContacts($_SESSION['utilisateur']->getId());
        //Génération de la vue
        $template = $this->getTwig()->load('contacts.html.twig');
        echo $template->render(array(
            'menu' => 'contacts',
            'contacts' => $contacts
        ));
    }
}