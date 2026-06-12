-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 10 juin 2026 à 21:22
-- Version du serveur : 10.4.19-MariaDB
-- Version de PHP : 8.0.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `grammepargramme`
--

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `color` varchar(20) DEFAULT '#6c757d'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `name`, `color`) VALUES
(1, 'Féculents / Céréales', '#fd7e14'),
(2, 'Produits Laitiers', '#0dcaf0'),
(3, 'Viandes / Poissons / Œufs', '#dc3545'),
(4, 'Matières Grasses', '#ffc107'),
(5, 'Fruits / Légumes', '#198754'),
(6, 'Produits Sucrés', '#6f42c1'),
(7, 'Boissons', '#0d6efd'),
(8, 'Plats préparés / Autres', '#6c757d');

-- --------------------------------------------------------

--
-- Structure de la table `off_food_items`
--

CREATE TABLE `off_food_items` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `barcode` varchar(50) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `off_url` varchar(255) DEFAULT NULL,
  `kcal_per_100g` int(11) NOT NULL,
  `fat_per_100g` decimal(5,2) NOT NULL DEFAULT 0.00,
  `saturated_fat_per_100g` decimal(5,2) NOT NULL DEFAULT 0.00,
  `carbohydrates_per_100g` decimal(5,2) NOT NULL DEFAULT 0.00,
  `sugar_per_100g` decimal(5,2) NOT NULL DEFAULT 0.00,
  `proteins_per_100g` decimal(5,2) NOT NULL DEFAULT 0.00,
  `fibers_per_100g` decimal(5,2) NOT NULL DEFAULT 0.00,
  `salt_per_100g` decimal(5,2) NOT NULL DEFAULT 0.00,
  `water_per_100g` decimal(5,2) NOT NULL DEFAULT 0.00,
  `is_recipe` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `category_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `food_unit` varchar(10) DEFAULT 'g'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `off_food_items`
--

INSERT INTO `off_food_items` (`id`, `name`, `barcode`, `image_path`, `off_url`, `kcal_per_100g`, `fat_per_100g`, `saturated_fat_per_100g`, `carbohydrates_per_100g`, `sugar_per_100g`, `proteins_per_100g`, `fibers_per_100g`, `salt_per_100g`, `water_per_100g`, `is_recipe`, `created_at`, `category_id`, `user_id`, `food_unit`) VALUES
(39, 'Extra Noir 85%', '3580288107754', 'https://images.openfoodfacts.org/images/products/358/028/810/7754/front_fr.3.400.jpg', 'https://fr.openfoodfacts.org/produit/3580288107754', 592, '50.00', '31.00', '18.00', '14.00', '9.70', '15.00', '0.02', '0.00', 0, '2026-06-09 04:37:49', 8, NULL, 'g'),
(40, 'Tomate Patricia', NULL, NULL, NULL, 17, '2.60', '0.00', '1.50', '0.80', '0.00', '1.20', '0.12', '0.00', 0, '2026-06-09 04:39:33', 5, 2, 'g'),
(41, 'Produit test', '2006050124251', NULL, NULL, 18, '2.36', '1.24', '3.20', '0.58', '1.40', '2.10', '0.09', '0.00', 0, '2026-06-09 04:43:55', 8, 2, 'g'),
(44, 'Milsani - Fromage Blanc Nature 0% Matière Grasse', '4068262050726', 'https://images.openfoodfacts.org/images/products/406/826/205/0726/front_fr.9.400.jpg', 'https://fr.openfoodfacts.org/produit/4068262050726', 54, '0.50', '0.20', '5.40', '5.40', '6.80', '0.50', '0.09', '0.00', 0, '2026-06-09 16:28:31', 2, NULL, 'g');

-- --------------------------------------------------------

--
-- Structure de la table `meal_moments`
--

CREATE TABLE `meal_moments` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `label` varchar(100) NOT NULL,
  `requires_custom_label` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `meal_moments`
--

INSERT INTO `meal_moments` (`id`, `name`, `label`, `requires_custom_label`) VALUES
(1, 'breakfast', 'Petit-déjeuner', 0),
(2, 'lunch', 'Déjeuner', 0),
(3, 'snack', 'Collation', 0),
(4, 'dinner', 'Dîner', 0),
(5, 'workout', 'Activité physique', 0),
(6, 'other', 'Autre (à préciser)', 1);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` varchar(20) NOT NULL DEFAULT 'USER'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `created_at`, `role`) VALUES
(1, 'david.brot@orange.fr', '$2y$10$nwx7wdgiiTxGXyzxlqwkfOJp5mu.KyCUCswt2O.crVZrzT5whaQC.', '2026-06-05 09:04:50', 'ADMIN'),
(2, 'patricia.klauder@laposte.net', '$2y$10$ctk76KAFFDNa/yyZLBREAeG47cgFvE/UwmkBcgINWvfVaKoKKNPhC', '2026-06-07 11:45:50', 'USER');

-- --------------------------------------------------------

--
-- Structure de la table `user_favorite_tags`
--

CREATE TABLE `user_favorite_tags` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tag_name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `user_favorite_tags`
--

INSERT INTO `user_favorite_tags` (`id`, `user_id`, `tag_name`, `created_at`) VALUES
(1, 1, 'Général', '2026-06-09 17:13:41'),
(2, 1, 'Fruits', '2026-06-09 17:14:05');

-- --------------------------------------------------------

--
-- Structure de la table `user_food_customization`
--

CREATE TABLE `user_food_customization` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `food_item_id` int(11) NOT NULL,
  `custom_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `user_food_tags`
--

CREATE TABLE `user_food_tags` (
  `user_id` int(11) NOT NULL,
  `food_item_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `user_food_tags`
--

INSERT INTO `user_food_tags` (`user_id`, `food_item_id`, `tag_id`, `assigned_at`) VALUES
(1, 39, 2, '2026-06-10 19:14:55'),
(2, 40, 1, '2026-06-09 18:45:01');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `off_food_items`
--
ALTER TABLE `off_food_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_food_category` (`category_id`);

--
-- Index pour la table `meal_moments`
--
ALTER TABLE `meal_moments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `user_favorite_tags`
--
ALTER TABLE `user_favorite_tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_tag` (`user_id`,`tag_name`);

--
-- Index pour la table `user_food_customization`
--
ALTER TABLE `user_food_customization`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_food_unique` (`user_id`,`food_item_id`),
  ADD KEY `food_item_id` (`food_item_id`);

--
-- Index pour la table `user_food_tags`
--
ALTER TABLE `user_food_tags`
  ADD PRIMARY KEY (`user_id`,`food_item_id`,`tag_id`),
  ADD KEY `food_item_id` (`food_item_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `off_food_items`
--
ALTER TABLE `off_food_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT pour la table `meal_moments`
--
ALTER TABLE `meal_moments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `user_favorite_tags`
--
ALTER TABLE `user_favorite_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `user_food_customization`
--
ALTER TABLE `user_food_customization`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `off_food_items`
--
ALTER TABLE `off_food_items`
  ADD CONSTRAINT `fk_food_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `user_favorite_tags`
--
ALTER TABLE `user_favorite_tags`
  ADD CONSTRAINT `user_favorite_tags_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `user_food_customization`
--
ALTER TABLE `user_food_customization`
  ADD CONSTRAINT `user_food_customization_ibfk_1` FOREIGN KEY (`food_item_id`) REFERENCES `off_food_items` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `user_food_tags`
--
ALTER TABLE `user_food_tags`
  ADD CONSTRAINT `user_food_tags_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_food_tags_ibfk_2` FOREIGN KEY (`food_item_id`) REFERENCES `off_food_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_food_tags_ibfk_3` FOREIGN KEY (`tag_id`) REFERENCES `user_favorite_tags` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
