<?php

/**
 * @author Thibault Latxague
 * @describe Controller de la page des groupes
 * @version 0.2
 */

/**
 * Undocumented class
 */
class ControllerGroupes extends Controller
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
     * Fonction qui permet de lister les groupes dont l'utilisateur connecté est le chef
     * @return void
     */
    public function lister(): void {
        $tableauGroupes = $this->listerGroupesUtilisateur();
        //$nombrePersonnes = $this->getNombrePersonnes($tableauGroupes);

        $template = $this->getTwig()->load('groupes.html.twig'); // Generer la page de réinitialisation mdp avec tableau d'erreurs
        echo $template->render(array('groupes' => $tableauGroupes));
    }

    /**
     * Fonction qui renvoie la liste des groupes de l'utilisateur connecté (no object)
     * @return array|null tableau des groupes
     */
    public function listerGroupesUtilisateur(): ?array {
        $pdo = $this->getPdo();
        $manager = new GroupeDao($pdo);
        $tableauGroupes = $manager->getGroupeFromUserId($_SESSION['utilisateur']->getId());
        return $tableauGroupes;
    }

    /**
     * Fonction permettant de supprimer le groupe dont on récupère l'id du lien
     * @return void
     */
    public function supprimer(): void {
        $id = $_GET['id'];
        $pdo = $this->getPdo();
        $manager = new GroupeDao($pdo);
        $tableauGroupes = $manager->supprimerGroupe($id);
        $this->lister();
    }

    /**
     * Fonction dont le but est d'afficher la page de modification lorsque le bouton modifier d'un groupe est cliqué
     * @return void 
     */
    public function afficherPageModification(): void {
        $id = $_GET['id'];
        $manager = new GroupeDao($this->getPdo());
        $groupeCourant = $manager->find($id);
        $contacts = $this->getListeContacts();
        $template = $this->getTwig()->load('groupes.html.twig'); // Generer la page de réinitialisation mdp avec tableau d'erreurs
        echo $template->render(array('modification' => true, 'groupeCourant' => $groupeCourant, 'contacts' => $contacts));
    }

    /**
     * Fonction donc le but est de renvoyer la liste des contacts de l'utilisateur qui créé le groupe
     * @return array|null tableau des contacts
     */
    public function getListeContacts(): ?array {
        $pdo = $this->getPdo();
        $managerUtilisateur = new UtilisateurDao($pdo);
        $contactsId = $managerUtilisateur->findAllContact($_SESSION['utilisateur']->getId());  
        $contacts=array();     
        foreach ($contactsId as $contact) {
            $contacts[] = $managerUtilisateur->find($contact['idUtilisateur2']);
        }
        return $contacts;
    }
}