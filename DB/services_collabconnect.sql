-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Genere le : jeu. 28 mai 2026 à 22:22
-- Version du serveur : 8.4.3
-- Version de PHP : 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de donnees : `services_collabconnect`
--

-- --------------------------------------------------------

--
-- Structure de la table `agents`
--

CREATE TABLE `agents` (
  `agent_id` int NOT NULL,
  `nom` varchar(150) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `zone` varchar(100) DEFAULT NULL,
  `specialite` varchar(100) DEFAULT NULL,
  `disponible` tinyint(1) DEFAULT '1',
  `statut` varchar(20) DEFAULT 'ACTIF',
  `date_creation` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `disponibilites_agents`
--

CREATE TABLE `disponibilites_agents` (
  `dispo_id` int NOT NULL,
  `agent_id` int DEFAULT NULL,
  `date_dispo` date DEFAULT NULL,
  `heure_debut` time DEFAULT NULL,
  `heure_fin` time DEFAULT NULL,
  `disponible` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `factures`
--

CREATE TABLE `factures` (
  `facture_id` int NOT NULL,
  `reservation_id` int DEFAULT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `numero_client` varchar(20) DEFAULT NULL,
  `montant` decimal(10,2) DEFAULT NULL,
  `statut` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'non_regle',
  `etat` enum('0','1','2') NOT NULL DEFAULT '0',
  `date_facture` datetime DEFAULT NULL,
  `numero_agent` varchar(20) DEFAULT NULL,
  `agent_id` varchar(60) DEFAULT NULL,
  `id_service` varchar(60) DEFAULT NULL,
  `designation` varchar(225) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dechargement des donnees de la table `factures`
--

INSERT INTO `factures` (`facture_id`, `reservation_id`, `reference`, `numero_client`, `montant`, `statut`, `etat`, `date_facture`, `numero_agent`, `agent_id`, `id_service`, `designation`) VALUES
(1, NULL, '111', '0758407197', 5000.00, 'non_regle', '0', '2026-05-28 19:42:23', '0758407197', NULL, '2', '5000'),
(2, NULL, 'F7-1946172026052838', '0758407197', 1500.00, 'non_regle', '0', '2026-05-28 19:46:17', '0758407197', NULL, '2', '1500'),
(3, 1, 'F3-2034462026052851', '0758407197', 7000.00, 'non_regle', '0', '2026-05-28 20:34:46', '0758407197', NULL, '3', '7000'),
(4, NULL, 'F4-2037372026052858', '0758407197', 5000.00, 'non_regle', '0', '2026-05-28 20:37:37', '0758407197', NULL, '4', '5000'),
(5, NULL, 'F4-2038312026052821', '0758407197', 5000.00, 'non_regle', '0', '2026-05-28 20:38:31', '0758407197', NULL, '4', '5000'),
(6, NULL, 'F7-2040292026052836', '3366622111', 60000.00, 'non_regle', '0', '2026-05-28 20:40:29', '0758407197', NULL, '2', '60000'),
(7, NULL, 'F7-2041042026052838', '3366622111', 60000.00, 'non_regle', '0', '2026-05-28 20:41:04', '0758407197', NULL, '2', '60000'),
(8, 1, 'B0A6FE', '0758407197', 10500.00, 'non_regle', '0', '2026-05-28 21:25:34', '5555555555', NULL, '3', '10500'),
(9, NULL, 'CO5D1BC7', '0758407197', 300.00, 'non_regle', '0', '2026-05-28 21:27:44', '5555555555', NULL, '5', '300');

-- --------------------------------------------------------

--
-- Structure de la table `forfait`
--

CREATE TABLE `forfait` (
  `id` int NOT NULL,
  `souscription` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `keyword` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `periode` varchar(225) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tarif` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `souscription_aff` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `affichage` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_service` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `transaction_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dechargement des donnees de la table `forfait`
--

INSERT INTO `forfait` (`id`, `souscription`, `keyword`, `periode`, `tarif`, `souscription_aff`, `affichage`, `id_service`, `transaction_code`, `is_active`) VALUES
(1, 'prestation', 'repassage', '1', '3500', 'panier', '3 500 FCFA / panier', '3', 'repassage', 1),
(2, 'prestation', 'menage', '1', '5000', 'm² / pièce', '5 000 FCFA / pièce de 9 m²', '4', 'menage', 1),
(11, 'electricite', 'electricite', '1', 'devis', 'prestation', 'Sur devis (installation/maintenance)', '1', 'electricite', 1),
(12, 'affiche', 'affiche', '1', 'devis', 'prestation', 'Sur devis (via formulaire web)', '5', 'affiche', 1),
(13, 'informatique', 'informatique', '1', 'devis', 'prestation', 'Sur devis (via formulaire web)', '2', 'informatique', 1);

-- --------------------------------------------------------

--
-- Structure de la table `menus_ussd`
--

CREATE TABLE `menus_ussd` (
  `id_menu` int UNSIGNED NOT NULL,
  `libelle` varchar(50) NOT NULL,
  `precedent` int UNSIGNED NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `position` int NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `langue` enum('FR','EN') NOT NULL DEFAULT 'FR',
  `abonnement` enum('YES','NO') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'NO'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dechargement des donnees de la table `menus_ussd`
--

INSERT INTO `menus_ussd` (`id_menu`, `libelle`, `precedent`, `title`, `position`, `is_active`, `langue`, `abonnement`) VALUES
(1, 'Reservation', 0, NULL, 1, 1, 'FR', 'NO'),
(2, 'Generer Facture', 0, NULL, 2, 1, 'FR', 'NO'),
(3, 'Repassage', 1, NULL, 1, 1, 'FR', 'NO'),
(4, 'Pack Menage', 1, NULL, 2, 1, 'FR', 'NO'),
(5, 'Conception Affiche', 1, NULL, 3, 1, 'FR', 'NO'),
(6, 'Electricite', 1, NULL, 4, 1, 'FR', 'NO'),
(7, 'Dev Informatique', 1, NULL, 5, 1, 'FR', 'NO'),
(8, 'Payer une Facture', 0, NULL, 3, 1, 'FR', 'NO');

-- --------------------------------------------------------

--
-- Structure de la table `next_table`
--

CREATE TABLE `next_table` (
  `id` int UNSIGNED NOT NULL,
  `next` varchar(255) NOT NULL DEFAULT '',
  `date` datetime NOT NULL DEFAULT '2016-06-20 00:00:00',
  `numero` varchar(15) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `next_ext` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `url` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `env` varchar(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `sessionId` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `next_sms` varchar(100) NOT NULL DEFAULT 'EN',
  `date_sms` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dechargement des donnees de la table `next_table`
--

INSERT INTO `next_table` (`id`, `next`, `date`, `numero`, `next_ext`, `url`, `env`, `sessionId`, `next_sms`, `date_sms`) VALUES
(1, 'paiementFacture_F3-2034462026052851', '2026-05-28 22:16:25', '0758407197', NULL, NULL, NULL, NULL, 'EN', NULL),
(2, 'menu_groupe_0_1', '2026-05-28 21:18:12', '4444444444', NULL, NULL, NULL, NULL, 'EN', NULL),
(3, 'menu_groupe_0_1', '2026-05-28 21:27:48', '5555555555', NULL, NULL, NULL, NULL, 'EN', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `paiements`
--

CREATE TABLE `paiements` (
  `paiement_id` int NOT NULL,
  `reservation_id` int DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `montant` decimal(10,2) DEFAULT NULL,
  `operateur` varchar(50) DEFAULT NULL,
  `statut` varchar(30) DEFAULT NULL,
  `raw_response` text,
  `date_paiement` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dechargement des donnees de la table `paiements`
--

INSERT INTO `paiements` (`paiement_id`, `reservation_id`, `transaction_id`, `montant`, `operateur`, `statut`, `raw_response`, `date_paiement`) VALUES
(1, 11, '1', 200.00, 'AIRTEL', '1', 'SUCCESS', '2026-05-28 20:42:38');

-- --------------------------------------------------------

--
-- Structure de la table `reservations`
--

CREATE TABLE `reservations` (
  `reservation_id` int NOT NULL,
  `reference` varchar(50) DEFAULT NULL,
  `client_nom` varchar(150) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `service_id` int DEFAULT NULL,
  `agent_id` int DEFAULT NULL,
  `date_rdv` datetime DEFAULT NULL,
  `specialite` varchar(225) DEFAULT NULL,
  `description` varchar(225) DEFAULT NULL,
  `statut` varchar(30) DEFAULT NULL,
  `montant` decimal(10,2) DEFAULT NULL,
  `paiement_statut` varchar(20) DEFAULT NULL,
  `date_creation` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dechargement des donnees de la table `reservations`
--

INSERT INTO `reservations` (`reservation_id`, `reference`, `client_nom`, `telephone`, `service_id`, `agent_id`, `date_rdv`, `specialite`, `description`, `statut`, `montant`, `paiement_statut`, `date_creation`) VALUES
(1, 'RSV-s3-2026052721300074', 'client n.0758407197', '0758407197', 3, NULL, '2026-05-30 00:00:00', NULL, NULL, '1', 200.00, '1', '2026-05-27 21:30:00'),
(2, 'RSV-s3-2026052721341093', 'client n.0758407197', '0758407197', 3, NULL, '2026-05-30 00:00:00', NULL, NULL, '1', 200.00, '1', '2026-05-27 21:34:10'),
(3, 'RSV-s3-2026052721501149', 'client n.0758407197', '0758407197', 3, NULL, '2026-05-30 00:00:00', NULL, NULL, '1', 200.00, '1', '2026-05-27 21:50:11'),
(4, 'RSV-s4-2026052721545133', 'client n.0758407197', '0758407197', 4, NULL, '2026-05-31 00:00:00', NULL, NULL, '1', 200.00, '1', '2026-05-27 21:54:51'),
(5, 'RSV-s3-2026052722051069', 'client n.0758407197', '0758407197', 3, NULL, '2026-05-30 00:00:00', NULL, NULL, '1', 200.00, '1', '2026-05-27 22:05:10'),
(6, 'RSV-s6-2026052722064438', 'client n.0758407197', '0758407197', 1, NULL, '2026-06-02 00:00:00', '2', 'reinstallation', '1', 200.00, '1', '2026-05-27 22:06:44'),
(7, 'RSV-s6-2026052722284635', 'client n.0758407197', '0758407197', 1, NULL, '2026-05-27 00:00:00', '1', 'installation', '1', 200.00, '1', '2026-05-27 22:28:46'),
(8, 'RSV-s3-2026052722425193', 'client n.0758407197', '0758407197', 3, NULL, '2026-05-29 00:00:00', NULL, NULL, '1', 200.00, '1', '2026-05-27 22:42:51'),
(9, 'RSV-s3-2026052722452593', 'client n.0758407197', '0758407197', 3, NULL, '2026-05-29 00:00:00', NULL, NULL, '1', 200.00, '1', '2026-05-27 22:45:25'),
(10, 'RSV-s3-2026052722474935', 'client n.0758407197', '0758407197', 3, NULL, '2026-05-29 00:00:00', NULL, NULL, '1', 200.00, '1', '2026-05-27 22:47:49'),
(11, 'RSV-s3-2026052820423839', 'client n.0758407197', '0758407197', 3, NULL, '2026-06-04 00:00:00', NULL, NULL, '1', 200.00, '1', '2026-05-28 20:42:38');

-- --------------------------------------------------------

--
-- Structure de la table `services`
--

CREATE TABLE `services` (
  `service_id` int NOT NULL,
  `libelle` varchar(150) DEFAULT NULL,
  `keyword` varchar(225) NOT NULL,
  `description` text,
  `infos` varchar(160) DEFAULT NULL,
  `precedent` varchar(225) DEFAULT NULL,
  `montant` decimal(10,2) DEFAULT NULL,
  `duree_estimee` int DEFAULT NULL,
  `code_service` int NOT NULL,
  `specialite` enum('OUI','NO') NOT NULL DEFAULT 'NO',
  `external` varchar(225) DEFAULT NULL,
  `url_central` text,
  `actif` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dechargement des donnees de la table `services`
--

INSERT INTO `services` (`service_id`, `libelle`, `keyword`, `description`, `infos`, `precedent`, `montant`, `duree_estimee`, `code_service`, `specialite`, `external`, `url_central`, `actif`) VALUES
(1, 'Electricite', 'electricite', NULL, NULL, '1', NULL, NULL, 6, 'OUI', NULL, NULL, 1),
(2, 'Dev Informatique', 'informatique', NULL, NULL, '1', NULL, NULL, 7, 'NO', NULL, NULL, 1),
(3, 'Repassage', 'repassage', NULL, NULL, '1', NULL, NULL, 3, 'NO', NULL, NULL, 1),
(4, 'Pack Menage', 'menage', 'Pack Menage', 'La facturation du menage se fait par piece, une piece de 9mettre carre coute 5000 Frs', '1', NULL, NULL, 4, 'NO', NULL, NULL, 1),
(5, 'Conception Affiche', 'affiche', NULL, 'Vous recevrez un lien par SMS pour remplir le formulaire de la demande de conception de visuel. ', '1', NULL, NULL, 5, 'NO', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int NOT NULL,
  `telephone` varchar(15) NOT NULL,
  `nom` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `node` varchar(20) NOT NULL,
  `level` varchar(20) NOT NULL,
  `service` varchar(120) NOT NULL,
  `id_service` int NOT NULL,
  `forfait` enum('JOUR','SEMAINE','MOIS','QUINZAINE','ILLIMIX') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'JOUR',
  `dateFin` datetime NOT NULL DEFAULT '2017-02-26 00:00:00',
  `lastPush` datetime NOT NULL DEFAULT '2013-01-01 00:00:00',
  `date_desabonn` datetime DEFAULT NULL,
  `active` enum('YES','NO') NOT NULL DEFAULT 'YES',
  `notification` enum('NO','YES') NOT NULL DEFAULT 'NO',
  `createdAt` datetime DEFAULT NULL,
  `updatedAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Index pour les tables dechargees
--

--
-- Index pour la table `agents`
--
ALTER TABLE `agents`
  ADD PRIMARY KEY (`agent_id`);

--
-- Index pour la table `disponibilites_agents`
--
ALTER TABLE `disponibilites_agents`
  ADD PRIMARY KEY (`dispo_id`);

--
-- Index pour la table `factures`
--
ALTER TABLE `factures`
  ADD PRIMARY KEY (`facture_id`);

--
-- Index pour la table `forfait`
--
ALTER TABLE `forfait`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_key_service` (`keyword`,`id_service`);

--
-- Index pour la table `menus_ussd`
--
ALTER TABLE `menus_ussd`
  ADD PRIMARY KEY (`id_menu`),
  ADD UNIQUE KEY `unicite_percedent_position` (`precedent`,`position`);

--
-- Index pour la table `next_table`
--
ALTER TABLE `next_table`
  ADD PRIMARY KEY (`id`),
  ADD KEY `date` (`date`),
  ADD KEY `numero` (`numero`);

--
-- Index pour la table `paiements`
--
ALTER TABLE `paiements`
  ADD PRIMARY KEY (`paiement_id`);

--
-- Index pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`reservation_id`);

--
-- Index pour la table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`),
  ADD UNIQUE KEY `id_menu` (`code_service`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_telephone` (`telephone`);

--
-- AUTO_INCREMENT pour les tables dechargees
--

--
-- AUTO_INCREMENT pour la table `agents`
--
ALTER TABLE `agents`
  MODIFY `agent_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `disponibilites_agents`
--
ALTER TABLE `disponibilites_agents`
  MODIFY `dispo_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `factures`
--
ALTER TABLE `factures`
  MODIFY `facture_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `forfait`
--
ALTER TABLE `forfait`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `menus_ussd`
--
ALTER TABLE `menus_ussd`
  MODIFY `id_menu` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `next_table`
--
ALTER TABLE `next_table`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `paiements`
--
ALTER TABLE `paiements`
  MODIFY `paiement_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reservation_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
