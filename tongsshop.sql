-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 13 déc. 2024 à 11:45
-- Version du serveur : 8.2.0
-- Version de PHP : 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `tongsshop`
--

-- --------------------------------------------------------

--
-- Structure de la table `articles`
--

DROP TABLE IF EXISTS `articles`;
CREATE TABLE IF NOT EXISTS `articles` (
  `id_art` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `quantite` int NOT NULL,
  `prix` float NOT NULL,
  `url_photo` varchar(255) DEFAULT NULL,
  `description` text,
  `date_ajout` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ID_STRIPE` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_art`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `articles`
--

INSERT INTO `articles` (`id_art`, `nom`, `quantite`, `prix`, `url_photo`, `description`, `date_ajout`, `ID_STRIPE`) VALUES
(1, 'Tongs Basiques Bleues', 96, 25, 'images/img_001.png', 'Tongs bleues parfaites pour la plage. Taille disponible : 30 à 55. \r\nMatériau : Caoutchouc de haute qualité.', '2024-09-18 13:17:27', 'price_1QT0elA4OZx7fcKBEWKvbWxy'),
(2, 'Tongs Basiques Rouges', 200, 25, 'images/img_002.png', 'Tongs rouges parfaites pour la plage. Taille disponible : 30 à 55. \r\nMatériau : Caoutchouc de haute qualité.', '2024-09-18 13:17:27', 'price_1QT0fJA4OZx7fcKBWHmSmrZY'),
(3, 'Tongs Basiques Vertes', 99, 25, 'images/img_003.png', 'Tongs vertes parfaites pour la plage. Taille disponible : 30 à 55. \r\nMatériau : Caoutchouc de haute qualité.', '2024-09-18 13:17:27', 'price_1QT12pA4OZx7fcKBUKCHucoo'),
(4, 'Claquettes Olympique de Marseille', 100, 30, 'images/img_004.png', 'Claquettes officielles de l\'Olympique de Marseille, confortables et parfaites pour les fans.', '2024-09-23 12:17:27', 'price_1QT13gA4OZx7fcKB8Nqo8WfD'),
(5, 'Claquettes Yeezy', 100, 150, 'images/img_005.png', 'Claquettes Yeezy, ultra-confortables et tendance, vous allez faire fureur !', '2024-09-23 13:17:27', 'price_1QT14NA4OZx7fcKBjLos5FqH'),
(6, 'Tongs Brésil', 77, 35, 'images/img_006.png', 'Des tongs stylées inspirées des plages brésiliennes, parfaites pour un look décontracté.', '2024-09-24 09:23:38', 'price_1QT158A4OZx7fcKBZtFNS6aA'),
(7, 'Reef Fanning', 96, 45, 'images/img_007.png', 'Tongs Reef Fanning avec semelle confortable et décapsuleur intégré, parfaites pour la plage.', '2024-09-27 13:06:58', 'price_1QT16XA4OZx7fcKBC7tydesY'),
(8, 'Tongs Hawaii', 0, 20, 'images/img_008.png', 'Des tongs colorées et résistantes, parfaites pour les vacances tropicales.', '2024-10-24 13:07:50', 'price_1QT17SA4OZx7fcKB01QiiKEV'),
(9, 'Tongs Gladiator', 89, 35, 'images/img_009.png', 'Des tongs inspirées par les sandales gladiateurs, confortables et stylées.', '2024-10-24 13:07:50', 'price_1QT18CA4OZx7fcKBa6nOAXkZ'),
(10, 'Tongs Cozy', 0, 25, 'images/img_010.png', 'Tongs en mousse à mémoire de forme pour un confort ultime.', '2024-10-24 13:07:50', 'price_1QT18hA4OZx7fcKBSrqVfinF');

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `id_client` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `adresse` text NOT NULL,
  `numero` int NOT NULL,
  `mail` varchar(255) NOT NULL,
  `mdp` varchar(255) NOT NULL,
  `date_inscription` datetime DEFAULT CURRENT_TIMESTAMP,
  `ID_STRIPE` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_client`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `clients`
--

INSERT INTO `clients` (`id_client`, `nom`, `prenom`, `adresse`, `numero`, `mail`, `mdp`, `date_inscription`, `ID_STRIPE`) VALUES
(1, 'combes-aguéra', 'anthony', '19 route du nord', 614643547, 'acombesaguera@gmail.com', '$2y$10$Yt.tDJDA9gyjSVyYAXZx4utyFppqwO6YSF4CxgvHd0KLyoY1ng1OK', '2024-09-27 14:55:59', NULL),
(2, 'combes', 'michel ', '19 rue michel colucci', 777465093, 'mcombes@sfr.fr', '$2y$10$mS85VTvseZnQRNPmLyp9heMg.kHoXH.U/HuW6ayufOEbsGTccYSX6', '2024-09-27 14:57:43', NULL),
(3, 'c-a', '\'', '15sf ', 614643547, 'a@g.com', '$2y$10$7hvetuyEZHREuOpFLLe.A.MInejQOj7NQFjEvhM9uVyrnGrA2GO.O', '2024-10-11 09:34:44', NULL),
(4, 'a', 'a', 'a', 615354795, 'a@a.fr', '$2y$10$dnERfmOrv.ur9AuWM9acfuJLY1zPEayloyZJjsb/mnBEjdAj583we', '2024-10-11 14:13:51', 'cus_RMAdnmznHdqF21'),
(5, 'LAUGE', 'Majandra', '54 rue des tournesols Montpellier', 612645286, 'majandra.l@gmail.com', '$2y$10$rqBzspqyxwj6/L7T06tclOSj9oIg9UShsWCgGHPrbc4hl2FG.IclW', '2024-10-12 10:58:10', NULL),
(6, 'Combes-Aguéra', 'Anthony', 'Caca', 614643547, 'a@aa.fr', '$2y$10$Ultk/YCwnqvwmonXMP/sHe90f0sQxKENbIlVo/5Pb59UcwM3Gpm7S', '2024-10-14 16:35:00', NULL),
(7, 'a', 'a', 'a', 2147483647, 'a@a.fr', '$2y$10$.qyv/PCIMjjBc2Fp5f12OOcxJHhbQwif7Zf1KcZtyY1iiTQRcbioe', '2024-10-15 11:39:27', NULL),
(8, 'a', 'a', 'a', 615354789, 'a@aaa.fr', '$2y$10$OVNiOmTgGKtsNE.4ulGVqO6rWQXLvlKej4wE37hDPscQ8SVLb9VW2', '2024-10-15 12:29:51', NULL),
(9, 'a', 'a', 'a', 0, 'an@a.fr', '$2y$10$NPGgJbDroqdDYocWp8zaEu1K5NStBBVfV3zyH4U3KdBSjMExqex9K', '2024-10-15 12:35:14', NULL),
(10, 'Laugé', 'Majandra', '89 rue du soleil', 612869458, 'm.lauge@gmail.com', '$2y$10$UKAoJKj7b08aLObsWqMAfO23in2HXuH4ONKjcZOD2DYy8IEFoUy3.', '2024-10-15 16:58:47', NULL),
(11, 'akkkkouuuh', 'ayoubb', 'grotte', 789564785, 'grotte@g', '$2y$10$J8ONE4r9VXB9XVO8XijujO8B.WyW77KwZZH1k4cG41KmzRe3MVtQS', '2024-10-16 11:36:44', NULL),
(12, 'Combes-Aguéra', 'Anthony', '50 rue monte cristo', 614643549, 'antocombague@gmail.com', '$2y$10$m48I58igrRx/TKNXl1YEMebkkzO5S3d3FVFC1FYZfa1KX9iQZ/3Mm', '2024-10-18 16:26:20', NULL),
(13, 'Siret', 'Daniel', 'Nantes', 632704337, 'daniel.siret@gmail.com', '$2y$10$jx8mcfXnNDnKZvrtgOqQd.zytNzbVkfW//InI1JLaL/bvn9DcHvVi', '2024-10-25 09:17:12', NULL),
(14, 'Combes', 'Aline', '19 rue michel colucci, Béziers', 2147483647, 'aline@gmail.com', '$2y$10$nLvpor3wNKGRQnIAC4lpBOmQvg7K64vi33KGOT6UXu0pccqguYkEG', '2024-10-25 18:47:25', NULL),
(15, 'Combes', 'Aline', '19 rue michel colucci, Béziers', 2147483647, 'aline@gmail.com', '$2y$10$y7TJDEBvVpF1pR2lDiWlhupS1bwFPPGPEZ87eKhSuk1PcbKbx1PvG', '2024-10-25 18:47:25', NULL),
(16, 'Harraga', 'Wassim', 'Rue du caca', 651349407, 'wazzzzzer34@gmail.com', '$2y$10$MZ0xsAL1dMLbCb2XetWGGOEQxZLS0XIc0xbISc8DQ8hQ0t8Y2Btfe', '2024-11-05 17:04:48', NULL),
(17, 'Combes--Aguéra', 'Anthony', '19 rue Michel Colucci', 145698578, 'antho.upv@gmail.com', '$2y$10$NXe0m7NVPm4ssj4MKr0/p.eGnW/TeaFNxY44pZRWRxcbixPn4QrTK', '2024-11-06 15:22:29', NULL),
(18, 'CA', 'a', 'MTP', 123456789, 'antho@gmail.com', '$2y$10$v9rANQ9fyxEfA3o9l/13FOq0aMlRDxjw7lbb7wqrFUPO5/FWRjL3K', '2024-12-07 10:31:41', 'cus_RM2oHw7oo0TaVJ'),
(19, 'lauge', 'maja', 'zaqsdfe', 223456789, 'm@gmail.com', '$2y$10$Kby5nCtLxGt4gzF6m91F9OcGmj2LpH3hJuyVUWNw3fJGaSPYx5W9.', '2024-12-07 17:11:09', 'cus_RM91OPBhCI9aEg'),
(21, 'zzefezf', 'zdzdd', 'zdz', 614674589, 'zdzdzdd@gmail.com', '$2y$10$.HCZ4bnNdrZe5f92W.GfauqhH3PXhC/NMsMgXoz.uPa/4rAq8AqOG', '2024-12-10 16:38:48', NULL),
(22, 'A', 'A', 'A', 645789635, 'a@test.fr', '$2y$10$zbihwojovfGB4FQ6A/qSXeloDfqHEOy22.khVpTD306cI6W4rCgGi', '2024-12-10 16:39:45', 'cus_RNGrZZ0d62wHWp'),
(26, 'test', 'b', 'mtp', 147258368, 'testb@gmail.com', '$2y$10$sYmGYf/ZFw8sz9k1OCYiyuBQeufGnWoki4GPnIlSvYJd6duowMLke', '2024-12-13 12:14:30', NULL),
(27, 'test', 'A', 'mtp', 147852369, 'testa@gmail.com', '$2y$10$ZZyCbZUFa6iFh7AD1GdU0uqWyUYSbRKU8MV3VSPWaWxSS2ugQ5vkS', '2024-12-13 12:18:34', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

DROP TABLE IF EXISTS `commandes`;
CREATE TABLE IF NOT EXISTS `commandes` (
  `id_commande` int NOT NULL AUTO_INCREMENT,
  `id_client` int NOT NULL,
  `date_commande` datetime DEFAULT CURRENT_TIMESTAMP,
  `total_commande` decimal(10,2) NOT NULL,
  `envoi` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_commande`),
  KEY `id_client` (`id_client`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `commandes`
--

INSERT INTO `commandes` (`id_commande`, `id_client`, `date_commande`, `total_commande`, `envoi`) VALUES
(1, 22, '2024-12-11 10:59:50', 1700.00, 0),
(2, 22, '2024-12-11 11:02:53', 160.00, 1),
(3, 22, '2024-12-11 11:19:49', 80.00, 0),
(5, 27, '2024-12-13 12:23:23', 1205.00, 0);

-- --------------------------------------------------------

--
-- Structure de la table `commande_items`
--

DROP TABLE IF EXISTS `commande_items`;
CREATE TABLE IF NOT EXISTS `commande_items` (
  `id_commande_item` int NOT NULL AUTO_INCREMENT,
  `id_commande` int NOT NULL,
  `id_art` int NOT NULL,
  `quantite` int NOT NULL,
  PRIMARY KEY (`id_commande_item`),
  KEY `id_commande` (`id_commande`),
  KEY `id_art` (`id_art`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `commande_items`
--

INSERT INTO `commande_items` (`id_commande_item`, `id_commande`, `id_art`, `quantite`) VALUES
(1, 1, 10, 68),
(2, 2, 6, 2),
(3, 2, 7, 2),
(4, 3, 7, 1),
(5, 3, 9, 1),
(7, 5, 6, 1),
(8, 5, 7, 1),
(9, 5, 10, 45);

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id_message` int NOT NULL AUTO_INCREMENT,
  `id_user` int NOT NULL,
  `message` varchar(256) NOT NULL,
  `date_envoi` datetime NOT NULL,
  PRIMARY KEY (`id_message`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
CREATE TABLE IF NOT EXISTS `reservations` (
  `id_art` int NOT NULL,
  `id_client` int NOT NULL,
  `quantite_reservee` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_art`,`id_client`),
  KEY `id_client` (`id_client`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD CONSTRAINT `commandes_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `clients` (`id_client`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `commande_items`
--
ALTER TABLE `commande_items`
  ADD CONSTRAINT `commande_items_ibfk_1` FOREIGN KEY (`id_commande`) REFERENCES `commandes` (`id_commande`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `commande_items_ibfk_2` FOREIGN KEY (`id_art`) REFERENCES `articles` (`id_art`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
