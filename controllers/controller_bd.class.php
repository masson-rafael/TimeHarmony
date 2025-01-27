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
class ControllerBd extends Controller
{
    /**
     * Constructeur par défaut
     *
     * @param \Twig\Environment $twig Environnement twig
     * @param \Twig\Loader\FilesystemLoader $loader Loader de fichier
     */
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader) {
        parent::__construct($twig, $loader);
    }

    public function sauvegarder(): void {
        $db = Bd::getInstance();
        $db->backupTotale();
    }

    public function restaurer(): void {
        $db = Bd::getInstance();
        $db->restoreDatabase();
    }
}