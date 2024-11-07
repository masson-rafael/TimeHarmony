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
        // $controllerName='index';
        // $methode='lister';
        $template = $twig->load('index.html.twig');
        echo $template->render(array('etat' => 'nonconnecte',));
        //echo $template->render(array('etat' => 'connecte',));
        // a terme, il faudra verifier si on est connecte avec la bd
    }
    else if ($controllerName == '' ){
        throw new Exception('Le controleur n\'est pas défini');
    }
    else if ($methode == '' ){
        throw new Exception('La méthode n\'est pas définie');
    }
    else {
        $controller = ControllerFactory::getController($controllerName, $loader, $twig);
    
        $controller->call($methode);
    }
    
}catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}



// $pdo = Bd::getInstance()->getConnexion();

// //recupération des catégories
// $managerCategorie = new CategorieDao($pdo);
// $tableau = $managerCategorie->findAllAssoc();
// $categories = $managerCategorie->hydrateAll($tableau);

// //Choix du template
// $template = $twig->load('index.html.twig');


// //Affichage de la page
// echo $template->render(array(
//     'categories' => $categories,
//     'menu' => 'categories',
//     // 'description' => "Le site des recettes de cuisine de l'IUT de Bayonne"
// ));
//-----------------------------------------------------------------------------------------------------------------------------------//

// echo $template->render(array(
//     'menu' => 'index'
// ));

//$creneauxLibres = obtenir($urlIcs, $debut, $fin);

// // Afficher les créneaux libres
// foreach ($creneauxLibres as $creneau) {
//     echo "Créneau libre de : " . $creneau['debut'] . " à " . $creneau['fin'] . "\n <br> <br>";
// }
