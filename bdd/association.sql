-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 04 mars 2025 à 09:15
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `bddsae401`
--

-- --------------------------------------------------------

--
-- Structure de la table `association`
--

CREATE TABLE `association` (
  `association_id` int(10) UNSIGNED NOT NULL,
  `association_name` varchar(255) NOT NULL,
  `association_siren` varchar(255) NOT NULL,
  `association_date` date NOT NULL,
  `association_desc` text NOT NULL,
  `association_mail` varchar(255) NOT NULL,
  `association_password` varchar(255) NOT NULL,
  `association_mission` varchar(255) DEFAULT NULL,
  `association_profile_picture` varchar(255) NOT NULL DEFAULT '	assets/uploads/profile_pictures/default.webp	',
  `association_background_image` varchar(255) NOT NULL DEFAULT '	assets/uploads/background_image/defaultAssociation.jpg	',
  `association_adress` longtext CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `association`
--

INSERT INTO `association` (`association_id`, `association_name`, `association_siren`, `association_date`, `association_desc`, `association_mail`, `association_password`, `association_mission`, `association_profile_picture`, `association_background_image`, `association_adress`) VALUES
(1, 'Les Restaurants du Cœur', '	339 863 417 00418', '2025-03-04', 'Les Restaurants du Cœur – Les Relais du Cœur, connus sous le nom de Les Restos du Cœura, sont une association loi de 1901 à but non lucratif et reconnue d\'utilité publique, créée en France par l\'humoriste et comédien Michel Colucci (dit Coluche) en 1985.', 'contact@restosducoeur.org', 'b1c26a34f1bf8a52f681edd097c20580c617b788', 'Les Restos du Cœur ont pour mission d\'apporter une assistance bénévole aux personnes en difficulté, que ce soit dans le domaine alimentaire, par l\'accès à des repas gratuits, ou dans le domaine de l\'insertion sociale et économique, par tout moyen appropri', 'assets/uploads/profile_pictures/default.webp	', 'assets/uploads/background_image/defaultAssociation.jpg	', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `association`
--
ALTER TABLE `association`
  ADD PRIMARY KEY (`association_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `association`
--
ALTER TABLE `association`
  MODIFY `association_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
