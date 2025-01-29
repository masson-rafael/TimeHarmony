<?php

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
        $template = $twig->load('index.html.twig');
        echo $template->render(array());
    }
    else if ($controllerName == '' ){
        throw new Exception('Le controleur n\'est pas défini');
    }
    else if ($methode == '' ){
        throw new Exception('La méthode n\'est pas définie');
    } else {
        $controller = ControllerFactory::getController($controllerName, $loader, $twig);
        $controller->call($methode);
        $controllerBackup = ControllerFactory::getController("bd", $loader, $twig);
        $controller->declencherBackup($controllerBackup);
    }
}catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}