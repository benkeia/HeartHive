-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 27 mars 2025 à 03:45
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
-- Structure de la table `applications`
--

CREATE TABLE `applications` (
  `application_id` int(11) NOT NULL,
  `mission_id` int(11) NOT NULL,
  `volunteer_id` int(11) NOT NULL,
  `motivation` text NOT NULL,
  `availability` text DEFAULT NULL,
  `application_date` datetime NOT NULL,
  `status` enum('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
  `response_date` datetime DEFAULT NULL,
  `response_message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `applications`
--

INSERT INTO `applications` (`application_id`, `mission_id`, `volunteer_id`, `motivation`, `availability`, `application_date`, `status`, `response_date`, `response_message`) VALUES
(1, 4, 6, 'Ouais', 'zd', '2025-03-26 22:51:31', 'pending', NULL, NULL),
(2, 3, 6, '', '', '2025-03-26 23:12:11', 'pending', NULL, NULL),
(3, 2, 1, '', '', '2025-03-27 00:53:18', 'pending', NULL, NULL),
(4, 3, 1, '', '', '2025-03-27 01:10:44', 'pending', NULL, NULL),
(5, 2, 4, '', '', '2025-03-27 01:44:10', 'pending', NULL, NULL),
(6, 29, 1, '', '', '2025-03-27 03:26:55', 'pending', NULL, NULL);

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
(1, 'Les Restaurants du Cœur', '	339 863 417 00418', '2025-03-04', 'Les Restaurants du Coeur - Les Relais du Coeur, communément appelés Les Restos du Coeur, est une association caritative française créée en 1985 par Coluche. Elle distribue de la nourriture aux plus démunis et participe à leur insertion sociale et économique.', 'contact@restosducoeur.org', 'b1c26a34f1bf8a52f681edd097c20580c617b788', 'Aider et apporter une assistance bénévole aux personnes démunies, notamment dans le domaine alimentaire par l\'accès à des repas gratuits.', 'https://picsum.photos/seed/assoc1logo/300/300', 'https://picsum.photos/seed/assoc1banner/1200/400', '{\"name\":\"Paris\",\"coordinates\":[2.3522,48.8566],\"range\":15}'),
(2, 'Solidarité & Co', '123456789', '2015-06-12', 'Solidarité & Co est une association caritative oeuvrant sur le terrain de l\'entraide sociale. Notre équipe de bénévoles accompagne les personnes en situation de précarité par des actions concrètes.', 'contact@solidariteco.fr', 'hashed_password_1', 'Apporter un soutien matériel, moral et administratif aux personnes en situation de précarité dans Paris et sa région.', 'https://picsum.photos/seed/assoc2logo/300/300', 'https://picsum.photos/seed/assoc2banner/1200/400', '{\"name\":\"Paris\",\"coordinates\":[2.3488,48.8534],\"range\":10}'),
(3, 'Les Artistes Libres', '987654321', '2012-03-25', 'Les Artistes Libres est un collectif d\'artistes indépendants dédié à la promotion de l\'art sous toutes ses formes. Nous organisons des expositions, ateliers et évènements culturels accessibles à tous.', 'info@artisteslibres.fr', 'hashed_password_2', 'Promouvoir l\'art sous toutes ses formes et rendre la culture accessible à tous, notamment dans les quartiers défavorisés.', 'https://picsum.photos/seed/assoc3logo/300/300', 'https://picsum.photos/seed/assoc3banner/1200/400', '{\"name\":\"Paris\",\"coordinates\":[2.3417,48.8607],\"range\":7}'),
(4, 'EcoAvenir', '192837465', '2018-09-30', 'EcoAvenir se consacre à la protection de notre environnement par des actions concrètes et de la sensibilisation. Nos initiatives incluent la plantation d\'arbres et l\'éducation environnementale.', 'contact@ecoavenir.org', 'hashed_password_3', 'Sensibiliser le public aux enjeux environnementaux et mettre en oeuvre des actions concrètes pour préserver la biodiversité locale.', 'https://picsum.photos/seed/assoc4logo/300/300', 'https://picsum.photos/seed/assoc4banner/1200/400', '{\"name\":\"Lyon\",\"coordinates\":[4.8357,45.7640],\"range\":12}'),
(5, 'Tech4All', '564738291', '2020-07-15', 'Tech4All travaille à réduire la fracture numérique en facilitant l\'accès aux technologies pour toutes les populations. Nous recyclons et redistribuons du matériel informatique.', 'contact@tech4all.com', 'hashed_password_4', 'Réduire la fracture numérique en facilitant l\'accès à l\'informatique pour tous et en proposant des formations adaptées.', 'https://picsum.photos/seed/assoc5logo/300/300', 'https://picsum.photos/seed/assoc5banner/1200/400', '{\"name\":\"Toulouse\",\"coordinates\":[1.4442,43.6045],\"range\":8}'),
(6, 'Sport Ensemble', '748392615', '2016-11-05', 'Sport Ensemble promeut la pratique sportive comme vecteur d\'inclusion sociale. Nous organisons des activités sportives gratuites ou à prix réduits dans les quartiers prioritaires.', 'contact@sportensemble.fr', 'hashed_password_5', 'Promouvoir la pratique sportive comme outil d\'inclusion sociale et de bien-être, particulièrement auprès des jeunes.', 'https://picsum.photos/seed/assoc6logo/300/300', 'https://picsum.photos/seed/assoc6banner/1200/400', '{\"name\":\"Marseille\",\"coordinates\":[5.3698,43.2965],\"range\":15}'),
(7, 'Les Gourmets Solidaires', '856471293', '2019-04-22', 'Les Gourmets Solidaires combat le gaspillage alimentaire en récoltant des surplus pour les redistribuer aux personnes dans le besoin. Nous organisons également des ateliers de cuisine.', 'contact@gourmetsolidaires.fr', 'hashed_password_6', 'Lutter contre le gaspillage alimentaire en collectant et redistribuant les invendus alimentaires aux personnes dans le besoin.', 'https://picsum.photos/seed/assoc7logo/300/300', 'https://picsum.photos/seed/assoc7banner/1200/400', '{\"name\":\"Bordeaux\",\"coordinates\":[-0.5795,44.8378],\"range\":9}'),
(8, 'Code Pour Tous', '147258369', '2017-01-18', 'Code Pour Tous démocratise l\'apprentissage de la programmation informatique dans les zones rurales et quartiers défavorisés. Nos ateliers permettent l\'acquisition de compétences numériques.', 'contact@codepourtous.fr', 'hashed_password_7', 'Démocratiser l\'apprentissage de la programmation et des compétences numériques, notamment auprès des publics éloignés du numérique.', 'https://picsum.photos/seed/assoc8logo/300/300', 'https://picsum.photos/seed/assoc8banner/1200/400', '{\"name\":\"Lille\",\"coordinates\":[3.0573,50.6292],\"range\":11}'),
(9, 'Refuge Animalier', '369852147', '2014-08-10', 'Le Refuge Animalier recueille, soigne et propose à l\'adoption des animaux abandonnés ou maltraités. Notre équipe se mobilise pour offrir une seconde chance à nos amis à quatre pattes.', 'contact@refugeanimalier.fr', 'hashed_password_8', 'Secourir, soigner et proposer à l\'adoption les animaux abandonnés ou maltraités tout en sensibilisant le public au bien-être animal.', 'https://picsum.photos/seed/assoc9logo/300/300', 'https://picsum.photos/seed/assoc9banner/1200/400', '{\"name\":\"Nice\",\"coordinates\":[7.2620,43.7102],\"range\":14}'),
(10, 'Musique & Partage', '951753468', '2013-05-29', 'Musique & Partage rend l\'apprentissage musical accessible à tous, particulièrement aux enfants des milieux défavorisés. Nous proposons des cours à tarifs solidaires.', 'contact@musiquepartage.fr', 'hashed_password_9', 'Rendre la pratique musicale accessible à tous, notamment aux enfants de milieux défavorisés, via des cours et prêts d\'instruments.', 'https://picsum.photos/seed/assoc10logo/300/300', 'https://picsum.photos/seed/assoc10banner/1200/400', '{\"name\":\"Strasbourg\",\"coordinates\":[7.7521,48.5734],\"range\":10}'),
(11, 'Avenir Durable', '258369147', '2021-02-14', 'Avenir Durable sensibilise aux enjeux écologiques et promeut les solutions durables dans notre quotidien. Nos projets incluent l\'installation de panneaux solaires communautaires.', 'contact@avenirdurable.fr', 'hashed_password_10', 'Promouvoir le développement durable et les énergies renouvelables à travers des projets locaux et des actions de sensibilisation.', 'https://picsum.photos/seed/assoc11logo/300/300', 'https://picsum.photos/seed/assoc11banner/1200/400', '{\"name\":\"Nantes\",\"coordinates\":[-1.5536,47.2173],\"range\":12}'),
(12, 'Solidarité Normande', '123456789', '2010-06-15', 'Solidarité Normande est une association fondée en 2010 qui apporte un soutien aux personnes en difficulté en Normandie. Nos actions incluent l\'aide alimentaire et l\'accompagnement social.', 'contact@solidarite-normande.fr', '', 'Apporter une aide matérielle et un accompagnement social aux personnes en difficulté en Normandie.', 'https://picsum.photos/seed/assoc12logo/300/300', 'https://picsum.photos/seed/assoc12banner/1200/400', '{\"coordinates\":[1.0083,49.2889],\"range\":10}'),
(13, 'Les Amis de Rouen', '987654321', '2005-03-12', 'Les Amis de Rouen oeuvrent depuis 2005 pour la valorisation du patrimoine culturel rouennais et l\'animation de la vie sociale locale. Notre association organise des événements culturels.', 'contact@amisderouen.fr', '', 'Valoriser le patrimoine culturel et historique de Rouen et promouvoir les initiatives citoyennes locales.', 'https://picsum.photos/seed/assoc13logo/300/300', 'https://picsum.photos/seed/assoc13banner/1200/400', '{\"name\":\"Rouen\",\"coordinates\":[1.0993,49.4432],\"range\":15}'),
(14, 'Nature et Environnement Caen', '112233445', '2012-08-23', 'Nature et Environnement Caen se consacre à la protection des espaces naturels du Calvados et à l\'éducation environnementale. Nos bénévoles organisent des actions de nettoyage.', 'contact@nature-caen.fr', '', 'Protéger l\'environnement naturel du Calvados et sensibiliser le public aux enjeux écologiques locaux.', 'https://picsum.photos/seed/assoc14logo/300/300', 'https://picsum.photos/seed/assoc14banner/1200/400', '{\"name\":\"Caen\",\"coordinates\":[-0.3707,49.1829],\"range\":20}'),
(15, 'Havre Solidaire', '556677889', '2018-06-30', 'Havre Solidaire intervient auprès des personnes sans-abri et en grande précarité au Havre. Nous distribuons des repas chauds quotidiens et proposons un accueil de jour.', 'contact@havre-solidaire.fr', '', 'Venir en aide aux personnes sans-abri et en grande précarité au Havre par des distributions alimentaires et un accompagnement social.', 'https://picsum.photos/seed/assoc15logo/300/300', 'https://picsum.photos/seed/assoc15banner/1200/400', '{\"name\":\"Le Havre\",\"coordinates\":[0.1077,49.4944],\"range\":10}'),
(16, 'Patrimoine Normand', '332211445', '1998-09-10', 'Patrimoine Normand se consacre depuis plus de 20 ans à la sauvegarde et à la mise en valeur du patrimoine architectural, historique et culturel normand.', 'contact@patrimoine-normand.fr', '', 'Préserver et mettre en valeur le patrimoine architectural et culturel normand pour les générations futures.', 'https://picsum.photos/seed/assoc16logo/300/300', 'https://picsum.photos/seed/assoc16banner/1200/400', '{\"name\":\"Évreux\",\"coordinates\":[1.1500,49.0260],\"range\":10}'),
(17, 'Enfants du Cotentin', '778899665', '2015-05-15', 'Enfants du Cotentin apporte un soutien éducatif et social aux enfants et adolescents issus de milieux défavorisés dans la presqu\'île du Cotentin.', 'contact@enfants-cotentin.fr', '', 'Soutenir l\'éducation et l\'épanouissement des enfants défavorisés du Cotentin par des activités éducatives et culturelles.', 'https://picsum.photos/seed/assoc17logo/300/300', 'https://picsum.photos/seed/assoc17banner/1200/400', '{\"name\":\"Cherbourg\",\"coordinates\":[-1.6356,49.6394],\"range\":25}'),
(18, 'Fédération Sportive Normande', '998877665', '2009-11-05', 'La Fédération Sportive Normande regroupe plus de 50 clubs amateurs de la région pour promouvoir la pratique sportive inclusive et accessible à tous.', 'contact@sport-normandie.fr', '', 'Développer la pratique sportive amateur en Normandie et rendre le sport accessible à tous les publics.', 'https://picsum.photos/seed/assoc18logo/300/300', 'https://picsum.photos/seed/assoc18banner/1200/400', '{\"name\":\"Alençon\",\"coordinates\":[0.0931,48.4336],\"range\":30}'),
(19, 'Agriculture Bio Manche', '554433221', '2017-07-18', 'Agriculture Bio Manche soutient les agriculteurs locaux dans leur transition vers l\'agriculture biologique. Notre association met en relation producteurs et consommateurs.', 'contact@bio-manche.fr', '', 'Promouvoir l\'agriculture biologique dans la Manche et créer des liens entre producteurs locaux et consommateurs.', 'https://picsum.photos/seed/assoc19logo/300/300', 'https://picsum.photos/seed/assoc19banner/1200/400', '{\"name\":\"Saint-Lô\",\"coordinates\":[-1.0903,49.1141],\"range\":20}'),
(20, 'Arts et Cultures Normandes', '445566778', '2013-04-22', 'Arts et Cultures Normandes oeuvre pour la préservation et la promotion des traditions artistiques et culturelles normandes. Nous documentons les danses, musiques et costumes traditionnels.', 'contact@art-culture-normandie.fr', '', 'Préserver et faire vivre les traditions artistiques et culturelles normandes à travers des événements et ateliers.', 'https://picsum.photos/seed/assoc20logo/300/300', 'https://picsum.photos/seed/assoc20banner/1200/400', '{\"name\":\"Lisieux\",\"coordinates\":[0.2290,49.1463],\"range\":15}'),
(21, 'Protection Animale Normandie', '887766554', '2006-02-28', 'Protection Animale Normandie gère plusieurs refuges dans la région pour recueillir, soigner et proposer à l\'adoption des animaux abandonnés ou maltraités.', 'contact@protectionanimaux.fr', '', 'Protéger les animaux abandonnés ou maltraités en Normandie et sensibiliser le public au respect de la cause animale.', 'https://picsum.photos/seed/assoc21logo/300/300', 'https://picsum.photos/seed/assoc21banner/1200/400', '{\"name\":\"Dieppe\",\"coordinates\":[1.0789,49.9229],\"range\":10}'),
(22, 'Handisport Normand', '332244556', '2010-10-17', 'Handisport Normand rend accessible la pratique sportive aux personnes en situation de handicap. Nous adaptons les équipements et organisons des compétitions inclusives.', 'contact@handisport-normandie.fr', '', 'Favoriser la pratique sportive adaptée pour les personnes en situation de handicap en Normandie.', 'https://picsum.photos/seed/assoc22logo/300/300', 'https://picsum.photos/seed/assoc22banner/1200/400', '{\"name\":\"Fécamp\",\"coordinates\":[0.3743,49.7577],\"range\":15}');

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
(12, 1, 50, 'complete_profile', 'profil', NULL, '2025-03-26 01:51:34'),
(13, 1, 25, 'apply_association', 'association_12', NULL, '2025-03-26 12:44:15'),
(14, 1, 10, 'update_profile', 'profil', NULL, '2025-03-26 12:44:40'),
(15, 1, 25, 'apply_association', 'association_18', NULL, '2025-03-26 20:05:00'),
(16, 1, 10, 'update_profile', 'profil', NULL, '2025-03-26 20:05:25'),
(17, 6, 25, 'apply_association', 'association_22', NULL, '2025-03-26 21:39:06'),
(18, 6, 25, 'apply_association', 'association_13', NULL, '2025-03-26 21:39:17'),
(19, 6, 5, 'daily_login', 'login', NULL, '2025-03-26 21:39:21'),
(20, 6, 25, 'apply_association', 'association_2', NULL, '2025-03-26 23:00:04'),
(21, 6, 5, 'daily_login', 'login', NULL, '2025-03-26 23:04:11'),
(22, 1, 5, 'daily_login', 'login', NULL, '2025-03-26 23:31:12'),
(23, 1, 25, 'apply_association', 'association_2', NULL, '2025-03-26 23:53:28'),
(24, 6, 25, 'apply_association', 'association_3', NULL, '2025-03-27 00:17:33'),
(25, 6, 25, 'apply_association', 'association_12', NULL, '2025-03-27 00:22:06'),
(26, 6, 25, 'apply_association', 'association_7', NULL, '2025-03-27 00:29:20'),
(27, 6, 10, 'update_profile', NULL, NULL, '2025-03-27 00:31:41'),
(28, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:31:43'),
(29, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:31:43'),
(30, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:31:44'),
(31, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:31:45'),
(32, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:31:45'),
(33, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:31:45'),
(34, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:31:45'),
(35, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:31:45'),
(36, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:31:45'),
(37, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:31:45'),
(38, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:31:45'),
(39, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:31:46'),
(40, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:31:46'),
(41, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:31:46'),
(42, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:31:46'),
(43, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:31:46'),
(44, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:31:46'),
(45, 6, 10, 'update_profile', NULL, NULL, '2025-03-27 00:32:51'),
(46, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:52'),
(47, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:52'),
(48, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:52'),
(49, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:53'),
(50, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:53'),
(51, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:53'),
(52, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:53'),
(53, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:53'),
(54, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:53'),
(55, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:53'),
(56, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:53'),
(57, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:54'),
(58, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:54'),
(59, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:54'),
(60, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:54'),
(61, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:54'),
(62, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:54'),
(63, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:55'),
(64, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:55'),
(65, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:55'),
(66, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:55'),
(67, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:55'),
(68, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:55'),
(69, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:56'),
(70, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:56'),
(71, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:56'),
(72, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:56'),
(73, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:56'),
(74, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:56'),
(75, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:56'),
(76, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:57'),
(77, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:57'),
(78, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:57'),
(79, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:57'),
(80, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:57'),
(81, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:58'),
(82, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:58'),
(83, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:58'),
(84, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:59'),
(85, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:59'),
(86, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:59'),
(87, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:59'),
(88, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:32:59'),
(89, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:33:00'),
(90, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:33:00'),
(91, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:33:01'),
(92, 6, 25, 'apply_association', 'association_4', NULL, '2025-03-27 00:33:39'),
(93, 6, 5, 'daily_login', NULL, NULL, '2025-03-27 00:34:07'),
(94, 3, 25, 'apply_association', 'association_1', NULL, '2025-03-27 00:42:09'),
(95, 3, 25, 'apply_association', 'association_13', NULL, '2025-03-27 00:43:06'),
(96, 4, 5, 'daily_login', 'login', NULL, '2025-03-27 00:44:12'),
(97, 4, 10, 'update_profile', 'profil', NULL, '2025-03-27 00:44:25'),
(98, 4, 25, 'apply_association', 'association_1', NULL, '2025-03-27 01:06:50'),
(99, 4, 5, 'daily_login', NULL, NULL, '2025-03-27 01:15:13'),
(100, 4, 5, 'daily_login', NULL, NULL, '2025-03-27 01:15:16'),
(101, 4, 5, 'daily_login', NULL, NULL, '2025-03-27 01:15:16'),
(102, 4, 5, 'daily_login', NULL, NULL, '2025-03-27 01:15:16'),
(103, 4, 5, 'daily_login', NULL, NULL, '2025-03-27 01:15:16'),
(104, 4, 5, 'daily_login', NULL, NULL, '2025-03-27 01:15:17'),
(105, 4, 5, 'daily_login', NULL, NULL, '2025-03-27 01:15:17'),
(106, 4, 5, 'daily_login', NULL, NULL, '2025-03-27 01:15:17'),
(107, 4, 5, 'daily_login', NULL, NULL, '2025-03-27 01:15:17'),
(108, 4, 5, 'daily_login', NULL, NULL, '2025-03-27 01:15:18'),
(109, 4, 5, 'daily_login', NULL, NULL, '2025-03-27 01:15:18'),
(110, 4, 5, 'daily_login', NULL, NULL, '2025-03-27 01:15:19'),
(111, 1, 25, 'apply_association', 'association_21', NULL, '2025-03-27 01:53:10'),
(112, 1, 25, 'apply_association', 'association_9', NULL, '2025-03-27 02:19:54'),
(113, 1, 25, 'apply_association', 'association_11', NULL, '2025-03-27 02:37:38'),
(114, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:42'),
(115, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:43'),
(116, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:43'),
(117, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:43'),
(118, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:44'),
(119, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:44'),
(120, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:44'),
(121, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:44'),
(122, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:44'),
(123, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:44'),
(124, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:44'),
(125, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:45'),
(126, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:45'),
(127, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:45'),
(128, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:45'),
(129, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:45'),
(130, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:45'),
(131, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:45'),
(132, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:46'),
(133, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:46'),
(134, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:46'),
(135, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:46'),
(136, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:46'),
(137, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:46'),
(138, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:46'),
(139, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:46'),
(140, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:47'),
(141, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:47'),
(142, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:47'),
(143, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:47'),
(144, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:47'),
(145, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:47'),
(146, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:48'),
(147, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:48'),
(148, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:49'),
(149, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:49'),
(150, 1, 5, 'daily_login', NULL, NULL, '2025-03-27 02:37:50');

-- --------------------------------------------------------

--
-- Structure de la table `interests`
--

CREATE TABLE `interests` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

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
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `availability` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`availability`)),
  `volunteers_needed` int(11) DEFAULT 1,
  `volunteers_registered` int(11) DEFAULT 0,
  `skills_required` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`skills_required`)),
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `location` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`location`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `missions`
--

INSERT INTO `missions` (`mission_id`, `association_id`, `title`, `description`, `image_url`, `availability`, `volunteers_needed`, `volunteers_registered`, `skills_required`, `tags`, `created_at`, `updated_at`, `location`) VALUES
(2, 2, 'sdfsd', 'sdfsdf', 'https://picsum.photos/seed/8033/800/600', NULL, 20, 0, '[\"test\"]', '[\"test\"]', '2025-03-25 20:57:05', '2025-03-27 02:18:43', '{\"name\": \"Le Thuit de l\'Oison\", \"coordinates\": [0.9386, 49.2579]}'),
(3, 2, 'ça marche ?', 'stp dis moi que ça marche ', 'https://picsum.photos/seed/6219/800/600', NULL, 1, 0, '[\"permis b\", \"flop\"]', '[\"chevres\", \"cool\", \"nique\"]', '2025-03-25 22:02:03', '2025-03-27 02:18:43', '{\"name\": \"Perpignan\", \"coordinates\": [2.9045, 42.699]}'),
(4, 2, 'Dresseur', 'On cherche un dresseur la team', 'https://picsum.photos/seed/5998/800/600', NULL, 20, 0, '[\"dressage\"]', '[\"chevres\", \"cool\", \"nique\"]', '2025-03-25 22:15:24', '2025-03-27 02:18:43', '{\"name\": \"Elbeuf\", \"coordinates\": [0.9974, 49.2773]}'),
(5, 1, 'Distribution alimentaire', 'Participez à notre mission de distribution alimentaire hebdomadaire auprès des personnes défavorisées. Nous avons besoin de bénévoles pour trier, préparer et distribuer des colis alimentaires.', 'https://picsum.photos/seed/1333/800/600', '[{\"day\":\"Mer\",\"hours\":[\"14:00\",\"15:00\",\"16:00\",\"17:00\"]},{\"day\":\"Sam\",\"hours\":[\"9:00\",\"10:00\",\"11:00\",\"12:00\"]}]', 8, 3, '[\"Accueil\", \"Logistique\"]', '[\"Solidarité\", \"Social\", \"Précarité\"]', '2025-01-15 09:30:00', '2025-03-27 02:18:43', '{\"name\":\"Paris 18ème\",\"coordinates\":[2.3488,48.8925],\"range\":5}'),
(6, 2, 'Aide aux devoirs', 'Rejoignez notre équipe pour aider les enfants défavorisés avec leurs devoirs après l\'école. Une expérience enrichissante qui fait vraiment la différence pour ces jeunes.', 'https://picsum.photos/seed/5671/800/600', '[{\"day\":\"Lun\",\"hours\":[\"16:00\",\"17:00\",\"18:00\"]},{\"day\":\"Jeu\",\"hours\":[\"16:00\",\"17:00\",\"18:00\"]}]', 5, 2, '[\"Animation\", \"Pédagogie\"]', '[\"Éducation\", \"Jeunesse\", \"Social\"]', '2025-01-20 13:45:00', '2025-03-27 02:18:43', '{\"name\":\"Lyon 7ème\",\"coordinates\":[4.8467,45.7485],\"range\":3}'),
(7, 3, 'Nettoyage de plage', 'Participez à notre opération de nettoyage des plages. Nous collectons les déchets plastiques et autres détritus pour préserver notre environnement marin et nos plages.', 'https://picsum.photos/seed/5357/800/600', '[{\"day\":\"Sam\",\"hours\":[\"9:00\",\"10:00\",\"11:00\",\"12:00\",\"13:00\"]}]', 15, 8, '[\"Environnement\", \"Travail manuel\"]', '[\"Environnement\", \"Écologie\", \"Protection\"]', '2025-02-05 08:15:00', '2025-03-27 02:18:43', '{\"name\":\"Marseille Plages\",\"coordinates\":[5.3698,43.2965],\"range\":7}'),
(8, 4, 'Animation pour personnes âgées', 'Venez animer des ateliers pour nos seniors en EHPAD. Jeux de société, lecture, musique ou simplement discussion, votre présence leur apporte beaucoup de joie.', 'https://picsum.photos/seed/8772/800/600', '[{\"day\":\"Mar\",\"hours\":[\"14:00\",\"15:00\",\"16:00\"]},{\"day\":\"Ven\",\"hours\":[\"14:00\",\"15:00\",\"16:00\"]}]', 6, 3, '[\"Animation\", \"Relationnel\"]', '[\"Social\", \"Seniors\", \"Santé\"]', '2025-02-10 10:20:00', '2025-03-27 02:18:43', '{\"name\":\"Rennes\",\"coordinates\":[-1.6778,48.1173],\"range\":4}'),
(9, 5, 'Maraude solidaire', 'Participez à nos maraudes du soir pour venir en aide aux personnes sans-abri. Distribution de nourriture, boissons chaudes, kits d\'hygiène et échanges chaleureux.', 'https://picsum.photos/seed/8790/800/600', '[{\"day\":\"Lun\",\"hours\":[\"19:00\",\"20:00\",\"21:00\",\"22:00\"]},{\"day\":\"Jeu\",\"hours\":[\"19:00\",\"20:00\",\"21:00\",\"22:00\"]}]', 10, 6, '[\"Social\", \"Relationnel\", \"Logistique\"]', '[\"Solidarité\", \"Social\", \"Précarité\"]', '2025-01-25 17:30:00', '2025-03-27 02:18:43', '{\"name\":\"Lille Centre\",\"coordinates\":[3.0586,50.6292],\"range\":5}'),
(10, 6, 'Accompagnement de personnes handicapées', 'Devenez accompagnateur pour nos sorties avec des personnes en situation de handicap. Théâtre, musée, promenade, votre présence est essentielle pour rendre ces activités accessibles.', 'https://picsum.photos/seed/7635/800/600', '[{\"day\":\"Mer\",\"hours\":[\"13:00\",\"14:00\",\"15:00\",\"16:00\"]},{\"day\":\"Sam\",\"hours\":[\"13:00\",\"14:00\",\"15:00\",\"16:00\"]}]', 8, 2, '[\"Accompagnement\", \"Relationnel\", \"Patience\"]', '[\"Handicap\", \"Inclusion\", \"Loisirs\"]', '2025-02-15 14:00:00', '2025-03-27 02:18:43', '{\"name\":\"Bordeaux\",\"coordinates\":[-0.5795,44.8378],\"range\":6}'),
(11, 7, 'Soutien administratif', 'Nous recherchons des bénévoles pour aider à diverses tâches administratives: classement, saisie de données, préparation de dossiers. Votre aide est précieuse pour le bon fonctionnement de notre association.', 'https://picsum.photos/seed/1804/800/600', '[{\"day\":\"Mar\",\"hours\":[\"9:00\",\"10:00\",\"11:00\",\"12:00\"]},{\"day\":\"Jeu\",\"hours\":[\"9:00\",\"10:00\",\"11:00\",\"12:00\"]}]', 3, 1, '[\"Administratif\", \"Informatique\", \"Organisation\"]', '[\"Administratif\", \"Gestion\", \"Support\"]', '2025-01-18 09:00:00', '2025-03-27 02:18:43', '{\"name\":\"Nantes\",\"coordinates\":[-1.5536,47.2173],\"range\":3}'),
(12, 8, 'Animation d\'ateliers créatifs', 'Animez des ateliers créatifs pour enfants défavorisés: dessin, peinture, bricolage... Aidez-les à développer leur créativité et leur confiance en eux.', 'https://picsum.photos/seed/3117/800/600', '[{\"day\":\"Mer\",\"hours\":[\"14:00\",\"15:00\",\"16:00\",\"17:00\"]},{\"day\":\"Sam\",\"hours\":[\"10:00\",\"11:00\",\"12:00\"]}]', 5, 2, '[\"Animation\", \"Créativité\", \"Pédagogie\"]', '[\"Enfants\", \"Art\", \"Créativité\"]', '2025-02-08 12:45:00', '2025-03-27 02:18:43', '{\"name\":\"Toulouse\",\"coordinates\":[1.4442,43.6045],\"range\":5}'),
(13, 9, 'Collecte de dons', 'Participez à notre campagne de collecte de dons en supermarché. Nous collectons des denrées alimentaires, produits d\'hygiène et fournitures scolaires pour les plus démunis.', 'https://picsum.photos/seed/9174/800/600', '[{\"day\":\"Sam\",\"hours\":[\"9:00\",\"10:00\",\"11:00\",\"12:00\",\"13:00\",\"14:00\",\"15:00\",\"16:00\"]}]', 12, 5, '[\"Communication\", \"Relationnel\"]', '[\"Solidarité\", \"Collecte\", \"Social\"]', '2025-01-28 15:20:00', '2025-03-27 02:18:43', '{\"name\":\"Strasbourg\",\"coordinates\":[7.7521,48.5734],\"range\":6}'),
(14, 10, 'Soutien aux réfugiés', 'Venez soutenir les réfugiés dans leur intégration: cours de français, aide aux démarches administratives, accompagnement dans la recherche de logement et d\'emploi.', 'https://picsum.photos/seed/8517/800/600', '[{\"day\":\"Lun\",\"hours\":[\"14:00\",\"15:00\",\"16:00\"]},{\"day\":\"Mer\",\"hours\":[\"14:00\",\"15:00\",\"16:00\"]},{\"day\":\"Ven\",\"hours\":[\"14:00\",\"15:00\",\"16:00\"]}]', 7, 3, '[\"Langues\", \"Administratif\", \"Accompagnement\"]', '[\"Réfugiés\", \"Solidarité\", \"Intégration\"]', '2025-02-12 10:30:00', '2025-03-27 02:18:43', '{\"name\":\"Montpellier\",\"coordinates\":[3.8767,43.6108],\"range\":4}'),
(15, 11, 'Sensibilisation à l\'écologie', 'Rejoignez notre équipe pour sensibiliser le public aux enjeux écologiques. Animation de stands, ateliers pédagogiques, distribution de documentation sur les éco-gestes.', 'https://picsum.photos/seed/5063/800/600', '[{\"day\":\"Mer\",\"hours\":[\"14:00\",\"15:00\",\"16:00\",\"17:00\"]},{\"day\":\"Sam\",\"hours\":[\"10:00\",\"11:00\",\"12:00\",\"13:00\",\"14:00\",\"15:00\"]}]', 6, 2, '[\"Communication\", \"Pédagogie\", \"Environnement\"]', '[\"Environnement\", \"Sensibilisation\", \"Développement durable\"]', '2025-01-30 12:15:00', '2025-03-27 02:18:43', '{\"name\":\"Nice\",\"coordinates\":[7.2620,43.7102],\"range\":5}'),
(16, 12, 'Accueil de jour', 'Notre centre d\'accueil de jour pour personnes sans-abri recherche des bénévoles pour l\'accueil, la préparation et le service des repas, l\'animation d\'ateliers et l\'écoute.', 'https://picsum.photos/seed/7767/800/600', '[{\"day\":\"Lun\",\"hours\":[\"9:00\",\"10:00\",\"11:00\",\"12:00\",\"13:00\"]},{\"day\":\"Mar\",\"hours\":[\"9:00\",\"10:00\",\"11:00\",\"12:00\",\"13:00\"]},{\"day\":\"Mer\",\"hours\":[\"9:00\",\"10:00\",\"11:00\",\"12:00\",\"13:00\"]},{\"day\":\"Jeu\",\"hours\":[\"9:00\",\"10:00\",\"11:00\",\"12:00\",\"13:00\"]},{\"day\":\"Ven\",\"hours\":[\"9:00\",\"10:00\",\"11:00\",\"12:00\",\"13:00\"]}]', 9, 4, '[\"Accueil\", \"Restauration\", \"Écoute\"]', '[\"Précarité\", \"Solidarité\", \"Social\"]', '2025-02-03 08:45:00', '2025-03-27 02:18:43', '{\"name\":\"Dijon\",\"coordinates\":[5.0415,47.3220],\"range\":3}'),
(17, 13, 'Médiation culturelle', 'Devenez médiateur culturel dans notre musée associatif. Guidez les visiteurs, expliquez les œuvres, animez des ateliers pédagogiques pour les scolaires.', 'https://picsum.photos/seed/4645/800/600', '[{\"day\":\"Mer\",\"hours\":[\"14:00\",\"15:00\",\"16:00\",\"17:00\"]},{\"day\":\"Sam\",\"hours\":[\"10:00\",\"11:00\",\"12:00\",\"13:00\",\"14:00\",\"15:00\",\"16:00\",\"17:00\"]},{\"day\":\"Dim\",\"hours\":[\"10:00\",\"11:00\",\"12:00\",\"13:00\",\"14:00\",\"15:00\",\"16:00\",\"17:00\"]}]', 5, 1, '[\"Culture\", \"Communication\", \"Pédagogie\"]', '[\"Culture\", \"Art\", \"Éducation\"]', '2025-01-22 13:30:00', '2025-03-27 02:18:43', '{\"name\":\"Caen\",\"coordinates\":[-0.3703,49.1829],\"range\":4}'),
(18, 14, 'Entretien de jardins partagés', 'Participez à l\'entretien de nos jardins partagés: jardinage, compostage, animation d\'ateliers sur les techniques de culture bio. Une belle façon de créer du lien social.', 'https://picsum.photos/seed/7923/800/600', '[{\"day\":\"Mer\",\"hours\":[\"14:00\",\"15:00\",\"16:00\",\"17:00\"]},{\"day\":\"Sam\",\"hours\":[\"9:00\",\"10:00\",\"11:00\",\"12:00\"]}]', 10, 5, '[\"Jardinage\", \"Environnement\", \"Animation\"]', '[\"Environnement\", \"Jardinage\", \"Social\"]', '2025-02-18 09:15:00', '2025-03-27 02:18:43', '{\"name\":\"Grenoble\",\"coordinates\":[5.7245,45.1885],\"range\":5}'),
(19, 15, 'Assistance informatique', 'Aidez à réduire la fracture numérique en accompagnant des personnes peu familières avec l\'informatique: initiation à l\'ordinateur, Internet, démarches en ligne, messagerie.', 'https://picsum.photos/seed/6681/800/600', '[{\"day\":\"Mar\",\"hours\":[\"14:00\",\"15:00\",\"16:00\",\"17:00\"]},{\"day\":\"Jeu\",\"hours\":[\"14:00\",\"15:00\",\"16:00\",\"17:00\"]}]', 4, 1, '[\"Informatique\", \"Pédagogie\", \"Patience\"]', '[\"Numérique\", \"Formation\", \"Inclusion\"]', '2025-01-24 14:45:00', '2025-03-27 02:18:43', '{\"name\":\"Tours\",\"coordinates\":[0.6848,47.3941],\"range\":3}'),
(20, 16, 'Secourisme événementiel', 'Participez à nos dispositifs de secourisme lors d\'événements sportifs et culturels. Formation assurée pour les bénévoles non qualifiés en secourisme.', 'https://picsum.photos/seed/8639/800/600', '[{\"day\":\"Sam\",\"hours\":[\"9:00\",\"10:00\",\"11:00\",\"12:00\",\"13:00\",\"14:00\",\"15:00\",\"16:00\",\"17:00\"]},{\"day\":\"Dim\",\"hours\":[\"9:00\",\"10:00\",\"11:00\",\"12:00\",\"13:00\",\"14:00\",\"15:00\",\"16:00\",\"17:00\"]}]', 12, 6, '[\"Secourisme\", \"Santé\", \"Travail en équipe\"]', '[\"Santé\", \"Secourisme\", \"Événementiel\"]', '2025-02-06 11:00:00', '2025-03-27 02:18:43', '{\"name\":\"Angers\",\"coordinates\":[-0.5632,47.4784],\"range\":6}'),
(21, 17, 'Protection animale', 'Notre refuge pour animaux recherche des bénévoles pour diverses missions: soins aux animaux, promenades, entretien des locaux, aide aux adoptions, communication.', 'https://picsum.photos/seed/4152/800/600', '[{\"day\":\"Lun\",\"hours\":[\"9:00\",\"10:00\",\"11:00\",\"12:00\"]},{\"day\":\"Mar\",\"hours\":[\"9:00\",\"10:00\",\"11:00\",\"12:00\"]},{\"day\":\"Mer\",\"hours\":[\"9:00\",\"10:00\",\"11:00\",\"12:00\"]},{\"day\":\"Jeu\",\"hours\":[\"9:00\",\"10:00\",\"11:00\",\"12:00\"]},{\"day\":\"Ven\",\"hours\":[\"9:00\",\"10:00\",\"11:00\",\"12:00\"]},{\"day\":\"Sam\",\"hours\":[\"9:00\",\"10:00\",\"11:00\",\"12:00\"]}]', 15, 8, '[\"Animaux\", \"Soins\", \"Relationnel\"]', '[\"Animaux\", \"Protection\", \"Bien-être animal\"]', '2025-01-28 08:30:00', '2025-03-27 02:18:43', '{\"name\":\"Le Mans\",\"coordinates\":[0.1972,48.0061],\"range\":5}'),
(22, 18, 'Soutien scolaire', 'Apportez un soutien scolaire personnalisé à des enfants et adolescents en difficulté. Votre aide peut vraiment changer leur parcours et leur donner confiance en eux.', 'https://picsum.photos/seed/2842/800/600', '[{\"day\":\"Lun\",\"hours\":[\"16:00\",\"17:00\",\"18:00\"]},{\"day\":\"Mar\",\"hours\":[\"16:00\",\"17:00\",\"18:00\"]},{\"day\":\"Jeu\",\"hours\":[\"16:00\",\"17:00\",\"18:00\"]},{\"day\":\"Ven\",\"hours\":[\"16:00\",\"17:00\",\"18:00\"]}]', 7, 3, '[\"Pédagogie\", \"Patience\", \"Matières scolaires\"]', '[\"Éducation\", \"Jeunesse\", \"Social\"]', '2025-02-02 15:45:00', '2025-03-27 02:18:43', '{\"name\":\"Reims\",\"coordinates\":[4.0347,49.2583],\"range\":4}'),
(23, 19, 'Collecte alimentaire', 'Participez à nos collectes alimentaires mensuelles en supermarché pour approvisionner notre épicerie solidaire. Un geste simple qui aide de nombreuses familles en difficulté.', 'https://picsum.photos/seed/9757/800/600', '[{\"day\":\"Sam\",\"hours\":[\"9:00\",\"10:00\",\"11:00\",\"12:00\",\"13:00\",\"14:00\",\"15:00\",\"16:00\",\"17:00\",\"18:00\"]}]', 20, 12, '[\"Logistique\", \"Communication\", \"Relationnel\"]', '[\"Solidarité\", \"Alimentation\", \"Précarité\"]', '2025-01-20 10:00:00', '2025-03-27 02:18:43', '{\"name\":\"Brest\",\"coordinates\":[-4.4869,48.3904],\"range\":7}'),
(24, 20, 'Visite à domicile', 'Rendez visite à des personnes âgées isolées à leur domicile pour partager un moment de conversation, faire une promenade ou les accompagner dans de petites démarches.', 'https://picsum.photos/seed/3259/800/600', '[{\"day\":\"Lun\",\"hours\":[\"14:00\",\"15:00\",\"16:00\"]},{\"day\":\"Mar\",\"hours\":[\"14:00\",\"15:00\",\"16:00\"]},{\"day\":\"Mer\",\"hours\":[\"14:00\",\"15:00\",\"16:00\"]},{\"day\":\"Jeu\",\"hours\":[\"14:00\",\"15:00\",\"16:00\"]},{\"day\":\"Ven\",\"hours\":[\"14:00\",\"15:00\",\"16:00\"]}]', 10, 4, '[\"Écoute\", \"Relationnel\", \"Patience\"]', '[\"Seniors\", \"Lien social\", \"Solitude\"]', '2025-02-10 12:30:00', '2025-03-27 02:18:43', '{\"name\":\"Le Havre\",\"coordinates\":[0.1079,49.4944],\"range\":5}'),
(25, 21, 'Tri et valorisation de vêtements', 'Aidez-nous à trier, valoriser et préparer à la vente les vêtements donnés à notre ressourcerie. Une action concrète pour l\'économie circulaire et la solidarité.', 'https://picsum.photos/seed/4025/800/600', '[{\"day\":\"Mar\",\"hours\":[\"9:00\",\"10:00\",\"11:00\",\"12:00\",\"13:00\",\"14:00\",\"15:00\",\"16:00\"]},{\"day\":\"Jeu\",\"hours\":[\"9:00\",\"10:00\",\"11:00\",\"12:00\",\"13:00\",\"14:00\",\"15:00\",\"16:00\"]},{\"day\":\"Sam\",\"hours\":[\"9:00\",\"10:00\",\"11:00\",\"12:00\",\"13:00\",\"14:00\",\"15:00\",\"16:00\"]}]', 8, 3, '[\"Logistique\", \"Tri\", \"Organisation\"]', '[\"Économie circulaire\", \"Solidarité\", \"Recyclage\"]', '2025-01-15 13:20:00', '2025-03-27 02:18:43', '{\"name\":\"Clermont-Ferrand\",\"coordinates\":[3.0870,45.7772],\"range\":4}'),
(26, 22, 'Accompagnement de sorties culturelles', 'Accompagnez des enfants défavorisés ou des personnes en situation de handicap lors de sorties culturelles: musée, théâtre, cinéma, concert. Partagez avec eux ces moments enrichissants.', 'https://picsum.photos/seed/9348/800/600', '[{\"day\":\"Mer\",\"hours\":[\"13:00\",\"14:00\",\"15:00\",\"16:00\",\"17:00\"]},{\"day\":\"Sam\",\"hours\":[\"13:00\",\"14:00\",\"15:00\",\"16:00\",\"17:00\"]}]', 6, 2, '[\"Accompagnement\", \"Culture\", \"Relationnel\"]', '[\"Culture\", \"Inclusion\", \"Loisirs\"]', '2025-02-14 09:30:00', '2025-03-27 02:18:43', '{\"name\":\"Metz\",\"coordinates\":[6.1757,49.1193],\"range\":5}'),
(27, 1, 'Aide à la réinsertion professionnelle', 'Accompagnez des personnes en réinsertion dans leur recherche d\'emploi: rédaction de CV, préparation aux entretiens, aide à la recherche en ligne, conseil en orientation.', 'https://picsum.photos/seed/6667/800/600', '[{\"day\":\"Lun\",\"hours\":[\"9:00\",\"10:00\",\"11:00\",\"12:00\"]},{\"day\":\"Mer\",\"hours\":[\"9:00\",\"10:00\",\"11:00\",\"12:00\"]},{\"day\":\"Ven\",\"hours\":[\"9:00\",\"10:00\",\"11:00\",\"12:00\"]}]', 5, 1, '[\"Ressources Humaines\", \"Communication\", \"Numérique\"]', '[\"Emploi\", \"Insertion\", \"Formation\"]', '2025-01-25 09:15:00', '2025-03-27 02:18:43', '{\"name\":\"Perpignan\",\"coordinates\":[2.8967,42.6986],\"range\":4}'),
(28, 2, 'Organisation d\'événements solidaires', 'Participez à l\'organisation de nos événements solidaires: collectes de fonds, repas partagés, festivals, ventes de charité. De nombreuses compétences recherchées.', 'https://picsum.photos/seed/4289/800/600', '[{\"day\":\"Mar\",\"hours\":[\"18:00\",\"19:00\",\"20:00\"]},{\"day\":\"Jeu\",\"hours\":[\"18:00\",\"19:00\",\"20:00\"]}]', 8, 3, '[\"Événementiel\", \"Communication\", \"Organisation\"]', '[\"Événement\", \"Solidarité\", \"Collecte de fonds\"]', '2025-02-08 16:40:00', '2025-03-27 02:18:43', '{\"name\":\"Limoges\",\"coordinates\":[1.2611,45.8336],\"range\":6}'),
(29, 3, 'Entretien d\'espaces naturels', 'Participez à l\'entretien et la préservation d\'espaces naturels protégés: débroussaillage, plantation, aménagement de sentiers, recensement d\'espèces, nettoyage.', 'https://picsum.photos/seed/9444/800/600', '[{\"day\":\"Sam\",\"hours\":[\"9:00\",\"10:00\",\"11:00\",\"12:00\",\"13:00\",\"14:00\",\"15:00\"]}]', 15, 7, '[\"Environnement\", \"Travail manuel\", \"Botanique\"]', '[\"Environnement\", \"Biodiversité\", \"Nature\"]', '2025-01-18 07:30:00', '2025-03-27 02:18:43', '{\"name\":\"Besançon\",\"coordinates\":[6.0241,47.2378],\"range\":8}'),
(30, 4, 'Mentorat de jeunes entrepreneurs', 'Devenez mentor de jeunes entrepreneurs issus de quartiers défavorisés. Partagez votre expérience professionnelle, conseillez-les et accompagnez-les dans le développement de leur projet.', 'https://picsum.photos/seed/6356/800/600', '[{\"day\":\"Mar\",\"hours\":[\"17:00\",\"18:00\",\"19:00\"]},{\"day\":\"Jeu\",\"hours\":[\"17:00\",\"18:00\",\"19:00\"]}]', 6, 2, '[\"Entrepreneuriat\", \"Gestion\", \"Mentorat\"]', '[\"Entrepreneuriat\", \"Jeunesse\", \"Insertion\"]', '2025-02-04 17:15:00', '2025-03-27 02:18:43', '{\"name\":\"Orléans\",\"coordinates\":[1.9099,47.9029],\"range\":5}'),
(31, 5, 'Animation d\'ateliers numériques', 'Animez des ateliers numériques pour débutants: utilisation de l\'ordinateur, navigation internet, réseaux sociaux, démarches administratives en ligne, protection des données.', 'https://picsum.photos/seed/2449/800/600', '[{\"day\":\"Lun\",\"hours\":[\"14:00\",\"15:00\",\"16:00\"]},{\"day\":\"Mer\",\"hours\":[\"14:00\",\"15:00\",\"16:00\"]},{\"day\":\"Ven\",\"hours\":[\"14:00\",\"15:00\",\"16:00\"]}]', 4, 1, '[\"Informatique\", \"Pédagogie\", \"Communication\"]', '[\"Numérique\", \"Formation\", \"Inclusion\"]', '2025-01-30 14:20:00', '2025-03-27 02:18:43', '{\"name\":\"Mulhouse\",\"coordinates\":[7.3389,47.7508],\"range\":3}'),
(32, 6, 'Médiateur de rue', 'Devenez médiateur dans les quartiers prioritaires: prévention des conflits, dialogue avec les jeunes, orientation vers les services sociaux, organisation d\'activités.', 'https://picsum.photos/seed/1177/800/600', '[{\"day\":\"Mer\",\"hours\":[\"14:00\",\"15:00\",\"16:00\",\"17:00\",\"18:00\"]},{\"day\":\"Ven\",\"hours\":[\"18:00\",\"19:00\",\"20:00\",\"21:00\"]},{\"day\":\"Sam\",\"hours\":[\"14:00\",\"15:00\",\"16:00\",\"17:00\",\"18:00\"]}]', 7, 2, '[\"Médiation\", \"Social\", \"Communication\"]', '[\"Médiation\", \"Jeunesse\", \"Quartiers prioritaires\"]', '2025-02-12 15:00:00', '2025-03-27 02:18:43', '{\"name\":\"Rouen\",\"coordinates\":[1.0993,49.4432],\"range\":6}'),
(33, 7, 'Aide à la traduction', 'Aidez les migrants dans leurs démarches en tant que traducteur bénévole. De nombreuses langues recherchées pour faciliter la communication avec les services sociaux et administratifs.', 'https://picsum.photos/seed/6537/800/600', '[{\"day\":\"Lun\",\"hours\":[\"10:00\",\"11:00\",\"12:00\"]},{\"day\":\"Jeu\",\"hours\":[\"10:00\",\"11:00\",\"12:00\"]}]', 5, 0, '[\"Langues\", \"Communication\", \"Administratif\"]', '[\"Langues\", \"Migrants\", \"Administratif\"]', '2025-01-22 10:45:00', '2025-03-27 02:18:43', '{\"name\":\"Amiens\",\"coordinates\":[2.3022,49.8941],\"range\":4}');

-- --------------------------------------------------------

--
-- Structure de la table `postulation`
--

CREATE TABLE `postulation` (
  `postulation_id` int(10) UNSIGNED NOT NULL,
  `postulation_user_id_fk` int(10) UNSIGNED NOT NULL,
  `postulation_association_id_fk` int(10) UNSIGNED NOT NULL,
  `postulation_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `postulation`
--

INSERT INTO `postulation` (`postulation_id`, `postulation_user_id_fk`, `postulation_association_id_fk`, `postulation_date`) VALUES
(1, 4, 1, '2025-03-24'),
(5, 1, 1, '2025-03-17'),
(7, 1, 15, '2025-03-26'),
(8, 1, 16, '2025-03-26'),
(9, 7, 22, '2025-03-26'),
(10, 8, 1, '2025-03-26'),
(11, 8, 22, '2025-03-26'),
(12, 8, 15, '2025-03-26'),
(13, 8, 13, '2025-03-26'),
(14, 8, 18, '2025-03-26'),
(15, 8, 20, '2025-03-26'),
(16, 8, 21, '2025-03-26'),
(17, 1, 14, '2025-03-26'),
(18, 1, 17, '2025-03-26'),
(19, 1, 20, '2025-03-26'),
(20, 1, 19, '2025-03-26'),
(24, 6, 13, '2025-03-26'),
(28, 6, 2, '2025-03-27'),
(30, 1, 2, '2025-03-27'),
(31, 6, 3, '2025-03-27'),
(32, 6, 12, '2025-03-27'),
(33, 6, 7, '2025-03-27'),
(34, 6, 4, '2025-03-27'),
(36, 3, 1, '2025-03-27'),
(37, 3, 13, '2025-03-27'),
(41, 4, 1, '2025-03-27'),
(42, 1, 21, '2025-03-27'),
(43, 1, 9, '2025-03-27'),
(44, 1, 18, '2025-03-27'),
(45, 1, 11, '2025-03-27');

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

-- --------------------------------------------------------

--
-- Structure de la table `skills`
--

CREATE TABLE `skills` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `user_adress` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `user_mail` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_type` smallint(5) UNSIGNED NOT NULL,
  `user_tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `user_disponibility` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `user_profile_picture` varchar(255) NOT NULL DEFAULT 'assets/uploads/profile_pictures/default.webp',
  `user_bio` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`user_id`, `user_name`, `user_firstname`, `user_date`, `user_adress`, `user_mail`, `user_password`, `user_type`, `user_tags`, `user_disponibility`, `user_profile_picture`, `user_bio`) VALUES
(1, 'Cochard', 'Damien', '2025-02-28', '{\"name\":\"Lannion\",\"coordinates\":[-3.4587994,48.7322183],\"range\":100}', 'cochard.damien@gmail.com', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 0, '{\"interests\":[\"Art\",\"Voyages\",\"Cuisine\"],\"skills\":[\"HTML\",\"PHP\"]}', '[{\"day\":\"Lun\",\"hours\":[\"15:00\",\"16:00\",\"17:00\",\"18:00\"]},{\"day\":\"Mer\",\"hours\":[\"16:00\",\"17:00\"]},{\"day\":\"Jeu\",\"hours\":[\"20:00\"]},{\"day\":\"Ven\",\"hours\":[\"16:00\",\"17:00\"]},{\"day\":\"Sam\",\"hours\":[\"17:00\",\"18:00\",\"19:00\"]},{\"day\":\"Dim\",\"hours\":[\"17:00\",\"18:00\",\"19:00\"]}]', '../uploads/profile_pictures/67e45e056d0eb.jpg', 'Passionné par le volley, j\'apprécierais grandement participer dans des associations sportives afin de partager !!'),
(2, 'admin', 'admin', '2005-07-06', 'Elbeuf', 'baptiste.saegaert@gmail.com', '58ad983135fe15c5a8e2e15fb5b501aedcf70dc2', 3, NULL, NULL, 'assets/uploads/profile_pictures/1740733141_sfgsd.jpg', NULL),
(3, 'admin', 'SAEGAERT', '1960-05-09', 'Paris', 'baptiste.saegaert@couilles.com', '7c222fb2927d828af22f592134e8932480637c0d', 3, NULL, NULL, 'assets/uploads/profile_pictures/1740737077', NULL),
(4, 'admqsdqin', 'SAEGAERT', '2003-07-06', '{\"name\":\"Elbeuf\",\"coordinates\":[0.9974,49.2773],\"range\":1}', 'baptistsdfqe.saegaert@gmail.com', '7c222fb2927d828af22f592134e8932480637c0d', 0, NULL, NULL, 'assets/uploads/profile_pictures/1740737524', 'wxcwc'),
(5, 'Wesley', 'PUTMAN', '2005-02-13', '{\"name\":\"Martot\",\"coordinates\":[1.0686,49.28],\"range\":10}', 'wesleyputmant@gmail.com', '2aba7190e9c90d9d8f8532c98928b7e514312af2', 0, NULL, NULL, 'assets/uploads/profile_pictures/1740737719', NULL),
(6, 'Alexandre', 'Lacaille', '2005-11-20', '{\"name\":\"Igoville\",\"coordinates\":[1.1477524,49.3181799],\"range\":40}', 'alexandre.lacaille@gmail.com', '7c222fb2927d828af22f592134e8932480637c0d', 0, '{\"interests\":[\"Cuisine\",\"Lecture\",\"Musique\"],\"skills\":[\"Communication\"]}', '[{\"day\":\"Lun\",\"hours\":[\"9:00\",\"10:00\",\"11:00\",\"12:00\",\"13:00\",\"14:00\",\"15:00\",\"16:00\",\"17:00\",\"18:00\",\"19:00\"]},{\"day\":\"Mer\",\"hours\":[\"11:00\",\"12:00\",\"13:00\",\"14:00\",\"15:00\",\"16:00\",\"18:00\",\"19:00\"]},{\"day\":\"Ven\",\"hours\":[\"19:00\"]},{\"day\":\"Dim\",\"hours\":[\"10:00\",\"11:00\",\"12:00\",\"13:00\",\"14:00\",\"15:00\",\"16:00\",\"17:00\",\"18:00\",\"19:00\"]}]', '../uploads/profile_pictures/67e322c74d6cc.jpg', 'Salut c\'est moi !'),
(7, 'julien', 'legallais', '2005-07-16', '{\"name\":\"Bolbec\",\"coordinates\":[0.4742448,49.5739091],\"range\":30}', 'julien.legallais@gmail.com', '7c222fb2927d828af22f592134e8932480637c0d', 0, '{\"interests\":[\"Sport\"],\"skills\":[\"CSS\"]}', '[{\"day\":\"Lun\",\"hours\":[\"12:00\",\"13:00\"]},{\"day\":\"Mar\",\"hours\":[\"11:00\",\"12:00\",\"13:00\",\"14:00\"]},{\"day\":\"Mer\",\"hours\":[\"12:00\"]},{\"day\":\"Jeu\",\"hours\":[\"12:00\",\"13:00\"]},{\"day\":\"Ven\",\"hours\":[\"12:00\",\"13:00\"]},{\"day\":\"Sam\",\"hours\":[\"11:00\",\"12:00\",\"13:00\"]},{\"day\":\"Dim\",\"hours\":[\"12:00\",\"13:00\"]}]', '../uploads/profile_pictures/67e341e8abc92.jpg', 'Ouais les gars'),
(8, 'Enry', 'Dubuc', '2005-12-27', '{\"name\":\"Val-de-Reuil\",\"coordinates\":[1.2128992,49.2716839],\"range\":15}', 'enry.dubuc@gmail.com', '7c222fb2927d828af22f592134e8932480637c0d', 0, '{\"interests\":[\"Musique\",\"Voyages\",\"Cuisine\",\"Art\"],\"skills\":[\"HTML\",\"JavaScript\",\"PHP\"]}', NULL, '../uploads/profile_pictures/67e354c662c97.jpg', 'Ouais2');

-- --------------------------------------------------------

--
-- Structure de la table `user_experience`
--

CREATE TABLE `user_experience` (
  `experience_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `total_points` int(10) UNSIGNED DEFAULT 0,
  `current_level` int(10) UNSIGNED DEFAULT 1,
  `points_to_next_level` int(10) UNSIGNED DEFAULT 100,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user_experience`
--

INSERT INTO `user_experience` (`experience_id`, `user_id`, `total_points`, `current_level`, `points_to_next_level`, `last_updated`) VALUES
(1, 8, 70, 1, 100, '2025-03-26 01:13:26'),
(2, 1, 520, 4, 1000, '2025-03-26 01:37:05'),
(3, 6, 525, 4, 1000, '2025-03-26 21:39:06'),
(4, 3, 50, 1, 100, '2025-03-27 00:42:09'),
(5, 4, 100, 2, 250, '2025-03-27 00:44:12');

-- --------------------------------------------------------

--
-- Structure de la table `user_rewards`
--

CREATE TABLE `user_rewards` (
  `user_reward_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `reward_id` int(10) UNSIGNED NOT NULL,
  `acquired_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `progress` int(10) UNSIGNED DEFAULT 0,
  `is_displayed` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  ADD PRIMARY KEY (`association_id`);

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
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `association`
--
ALTER TABLE `association`
  MODIFY `association_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT pour la table `experience_transactions`
--
ALTER TABLE `experience_transactions`
  MODIFY `transaction_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

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
  MODIFY `mission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT pour la table `postulation`
--
ALTER TABLE `postulation`
  MODIFY `postulation_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

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
  MODIFY `experience_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `user_rewards`
--
ALTER TABLE `user_rewards`
  MODIFY `user_reward_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `experience_transactions`
--
ALTER TABLE `experience_transactions`
  ADD CONSTRAINT `experience_transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `postulation`
--
ALTER TABLE `postulation`
  ADD CONSTRAINT `postulation_association_id_fk` FOREIGN KEY (`postulation_association_id_fk`) REFERENCES `association` (`association_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `postulation_user_id_fk` FOREIGN KEY (`postulation_user_id_fk`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `user_experience`
--
ALTER TABLE `user_experience`
  ADD CONSTRAINT `fk_user_exp_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

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
