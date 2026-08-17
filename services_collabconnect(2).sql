-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 17 août 2026 à 17:14
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
-- Base de données : `services_collabconnect`
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
-- Déchargement des données de la table `forfait`
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
-- Déchargement des données de la table `menus_ussd`
--

INSERT INTO `menus_ussd` (`id_menu`, `libelle`, `precedent`, `title`, `position`, `is_active`, `langue`, `abonnement`) VALUES
(1, 'Reservation', 0, NULL, 1, 1, 'FR', 'NO'),
(2, 'Generer Facture', 0, NULL, 2, 1, 'FR', 'NO'),
(3, 'Repassage', 1, NULL, 1, 1, 'FR', 'NO'),
(4, 'Pack Ménage', 1, NULL, 2, 1, 'FR', 'NO'),
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
  `paiement_id` varchar(255) DEFAULT NULL,
  `date_creation` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
-- Déchargement des données de la table `services`
--

INSERT INTO `services` (`service_id`, `libelle`, `keyword`, `description`, `infos`, `precedent`, `montant`, `duree_estimee`, `code_service`, `specialite`, `external`, `url_central`, `actif`) VALUES
(1, 'Electricite', 'electricite', NULL, NULL, '1', NULL, NULL, 6, 'OUI', NULL, NULL, 1),
(2, 'Dev Informatique', 'informatique', NULL, NULL, '1', NULL, NULL, 7, 'NO', NULL, NULL, 1),
(3, 'Repassage', 'repassage', NULL, NULL, '1', NULL, NULL, 3, 'NO', NULL, NULL, 1),
(4, 'Pack Ménage', 'menage', 'Pack Ménage', 'La facturation du menage se fait par piece, une piece de 9mettre carre coute 5000 Frs', '1', NULL, NULL, 4, 'NO', NULL, NULL, 1),
(5, 'Conception Affiche', 'affiche', NULL, 'Vous recevrez un lien par SMS pour remplir le formulaire de la demande de conception de visuel. ', '1', NULL, NULL, 5, 'NO', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Structure de la table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint NOT NULL,
  `reference_facture` varchar(100) NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `airtel_money_id` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) NOT NULL,
  `montant` decimal(12,2) NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'TIP',
  `response_code` varchar(50) DEFAULT NULL,
  `result_code` varchar(50) DEFAULT NULL,
  `message` text,
  `provider` varchar(20) DEFAULT 'AIRTEL',
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_mise_a_jour` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
-- Index pour les tables déchargées
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
-- Index pour la table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`),
  ADD KEY `idx_transaction_id` (`transaction_id`),
  ADD KEY `idx_reference_facture` (`reference_facture`),
  ADD KEY `idx_status` (`status`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_telephone` (`telephone`);

--
-- AUTO_INCREMENT pour les tables déchargées
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
  MODIFY `facture_id` int NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `paiements`
--
ALTER TABLE `paiements`
  MODIFY `paiement_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reservation_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
