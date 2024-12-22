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
}