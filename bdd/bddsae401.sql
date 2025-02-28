-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 28 fév. 2025 à 09:21
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12
SET
  SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET
  time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;

/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;

/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;

/*!40101 SET NAMES utf8mb4 */
;

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
  `association_password` varchar(255) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Structure de la table `postulation`
--
CREATE TABLE `postulation` (
  `postulation_id` int(10) UNSIGNED NOT NULL,
  `postulation_user_id_fk` int(10) UNSIGNED NOT NULL,
  `postulation_association_id_fk` int(10) UNSIGNED NOT NULL,
  `postulation_date` date NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Structure de la table `user`
--
CREATE TABLE `user` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_firstname` varchar(255) NOT NULL,
  `user_date` date NOT NULL,
  `user_adress` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `user_mail` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_type` smallint(5) UNSIGNED NOT NULL,
  `user_tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `user_disponibility` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--
INSERT INTO
  `user` (
    `user_id`,
    `user_name`,
    `user_firstname`,
    `user_date`,
    `user_adress`,
    `user_mail`,
    `user_password`,
    `user_type`,
    `user_tags`,
    `user_disponibility`
  )
VALUES
  (
    1,
    'Cochard',
    'Damien',
    '2025-02-28',
    NULL,
    'cochard.damien@gmail.com',
    '99e6b749acbfbe2a596df99e91d24d6e1fdbee00',
    0,
    NULL,
    NULL
  );

--
-- Index pour les tables déchargées
--
--
-- Index pour la table `association`
--
ALTER TABLE
  `association`
ADD
  PRIMARY KEY (`association_id`);

--
-- Index pour la table `postulation`
--
ALTER TABLE
  `postulation`
ADD
  PRIMARY KEY (`postulation_id`),
ADD
  KEY `postulation_user_id_fk` (`postulation_user_id_fk`),
ADD
  KEY `postulation_association_id_fk` (`postulation_association_id_fk`);

--
-- Index pour la table `user`
--
ALTER TABLE
  `user`
ADD
  PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--
--
-- AUTO_INCREMENT pour la table `association`
--
ALTER TABLE
  `association`
MODIFY
  `association_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `postulation`
--
ALTER TABLE
  `postulation`
MODIFY
  `postulation_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE
  `user`
MODIFY
  `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 2;

--
-- Contraintes pour les tables déchargées
--
--
-- Contraintes pour la table `postulation`
--
ALTER TABLE
  `postulation`
ADD
  CONSTRAINT `postulation_association_id_fk` FOREIGN KEY (`postulation_association_id_fk`) REFERENCES `association` (`association_id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD
  CONSTRAINT `postulation_user_id_fk` FOREIGN KEY (`postulation_user_id_fk`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;

/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;

/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;