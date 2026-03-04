-- phpMyAdmin SQL Dump
-- version 5.2.1deb1+deb12u1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : mer. 04 mars 2026 à 09:11
-- Version du serveur : 10.11.14-MariaDB-0+deb12u2
-- Version de PHP : 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `dbaltixier1`
--

-- --------------------------------------------------------

--
-- Structure de la table `Assureur`
--

CREATE TABLE `Assureur` (
  `IdAssureur` int(11) NOT NULL,
  `Nom` varchar(100) NOT NULL,
  `NumRue` decimal(10,0) NOT NULL,
  `NumTel` varchar(20) NOT NULL,
  `CodePostal` varchar(5) NOT NULL,
  `Rue` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `Assureur`
--

INSERT INTO `Assureur` (`IdAssureur`, `Nom`, `NumRue`, `NumTel`, `CodePostal`, `Rue`) VALUES
(1, 'AXA', 10, '01 23 45 67 89', '75008', 'Avenue des Champs-Élysées'),
(2, 'Allianz', 5, '04 56 78 90 12', '69002', 'Rue de la République'),
(3, 'MAIF', 22, '05 34 56 78 90', '34000', 'Boulevard Victor Hugo'),
(4, 'Groupama', 15, '03 12 34 56 78', '13002', 'Rue Saint-Ferréol');

-- --------------------------------------------------------

--
-- Structure de la table `cars`
--

CREATE TABLE `cars` (
  `voiture` int(11) NOT NULL,
  `modele` varchar(150) NOT NULL,
  `couleur` varchar(180) NOT NULL,
  `puissance` int(11) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `cars`
--

INSERT INTO `cars` (`voiture`, `modele`, `couleur`, `puissance`, `image_path`) VALUES
(12, 'bmwa', 'blanc', 500, 'html/icons/bmw1.jpeg'),
(13, 'test444', 'aze', 444, 'html/icons/6900055a52469_bmw1.jpeg'),
(16, 'test111', 'black', 111, ''),
(17, 'serie 4 ', 'blanc ', 600, 'html/icons/690349db960c1_690005bf56fad_bm.jpeg'),
(18, 'aze', 'blanc', 44, ''),
(19, 'kikk', 'zdz', -3, ''),
(20, 'kikk', 'zdz', -3, ''),
(21, 'kikk', 'zdz', -3, ''),
(22, 'kikk', 'zdz', -3, ''),
(23, 'kikk', 'zdz', -3, ''),
(24, 'kikk', 'zdz', -3, ''),
(25, 'kikk', 'zdz', -3, ''),
(26, 'kikk', 'zdz', -3, ''),
(27, 'kikk', 'zdz', -3, ''),
(28, 'kikk', 'zdz', -3, ''),
(29, 'kikk', 'zdz', -3, 'html/icons/6942b50f2a2ff_Screenshot 2025-09-11 190315.png'),
(30, 'kikk', 'zdz', -3, 'html/icons/6942b50fdbf16_Screenshot 2025-12-15 213034.png'),
(31, 'kikk', 'zdz', -3, 'html/icons/6942b51105a46_Screenshot 2025-12-15 213034.png'),
(32, 'kikk', 'zdz', -3, 'html/icons/6942b511aaaaf_Screenshot 2025-12-15 213034.png'),
(33, 'kikk', 'zdz', -3, 'html/icons/6942b5139e165_Screenshot 2025-12-15 213034.png'),
(34, 'kikk', 'zdz', -3, 'html/icons/6942b515a68df_Screenshot 2025-12-15 213034.png'),
(35, 'kikk', 'zdz', -3, 'html/icons/6942b5178c1b5_Screenshot 2025-12-15 213034.png'),
(36, 'azeazeaz', 'blanc', 44, ''),
(37, 'geieuzfie', 'poulet', 5, ''),
(38, 'test', 'blanc', 44, ''),
(39, 'Mclaren P1', 'Gris', 916, 'html/icons/695e3039f2fd5_image_2026-01-07_110642891.png'),
(40, 'Mclaren P1', 'rouge', 500, '');

-- --------------------------------------------------------

--
-- Structure de la table `Client`
--

CREATE TABLE `Client` (
  `IdClient` int(11) NOT NULL,
  `Nom` varchar(500) NOT NULL,
  `Prenom` varchar(500) NOT NULL,
  `DateNaiss` date NOT NULL,
  `Nationalite` varchar(100) NOT NULL,
  `NumTel` varchar(20) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `NumPermis` varchar(100) NOT NULL,
  `Commentaire` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `Client`
--

INSERT INTO `Client` (`IdClient`, `Nom`, `Prenom`, `DateNaiss`, `Nationalite`, `NumTel`, `Email`, `NumPermis`, `Commentaire`) VALUES
(1, 'Dupont', 'Jean', '1985-02-15', 'Française', '06 12 34 56 78', 'alban.tixier@hotmail.com', 'AB123456', 'Client fidèle'),
(2, 'Martin', 'Claire', '1990-07-22', 'Française', '07 98 76 54 32', 'claire.martin@example.com', 'CD987654', NULL),
(3, 'Nguyen', 'Thi', '1988-11-10', 'Vietnamienne', '06 45 67 89 01', 'thi.nguyen@example.com', 'EF654321', 'Préférence pour véhicules électriques'),
(4, 'Smith', 'John', '1975-03-05', 'Américaine', '01 23 45 67 89', 'john.smith@example.com', 'GH321987', NULL),
(37, 'Dupont', 'Jean', '1985-02-15', 'Française', '06 12 34 56 78', 'jean.dup@example.com', 'AB123459', 'Client fidèle'),
(38, 'Dupont', 'Jean', '1995-10-25', 'Française', '0607080910', 'jean.dupont@rentpark.fr', '24AA98765', NULL),
(40, 'Durand', 'Marc', '1988-12-01', 'Française', '0700000000', 'marc.durand.test@exemple.com', '99ZZ12345', NULL),
(44, 'a', 'a', '2008-03-01', 'a', '0606060606', 'quentin.miotto@etu.uca.fr', '15AA00000', NULL),
(45, 'roudier', 'andrea', '2006-05-16', 'francaise', '0606060606', 'clementroudier8@gmail.com', '15AA00001', NULL),
(48, 'Dupont', 'Jean', '1990-05-12', 'Française', '0601020304', 'jean.dupont@mail.com', 'PERMIS123', NULL),
(50, 'a', 'andrea', '2008-02-16', 'francaise', '0600000000', 'miotto.quentin@gmail.com', '15AA00008', NULL),
(53, 'roudier', 'andrea', '2026-02-20', 'francaise', '0606060606', 'abb.a@gmail.com', '15AA00007', NULL),
(54, 'roudier', 'andrea', '2026-02-07', 'francaise', '0606060606', 'a.aa@gmail.com', '15AA00078', NULL),
(55, 'roudier', 'andrea', '2026-01-24', 'francaise', '0606060606', 'arrr.a@gmail.com', '15AA00778', NULL),
(57, 'roudier', 'andrea', '2026-01-30', 'francaise', '0606060606', 'clementroudddier8@gmail.com', '15AA0000D', NULL),
(58, 'roudier', 'andrea', '2026-02-10', 'francaise', '0606060606', 'clementroudierddd8@gmail.com', '15AA00045', NULL),
(60, 'roudier', 'andrea', '2008-02-22', 'francaise', '0606060606', 'a.a@gmail.com', '15AA0S000', NULL),
(63, 'a', 'a', '2026-02-05', 'a', '0606060606', 'a.vass@gmail.com', '15AA0D000', NULL),
(64, 'a', 'a', '2026-02-05', 'a', '0606060606', 'a.vasds@gmail.com', '15AA0D00D', NULL),
(65, 'roudier', 'andrea', '2026-02-06', 'francaise', '0606060606', 'clementrddoudier8@gmail.com', '15AA0DD08', NULL),
(66, 'roudier', 'andrea', '2026-02-06', 'francaise', '0606060606', 'clementroddudier8@gmail.com', '15AA0SD00', NULL),
(67, 'roudier', 'andrea', '2026-02-07', 'francaise', '0606060606', 'chatgptweb2@gmddail.com', '15AA0000Z', NULL),
(68, 'albazn', 'fefezf', '2006-08-03', 'arabe', '06 06 06 06 06', 'azeazezaea@hotmail.com', '12AAZFZEFEAF', NULL),
(69, 'akban', 'teztzet', '2006-04-12', 'francaise', '06 06 06 06 06 ', 'alban.tixiezr@hoezrkgerg.com', '15AAAA0000', NULL),
(72, 'oiusfdjg', 'andrea', '2026-01-28', 'a', '0606060606', 'q.motto@gmail.com', '15AA00067', NULL),
(73, 'ROUDIER', 'clémentine', '2007-02-05', 'ARABE DE MERDE', '06 06 06 06 06', 'roudier__clementine@gmouille.fr', 'ABCDEFGHIJKLMNOP', NULL),
(74, 'Perrier', 'Clara', '2026-03-27', 'oui', '0676767670', 'clarala92i@sucemail.com', 'oiuytrez', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `Contrat`
--

CREATE TABLE `Contrat` (
  `IdContrat` int(11) NOT NULL,
  `DateDebut` date NOT NULL,
  `DateFin` date NOT NULL,
  `Statut` enum('EnCours','Terminé','EnCoursValidation','Annulé','Validé') NOT NULL DEFAULT 'EnCoursValidation',
  `IdClient` int(11) DEFAULT NULL,
  `EtatAvant` int(11) DEFAULT NULL,
  `EtatApres` int(11) DEFAULT NULL,
  `IdVehicule` char(17) DEFAULT NULL,
  `Marque` varchar(100) DEFAULT NULL,
  `NomModele` varchar(100) DEFAULT NULL,
  `AnneeModele` decimal(4,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `Contrat`
--

INSERT INTO `Contrat` (`IdContrat`, `DateDebut`, `DateFin`, `Statut`, `IdClient`, `EtatAvant`, `EtatApres`, `IdVehicule`, `Marque`, `NomModele`, `AnneeModele`) VALUES
(24, '2026-02-11', '2026-02-14', 'Annulé', 65, 5, NULL, 'TEST22335564', 'BMW', 'Série 3', 2021),
(25, '2026-02-11', '2026-02-14', 'Validé', 66, 5, NULL, 'TEST22335564', 'BMW', 'Série 3', 2021),
(26, '2026-02-13', '2026-02-21', 'Annulé', 67, 5, NULL, 'TEST22335564', 'BMW', 'Série 3', 2021),
(27, '2026-02-11', '2026-02-28', 'EnCoursValidation', 68, 5, NULL, 'WBA3A5C55DF123456', 'BMW', 'Série 3', 2021),
(28, '2026-02-12', '2026-02-20', 'EnCoursValidation', 69, 5, NULL, 'WBA3A5C55DF123456', 'BMW', 'Série 3', 2021),
(29, '2026-02-20', '2026-02-27', 'Validé', 72, 5, NULL, 'VF1RFB00368234568', 'Renault', 'Koleos', 2020),
(30, '2026-02-15', '2026-02-26', 'EnCoursValidation', 72, 5, NULL, 'TEST22335564', 'BMW', 'Série 3', 2021),
(31, '2026-02-15', '2026-02-26', 'EnCoursValidation', 72, 5, NULL, 'TEST22335564', 'BMW', 'Série 3', 2021),
(32, '2026-02-21', '2026-02-27', 'EnCoursValidation', 44, 5, NULL, 'TEST22335564', 'BMW', 'Série 3', 2021),
(33, '2026-02-26', '2026-02-27', 'EnCoursValidation', 44, 5, NULL, '1HGCM82633A004367', 'BMW', 'Série 3', 2021),
(34, '2026-03-06', '2026-03-06', 'EnCoursValidation', 44, 5, NULL, 'TEST22335564', 'BMW', 'Série 3', 2021),
(35, '2026-02-26', '2026-02-28', 'EnCoursValidation', 50, 5, NULL, 'VF1RFB00368234568', 'Renault', 'Koleos', 2020),
(36, '2026-02-27', '2026-03-01', 'EnCoursValidation', 50, 5, NULL, 'TEST22335564', 'BMW', 'Série 3', 2021),
(37, '2026-02-26', '2026-03-01', 'EnCoursValidation', 44, 5, NULL, 'TEST22335564', 'BMW', 'Série 3', 2021),
(39, '2026-02-26', '2026-03-06', 'Annulé', 60, 5, NULL, '1HGCM82633A004367', 'BMW', 'Série 3', 2021),
(40, '2026-04-08', '2026-04-17', 'EnCoursValidation', 69, NULL, NULL, 'WBA3A5C55DF123456', NULL, NULL, NULL),
(41, '2026-03-21', '2026-03-29', 'EnCoursValidation', 50, 5, NULL, 'TEST22335564', 'BMW', 'Série 3', 2021),
(42, '2026-03-14', '2026-03-21', 'EnCoursValidation', 44, 5, NULL, 'TEST22335564', 'BMW', 'Série 3', 2021),
(43, '2026-03-08', '2026-03-21', 'EnCoursValidation', 44, 5, NULL, 'TEST22335564', 'BMW', 'Série 3', 2021);

-- --------------------------------------------------------

--
-- Structure de la table `EtatDesLieux`
--

CREATE TABLE `EtatDesLieux` (
  `IdEtatLieu` int(11) NOT NULL,
  `Collaborateur` varchar(100) NOT NULL,
  `PhotosEtat` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `EtatDesLieux`
--

INSERT INTO `EtatDesLieux` (`IdEtatLieu`, `Collaborateur`, `PhotosEtat`) VALUES
(1, 'Alice Martin', 'etat_1.jpg'),
(2, 'Bob Dupont', 'etat_2.jpg'),
(3, 'Charlie Nguyen', 'etat_3.jpg'),
(4, 'David Smith', 'etat_4.jpg'),
(5, 'Elodie Laurent', 'etat_5.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `Fournisseur`
--

CREATE TABLE `Fournisseur` (
  `IdFournisseur` int(11) NOT NULL,
  `Nom` varchar(100) NOT NULL,
  `Rue` varchar(100) NOT NULL,
  `CodePostal` varchar(5) NOT NULL,
  `Ville` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `Fournisseur`
--

INSERT INTO `Fournisseur` (`IdFournisseur`, `Nom`, `Rue`, `CodePostal`, `Ville`) VALUES
(1, 'AutoDistribution', '12 Rue des Moteurs', '75012', 'Paris'),
(2, 'Garage Central', '45 Avenue des Champs', '69003', 'Lyon'),
(3, 'Fournitures Auto Sud', '78 Boulevard de la Liberté', '34000', 'Montpellier'),
(4, 'Véhicules & Co', '9 Rue du Commerce', '13001', 'Marseille');

-- --------------------------------------------------------

--
-- Structure de la table `Modele`
--

CREATE TABLE `Modele` (
  `Marque` varchar(100) NOT NULL,
  `Nom` varchar(100) NOT NULL,
  `Annee` decimal(4,0) NOT NULL,
  `Prix` decimal(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `Modele`
--

INSERT INTO `Modele` (`Marque`, `Nom`, `Annee`, `Prix`) VALUES
('Audi', 'A4', 2022, 47000.00),
('BMW', 'Série 3', 2021, 45000.00),
('Ford', 'Focus', 2019, 22000.00),
('Honda', 'Accord', 2019, 25000.00),
('Hyundai', 'Ioniq', 2022, 35000.00),
('Nissan', 'Qashqai', 2020, 31000.00),
('Peugeot', '3008', 2021, 30000.00),
('Renault', 'Clio', 2020, 35000.00),
('Renault', 'Koleos', 2020, 32000.00),
('Toyota', 'Corolla', 2020, 23000.00),
('Volkswagen', 'Golf', 2021, 27000.00);

-- --------------------------------------------------------

--
-- Structure de la table `PieceJustificatives`
--

CREATE TABLE `PieceJustificatives` (
  `IdPiece` int(11) NOT NULL,
  `PhotoIdentite` varchar(100) NOT NULL,
  `PhotoPermis` varchar(100) NOT NULL,
  `IdContrat` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `Rappel`
--

CREATE TABLE `Rappel` (
  `Id` int(11) NOT NULL,
  `Titre` varchar(100) NOT NULL,
  `Description` varchar(500) NOT NULL,
  `Date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `Rappel`
--

INSERT INTO `Rappel` (`Id`, `Titre`, `Description`, `Date`) VALUES
(1, 'lele', 'leleleleelelelelelelelel', '2026-02-25'),
(2, 'je test la', 'rolalalalalalaal c\'est juste un test', '2026-02-27'),
(3, 'blablalbal', 'esoifjezipohfqzhgiuhzqg', '2026-02-22'),
(4, 'efejfiefj', 'ijrzepogjrpzoijg', '2026-02-26'),
(5, 'dknz', 'ejnznfzejfnjekznfkjzne', '2026-02-27'),
(6, 'eifjeifj', 'iejzfojezpij', '2026-02-28');

-- --------------------------------------------------------

--
-- Structure de la table `testphp`
--

CREATE TABLE `testphp` (
  `voiture` int(150) NOT NULL,
  `modele` varchar(150) NOT NULL,
  `couleur` varchar(180) NOT NULL,
  `puissance` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `testphp`
--

INSERT INTO `testphp` (`voiture`, `modele`, `couleur`, `puissance`) VALUES
(0, 'bmw 118d', 'noire', 100);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','employe','user') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(79, 'clroudier', '$2y$12$u0uFfPbR0kUaXo5BrSKW6Or9QLCN0E05c5YC9ILUZsJQYp3ICAH/.', 'admin'),
(80, 'qumiotto', '$2y$12$EZy9rqqtyvtXRkVY8YjM2.U4cTAn0pjH/1edBf20WWFd2Ol4nAfXe', 'admin'),
(82, 'altixier', '$2y$12$8sBm4wlsuvxdZvBi9am6K.nlRC26/Qn2tWyVle3FqL64gT9IdQ.8q', 'admin'),
(83, 'dagauthier', '$2y$12$A6z9hCB28KL02bMjeJ5f9OZ14SvWTdT0tgYLNrgxIw15k42NjfZ2y', 'admin'),
(84, 'angrimaud', '$2y$12$Na5gWeapQp1sq.WUCoBH7eYrAxWoQ4Zg2vPwpGXpXzI5bkWeCJBYq', 'admin'),
(85, 'racaumond', '$2y$12$jTc/GBqfEnrfp0097PJE7.nE7x7prE/yJILTgNp8P6bLr.JKkEKdu', 'admin'),
(94, 'test', '$2y$12$JsHvx0UJER8sh1rlk0/UxeaxRm9upccFbwcA.ln7tmnk3fYSf.RjG', 'employe'),
(96, 'te', '$2y$12$XaJrfjbLB7Hi6nXtiNpXDOOdeVb.vNA117kPxxvw/uVPto8mIxFSy', 'employe'),
(102, 'angetbeau', '$2y$12$rrsjEemAt8fNI9.5yj3Ld.mlQi5ZSx7NNLzbrcFMTL7F3wWOCEjb.', 'employe'),
(103, 'eez', '$2y$12$Fe6u1cHe7IGmfqyVdF5eBeICAc6W5QXuCL/MplsVR2JqClM82Qfwm', 'employe'),
(105, 'admin', '$2y$12$3hX5V2xVo/I2eqhUg1iuT.8t/ruO7FCAzbCdf/UeB08VDQ4snJ04i', 'admin'),
(107, 'rere', '$2y$12$2pM1SfynhcKoAHJwiO6feuqIOvowoBWpOkLssJUtTfny/nlmE2R9m', 'admin'),
(108, 'testllll', '$2y$12$cZc0utMyOg4VVMeusnRqjeYdP2hegFGNnsOQhy7lFxBmIe/z8C9Ee', 'admin'),
(109, 'tetetetetet', '$2y$12$RGtF2v3a0rytRiUmnrig7.c/OdPzDUPo51q1vXiL2WTFp.mRcHUp6', 'employe'),
(110, 'tititititititit', '$2y$12$qYFBqWiQ/csEo48vXFGxx.xN7nZWj3B5mLkKIxPYPtjScMoeNXZO2', 'admin'),
(113, 'vhbrhvbhrbghrbg', '$2y$12$FnFkFAvHNll.dv65h7G7Eu5ZxmRvdUWliZylmOvY1YjB14u41RERu', 'admin'),
(114, 'testmoi', 'passhash', 'user'),
(117, 'je', '$2y$10$Db4xZmA58MMqtFpdNfq2pe5i/5LZ5V9DU5/hWuOZ5S19UxxBN3vTy', 'admin'),
(118, 'testEmploye', '$2y$10$lhyRRtS8pRnDDmqOqoxuP.3dhzXmDV3hSEYyV0HI6O4BD4Yh9LFvm', 'employe'),
(120, 'e', '$2y$12$Cr3pqEQgdqfzlylwwOSYD.cMGFGt7UdH8A0d8w/urf//x1gU9RvQi', 'employe');

-- --------------------------------------------------------

--
-- Structure de la table `Vehicule`
--

CREATE TABLE `Vehicule` (
  `NumSerie` char(17) NOT NULL,
  `Energie` varchar(100) NOT NULL,
  `NbPlaces` decimal(2,0) NOT NULL,
  `Categorie` varchar(20) NOT NULL,
  `Transmission` enum('Propulsion','Traction','Intégrale') NOT NULL DEFAULT 'Traction',
  `Boite` enum('Manuelle','Automatique','Semi-Manuelle') NOT NULL DEFAULT 'Manuelle',
  `Etat` enum('Libre','Louée','Vendue','Réparation') NOT NULL DEFAULT 'Libre',
  `Puissance` decimal(4,0) NOT NULL,
  `DateAchat` date NOT NULL,
  `DateExpirationControleTech` date NOT NULL,
  `DateDernierControleTech` date NOT NULL,
  `Marque` varchar(100) NOT NULL,
  `Nom` varchar(100) NOT NULL,
  `Annee` decimal(4,0) NOT NULL,
  `IdAssureur` int(11) NOT NULL,
  `IdFournisseur` int(11) NOT NULL,
  `ImagePath` varchar(255) NOT NULL,
  `Couleur` varchar(30) DEFAULT NULL,
  `Prix` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `Vehicule`
--

INSERT INTO `Vehicule` (`NumSerie`, `Energie`, `NbPlaces`, `Categorie`, `Transmission`, `Boite`, `Etat`, `Puissance`, `DateAchat`, `DateExpirationControleTech`, `DateDernierControleTech`, `Marque`, `Nom`, `Annee`, `IdAssureur`, `IdFournisseur`, `ImagePath`, `Couleur`, `Prix`) VALUES
('1HGCM82633A004367', 'Essence', 3, 'Berline', 'Traction', 'Manuelle', 'Libre', 150, '2019-03-15', '2025-03-15', '2024-03-10', 'BMW', 'Série 3', 2021, 1, 1, 'html/icons/cars/69a22938d1b29_698c33ffb39c2_698b741c047b0_bmw_serie3.jpg', 'Noir', 100.00),
('TEST22335564', 'Essence', 5, 'Berline', 'Intégrale', 'Manuelle', 'Libre', 500, '2024-01-01', '2025-01-16', '2025-01-01', 'BMW', 'Série 3', 2021, 1, 1, 'html/icons/cars/698c33f3cff7e_695e3039f2fd5_image_2026-01-07_110642891.png', 'Rouge', 100.00),
('VF1RFB00368234568', 'Diesel', 7, 'SUV', 'Intégrale', 'Automatique', 'Louée', 180, '2020-06-20', '2025-06-20', '2024-06-15', 'Renault', 'Koleos', 2020, 2, 2, 'html/icons/cars/698dccd292646_698c33ffb39c2_698b741c047b0_bmw_serie3.jpg', 'Noir', 200.00),
('WBA3A5C55DF123456', 'Hybride', 5, 'Berline', 'Propulsion', 'Automatique', 'Libre', 200, '2021-09-10', '2026-09-10', '2025-09-05', 'BMW', 'Série 3', 2021, 3, 3, 'html/icons/cars/698dccdf54dda_690349db960c1_690005bf56fad_bm.jpeg', 'Noir', 300.00);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `Assureur`
--
ALTER TABLE `Assureur`
  ADD PRIMARY KEY (`IdAssureur`);

--
-- Index pour la table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`voiture`);

--
-- Index pour la table `Client`
--
ALTER TABLE `Client`
  ADD PRIMARY KEY (`IdClient`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `NumPermis` (`NumPermis`);

--
-- Index pour la table `Contrat`
--
ALTER TABLE `Contrat`
  ADD PRIMARY KEY (`IdContrat`),
  ADD KEY `IdClient` (`IdClient`),
  ADD KEY `EtatAvant` (`EtatAvant`),
  ADD KEY `EtatApres` (`EtatApres`),
  ADD KEY `IdVehicule` (`IdVehicule`),
  ADD KEY `Marque` (`Marque`,`NomModele`,`AnneeModele`);

--
-- Index pour la table `EtatDesLieux`
--
ALTER TABLE `EtatDesLieux`
  ADD PRIMARY KEY (`IdEtatLieu`);

--
-- Index pour la table `Fournisseur`
--
ALTER TABLE `Fournisseur`
  ADD PRIMARY KEY (`IdFournisseur`);

--
-- Index pour la table `Modele`
--
ALTER TABLE `Modele`
  ADD PRIMARY KEY (`Marque`,`Nom`,`Annee`);

--
-- Index pour la table `PieceJustificatives`
--
ALTER TABLE `PieceJustificatives`
  ADD PRIMARY KEY (`IdPiece`),
  ADD KEY `IdContrat` (`IdContrat`);

--
-- Index pour la table `Rappel`
--
ALTER TABLE `Rappel`
  ADD PRIMARY KEY (`Id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Index pour la table `Vehicule`
--
ALTER TABLE `Vehicule`
  ADD PRIMARY KEY (`NumSerie`),
  ADD KEY `IdAssureur` (`IdAssureur`),
  ADD KEY `IdFournisseur` (`IdFournisseur`),
  ADD KEY `Marque` (`Marque`,`Nom`,`Annee`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `Assureur`
--
ALTER TABLE `Assureur`
  MODIFY `IdAssureur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `cars`
--
ALTER TABLE `cars`
  MODIFY `voiture` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT pour la table `Client`
--
ALTER TABLE `Client`
  MODIFY `IdClient` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT pour la table `Contrat`
--
ALTER TABLE `Contrat`
  MODIFY `IdContrat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT pour la table `EtatDesLieux`
--
ALTER TABLE `EtatDesLieux`
  MODIFY `IdEtatLieu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `Fournisseur`
--
ALTER TABLE `Fournisseur`
  MODIFY `IdFournisseur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `PieceJustificatives`
--
ALTER TABLE `PieceJustificatives`
  MODIFY `IdPiece` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `Rappel`
--
ALTER TABLE `Rappel`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `Contrat`
--
ALTER TABLE `Contrat`
  ADD CONSTRAINT `Contrat_ibfk_1` FOREIGN KEY (`IdClient`) REFERENCES `Client` (`IdClient`),
  ADD CONSTRAINT `Contrat_ibfk_2` FOREIGN KEY (`EtatAvant`) REFERENCES `EtatDesLieux` (`IdEtatLieu`),
  ADD CONSTRAINT `Contrat_ibfk_3` FOREIGN KEY (`EtatApres`) REFERENCES `EtatDesLieux` (`IdEtatLieu`),
  ADD CONSTRAINT `Contrat_ibfk_4` FOREIGN KEY (`IdVehicule`) REFERENCES `Vehicule` (`NumSerie`),
  ADD CONSTRAINT `Contrat_ibfk_5` FOREIGN KEY (`Marque`,`NomModele`,`AnneeModele`) REFERENCES `Modele` (`Marque`, `Nom`, `Annee`);

--
-- Contraintes pour la table `PieceJustificatives`
--
ALTER TABLE `PieceJustificatives`
  ADD CONSTRAINT `PieceJustificatives_ibfk_1` FOREIGN KEY (`IdContrat`) REFERENCES `Contrat` (`IdContrat`);

--
-- Contraintes pour la table `Vehicule`
--
ALTER TABLE `Vehicule`
  ADD CONSTRAINT `Vehicule_ibfk_1` FOREIGN KEY (`IdAssureur`) REFERENCES `Assureur` (`IdAssureur`),
  ADD CONSTRAINT `Vehicule_ibfk_2` FOREIGN KEY (`IdFournisseur`) REFERENCES `Fournisseur` (`IdFournisseur`),
  ADD CONSTRAINT `Vehicule_ibfk_3` FOREIGN KEY (`Marque`,`Nom`,`Annee`) REFERENCES `Modele` (`Marque`, `Nom`, `Annee`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
