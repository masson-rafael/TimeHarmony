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
-- Base de données : 'timeharmony'
--

-- --------------------------------------------------------

--
-- Suppression de toutes les tables
--

DROP TABLE IF EXISTS `timeharmony_contacter`;
DROP TABLE IF EXISTS `timeharmony_demander`;
DROP TABLE IF EXISTS `timeharmony_composer`;
DROP TABLE IF EXISTS `timeharmony_ajouter`;
DROP TABLE IF EXISTS `timeharmony_groupe`;
DROP TABLE IF EXISTS `timeharmony_preferences`;
DROP TABLE IF EXISTS `timeharmony_creneauLibre`;
DROP TABLE IF EXISTS `timeharmony_agenda`;
DROP TABLE IF EXISTS `timeharmony_categorie`;
DROP TABLE IF EXISTS `timeharmony_utilisateur`;

--
-- Structure de la table 'utilisateur'
--

CREATE TABLE IF NOT EXISTS `timeharmony_utilisateur` (
    `id` int NOT NULL AUTO_INCREMENT,
    `nom` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
    `prenom` text COLLATE utf8mb4_general_ci,
    `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
    `motDePasse` VARCHAR(255) NOT NULL,
    `photoDeProfil` varchar(255) NOT NULL,
    `estAdmin` boolean DEFAULT false NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


--
-- Déchargement des données de la table 'article'
--

-- --------------------------------------------------------

--
-- Structure de la table 'categorie'
--

CREATE TABLE IF NOT EXISTS `timeharmony_categorie` (
    `id` int NOT NULL,
    `nom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table 'categorie'
--

-- --------------------------------------------------------

--
-- Structure de la table 'agenda'
--

CREATE TABLE IF NOT EXISTS `timeharmony_agenda` (
    `id` int NOT NULL AUTO_INCREMENT,
    `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    `couleur` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    `nom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    `idUtilisateur` int NOT NULL,
    `idCategorie` int NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`idUtilisateur`) REFERENCES `timeharmony_utilisateur` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`idCategorie`) REFERENCES `timeharmony_categorie` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table 'agenda'
--

-- --------------------------------------------------------

--
-- Structure de la table 'creneauLibre'
--

CREATE TABLE IF NOT EXISTS `timeharmony_creneauLibre` (
    `id` int NOT NULL AUTO_INCREMENT,
    `dateDebut` DATETIME NOT NULL,
    `dateFin` DATETIME NOT NULL,
    `idAgenda` int NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`idAgenda`) REFERENCES `timeharmony_agenda` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


--
-- Déchargement des données de la table 'creneauLibre'
--

-- --------------------------------------------------------

--
-- Structure de la table 'preferences'
--

CREATE TABLE IF NOT EXISTS `timeharmony_preferences` (
    `id` int NOT NULL AUTO_INCREMENT,
    `dateDebut` DATETIME NOT NULL,
    `dateFin` DATETIME NOT NULL,
    `disponibilite` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    `idUtilisateur` int NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`idUtilisateur`) REFERENCES `timeharmony_utilisateur` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table 'preferences'
--


-- --------------------------------------------------------

--
-- Structure de la table 'groupe'
--

CREATE TABLE IF NOT EXISTS `timeharmony_groupe` (
    `id` int NOT NULL AUTO_INCREMENT,
    `nom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    `idChef` int NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table 'groupe'
--

-- --------------------------------------------------------

--
-- Structure de la table 'composer'
--

CREATE TABLE IF NOT EXISTS `timeharmony_composer` (
    `idGroupe` int NOT NULL,
    `idUtilisateur` int NOT NULL,
    PRIMARY KEY (`idGroupe`,`idUtilisateur`),
    FOREIGN KEY (`idGroupe`) REFERENCES `timeharmony_groupe` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`idUtilisateur`) REFERENCES `timeharmony_utilisateur` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table 'composer'
--

-- --------------------------------------------------------

--
-- Structure de la table 'ajouter'
--

CREATE TABLE IF NOT EXISTS `timeharmony_ajouter` (
    `idGroupe` int NOT NULL,
    `idUtilisateur` int NOT NULL,
    PRIMARY KEY (`idGroupe`,`idUtilisateur`),
    FOREIGN KEY (`idGroupe`) REFERENCES `timeharmony_groupe` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`idUtilisateur`) REFERENCES `timeharmony_utilisateur` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table 'ajouter'
--

-- --------------------------------------------------------

--
-- Structure de la table 'contacter'
--

CREATE TABLE IF NOT EXISTS `timeharmony_contacter` (
    `idUtilisateur1` int NOT NULL,
    `idUtilisateur2` int NOT NULL,
    PRIMARY KEY (`idUtilisateur1`, `idUtilisateur2`),
    FOREIGN KEY (`idUtilisateur1`) REFERENCES `timeharmony_utilisateur` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`idUtilisateur2`) REFERENCES `timeharmony_utilisateur` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table 'contacter'
--

-- --------------------------------------------------------

--
-- Structure de la table 'demander'
--

CREATE TABLE IF NOT EXISTS `timeharmony_demander` (
    `idUtilisateur1` int NOT NULL,
    `idUtilisateur2` int NOT NULL,
    PRIMARY KEY (`idUtilisateur1`, `idUtilisateur2`),
    FOREIGN KEY (`idUtilisateur1`) REFERENCES `timeharmony_utilisateur` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`idUtilisateur2`) REFERENCES `timeharmony_utilisateur` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table 'demander'
--

-- --------------------------------------------------------

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- --------------------------------------------------------

--
-- Ajout de données si besoin
--

INSERT INTO `timeharmony_utilisateur` (`nom`, `prenom`, `email`, `motDePasse`, `photoDeProfil`, `estAdmin`) VALUES
 ('latxague', 'thibault', 'tlaxtague@iutbayonne.univ-pau.fr', 'admin', 'photo.jpg', true),
 ('masson', 'rafael', 'rmasson003@iutbayonne.univ-pau.fr', 'nimad', 'photo.jpg', false);