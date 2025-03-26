-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:8889
-- Généré le : mer. 26 mars 2025 à 21:42
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
-- Structure de la table `applications`
--

CREATE TABLE `applications` (
  `application_id` int(11) NOT NULL,
  `mission_id` int(11) NOT NULL,
  `volunteer_id` int(11) NOT NULL,
  `motivation` text NOT NULL,
  `availability` text,
  `application_date` datetime NOT NULL,
  `status` enum('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
  `response_date` datetime DEFAULT NULL,
  `response_message` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `transaction_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(12, 1, 50, 'complete_profile', 'profil', NULL, '2025-03-26 01:51:34'),
(13, 15, 5, 'daily_login', 'login', NULL, '2025-03-26 21:33:46'),
(14, 15, 10, 'update_profile', 'profil', NULL, '2025-03-26 21:37:29');

-- --------------------------------------------------------

--
-- Structure de la table `interests`
--

CREATE TABLE `interests` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `interests`
--

INSERT INTO `interests` (`id`, `name`) VALUES
(8, 'Animaux'),
(5, 'Culture'),
(9, 'Développement durable'),
(10, 'Droits humains'),
(1, 'Éducation'),
(2, 'Environnement'),
(13, 'Handicap'),
(7, 'Humanitaire'),
(11, 'Insertion'),
(14, 'Jeunesse'),
(12, 'Patrimoine'),
(3, 'Santé'),
(15, 'Seniors'),
(4, 'Social'),
(6, 'Sport');

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message_content` text NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `messages`
--

INSERT INTO `messages` (`message_id`, `sender_id`, `receiver_id`, `message_content`, `is_read`, `created_at`) VALUES
(1, 4, 5, 'Bonjour ! J\'ai vu votre profil et je suis intéressé par votre mission.', 0, '2023-03-24 09:15:00'),
(2, 5, 4, 'Bonjour ! Merci pour votre intérêt. Quelles sont vos disponibilités ?', 1, '2023-03-24 09:30:00'),
(3, 4, 5, 'Je suis disponible les week-ends et certains soirs de semaine.', 0, '2023-03-24 10:00:00'),
(4, 5, 4, 'Parfait ! Nous recherchons justement quelqu\'un pour notre événement du samedi prochain.', 1, '2023-03-24 10:15:00'),
(5, 4, 5, 'Super ! Je serais ravi de participer. Pouvez-vous me donner plus de détails ?', 0, '2023-03-24 13:20:00'),
(6, 5, 4, 'Bien sûr, il s\'agit d\'une collecte de dons pour notre projet d\'aide aux sans-abris.', 1, '2023-03-24 14:45:00'),
(7, 4, 5, 'C\'est exactement le type de mission qui m\'intéresse. Comptez sur moi !', 0, '2023-03-24 15:10:00'),
(8, 5, 4, 'Excellent ! Je vous enverrai les informations pratiques par mail. Merci pour votre engagement !', 1, '2023-03-24 15:30:00'),
(9, 4, 5, 'Merci à vous !!', 0, '2025-03-25 17:42:20'),
(10, 4, 5, 'sdfs', 0, '2025-03-25 17:42:22'),
(11, 4, 5, 'sdf', 0, '2025-03-25 17:42:23'),
(12, 4, 5, 'sdf', 0, '2025-03-25 17:42:23'),
(13, 4, 5, 'sdf', 0, '2025-03-25 17:42:24'),
(14, 4, 5, 'sdf', 0, '2025-03-25 17:42:24'),
(15, 4, 5, 'sdf', 0, '2025-03-25 17:42:25'),
(16, 4, 5, 'sdf', 0, '2025-03-25 17:42:25'),
(17, 4, 5, 'test', 0, '2025-03-25 18:28:33'),
(18, 4, 5, 'kjhlkjh', 0, '2025-03-25 18:37:06'),
(19, 4, 5, 'pjoih', 0, '2025-03-25 18:37:07'),
(20, 4, 5, 'lkjhjhgugiohiuhj', 0, '2025-03-25 18:37:09');

-- --------------------------------------------------------

--
-- Structure de la table `missions`
--

CREATE TABLE `missions` (
  `mission_id` int(11) NOT NULL,
  `association_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `availability` json DEFAULT NULL,
  `volunteers_needed` int(11) DEFAULT '1',
  `volunteers_registered` int(11) DEFAULT '0',
  `skills_required` json DEFAULT NULL,
  `tags` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `location` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `missions`
--

INSERT INTO `missions` (`mission_id`, `association_id`, `title`, `description`, `image_url`, `availability`, `volunteers_needed`, `volunteers_registered`, `skills_required`, `tags`, `created_at`, `updated_at`, `location`) VALUES
(2, 2, 'sdfsd', 'sdfsdf', 'uploads/missions/67e318a104edb_Plan de travail 1.png', NULL, 20, 0, '[\"test\"]', '[\"test\"]', '2025-03-25 20:57:05', '2025-03-25 20:57:05', '{\"name\": \"Le Thuit de l\'Oison\", \"coordinates\": [0.9386, 49.2579]}'),
(3, 2, 'ça marche ?', 'stp dis moi que ça marche ', 'uploads/missions/67e327db9d534_Plan de travail 1.png', NULL, 1, 0, '[\"permis b\", \"flop\"]', '[\"chevres\", \"cool\", \"nique\"]', '2025-03-25 22:02:03', '2025-03-25 22:02:03', '{\"name\": \"Perpignan\", \"coordinates\": [2.9045, 42.699]}'),
(4, 2, 'Dresseur', 'On cherche un dresseur la team', 'uploads/missions/67e32afc5c98f_461144609_909331064420254_5148244148240979477_n.jpg', NULL, 20, 0, '[\"dressage\"]', '[\"chevres\", \"cool\", \"nique\"]', '2025-03-25 22:15:24', '2025-03-25 22:15:24', '{\"name\": \"Elbeuf\", \"coordinates\": [0.9974, 49.2773]}');

-- --------------------------------------------------------

--
-- Structure de la table `postulation`
--

CREATE TABLE `postulation` (
  `postulation_id` int(10) UNSIGNED NOT NULL,
  `postulation_user_id_fk` int(10) UNSIGNED NOT NULL,
  `postulation_association_id_fk` int(10) UNSIGNED NOT NULL,
  `postulation_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `postulation`
--

INSERT INTO `postulation` (`postulation_id`, `postulation_user_id_fk`, `postulation_association_id_fk`, `postulation_date`) VALUES
(1, 4, 1, '2025-03-24');

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
  `reward_points_required` int(10) UNSIGNED DEFAULT '0',
  `reward_category` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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

-- --------------------------------------------------------

--
-- Structure de la table `skills`
--

CREATE TABLE `skills` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `skills`
--

INSERT INTO `skills` (`id`, `name`) VALUES
(20, 'Accompagnement'),
(19, 'Accueil'),
(11, 'Administratif'),
(1, 'Animation'),
(9, 'Artistique'),
(5, 'Bricolage'),
(2, 'Communication'),
(13, 'Comptabilité'),
(17, 'Conduite'),
(4, 'Cuisine'),
(12, 'Enseignement'),
(16, 'Gestion de projet'),
(3, 'Informatique'),
(6, 'Jardinage'),
(14, 'Juridique'),
(8, 'Linguistique'),
(15, 'Logistique'),
(18, 'Manutention'),
(7, 'Médical'),
(10, 'Photographique');

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
(4, 'Baptiste', 'SAEGAERT', '2005-07-06', '{\"name\":\"Elbeuf\",\"coordinates\":[0.9974,49.2773],\"range\":1}', 'baptiste.saegaert@gmail.com', '7c222fb2927d828af22f592134e8932480637c0d', 0, NULL, '[]', 'assets/uploads/profile_pictures/1740737524', 'AHHHHHHHHHHHHH'),
(5, 'Wesley', 'PUTMAN', '2005-02-13', '{\"name\":\"Martot\",\"coordinates\":[1.0686,49.28],\"range\":10}', 'barryvert@gmail.com\n', '7c222fb2927d828af22f592134e8932480637c0d', 0, NULL, NULL, 'assets/uploads/profile_pictures/1740737719', NULL),
(6, 'Julien', 'Jul', '1990-01-14', '{\"name\":\"Marseille\",\"coordinates\":[5.3806,43.2803],\"range\":5}', 'jul@gmail.com', '7c222fb2927d828af22f592134e8932480637c0d', 0, NULL, NULL, 'assets/uploads/profile_pictures/default.webp', NULL),
(7, 'User', 'Test', '1990-01-14', '{\"name\":\"Plan-de-Cuques\",\"coordinates\":[5.4639,43.3642],\"range\":30}', 'julien@gmail.com', '7c222fb2927d828af22f592134e8932480637c0d', 0, NULL, NULL, 'assets/uploads/profile_pictures/default.webp', NULL),
(8, 'Victoria', 'PULS', '2004-02-05', NULL, 'vic@gmail.com', '7c222fb2927d828af22f592134e8932480637c0d', 0, '{\"interests\":[\"Animaux\",\"Insertion\",\"Jeunesse\"],\"skills\":[\"Accueil\",\"Animation\"]}', '[]', 'assets/uploads/profile_pictures/default.webp', NULL),
(9, 'Landais', 'Nolan', '2005-02-01', NULL, 'Nolan@gmail.com', '7c222fb2927d828af22f592134e8932480637c0d', 0, NULL, NULL, 'assets/uploads/profile_pictures/default.webp', NULL),
(10, 'Benjamin', 'Billz', '2005-04-03', NULL, 'philou@gmail.com', '7c222fb2927d828af22f592134e8932480637c0d', 0, NULL, NULL, 'assets/uploads/profile_pictures/default.webp', NULL),
(11, 'boulo', 'bobé', '1997-06-05', NULL, 'drftyrt@gmail.com', '7c222fb2927d828af22f592134e8932480637c0d', 0, '{\"interests\":[\"Animaux\",\"Insertion\"],\"skills\":[\"Accueil\",\"Administratif\"]}', '[{\"day\":0,\"slot\":0},{\"day\":0,\"slot\":1}]', 'assets/uploads/profile_pictures/default.webp', NULL),
(12, 'vidsfqsdc@gmail.com', 'edfdqsd', '2000-01-01', '{\"name\":\"Elbeuf\",\"coordinates\":[0.9974,49.2773],\"range\":10}', 'zerzerbaptiste.saegaert@gmail.com', '7c222fb2927d828af22f592134e8932480637c0d', 0, NULL, NULL, 'assets/uploads/profile_pictures/1743003609', NULL),
(13, 'CECIESTUNTEST@gmail.com', 'CECIESTUNTEST', '2000-01-01', '{\"name\":\"Paris\",\"coordinates\":[2.347,48.8589],\"range\":10}', 'TESTMOI@gmail.com', '7c222fb2927d828af22f592134e8932480637c0d', 0, '{\"interests\":[\"D\\u00e9veloppement durable\",\"Jeunesse\"],\"skills\":[\"Animation\",\"Administratif\"]}', '[{\"day\":\"Lun\",\"hours\":[\"8:00\",\"9:00\",\"10:00\",\"11:00\",\"12:00\",\"13:00\"]},{\"day\":\"Mer\",\"hours\":[\"9:00\",\"10:00\",\"11:00\",\"12:00\",\"13:00\",\"14:00\"]},{\"day\":\"Mar\",\"hours\":[\"13:00\",\"14:00\",\"15:00\",\"16:00\",\"17:00\",\"18:00\"]}]', 'assets/uploads/profile_pictures/1743003753', NULL),
(14, 'test@gmail.com', 'Bonjour', '2007-03-02', '{\"name\":\"Le Thuit de l\'Oison\",\"coordinates\":[0.9386,49.2579],\"range\":20}', 'stpmarche@gmail.com', '7c222fb2927d828af22f592134e8932480637c0d', 0, '{\"interests\":[\"Animaux\",\"Culture\",\"Humanitaire\"],\"skills\":[\"Administratif\",\"Gestion de projet\",\"Photographique\"]}', '[{\"day\":\"Lun\",\"hours\":[\"8:00\",\"9:00\",\"10:00\",\"11:00\",\"12:00\",\"13:00\"]},{\"day\":\"Mer\",\"hours\":[\"9:00\",\"10:00\",\"11:00\",\"12:00\",\"13:00\",\"14:00\",\"15:00\",\"16:00\",\"17:00\",\"18:00\"]},{\"day\":\"Mar\",\"hours\":[\"12:00\",\"13:00\",\"14:00\",\"15:00\",\"16:00\",\"17:00\",\"18:00\"]}]', 'assets/uploads/profile_pictures/1743005317', NULL),
(15, 'Francis', 'thelast', '2003-03-03', '{\"name\":\"Sain-Bel\",\"coordinates\":[4.6017,45.8115],\"range\":10}', 'francis@gmail.com', '7c222fb2927d828af22f592134e8932480637c0d', 0, '{\"interests\":[],\"skills\":[]}', '[]', 'assets/uploads/profile_pictures/1743005718', 'dffgsdfsf'),
(16, 'Elise', 'SAEGAERT', '1980-07-23', '{\"name\":\"Le Thuit de l\'Oison\",\"coordinates\":[0.9386,49.2579],\"range\":10}', 'elise.julien.saegaert@gmail.com', '7c222fb2927d828af22f592134e8932480637c0d', 0, NULL, NULL, 'assets/uploads/profile_pictures/default.webp', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `user_experience`
--

CREATE TABLE `user_experience` (
  `experience_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `total_points` int(10) UNSIGNED DEFAULT '0',
  `current_level` int(10) UNSIGNED DEFAULT '1',
  `points_to_next_level` int(10) UNSIGNED DEFAULT '100',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `user_experience`
--

INSERT INTO `user_experience` (`experience_id`, `user_id`, `total_points`, `current_level`, `points_to_next_level`, `last_updated`) VALUES
(1, 8, 70, 1, 100, '2025-03-26 01:13:26'),
(2, 1, 160, 2, 250, '2025-03-26 01:37:05'),
(3, 15, 15, 1, 100, '2025-03-26 21:33:46');

-- --------------------------------------------------------

--
-- Structure de la table `user_rewards`
--

CREATE TABLE `user_rewards` (
  `user_reward_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `reward_id` int(10) UNSIGNED NOT NULL,
  `acquired_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `progress` int(10) UNSIGNED DEFAULT '0',
  `is_displayed` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`application_id`),
  ADD UNIQUE KEY `mission_volunteer_idx` (`mission_id`,`volunteer_id`),
  ADD KEY `volunteer_id` (`volunteer_id`);

--
-- Index pour la table `association`
--
ALTER TABLE `association`
  ADD PRIMARY KEY (`association_id`),
  ADD KEY `fk_association_user` (`user_id`);

--
-- Index pour la table `experience_transactions`
--
ALTER TABLE `experience_transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `interests`
--
ALTER TABLE `interests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `idx_sender` (`sender_id`),
  ADD KEY `idx_receiver` (`receiver_id`);

--
-- Index pour la table `missions`
--
ALTER TABLE `missions`
  ADD PRIMARY KEY (`mission_id`),
  ADD KEY `association_id` (`association_id`);

--
-- Index pour la table `postulation`
--
ALTER TABLE `postulation`
  ADD PRIMARY KEY (`postulation_id`),
  ADD KEY `postulation_user_id_fk` (`postulation_user_id_fk`),
  ADD KEY `postulation_association_id_fk` (`postulation_association_id_fk`);

--
-- Index pour la table `rewards`
--
ALTER TABLE `rewards`
  ADD PRIMARY KEY (`reward_id`);

--
-- Index pour la table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- Index pour la table `user_experience`
--
ALTER TABLE `user_experience`
  ADD PRIMARY KEY (`experience_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Index pour la table `user_rewards`
--
ALTER TABLE `user_rewards`
  ADD PRIMARY KEY (`user_reward_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `reward_id` (`reward_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `applications`
--
ALTER TABLE `applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `association`
--
ALTER TABLE `association`
  MODIFY `association_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `experience_transactions`
--
ALTER TABLE `experience_transactions`
  MODIFY `transaction_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `interests`
--
ALTER TABLE `interests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `missions`
--
ALTER TABLE `missions`
  MODIFY `mission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `postulation`
--
ALTER TABLE `postulation`
  MODIFY `postulation_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `rewards`
--
ALTER TABLE `rewards`
  MODIFY `reward_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `skills`
--
ALTER TABLE `skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `user_experience`
--
ALTER TABLE `user_experience`
  MODIFY `experience_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `user_rewards`
--
ALTER TABLE `user_rewards`
  MODIFY `user_reward_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `association`
--
ALTER TABLE `association`
  ADD CONSTRAINT `fk_association_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `missions`
--
ALTER TABLE `missions`
  ADD CONSTRAINT `missions_ibfk_1` FOREIGN KEY (`association_id`) REFERENCES `association` (`association_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `postulation`
--
ALTER TABLE `postulation`
  ADD CONSTRAINT `postulation_association_id_fk` FOREIGN KEY (`postulation_association_id_fk`) REFERENCES `association` (`association_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `postulation_user_id_fk` FOREIGN KEY (`postulation_user_id_fk`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `user_rewards`
--
ALTER TABLE `user_rewards`
  ADD CONSTRAINT `fk_reward_id` FOREIGN KEY (`reward_id`) REFERENCES `rewards` (`reward_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
