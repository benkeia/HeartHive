-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 26 mars 2025 à 13:41
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
-- Structure de la table `experience_transactions`
--

CREATE TABLE `experience_transactions` (
  `transaction_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `points` int(11) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `related_entity_type` varchar(50) DEFAULT NULL,
  `related_entity_id` int(10) UNSIGNED DEFAULT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `experience_transactions`
--

INSERT INTO `experience_transactions` (`transaction_id`, `user_id`, `points`, `reason`, `related_entity_type`, `related_entity_id`, `transaction_date`) VALUES
(1, 8, 5, 'daily_login', 'login', NULL, '2025-03-26 01:13:26'),
(2, 8, 20, 'add_profile_picture', 'photo', NULL, '2025-03-26 01:13:42'),
(3, 8, 10, 'update_profile', 'profil', NULL, '2025-03-26 01:13:42'),
(4, 8, 10, 'update_profile', 'profil', NULL, '2025-03-26 01:13:56'),
(5, 8, 25, 'apply_association', 'association_22', NULL, '2025-03-26 01:26:00'),
(6, 1, 5, 'daily_login', 'login', NULL, '2025-03-26 01:37:05'),
(7, 1, 20, 'add_profile_picture', 'photo', NULL, '2025-03-26 01:37:24'),
(8, 1, 10, 'update_profile', 'profil', NULL, '2025-03-26 01:37:24'),
(9, 1, 25, 'apply_association', 'association_17', NULL, '2025-03-26 01:39:18'),
(10, 1, 25, 'apply_association', 'association_20', NULL, '2025-03-26 01:39:48'),
(11, 1, 25, 'apply_association', 'association_19', NULL, '2025-03-26 01:39:54'),
(12, 1, 50, 'complete_profile', 'profil', NULL, '2025-03-26 01:51:34');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `experience_transactions`
--
ALTER TABLE `experience_transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `experience_transactions`
--
ALTER TABLE `experience_transactions`
  MODIFY `transaction_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `experience_transactions`
--
ALTER TABLE `experience_transactions`
  ADD CONSTRAINT `experience_transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
