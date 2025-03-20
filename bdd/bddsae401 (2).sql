-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 20 mars 2025 à 10:54
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
(1, 'Les Restaurants du Cœur', '	339 863 417 00418', '2025-03-04', 'Les Restaurants du Cœur – Les Relais du Cœur, connus sous le nom de Les Restos du Cœura, sont une association loi de 1901 à but non lucratif et reconnue d\'utilité publique, créée en France par l\'humoriste et comédien Michel Colucci (dit Coluche) en 1985.', 'contact@restosducoeur.org', 'b1c26a34f1bf8a52f681edd097c20580c617b788', 'Les Restos du Cœur ont pour mission d\'apporter une assistance bénévole aux personnes en difficulté, que ce soit dans le domaine alimentaire, par l\'accès à des repas gratuits, ou dans le domaine de l\'insertion sociale et économique, par tout moyen appropri', 'assets/uploads/profile_pictures/default.webp	', 'assets/uploads/background_image/defaultAssociation.jpg	', NULL),
(2, 'Solidarité & Co', '123456789', '2015-06-12', 'Association caritative d\'entraide.', 'contact@solidariteco.fr', 'hashed_password_1', 'Aider les personnes en difficulté', 'assets/uploads/profile_pictures/default.webp', 'assets/uploads/background_image/defaultAssociation.jpg', '10 rue de la Paix, Paris'),
(3, 'Les Artistes Libres', '987654321', '2012-03-25', 'Promotion des arts et des artistes locaux.', 'info@artisteslibres.fr', 'hashed_password_2', 'Soutenir la culture et l\'art local', 'assets/uploads/profile_pictures/default.webp', 'assets/uploads/background_image/defaultAssociation.jpg', '22 avenue Montmartre, Paris'),
(4, 'EcoAvenir', '192837465', '2018-09-30', 'Protection de l\'environnement et actions écologiques.', 'contact@ecoavenir.org', 'hashed_password_3', 'Sensibiliser à l\'écologie', 'assets/uploads/profile_pictures/default.webp', 'assets/uploads/background_image/defaultAssociation.jpg', '5 rue des Forêts, Lyon'),
(5, 'Tech4All', '564738291', '2020-07-15', 'Accès à la technologie pour tous.', 'contact@tech4all.com', 'hashed_password_4', 'Réduire la fracture numérique', 'assets/uploads/profile_pictures/default.webp', 'assets/uploads/background_image/defaultAssociation.jpg', '12 boulevard du Numérique, Toulouse'),
(6, 'Sport Ensemble', '748392615', '2016-11-05', 'Encourager le sport pour tous.', 'contact@sportensemble.fr', 'hashed_password_5', 'Promouvoir le sport amateur', 'assets/uploads/profile_pictures/default.webp', 'assets/uploads/background_image/defaultAssociation.jpg', '8 rue des Champions, Marseille'),
(7, 'Les Gourmets Solidaires', '856471293', '2019-04-22', 'Lutte contre le gaspillage alimentaire.', 'contact@gourmetsolidaires.fr', 'hashed_password_6', 'Redistribuer les surplus alimentaires', 'assets/uploads/profile_pictures/default.webp', 'assets/uploads/background_image/defaultAssociation.jpg', '33 place des Saveurs, Bordeaux'),
(8, 'Code Pour Tous', '147258369', '2017-01-18', 'Formation au code informatique pour tous.', 'contact@codepourtous.fr', 'hashed_password_7', 'Apprendre à coder pour mieux réussir', 'assets/uploads/profile_pictures/default.webp', 'assets/uploads/background_image/defaultAssociation.jpg', '19 rue du Savoir, Lille'),
(9, 'Refuge Animalier', '369852147', '2014-08-10', 'Sauvetage et adoption des animaux abandonnés.', 'contact@refugeanimalier.fr', 'hashed_password_8', 'Protéger et sauver les animaux', 'assets/uploads/profile_pictures/default.webp', 'assets/uploads/background_image/defaultAssociation.jpg', '7 chemin des Animaux, Nice'),
(10, 'Musique & Partage', '951753468', '2013-05-29', 'Favoriser l\'apprentissage de la musique.', 'contact@musiquepartage.fr', 'hashed_password_9', 'Rendre la musique accessible à tous', 'assets/uploads/profile_pictures/default.webp', 'assets/uploads/background_image/defaultAssociation.jpg', '3 rue du Conservatoire, Strasbourg'),
(11, 'Avenir Durable', '258369147', '2021-02-14', 'Développement durable et énergies renouvelables.', 'contact@avenirdurable.fr', 'hashed_password_10', 'Promouvoir les énergies renouvelables', 'assets/uploads/profile_pictures/default.webp', 'assets/uploads/background_image/defaultAssociation.jpg', '50 avenue Verte, Nantes'),
(12, 'Solidarité Normande', '123456789', '2010-06-15', '', 'contact@solidarite-normande.fr', '', 'Aide aux personnes en difficulté dans la région normande.', 'assets/uploads/profile_pictures/default.webp', '	assets/uploads/background_image/defaultAssociation.webp', '{\"coordinates\":[1.0083,49.2889],\"range\":10}'),
(13, 'Les Amis de Rouen', '987654321', '2005-03-12', '', 'contact@amisderouen.fr', '', 'Soutien aux initiatives culturelles et sociales à Rouen.', 'assets/uploads/profile_pictures/default.webp', '	assets/uploads/background_image/defaultAssociation.jpg	', '{\"name\":\"Rouen\",\"coordinates\":[1.0993,49.4432],\"range\":15}'),
(14, 'Nature et Environnement Caen', '112233445', '2012-08-23', '', 'contact@nature-caen.fr', '', 'Protection de l’environnement et sensibilisation à l’écologie.', 'assets/uploads/profile_pictures/default.webp', '	assets/uploads/background_image/defaultAssociation.jpg	', '{\"name\":\"Caen\",\"coordinates\":[-0.3707,49.1829],\"range\":20}'),
(15, 'Havre Solidaire', '556677889', '2018-06-30', '', 'contact@havre-solidaire.fr', '', 'Aide aux sans-abris et distribution alimentaire.', 'assets/uploads/profile_pictures/default.webp', '	assets/uploads/background_image/defaultAssociation.jpg	', '{\"name\":\"Le Havre\",\"coordinates\":[0.1077,49.4944],\"range\":10}'),
(16, 'Patrimoine Normand', '332211445', '1998-09-10', '', 'contact@patrimoine-normand.fr', '', 'Préservation du patrimoine historique et architectural en Normandie.', 'assets/uploads/profile_pictures/default.webp', '	assets/uploads/background_image/defaultAssociation.jpg	', '{\"name\":\"Évreux\",\"coordinates\":[1.1500,49.0260],\"range\":10}'),
(17, 'Enfants du Cotentin', '778899665', '2015-05-15', '', 'contact@enfants-cotentin.fr', '', 'Aide aux enfants défavorisés et accès à l’éducation.', 'assets/uploads/profile_pictures/default.webp', '	assets/uploads/background_image/defaultAssociation.jpg	', '{\"name\":\"Cherbourg\",\"coordinates\":[-1.6356,49.6394],\"range\":25}'),
(18, 'Fédération Sportive Normande', '998877665', '2009-11-05', '', 'contact@sport-normandie.fr', '', 'Développement du sport amateur en Normandie.', 'assets/uploads/profile_pictures/default.webp', '	assets/uploads/background_image/defaultAssociation.jpg	', '{\"name\":\"Alençon\",\"coordinates\":[0.0931,48.4336],\"range\":30}'),
(19, 'Agriculture Bio Manche', '554433221', '2017-07-18', '', 'contact@bio-manche.fr', '', 'Promotion de l’agriculture biologique dans la Manche.', 'assets/uploads/profile_pictures/default.webp', '	assets/uploads/background_image/defaultAssociation.jpg	', '{\"name\":\"Saint-Lô\",\"coordinates\":[-1.0903,49.1141],\"range\":20}'),
(20, 'Arts et Cultures Normandes', '445566778', '2013-04-22', '', 'contact@art-culture-normandie.fr', '', 'Valorisation des arts et traditions normandes.', 'assets/uploads/profile_pictures/default.webp', '	assets/uploads/background_image/defaultAssociation.jpg	', '{\"name\":\"Lisieux\",\"coordinates\":[0.2290,49.1463],\"range\":15}'),
(21, 'Protection Animale Normandie', '887766554', '2006-02-28', '', 'contact@protectionanimaux.fr', '', 'Rescue et soins aux animaux abandonnés en Normandie.', 'assets/uploads/profile_pictures/default.webp', '	assets/uploads/background_image/defaultAssociation.jpg	', '{\"name\":\"Dieppe\",\"coordinates\":[1.0789,49.9229],\"range\":10}'),
(22, 'Handisport Normand', '332244556', '2010-10-17', '', 'contact@handisport-normandie.fr', '', 'Développement du sport pour les personnes en situation de handicap.', 'assets/uploads/profile_pictures/default.webp', '	assets/uploads/background_image/defaultAssociation.jpg	', '{\"name\":\"Fécamp\",\"coordinates\":[0.3743,49.7577],\"range\":15}');

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
(5, 1, 1, '2025-03-17');

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
(1, 'Cochard', 'Damien', '2025-02-28', '{\"name\":\"Martot\",\"coordinates\":[1.0686,49.28],\"range\":10}', 'cochard.damien@gmail.com', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 0, NULL, NULL, 'assets/uploads/profile_pictures/default.webp', 'Passionné par le volley, j\'apprécierais grandement participer dans des associations sportives afin de partager !'),
(2, 'admin', 'admin', '2005-07-06', 'Elbeuf', 'baptiste.saegaert@gmail.com', '58ad983135fe15c5a8e2e15fb5b501aedcf70dc2', 3, NULL, NULL, 'assets/uploads/profile_pictures/1740733141_sfgsd.jpg', NULL),
(3, 'admin', 'SAEGAERT', '1960-05-09', 'Paris', 'baptiste.saegaert@couilles.com', '7c222fb2927d828af22f592134e8932480637c0d', 3, NULL, NULL, 'assets/uploads/profile_pictures/1740737077', NULL),
(4, 'admqsdqin', 'SAEGAERT', '2003-07-06', '{\"name\":\"Elbeuf\",\"coordinates\":[0.9974,49.2773],\"range\":1}', 'baptistsdfqe.saegaert@gmail.com', '7c222fb2927d828af22f592134e8932480637c0d', 0, NULL, NULL, 'assets/uploads/profile_pictures/1740737524', NULL),
(5, 'Wesley', 'PUTMAN', '2005-02-13', '{\"name\":\"Martot\",\"coordinates\":[1.0686,49.28],\"range\":10}', 'wesleyputmant@gmail.com', '2aba7190e9c90d9d8f8532c98928b7e514312af2', 0, NULL, NULL, 'assets/uploads/profile_pictures/1740737719', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `association`
--
ALTER TABLE `association`
  ADD PRIMARY KEY (`association_id`);

--
-- Index pour la table `postulation`
--
ALTER TABLE `postulation`
  ADD PRIMARY KEY (`postulation_id`),
  ADD KEY `postulation_user_id_fk` (`postulation_user_id_fk`),
  ADD KEY `postulation_association_id_fk` (`postulation_association_id_fk`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `association`
--
ALTER TABLE `association`
  MODIFY `association_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT pour la table `postulation`
--
ALTER TABLE `postulation`
  MODIFY `postulation_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `postulation`
--
ALTER TABLE `postulation`
  ADD CONSTRAINT `postulation_association_id_fk` FOREIGN KEY (`postulation_association_id_fk`) REFERENCES `association` (`association_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `postulation_user_id_fk` FOREIGN KEY (`postulation_user_id_fk`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
