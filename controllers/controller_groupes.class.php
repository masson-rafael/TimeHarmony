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
}