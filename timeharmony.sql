-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : sam. 19 oct. 2024 à 14:05
-- Modifier par : Rafael Masson
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

DROP TABLE IF EXISTS `timeharmony_trouver`;
DROP TABLE IF EXISTS `timeharmony_composer`;
DROP TABLE IF EXISTS `timeharmony_demander`;
DROP TABLE IF EXISTS `timeharmony_contacter`;
DROP TABLE IF EXISTS `timeharmony_preference`;
DROP TABLE IF EXISTS `timeharmony_creneauLibre`;
DROP TABLE IF EXISTS `timeharmony_agenda`;
DROP TABLE IF EXISTS `timeharmony_groupe`;
DROP TABLE IF EXISTS `timeharmony_utilisateur`;
DROP TABLE IF EXISTS `timeharmony_creneauRdv`;
DROP TABLE IF EXISTS `timeharmony_disponibilite`;


--
-- Structure de la table 'disponibilite'
--

CREATE TABLE IF NOT EXISTS `timeharmony_disponibilite` (
    `id` int NOT NULL AUTO_INCREMENT,
    `nom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Chargement des données de la table 'disponibilite'
--

INSERT INTO `timeharmony_disponibilite` (`nom`) VALUES
('Disponible'), ('Si nécessaire'), ('Indisponible');

--
-- Déchargement des données de la table 'disponibilite'
--

-- --------------------------------------------------------

--
-- Structure de la table 'creneauRdv'
--

CREATE TABLE IF NOT EXISTS `timeharmony_creneauRdv` (
    `id` int NOT NULL AUTO_INCREMENT,
    `dateDebut` DATETIME NOT NULL,
    `dateFin` DATETIME NOT NULL,
    `pourcentageCoincidence` int NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Chargement des données de la table 'creneauRdv'
--

INSERT INTO `timeharmony_creneauRdv` (`dateDebut`, `dateFin`, `pourcentageCoincidence`) VALUES
 ('2024-10-07 00:00:00','2024-10-07 08:15:00', 100),
 ('2024-10-07 14:30:00','2024-10-07 16:30:00', 92),
 ('2024-10-08 08:00:00','2024-10-09 10:45:00', 78),
 ('2024-10-09 15:00:00','2024-10-10 15:30:00', 60),
 ('2024-10-10 19:00:00','2024-10-12 00:00:00', 51);

--
-- Déchargement des données de la table 'creneauRdv'
--

-- --------------------------------------------------------

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
    `tentativesEchouees` int DEFAULT 0 NOT NULL,
    `dateDernierEchecConnexion` DATETIME DEFAULT NULL,
    `statutCompte` varchar(255) DEFAULT 'desactive' NOT NULL,
    `token` varchar(100) DEFAULT NULL,
    `dateExpirationToken` DATETIME DEFAULT NULL,
    `estAdmin` boolean DEFAULT false NOT NULL,
    `tokenActivationCompte` varchar(100) DEFAULT NULL,
    `dateExpirationTokenActivationCompte` DATETIME DEFAULT NULL,
    `dateDerniereConnexion` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Chargement des données de la table 'utilisateur'
--

INSERT INTO `timeharmony_utilisateur` (`nom`, `prenom`, `email`, `motDePasse`, `photoDeProfil`, `estAdmin`, `statutCompte`) VALUES
 ('latxague', 'thibault', 'tlatxague@iutbayonne.univ-pau.fr', '$2y$10$qvncYojGHH/dKVUuOn.rH.v5CgT4pjjAPWzN/Pa467mUelIxAPs5i', 'thibault.jpg', true, 'actif'),
 ('masson', 'rafael', 'rmasson003@iutbayonne.univ-pau.fr', '$2y$10$xbOlwSyP7aYhW14EtImaUuOo4pBjJ2Z4RoQSTmlG9/Kt0JAvW./h.', 'raf.jpg', true, 'actif'),
 ('keita', 'mouhamadou', 'mkmouhamadou@iutbayonne.univ-pau.fr', '$2y$10$Ez.7guQv7jseSeGwzXBWJe3qTVig.U9NR2PvkdnZwUjuAG82RY.Ru', 'mkm.png', true, 'actif'),
 ('autant', 'félix', 'fautant@iutbayonne.univ-pau.fr', '$2y$10$.qzWFHVUE3cpUVy3TAs1yO/.QVNp.gjKqwoKSgV0kpnUvwTsSW1r2', 'felix.jpg', true, 'actif'),
 ('etcheverry', 'patrick', 'patrick.etcheverry@iutbayonne.univ-pau.fr', '$2y$10$mBTdsf6nYV6ZAYm7wuD4p.lyH1xlxHCBFZxFQrjp9MQTCWPmndupi', 'petch.jpg', false, 'actif'),
 ('moulin', 'antoine', 'antoine.moulin@iutbayonne.univ-pau.fr', '$2y$10$zkyNPYF7XebIXxNH4QW6veoVeeGWUbWhRcVKRlQF0UG9cvbVp/Shu', 'antoine.jpg', false, 'actif'),
 ('pantxika', 'dagorret', 'pantxika.dagorret@iutbayonne.univ-pau.fr', '$2y$10$F8u3lSyuhU4mugsBJVIgmeccowZupq5dgQl5SaPhjJ60a/l8VtmGq', 'pantxika.jpg', false, 'actif'),
 ('sophie', 'voisin', 'sophie.voisin@iutbayonne.univ-pau.fr', '$2y$10$EbNnJgh848jUnqrqeVHKx.vchALW7ZHdUPFIgiRLwsg45AfOA.50O', 'sophie.jpg', false, 'actif'),
 ('christophe', 'marquesuzaa', 'christophe.marquesuzaa@iutbayonne.univ-pau.fr', '$2y$10$DqHEdsa3Y1cJ4KC2IccV3eImTTx8k622vnI1KnYPkVg3UndA2YgZm', 'christophe.png', false, 'actif'),
 ('olivier', 'dezeque', 'olivier.dezeque@iutbayonne.univ-pau.fr', '$2y$10$Wx.XZ34n6xF93ToBnzfdiOb8rXxIPgVUcq5n4fxpP2jA3zF/I/8Ma', 'olivier.jpg', false, 'desactive'),
 ('chiara', 'rustici', 'chiara.rustici@iutbayonne.univ-pau.fr', '$2y$10$INfusoSi/dQSmXxy5RtQhuPN.UyKuESik1QO.i8gOZpg8MGtO3bS2', 'chiara.jpg', false, 'desactive'),
 ('ugo', 'bergos', 'ubergos@iutbayonne.univ-pau.fr', '$2y$10$hDI16y1VKBCUjPM58D5uQuVLRB6NldjWmAumJXxGwvwgbgdZvq5FG', 'ugo.png', true, 'actif'),
 ('manon', 'klein-pol', 'mkpol@iutbayonne.univ-pau.fr', '$2y$10$O4pjDJhpb0zBriz2NZuNpOoqNT5DYyHByZI8tVG53Gx1jGpOSMR/e', 'manon.jpg', true, 'actif');


--
-- Déchargement des données de la table 'utilisateur'
--

-- --------------------------------------------------------

--
-- Structure de la table 'groupe'
--

CREATE TABLE IF NOT EXISTS `timeharmony_groupe` (
    `id` int NOT NULL AUTO_INCREMENT,
    `nom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    `idProprietaire` int NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`idProprietaire`) REFERENCES `timeharmony_utilisateur` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Chargement des données de la table 'groupe'
--

INSERT INTO `timeharmony_groupe` (`nom`, `description`, `idProprietaire`) VALUES
 ('Réunion avec Monsieur Etcheverry', 'Groupe de l\'équipe TimeHarmony avec notre tuteur, Monsieur Etcheverry', 1),
 ('Equipe TimeHarmony', 'Groupe de l\'équipe TimeHarmony', 2),
 ('Groupe Test 1', 'Groupe de l\'équipe TimeHarmony test 1', 1),
 ('Groupe Test 2', 'Groupe de l\'équipe TimeHarmony test 2', 1),
 ('Groupe Test 3', 'Groupe de l\'équipe TimeHarmony test 3', 1),
 ('Groupe Test 4', 'Groupe de l\'équipe TimeHarmony test 4', 1);

--
-- Déchargement des données de la table 'groupe'
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
    PRIMARY KEY (`id`),
    FOREIGN KEY (`idUtilisateur`) REFERENCES `timeharmony_utilisateur` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Chargement des données de la table 'agenda'
--

INSERT INTO `timeharmony_agenda` (`url`, `couleur`, `nom`, `idUtilisateur`) VALUES
 ('https://calendar.google.com/calendar/ical/thibault.latxague%40gmail.com/public/basic.ics', '#FF0000', 'Agenda de Thibault', 1),
 ('https://calendar.google.com/calendar/ical/9884fe2e75951b72d761f87bd8b1f538a099a360dfdd51d0cdabbc8088a50a7d%40group.calendar.google.com/public/basic.ics', '#FF0000', 'Agenda de Raf', 2),
 ('https://calendar.google.com/calendar/ical/4dc9995f18e32a42b475e7a7655aacdc710248f4bcb9e8a6e83c573db2a8a1e8%40group.calendar.google.com/public/basic.ics', '#FF0000', 'Agenda de MkM', 3),
 ('https://calendar.google.com/calendar/ical/264e2999f1c412d5d445ce92839e8ff4c81240c026fb09911c4edca30f1e8428%40group.calendar.google.com/public/basic.ics', '#FF0000', 'Agenda de Felix 1', 4),
 ('https://calendar.google.com/calendar/ical/e54ad5a5c1241175cd1176610d85e883b555e908a33d0248c39b6c838a717bdd%40group.calendar.google.com/public/basic.ics', '#FF0000', 'Agenda de Felix 2', 4),
 ('https://calendar.google.com/calendar/ical/e1d431d5f75fa832d6002910ee5cf8c910b63453e4633d090fc90b077a0770ce%40group.calendar.google.com/public/basic.ics', '#FF0000', 'Agenda de Pat-Man', 5);
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
-- Chargement des données de la table 'creneauLibre'
--

INSERT INTO `timeharmony_creneauLibre` (`dateDebut`, `dateFin`, `idAgenda`) VALUES
 ('2024-10-07 00:00:00','2024-10-07 08:15:00', 1),
 ('2024-10-07 14:30:00','2024-10-07 16:30:00', 1),
 ('2024-10-08 08:00:00','2024-10-09 10:45:00', 1),
 ('2024-10-09 15:00:00','2024-10-10 15:30:00', 1),
 ('2024-10-10 19:00:00','2024-10-12 00:00:00', 1);

--
-- Déchargement des données de la table 'creneauLibre'
--

-- --------------------------------------------------------

--
-- Structure de la table 'preference'
--

CREATE TABLE IF NOT EXISTS `timeharmony_preference` (
    `id` int NOT NULL AUTO_INCREMENT,
    `dateDebut` DATETIME NOT NULL,
    `dateFin` DATETIME NOT NULL,
    `idUtilisateur` int NOT NULL,
    `idDisponibilite` int NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`idUtilisateur`) REFERENCES `timeharmony_utilisateur` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`idDisponibilite`) REFERENCES `timeharmony_disponibilite` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Chargement des données de la table 'agenda'
--

INSERT INTO `timeharmony_preference` (`dateDebut`, `dateFin`, `idUtilisateur`, `idDisponibilite`) VALUES
 ('2024-10-07 00:00:00','2024-10-09 00:00:00', 1, 1),
 ('2024-10-09 00:00:00','2024-10-09 12:00:00', 1, 3),
 ('2024-10-09 12:00:00','2024-10-11 00:00:00', 1, 1),
 ('2024-10-11 00:00:00','2024-10-13 23:59:59', 1, 3);
-- ces dates du 7 octobre au 13 octobre reprensentes une semaine type, de lundi 00h à dimanche 23h59

--
-- Déchargement des données de la table 'preference'
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
-- Chargement des données de la table 'contacter'
--

INSERT INTO `timeharmony_contacter` (`idUtilisateur1`, `idUtilisateur2`) VALUES
 (1, 2), (1, 3), (1, 4), (1, 5), 
 (2, 1), (2, 3), (2, 4), (2, 5), 
 (3, 1), (3, 2), (3, 4), 
 (4, 1), (4, 2), (4, 3), 
 (5, 1), (5, 2);

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
-- Chargement des données de la table 'demander'
--

INSERT INTO `timeharmony_demander` (`idUtilisateur1`, `idUtilisateur2`) VALUES
 (1, 6), (2, 6), (3, 5);

--
-- Déchargement des données de la table 'demander'
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
-- Chargement des données de la table 'composer'
--

INSERT INTO `timeharmony_composer` (`idGroupe`, `idUtilisateur`) VALUES
 (1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (2, 1), (2, 2), (2, 3), (2, 4),
 (4,1), (4,2), (4,3), (4,4), (4,5),
 (5,1), (5,2), (5,3), (5,4), (5,5),
 (6,1), (6,2), (6,3), (6,4), (6,5);

--
-- Déchargement des données de la table 'composer'
--

-- --------------------------------------------------------

--
-- Structure de la table 'trouver'
--

CREATE TABLE IF NOT EXISTS `timeharmony_trouver` (
    `idCreneauRdv` int NOT NULL,
    `idUtilisateur` int NOT NULL,
    `idDisponibilite` int NOT NULL,
    PRIMARY KEY (`idCreneauRdv`, `idUtilisateur`, `idDisponibilite`),
    FOREIGN KEY (`idCreneauRdv`) REFERENCES `timeharmony_creneauRdv` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`idUtilisateur`) REFERENCES `timeharmony_utilisateur` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`idDisponibilite`) REFERENCES `timeharmony_disponibilite` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Chargement des données de la table 'trouver'
--

INSERT INTO `timeharmony_trouver` (`idCreneauRdv`, `idUtilisateur`, `idDisponibilite`) VALUES
(1, 1, 1), (1, 2, 1), (1, 3, 1), (1, 4, 1), (1, 5, 1);

--
-- Déchargement des données de la table 'trouver'
--

-- --------------------------------------------------------

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- --------------------------------------------------------