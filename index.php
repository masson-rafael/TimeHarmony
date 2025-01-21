<?php

/**
 * @todo vérifier "echo $template->render(array('etat' => 'connecte',));"
 */

require_once 'include.php';

try  {
    if (isset($_GET['controleur'])){
        $controllerName=$_GET['controleur'];
    }else{
        $controllerName='';
    }

    if (isset($_GET['methode'])){
        $methode=$_GET['methode'];
    }else{
        $methode='';
    }

    //Gestion de la page d'accueil par défaut
    if ($controllerName == '' && $methode ==''){
        //$controllerName='index';
        //$methode='lister';
        $template = $twig->load('index.html.twig');
        echo $template->render(array());
    }
    else if ($controllerName == '' ){
        throw new Exception('Le controleur n\'est pas défini');
    }
    else if ($methode == '' ){
        throw new Exception('La méthode n\'est pas définie');
    }

    $controller = ControllerFactory::getController($controllerName, $loader, $twig);
    $controller->declencherBackup(ControllerFactory::getController("bd", $loader, $twig));
    $controller->call($methode);
    
}catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}