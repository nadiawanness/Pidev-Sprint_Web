-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  mer. 10 mars 2021 à 11:43
-- Version du serveur :  5.7.26
-- Version de PHP :  7.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `sprint1`
--

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

DROP TABLE IF EXISTS `categorie`;
CREATE TABLE IF NOT EXISTS `categorie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `help` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`id`, `nom`, `photo`, `help`) VALUES
(1, 'Esprit Ingénieur', '4b56bf7aa8a9bebb8aa0258d1e0c37a2.jpeg', 'tic');

-- --------------------------------------------------------

--
-- Structure de la table `comment`
--

DROP TABLE IF EXISTS `comment`;
CREATE TABLE IF NOT EXISTS `comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `offre_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_9474526C4CC8505A` (`offre_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `comment`
--

INSERT INTO `comment` (`id`, `author_name`, `content`, `created_at`, `offre_id`) VALUES
(9, '2', '157', '2021-03-07 20:31:46', 6),
(10, 'ben chikha', 'ahla', '2021-03-07 20:52:18', 6),
(11, 'mohamedali.benchikha@esprit.tn', 'aaa', '2021-03-07 21:23:57', 6),
(12, 'mohamedali.benchikha@esprit.tn', 'mmm', '2021-03-07 22:17:13', 6),
(13, 'mohamedali.benchikha@esprit.tn', '123', '2021-03-07 22:43:59', 7),
(15, '10', 'hello', '2021-03-10 11:09:07', 6);

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20210309011815', '2021-03-09 01:19:26', 460);

-- --------------------------------------------------------

--
-- Structure de la table `offre`
--

DROP TABLE IF EXISTS `offre`;
CREATE TABLE IF NOT EXISTS `offre` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `idcategoriy_id` int(11) DEFAULT NULL,
  `abn` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_AF86866FE0957003` (`idcategoriy_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `offre`
--

INSERT INTO `offre` (`id`, `nom`, `email`, `logo`, `title`, `description`, `idcategoriy_id`, `abn`) VALUES
(6, 'facebook', 'contact@fb.com', '60d194ca24557e3ddd92afcbe52ef32c.png', 'Development Java', 'aaa', 1, 285),
(7, 'LinkeInd', 'contact@linkeind.com', 'ca21a434272ec60b1d9c127c57225cdd.png', 'Development Web', 'aa', 1, 4);

-- --------------------------------------------------------

--
-- Structure de la table `postuler`
--

DROP TABLE IF EXISTS `postuler`;
CREATE TABLE IF NOT EXISTS `postuler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `offre_id` int(11) DEFAULT NULL,
  `recruteur_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8EC5A68D4CC8505A` (`offre_id`),
  KEY `IDX_8EC5A68DBB0859F1` (`recruteur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `postuler`
--

INSERT INTO `postuler` (`id`, `offre_id`, `recruteur_id`) VALUES
(31, 6, 4),
(34, 6, 3);

-- --------------------------------------------------------

--
-- Structure de la table `recherche`
--

DROP TABLE IF EXISTS `recherche`;
CREATE TABLE IF NOT EXISTS `recherche` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `recruteur`
--

DROP TABLE IF EXISTS `recruteur`;
CREATE TABLE IF NOT EXISTS `recruteur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomsociete` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adresse` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mail` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numsociete` int(11) DEFAULT NULL,
  `mdp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `competence` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `recruteur`
--

INSERT INTO `recruteur` (`id`, `nom`, `prenom`, `nomsociete`, `adresse`, `mail`, `numsociete`, `mdp`, `type`, `photo`, `competence`) VALUES
(1, '1', '1', '1', '1', '1', 1, '1', 'recruteur', NULL, NULL),
(3, '2', '2', '2', '2', '2', 2, '2', 'candidat', NULL, NULL),
(4, 'ben chikha', 'mohamed ali', 'a', 'a', 'mohamedali.benchikha@esprit.tn', 1, '1', 'candidat', NULL, NULL),
(5, '9', '9', '9', '9', '9', 9, '9', 'recruteur', '4e31905d34702c2905e0a7b29d521e92.png', '9'),
(6, '9', '9', '9', '9', '9', 9, '9', 'recruteur', 'a64488f99a3bee79ff6a3081b90956b0.png', '9'),
(7, '10', '10', NULL, '10', '10', NULL, '10', 'candidat', 'e5cd8bf265cd138f638e166a69527fc4.png', 'ahlan');

-- --------------------------------------------------------

--
-- Structure de la table `recruteur_offre`
--

DROP TABLE IF EXISTS `recruteur_offre`;
CREATE TABLE IF NOT EXISTS `recruteur_offre` (
  `recruteur_id` int(11) NOT NULL,
  `offre_id` int(11) NOT NULL,
  PRIMARY KEY (`recruteur_id`,`offre_id`),
  KEY `IDX_6727AA7BBB0859F1` (`recruteur_id`),
  KEY `IDX_6727AA7B4CC8505A` (`offre_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `recruteur_offre`
--

INSERT INTO `recruteur_offre` (`recruteur_id`, `offre_id`) VALUES
(1, 6),
(1, 7);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `FK_9474526C4CC8505A` FOREIGN KEY (`offre_id`) REFERENCES `offre` (`id`);

--
-- Contraintes pour la table `postuler`
--
ALTER TABLE `postuler`
  ADD CONSTRAINT `FK_8EC5A68D4CC8505A` FOREIGN KEY (`offre_id`) REFERENCES `offre` (`id`),
  ADD CONSTRAINT `FK_8EC5A68DBB0859F1` FOREIGN KEY (`recruteur_id`) REFERENCES `recruteur` (`id`);

--
-- Contraintes pour la table `recruteur_offre`
--
ALTER TABLE `recruteur_offre`
  ADD CONSTRAINT `FK_6727AA7B4CC8505A` FOREIGN KEY (`offre_id`) REFERENCES `offre` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_6727AA7BBB0859F1` FOREIGN KEY (`recruteur_id`) REFERENCES `recruteur` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
