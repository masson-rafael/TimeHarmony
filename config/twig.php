<?php

require_once 'modeles/utilisateur.class.php';
require_once 'modeles/utilisateur.dao.php';
require_once 'modeles/bd.class.php';

//ajout de la classe IntlExtension et creation de l’alias IntlExtension
use Twig\Extra\Intl\IntlExtension;

//initialisation twig : chargement du dossier contenant les templates
$loader = new Twig\Loader\FilesystemLoader('templates');

//Paramétrage de l'environnement twig
$twig = new Twig\Environment($loader, [
    /*passe en mode debug à enlever en environnement de prod : permet d'utiliser dans un templates {{dump
    (variable)}} pour afficher le contenu d'une variable. Nécessite l'utilisation de l'extension debug*/
    'debug' => true,
]);

// Dans votre fichier d'initialisation (par exemple index.php ou bootstrap.php)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['utilisateur']) && ! empty($_SESSION['utilisateur'])) {

        $db = Bd::getInstance();
        $pdo = $db->getConnexion();
        $managerUtilisateur = new UtilisateurDao($pdo);
        $utilisateur = $managerUtilisateur->find($_SESSION['utilisateur']);
    
        $utilisateurCourant = new Utilisateur(
        $utilisateur->getId(),
        $utilisateur->getNom(),
        $utilisateur->getPrenom(),
        $utilisateur->getEmail(),
        null,
        $utilisateur->getPhotoDeProfil(),
        $utilisateur->getEstAdmin(),
        $utilisateur->getDateDerniereConnexion());

    $twig->addGlobal('utilisateurGlobal', value: $utilisateurCourant);
} else {
    $twig->addGlobal('utilisateurGlobal', null);
}

//Définition de la timezone pour que les filtres date tiennent compte du fuseau horaire français.
$twig->getExtension(\Twig\Extension\CoreExtension::class)->setTimezone('Europe/Paris');

//Ajouter l'extension debug
$twig->addExtension(new \Twig\Extension\DebugExtension());

//Ajout de l'extension d'internationalisation qui permet d'utiliser les filtres de date dans twig
$twig->addExtension(new IntlExtension());