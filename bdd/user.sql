-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:8889
-- Généré le : ven. 28 fév. 2025 à 10:24
-- Version du serveur : 5.7.39
-- Version de PHP : 7.4.33

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
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_firstname` varchar(255) NOT NULL,
  `user_date` date NOT NULL,
  `user_adress` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `user_mail` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_type` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `user_tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `user_disponibility` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `user_profile_picture` varchar(255) NOT NULL DEFAULT 'assets/uploads/profile_pictures/default.webp',
  `user_bio` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`user_id`, `user_name`, `user_firstname`, `user_date`, `user_adress`, `user_mail`, `user_password`, `user_type`, `user_tags`, `user_disponibility`, `user_profile_picture`, `user_bio`) VALUES
(1, 'Cochard', 'Damien', '2025-02-28', NULL, 'cochard.damien@gmail.com', '99e6b749acbfbe2a596df99e91d24d6e1fdbee00', 0, NULL, NULL, 'assets/uploads/profile_pictures/default.webp', NULL),
(2, 'admin', 'admin', '2005-07-06', 'Elbeuf', 'baptiste.saegaert@gmail.com', '58ad983135fe15c5a8e2e15fb5b501aedcf70dc2', 3, NULL, NULL, 'assets/uploads/profile_pictures/1740733141_sfgsd.jpg', NULL),
(3, 'admin', 'SAEGAERT', '1960-05-09', 'Paris', 'baptiste.saegaert@couilles.com', '7c222fb2927d828af22f592134e8932480637c0d', 3, NULL, NULL, 'assets/uploads/profile_pictures/1740737077', NULL),
(4, 'admqsdqin', 'SAEGAERT', '2003-07-06', '{\"name\":\"Elbeuf\",\"coordinates\":[0.9974,49.2773],\"range\":1}', 'baptistsdfqe.saegaert@gmail.com', '7c222fb2927d828af22f592134e8932480637c0d', 0, NULL, NULL, 'assets/uploads/profile_pictures/1740737524', NULL),
(5, 'Wesley', 'PUTMAN', '2005-02-13', '{\"name\":\"Martot\",\"coordinates\":[1.0686,49.28],\"range\":10}', 'wesleyputmant@gmail.com', '2aba7190e9c90d9d8f8532c98928b7e514312af2', 0, NULL, NULL, 'assets/uploads/profile_pictures/1740737719', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
