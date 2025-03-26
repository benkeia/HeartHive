-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:8889
-- Généré le : mer. 26 mars 2025 à 13:14
-- Version du serveur : 5.7.39
-- Version de PHP : 8.2.0

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
  `association_category` varchar(50) DEFAULT NULL,
  `association_mail` varchar(255) NOT NULL,
  `association_password` varchar(255) NOT NULL,
  `association_address` text,
  `association_postal` varchar(10) DEFAULT NULL,
  `association_city` varchar(100) DEFAULT NULL,
  `association_country` varchar(100) DEFAULT 'France',
  `association_location` text,
  `association_phone` varchar(20) DEFAULT NULL,
  `association_website` varchar(255) DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `association_mission` varchar(255) DEFAULT NULL,
  `association_profile_picture` varchar(255) NOT NULL DEFAULT 'assets/uploads/profile_pictures/default.webp',
  `association_background_image` varchar(255) NOT NULL DEFAULT 'assets/uploads/background_image/defaultAssociation.jpg',
  `association_adress` longtext CHARACTER SET utf8
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `association`
--

INSERT INTO `association` (`association_id`, `association_name`, `association_siren`, `association_date`, `association_desc`, `association_category`, `association_mail`, `association_password`, `association_address`, `association_postal`, `association_city`, `association_country`, `association_location`, `association_phone`, `association_website`, `user_id`, `association_mission`, `association_profile_picture`, `association_background_image`, `association_adress`) VALUES
(1, 'Les Restaurants du Cœur', '987654321', '1985-12-01', 'Association fournissant de l\'aide alimentaire aux plus démunis.', NULL, 'contact@restosducoeur.org', 'MotDePasseSecurise', NULL, NULL, NULL, 'France', NULL, NULL, NULL, NULL, 'Lutter contre la précarité alimentaire.', 'assets/uploads/profile_pictures/restosducoeur.webp', 'assets/uploads/background_image/restosducoeur.jpg', '{\"name\":\"Marseille\",\"coordinates\":[5.3806,43.2803],\"range\":5}'),
(2, 'La SPA', '1234', '2025-03-02', 'Une asso qui aide les animaux', NULL, 'spa@gmail.com', '7c222fb2927d828af22f592134e8932480637c0d', NULL, NULL, NULL, 'France', NULL, NULL, NULL, NULL, NULL, 'assets/uploads/profile_pictures/default.webp', 'assets/uploads/background_image/defaultAssociation.jpg', '{\"name\":\"Marseille\",\"coordinates\":[5.3806,43.2803],\"range\":5}'),
(3, 'sdw', 'dd', '2025-03-26', 'sdfsdqfqs', NULL, 'baptiste.saegaert@gmail.com', '7c222fb2927d828af22f592134e8932480637c0d', NULL, NULL, NULL, 'France', NULL, NULL, NULL, NULL, NULL, 'assets/uploads/profile_pictures/default.webp', 'assets/uploads/background_image/defaultAssociation.jpg', NULL),
(4, 'asoooo', '76543', '2025-03-26', 'efgeqzq', NULL, 'baptiste.saegaert@gmail.com', '7c222fb2927d828af22f592134e8932480637c0d', NULL, NULL, NULL, 'France', NULL, NULL, NULL, NULL, NULL, 'assets/uploads/profile_pictures/default.webp', 'assets/uploads/background_image/defaultAssociation.jpg', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `association`
--
ALTER TABLE `association`
  ADD PRIMARY KEY (`association_id`),
  ADD KEY `fk_association_user` (`user_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `association`
--
ALTER TABLE `association`
  MODIFY `association_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `association`
--
ALTER TABLE `association`
  ADD CONSTRAINT `fk_association_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
