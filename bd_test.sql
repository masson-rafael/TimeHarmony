-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : sam. 19 oct. 2024 à 14:05
-- Version du serveur : 8.3.0
-- Version de PHP : 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : 'bd_test'
--

-- --------------------------------------------------------

--
-- Structure de la table 'utilisateur'
--

CREATE TABLE IF NOT EXISTS `utilisateur` (
    `id` int NOT NULL AUTO_INCREMENT,
    `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    `nom` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    `prenom` text COLLATE utf8mb4_unicode_ci,
    `pdp` varchar(255) NOT NULL,
    `estAdmin` boolean DEFAULT false NOT NULL,
    `mdp` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
-- Déchargement des données de la table 'article'
--

INSERT INTO `utilisateur` (`id`, `email`, `nom`, `prenom`, `pdp`, `estAdmin`, `mdp`) VALUES
(1, 'ff', 'ff', 'ff', 'ff', false, 'abcde'),
(2, 'ff', 'ff', 'ff', 'ff', true, 'fghij');


-- --------------------------------------------------------

--
-- Structure de la table 'categorie'
--

CREATE TABLE IF NOT EXISTS `categorie` (
    `id` int NOT NULL,
    `categorie` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table 'categorie'
--


-- --------------------------------------------------------

--
-- Structure de la table 'agenda'
--

CREATE TABLE IF NOT EXISTS `agenda` (
    `id` int NOT NULL AUTO_INCREMENT,
    `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `couleur` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `nom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `idUtilisateur` int NOT NULL,
    `idCategorie` int NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`idCategorie`) REFERENCES `categorie` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table 'agenda'
--

-- --------------------------------------------------------

--
-- Structure de la table 'creneauLibre'
--

CREATE TABLE IF NOT EXISTS `creneauLibre` (
    `id` int NOT NULL AUTO_INCREMENT,
    `dateCreneau` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `hDeb` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `hFin` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `idAgenda` int NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`idAgenda`) REFERENCES `agenda` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table 'creneauLibre'
--

-- --------------------------------------------------------

--
-- Structure de la table 'preferences'
--

CREATE TABLE IF NOT EXISTS `preferences` (
    `id` int NOT NULL AUTO_INCREMENT,
    `jour` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `heureDeb` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `heureFin` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `disponibilite` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `idUtilisateur` int NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table 'preferences'
--


-- --------------------------------------------------------

--
-- Structure de la table 'groupe'
--

CREATE TABLE IF NOT EXISTS `groupe` (
    `id` int NOT NULL AUTO_INCREMENT,
    `nom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `chef` int NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`chef`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table 'groupe'
--


-- --------------------------------------------------------

--
-- Structure de la table 'contacter'
--

CREATE TABLE IF NOT EXISTS `contacter` (
    `idUtilisateur1` int NOT NULL,
    `idUtilisateur2` int NOT NULL,
    PRIMARY KEY (`idUtilisateur1`, `idUtilisateur2`),
    FOREIGN KEY (`idUtilisateur1`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`idUtilisateur2`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table 'contacter'
--

-- --------------------------------------------------------

--
-- Structure de la table 'demander'
--

CREATE TABLE IF NOT EXISTS `demander` (
    `idUtilisateur1` int NOT NULL,
    `idUtilisateur2` int NOT NULL,
    PRIMARY KEY (`idUtilisateur1`, `idUtilisateur2`),
    FOREIGN KEY (`idUtilisateur1`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`idUtilisateur2`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table 'demander'
--

-- --------------------------------------------------------

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;