-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 18 déc. 2024 à 16:13
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
-- Base de données : `yweather`
--

-- --------------------------------------------------------

--
-- Structure de la table `place`
--

CREATE TABLE `place` (
  `place_id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `place`
--

INSERT INTO `place` (`place_id`, `name`, `latitude`, `longitude`) VALUES
(1, 'Montpellier', 123, 43),
(2, 'Strasbourg', 33, 2),
(3, 'Alsace', 9, 87);

-- --------------------------------------------------------

--
-- Structure de la table `place_time_weather`
--

CREATE TABLE `place_time_weather` (
  `place_time_weather_id` int(11) NOT NULL,
  `time_id` int(11) DEFAULT NULL,
  `place_id` int(11) DEFAULT NULL,
  `weather_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `place_time_weather`
--

INSERT INTO `place_time_weather` (`place_time_weather_id`, `time_id`, `place_id`, `weather_id`) VALUES
(3, 1, 1, 1),
(4, 2, 1, 1),
(5, 2, 2, 1);

-- --------------------------------------------------------

--
-- Structure de la table `time`
--

CREATE TABLE `time` (
  `time_id` int(11) NOT NULL,
  `day` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `hour` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `time`
--

INSERT INTO `time` (`time_id`, `day`, `hour`) VALUES
(1, '2024-12-18 15:03:42', '0000-00-00 00:00:00'),
(2, '2024-12-18 15:04:08', '0000-00-00 00:00:00'),
(3, '2024-12-18 15:04:25', '0000-00-00 00:00:00'),
(4, '2024-12-18 15:04:30', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `weather`
--

CREATE TABLE `weather` (
  `weather_id` int(11) NOT NULL,
  `temperature` float DEFAULT NULL,
  `precipitation` float DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `wind` float DEFAULT NULL,
  `humidity` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `weather`
--

INSERT INTO `weather` (`weather_id`, `temperature`, `precipitation`, `state`, `wind`, `humidity`) VALUES
(1, 2, 2, 'test', 1, 1),
(2, 2, 2, 'test', 1, 1),
(3, 4, 42, 'teeeeest', 4, 4);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `place`
--
ALTER TABLE `place`
  ADD PRIMARY KEY (`place_id`);

--
-- Index pour la table `place_time_weather`
--
ALTER TABLE `place_time_weather`
  ADD PRIMARY KEY (`place_time_weather_id`);

--
-- Index pour la table `time`
--
ALTER TABLE `time`
  ADD PRIMARY KEY (`time_id`);

--
-- Index pour la table `weather`
--
ALTER TABLE `weather`
  ADD PRIMARY KEY (`weather_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `place`
--
ALTER TABLE `place`
  MODIFY `place_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `place_time_weather`
--
ALTER TABLE `place_time_weather`
  MODIFY `place_time_weather_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `time`
--
ALTER TABLE `time`
  MODIFY `time_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `weather`
--
ALTER TABLE `weather`
  MODIFY `weather_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
