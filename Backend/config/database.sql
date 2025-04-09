-- Création de la base de données
CREATE DATABASE IF NOT EXISTS MondialAutomobile
DEFAULT CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE MondialAutomobile;

-- Table : utilisateurs
CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL, -- Mot de passe hashé
    role ENUM('admin', 'attente') DEFAULT 'attente', -- Mise à jour des rôles
    email VARCHAR(150) NOT NULL UNIQUE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table : voitures
CREATE TABLE IF NOT EXISTS voitures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    marque VARCHAR(100) NOT NULL,
    modele VARCHAR(100) NOT NULL,
    annee INT NOT NULL,
    prix DECIMAL(10,2) NOT NULL,
    kilometrage INT NOT NULL,
    carburant VARCHAR(50) DEFAULT NULL,
    boite VARCHAR(50) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    images TEXT DEFAULT NULL, -- Chaîne JSON ou séparée
    date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
    est_vendu BOOLEAN DEFAULT FALSE,
    vendeur_id INT DEFAULT NULL,
    FOREIGN KEY (vendeur_id) REFERENCES utilisateurs(id) ON DELETE SET NULL ON UPDATE CASCADE
);

-- Table : reprises
CREATE TABLE IF NOT EXISTS reprises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) DEFAULT NULL,
    email VARCHAR(150) DEFAULT NULL,
    marque VARCHAR(100) DEFAULT NULL,
    modele VARCHAR(100) DEFAULT NULL,
    annee INT DEFAULT NULL,
    kilometrage INT DEFAULT NULL,
    statut ENUM('En attente', 'Acceptée', 'Refusée') DEFAULT 'En attente',
    date_demande DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table : services
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_service VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    icone VARCHAR(255) DEFAULT NULL,
    prix DECIMAL(10,2) DEFAULT NULL
);

-- Table : messages_contact
CREATE TABLE IF NOT EXISTS messages_contact (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) DEFAULT NULL,
    email VARCHAR(150) NOT NULL,
    sujet VARCHAR(150) DEFAULT NULL,
    message TEXT NOT NULL,
    date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
    reponse TEXT DEFAULT NULL,
    admin_id INT DEFAULT NULL,
    FOREIGN KEY (admin_id) REFERENCES utilisateurs(id) ON DELETE SET NULL ON UPDATE CASCADE
);

-- Table : avis_google
CREATE TABLE IF NOT EXISTS avis_google (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    note INT NOT NULL CHECK (note BETWEEN 1 AND 5),
    commentaire TEXT DEFAULT NULL,
    date_avis DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table : panier
CREATE TABLE IF NOT EXISTS panier (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    voiture_id INT NOT NULL,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (voiture_id) REFERENCES voitures(id) ON DELETE CASCADE ON UPDATE CASCADE
);
