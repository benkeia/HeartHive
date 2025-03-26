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
-- Structure de la table `rewards`
--

CREATE TABLE `rewards` (
  `reward_id` int(10) UNSIGNED NOT NULL,
  `reward_name` varchar(100) NOT NULL,
  `reward_type` enum('badge','certificate','level','card') NOT NULL,
  `reward_description` text NOT NULL,
  `reward_image` varchar(255) DEFAULT NULL,
  `reward_pdf_template` varchar(255) DEFAULT NULL,
  `reward_points_required` int(10) UNSIGNED DEFAULT 0,
  `reward_category` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `rewards`
--

INSERT INTO `rewards` (`reward_id`, `reward_name`, `reward_type`, `reward_description`, `reward_image`, `reward_pdf_template`, `reward_points_required`, `reward_category`, `is_active`, `created_at`) VALUES
(1, 'Carte Première Mission', 'card', 'Obtenue après votre première mission bénévole', NULL, NULL, 10, 'Débutant', 1, '2025-03-26 00:42:20'),
(2, 'Carte Assiduité', 'card', 'Connexion à l\'application 7 jours consécutifs', NULL, NULL, 50, 'Engagement', 1, '2025-03-26 00:42:20'),
(3, 'Carte Impact Social', 'card', 'Avoir aidé plus de 50 personnes', NULL, NULL, 200, 'Impact', 1, '2025-03-26 00:42:20'),
(4, 'Carte Spécialiste', 'card', '10 missions dans le même domaine', NULL, NULL, 300, 'Expertise', 1, '2025-03-26 00:42:20'),
(5, 'Carte Polyvalence', 'card', 'Missions dans 5 domaines différents', NULL, NULL, 350, 'Diversité', 1, '2025-03-26 00:42:20'),
(6, 'Carte Mentor', 'card', 'Avoir guidé 3 nouveaux bénévoles', NULL, NULL, 500, 'Leadership', 1, '2025-03-26 00:42:20'),
(7, 'Carte Ambassadeur', 'card', 'Avoir parrainé 5 nouveaux membres', NULL, NULL, 600, 'Communauté', 1, '2025-03-26 00:42:20'),
(8, 'Certificat de Bénévolat - Bronze', 'certificate', '50 heures de bénévolat accomplies', NULL, 'templates/certificate_bronze.pdf', 500, 'Certification', 1, '2025-03-26 00:42:28'),
(9, 'Certificat de Bénévolat - Argent', 'certificate', '100 heures de bénévolat accomplies', NULL, 'templates/certificate_silver.pdf', 1000, 'Certification', 1, '2025-03-26 00:42:28'),
(10, 'Certificat de Bénévolat - Or', 'certificate', '250 heures de bénévolat accomplies', NULL, 'templates/certificate_gold.pdf', 2500, 'Certification', 1, '2025-03-26 00:42:28'),
(11, 'Certificat d\'Expertise', 'certificate', 'Expert reconnu dans un domaine spécifique', NULL, 'templates/certificate_expert.pdf', 2000, 'Expertise', 1, '2025-03-26 00:42:28'),
(12, 'Certificat de Leadership', 'certificate', 'Capacités de leadership démontrées', NULL, 'templates/certificate_leadership.pdf', 3000, 'Leadership', 1, '2025-03-26 00:42:28');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `rewards`
--
ALTER TABLE `rewards`
  ADD PRIMARY KEY (`reward_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `rewards`
--
ALTER TABLE `rewards`
  MODIFY `reward_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
