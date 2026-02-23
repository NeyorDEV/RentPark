-- ====================================
-- Fichier rentpark.sql
-- Base: RentPark
-- ====================================

-- Création de la base
CREATE DATABASE IF NOT EXISTS RentPark
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE RentPark;

-- =======================
-- Table Assureur
-- =======================
CREATE TABLE Assureur (
    IdAssureur INT(11) NOT NULL PRIMARY KEY,
    Nom VARCHAR(100) NOT NULL,
    NumRue DECIMAL(10,0) NOT NULL,
    NumTel VARCHAR(20) NOT NULL,
    CodePostal VARCHAR(5) NOT NULL,
    Rue VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO Assureur VALUES
(1,'AssureurTest',12,'0123456789','75001','Rue Test');

-- =======================
-- Table Client
-- =======================
CREATE TABLE Client (
    IdClient INT(11) NOT NULL PRIMARY KEY,
    Nom VARCHAR(500) NOT NULL,
    Prenom VARCHAR(500) NOT NULL,
    DateNaiss DATE NOT NULL,
    Nationalite VARCHAR(100) NOT NULL,
    NumTel VARCHAR(20) NOT NULL,
    Email VARCHAR(255) NOT NULL,
    NumPermis VARCHAR(100) NOT NULL,
    Commentaire TEXT DEFAULT NULL,
    UNIQUE KEY Email (Email),
    UNIQUE KEY NumPermis (NumPermis)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO Client VALUES
(1,'Dupont','Jean','1980-01-01','Française','0123456789','j.dupont@test.com','AB123456',NULL);

-- =======================
-- Table Compte
-- =======================
CREATE TABLE Compte (
    UserName VARCHAR(50) NOT NULL PRIMARY KEY,
    MdpHash VARCHAR(255) NOT NULL,
    Role ENUM('admin', 'collaborateur', 'client') NOT NULL,
    IdClient INT(11) DEFAULT NULL,
    CONSTRAINT fk_compte_client FOREIGN KEY (IdClient) REFERENCES Client(IdClient)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO Compte VALUES
('admin','hashpassword','admin',1);

-- =======================
-- Table Fournisseur
-- =======================
CREATE TABLE Fournisseur (
    IdFournisseur INT(11) NOT NULL PRIMARY KEY,
    Nom VARCHAR(100) NOT NULL,
    Rue VARCHAR(100) NOT NULL,
    CodePostal VARCHAR(5) NOT NULL,
    Ville VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO Fournisseur VALUES
(1,'FournisseurTest','10 Rue Test','75002','Paris');

-- =======================
-- Table Modele
-- =======================
CREATE TABLE Modele (
    Marque VARCHAR(100) NOT NULL,
    Nom VARCHAR(100) NOT NULL,
    Annee DECIMAL(4,0) NOT NULL,
    Prix DECIMAL(8,2) NOT NULL,
    PRIMARY KEY(Marque, Nom, Annee)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO Modele VALUES
('Toyota','Corolla',2020,20000.00);

-- =======================
-- Table Vehicule
-- =======================
CREATE TABLE Vehicule (
    NumSerie CHAR(17) NOT NULL PRIMARY KEY,
    Energie VARCHAR(100) NOT NULL,
    NbPlaces DECIMAL(2,0) NOT NULL,
    Categorie VARCHAR(20) NOT NULL,
    Transmission ENUM('Propulsion','Traction','Intégrale') NOT NULL DEFAULT 'Traction',
    Boite ENUM('Manuelle','Automatique','Semi-Manuelle') NOT NULL DEFAULT 'Manuelle',
    Etat ENUM('Libre','Louée','Vendue','Réparation') NOT NULL DEFAULT 'Libre',
    Puissance DECIMAL(4,0) NOT NULL,
    DateAchat DATE NOT NULL,
    DateExpirationControleTech DATE NOT NULL,
    DateDernierControleTech DATE NOT NULL,
    Marque VARCHAR(100) NOT NULL,
    Nom VARCHAR(100) NOT NULL,
    Annee DECIMAL(4,0) NOT NULL,
    IdAssureur INT(11) NOT NULL,
    IdFournisseur INT(11) NOT NULL,
    ImagePath VARCHAR(255) NOT NULL,
    Couleur VARCHAR(30) DEFAULT NULL,
    Prix DECIMAL(5,2) DEFAULT NULL,
    KEY IdAssureur (IdAssureur),
    KEY IdFournisseur (IdFournisseur),
    KEY Marque (Marque),
    KEY Nom (Nom),
    CONSTRAINT fk_vehicule_assureur FOREIGN KEY (IdAssureur) REFERENCES Assureur(IdAssureur),
    CONSTRAINT fk_vehicule_fournisseur FOREIGN KEY (IdFournisseur) REFERENCES Fournisseur(IdFournisseur)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO Vehicule VALUES
('123456789ABCDEFG','Essence',5,'Berline','Traction','Manuelle','Libre',130,'2023-01-01','2024-01-01','2023-12-01','Toyota','Corolla',2020,1,1,'html/icons/cars/698dccd292646_698c33ffb39c2_698b741c047b0_bmw_serie3.jpg','Blanc',20000.00);

-- =======================
-- Table Contrat
-- =======================
CREATE TABLE Contrat (
    IdContrat INT(11) NOT NULL PRIMARY KEY,
    DateDebut DATE NOT NULL,
    DateFin DATE NOT NULL,
    Statut ENUM('EnCours','Terminé','EnCoursValidation','Annulé','Validé') NOT NULL DEFAULT 'EnCoursValidation',
    IdClient INT(11) DEFAULT NULL,
    EtatAvant INT(11) DEFAULT NULL,
    EtatApres INT(11) DEFAULT NULL,
    IdVehicule CHAR(17) DEFAULT NULL,
    Marque VARCHAR(100) DEFAULT NULL,
    NomModele VARCHAR(100) DEFAULT NULL,
    AnneeModele DECIMAL(4,0) DEFAULT NULL,
    KEY IdClient (IdClient),
    KEY EtatAvant (EtatAvant),
    KEY EtatApres (EtatApres),
    KEY IdVehicule (IdVehicule),
    KEY Marque (Marque),
    KEY NomModele (NomModele),
    CONSTRAINT fk_contrat_client FOREIGN KEY (IdClient) REFERENCES Client(IdClient)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO Contrat VALUES
(1,'2024-02-01','2024-02-10','EnCoursValidation',1,NULL,NULL,'123456789ABCDEFG','Toyota','Corolla',2020);

-- =======================
-- Table EtatDesLieux
-- =======================
CREATE TABLE EtatDesLieux (
    IdEtatLieu INT(11) NOT NULL PRIMARY KEY,
    Collaborateur VARCHAR(100) NOT NULL,
    PhotosEtat VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO EtatDesLieux VALUES
(1,'Alice','/photos/etat1.jpg');

-- =======================
-- Table PieceJustificatives
-- =======================
CREATE TABLE PieceJustificatives (
    IdPiece INT(11) NOT NULL PRIMARY KEY,
    PhotoIdentite VARCHAR(100) NOT NULL,
    PhotoPermis VARCHAR(100) NOT NULL,
    IdContrat INT(11) DEFAULT NULL,
    KEY IdContrat (IdContrat),
    CONSTRAINT fk_piece_contrat FOREIGN KEY (IdContrat) REFERENCES Contrat(IdContrat)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO PieceJustificatives VALUES
(1,'/photos/id1.jpg','/photos/permis1.jpg',1);

-- =======================
-- Table Rappel
-- =======================
CREATE TABLE Rappel (
    Id INT(11) NOT NULL PRIMARY KEY,
    Titre VARCHAR(100) NOT NULL,
    Description VARCHAR(500) NOT NULL,
    Date DATE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO Rappel VALUES
(1,'Rappel Contrat','Vérifier le contrat 1','2024-02-05');

-- =======================
-- Table Users
-- =======================
CREATE TABLE Users (
    id INT(11) NOT NULL PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','employe','client') NOT NULL,
    UNIQUE KEY username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO Users VALUES
(1,'admin','adminpass','admin');

