-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : lun. 26 avr. 2021 à 13:55
-- Version du serveur :  10.3.25-MariaDB-0ubuntu0.20.04.1-log
-- Version de PHP : 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `p1913852`
--

-- --------------------------------------------------------

--
-- Structure de la table `Categorie`
--

CREATE TABLE `Categorie` (
  `catId` int(11) NOT NULL,
  `nomCat` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `Categorie`
--

INSERT INTO `Categorie` (`catId`, `nomCat`) VALUES
(0, 'animaux'),
(1, 'paysage'),
(2, 'art'),
(3, 'portrait');

-- --------------------------------------------------------

--
-- Structure de la table `Photo`
--

CREATE TABLE `Photo` (
  `photoId` int(11) NOT NULL,
  `nomFic` varchar(250) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `catId` int(11) DEFAULT NULL,
  `contenu` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `Photo`
--

INSERT INTO `Photo` (`photoId`, `nomFic`, `description`, `catId`, `contenu`) VALUES
(0, 'éléphant', 'Un éléphant d\'Asie sous la lumière des sous-bois', 0, ''),
(1, 'gratte ciel', 'Un quartier fluvial rempli de gratte-ciel', 1, ''),
(2, 'renard', 'Un renard roux sur de la neige regardant l\'objectif', 0, ''),
(3, 'renard neige', 'Un jeune renard des neiges regardant l\'objectif', 0, '');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `Categorie`
--
ALTER TABLE `Categorie`
  ADD PRIMARY KEY (`catId`);

--
-- Index pour la table `Photo`
--
ALTER TABLE `Photo`
  ADD PRIMARY KEY (`photoId`),
  ADD KEY `catId` (`catId`);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `Photo`
--
ALTER TABLE `Photo`
  ADD CONSTRAINT `Photo_ibfk_1` FOREIGN KEY (`catId`) REFERENCES `Categorie` (`catId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
