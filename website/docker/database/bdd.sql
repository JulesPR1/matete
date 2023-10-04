-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : lun. 14 mars 2022 à 11:40
-- Version du serveur :  8.0.28-0ubuntu0.20.04.3
-- Version de PHP : 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

use matete;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `matete`
--

-- --------------------------------------------------------

--
-- Structure de la table `annonce`
--

CREATE TABLE `annonce` (
  `id` int NOT NULL,
  `la_categorie_id` int NOT NULL,
  `emplacement_id` int NOT NULL,
  `le_user_id` int NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantite` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `annonce`
--

INSERT INTO `annonce` (`id`, `la_categorie_id`, `emplacement_id`, `le_user_id`, `nom`, `description`, `quantite`, `image`, `created_at`) VALUES
(1, 2, 1, 1, 'Patate', 'Des patates trop bonnes', '50', 'https://blogdemaths.files.wordpress.com/2014/02/une-patate.jpg', '2021-12-21 21:49:34'),
(2, 2, 2, 1, 'Tomates', 'Des tomates trop incroyables', '20', 'https://mapetiteassiette.com/wp-content/uploads/2021/08/800x600-tomate-min.png', '2021-12-21 22:37:02'),
(7, 4, 3, 5, 'Des framboises', 'erergergeg', '5', 'https://img.cuisineaz.com/680x357/2018/06/18/i140456-framboises.jpeg', '2022-02-02 12:06:05'),
(9, 1, 4, 5, 'Fraises du jardin', 'Des fraises tombés par terre', '50', 'https://res.cloudinary.com/hv9ssmzrz/image/fetch/c_fill,f_auto,h_600,q_auto,w_800/https://s3-eu-west-1.amazonaws.com/images-ca-1-0-1-eu/tag_photos/original/1430/fraises-caissette.jpg', '2021-12-24 18:08:20'),
(10, 6, 5, 8, 'Des petits poids', 'Des tout petits poids trop kawaii', '40', 'https://www.natureo-bio.fr/wp-content/uploads/2017/07/FLEG-Laqualite-min.jpg', '2022-02-02 12:57:01'),
(11, 2, 6, 8, 'Potiron', 'Un potiron ta peur', '1', 'https://i0.wp.com/www.alsagarden.com/blog/wp-content/uploads/2016/02/l%C3%A9gume-g%C3%A9ant-1.jpg?fit=800%2C600&ssl=1', '2022-01-02 21:48:25'),
(12, 6, 8, 8, 'Okra', 'C\'est un peu bizarre', '10', 'https://wordpress.soscuisine.com/2013/09/okra-4001742_1920.jpg', '2022-02-02 12:57:17'),
(13, 10, 9, 9, 'Fruit de la passion', 'C\'est pas très bon ça', '8', 'https://www.lacourdorgeres.com/Edelweiss.Upload/news/138/big/fruit-de-la-passion,-le-gout-des-tropiques_0.jpg', '2022-02-02 13:02:02'),
(14, 10, 10, 9, 'le Hala', 'C\'est quoi ce truc encore', '2', 'https://www.finedininglovers.fr/sites/g/files/xknfdk1291/files/styles/im_landscape_100/public/fdl_content_import_scripts/Original_4532_l-19363-Hala-Fruit-Reddit_0.jpg.webp?itok=IH_H8Gog', '2022-02-02 13:01:50'),
(15, 1, 11, 9, 'Pastèque', 'C\'est une vraie masterclass', '3', 'https://cdn.futura-sciences.com/buildsv6/images/wide1920/0/7/9/0791aa180e_127639_pasteque-c-beats-fotolia.jpg', '2022-01-02 21:58:52'),
(16, 2, 13, 1, 'Des radis', 'Des radis délicieux', '10', 'https://cdn.futura-sciences.com/buildsv6/images/largeoriginal/3/a/0/3a00fb3a1a_50035788_radis-dr.jpg', '2022-01-03 10:24:12'),
(17, 1, 12, 1, 'Pommes', 'Des pommes ramassées par terre', '8', 'https://www.extrado.fr/sites/extrado.fr/files/fete_de_la_pomme_2019.jpg', '2022-01-03 10:54:50');

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

CREATE TABLE `categorie` (
  `id` int NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`id`, `nom`, `color`) VALUES
(1, 'Fruit', '#CFCC29'),
(2, 'Légume', '#6BCF29'),
(4, 'fruits rouges', '#C70039'),
(6, 'Légume verts', '#26a269'),
(10, 'Fruit exotique', '#dc8add');

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20211123142946', '2021-12-21 13:27:54', 372),
('DoctrineMigrations\\Version20211218211054', '2021-12-21 13:27:54', 37),
('DoctrineMigrations\\Version20211221122819', '2021-12-21 13:28:24', 38),
('DoctrineMigrations\\Version20220202111755', '2022-02-02 12:18:09', 110);

-- --------------------------------------------------------

--
-- Structure de la table `emplacement`
--

CREATE TABLE `emplacement` (
  `id` int NOT NULL,
  `le_user_id` int NOT NULL,
  `adresse` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_postal` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `emplacement`
--

INSERT INTO `emplacement` (`id`, `le_user_id`, `adresse`, `code_postal`) VALUES
(1, 1, '40 rue vallée du Rhone', '66140'),
(2, 1, '24 rue des muscats', '66540'),
(3, 5, '24 rue des carlettes', '66000'),
(4, 5, '5 rue du pic barbet', '66180'),
(5, 8, '53 rue Clement Marot', '66000'),
(6, 8, '24 avenue du Stade', '66350'),
(8, 8, '4 Rue des Troubadours', '66350'),
(9, 9, '55 Rue Dame Saurimonde', '66330'),
(10, 9, '3 Avenue Couloubrettes', '66280'),
(11, 9, '4 Rue Watteau', '66750'),
(12, 1, '5 rue Croix-Barret', '69007'),
(13, 1, '35 Rue Louis Blériot', '69780'),
(14, 1, '2 rue Antoine Billon', '69200'),
(16, 9, '32 rue Pnte Cadet', '42000'),
(17, 11, '2 Avenue Armand Guilbaud', '92160'),
(18, 1, '2 Avenue Armand Guilbaud', '245687');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `suspendu` tinyint(1) DEFAULT NULL,
  `num_tel` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `username`, `suspendu`, `num_tel`) VALUES
(1, 'jules@gmail.com', '[\"ROLE_ADMIN\"]', '$2y$13$GkhPfkEDXQcgMdWOKxbP6ugXDtCVHK39bEycEatqt50A.kQH7nxK6', 'jules', NULL, '0506060606'),
(2, 'b@gmail.com', '[\"ROLE_USER\"]', '$2y$13$25qI1pWXjsQ1AeG4Yr9bB.YmmY09vly3p4PCRPQMVVsyYtbOKvUmS', 'user b', NULL, '0606060606'),
(4, 'clement@gmail.com', '[\"ROLE_USER\"]', '$2y$13$R4T57Gq/cL42EXqt6KHz4uBu/Ak5yKRnQ.epsyAj8ySLRVg.E0Ude', 'Clement M', 1, '0648795232'),
(5, 'maxime@gmail.com', '[\"ROLE_USER\"]', '$2y$13$RgUME9TdGzCeJT1o51or2.WawKSLeP4tbUSfuptNsOOKsAk1s6Pom', 'Maxime', NULL, '0606060606'),
(8, 'mathieu@outlook.fr', '[\"ROLE_USER\"]', '$2y$13$IvMgSweRzrEsbv7LcmEvtemytk6sh8J5G2AV8ogI6u6MUOUB6Oz4K', 'Mathieu', NULL, '0675894125'),
(9, 'arnoob@gmail.com', '[\"ROLE_USER\"]', '$2y$13$GGm0AHdSE2mNQKlhI7iASONFP/kZuHU8oa.ETUN3YIk.jocog.oMm', 'Arnoo', NULL, '0694872631'),
(10, 'azezae@gmail.com', '[\"ROLE_USER\"]', '$2y$13$0ZTZif042Yde/zpNkyDkoOhb1/6MjHHfnAUc3bd3.gdvqKEup7a5S', 'azeazeaze', 0, '0808080808'),
(11, 'test@gmail.com', '[\"ROLE_USER\"]', '$2y$13$DuIrureM/LucgueSqSbY6efaupmly2XMXkzOC1pFYN0gdG801ahEK', 'test66', 1, '0505050505');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `annonce`
--
ALTER TABLE `annonce`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_F65593E5281042B9` (`la_categorie_id`),
  ADD KEY `IDX_F65593E5C4598A51` (`emplacement_id`),
  ADD KEY `IDX_F65593E588A1A5E2` (`le_user_id`);

--
-- Index pour la table `categorie`
--
ALTER TABLE `categorie`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `emplacement`
--
ALTER TABLE `emplacement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_C0CF65F688A1A5E2` (`le_user_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`),
  ADD UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `annonce`
--
ALTER TABLE `annonce`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `categorie`
--
ALTER TABLE `categorie`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `emplacement`
--
ALTER TABLE `emplacement`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `annonce`
--
ALTER TABLE `annonce`
  ADD CONSTRAINT `FK_F65593E5281042B9` FOREIGN KEY (`la_categorie_id`) REFERENCES `categorie` (`id`),
  ADD CONSTRAINT `FK_F65593E588A1A5E2` FOREIGN KEY (`le_user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_F65593E5C4598A51` FOREIGN KEY (`emplacement_id`) REFERENCES `emplacement` (`id`);

--
-- Contraintes pour la table `emplacement`
--
ALTER TABLE `emplacement`
  ADD CONSTRAINT `FK_C0CF65F688A1A5E2` FOREIGN KEY (`le_user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
