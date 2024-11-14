<?php
/**
 * @author Félix Autant
 * @describe controller factory
 * @version 0.1
 */

class ControllerFactory
{
    /**
     * get le controller
     *
     * @param string|null $controleur nom du controleur 
     * @param \Twig\Loader\FilesystemLoader $loader 
     * @param \Twig\Environment $twig 
     * @return void
     */
    public static function getController($controleur, \Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig)
    {
        $controllerName="Controller".ucfirst($controleur);
        if (!class_exists($controllerName)) {
            throw new Exception("Le controleur $controllerName n'existe pas");
        }
        return new $controllerName($twig, $loader);
    }
}