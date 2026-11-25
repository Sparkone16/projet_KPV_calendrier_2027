-- Création de la base WEBL2_PROJET_2026_27
CREATE DATABASE IF NOT EXISTS WEBL2_PROJET_2026_27;

USE WEBL2_PROJET_2026_27;

-- Création de la table G_KPV_calendrier
CREATE TABLE IF NOT EXISTS G_KPV_calendrier
(
    id_calendrier INT AUTO_INCREMENT PRIMARY KEY,
    nom_calendrier VARCHAR(255) NOT NULL,
    description_calendrier TEXT,
    couleur_calendrier VARCHAR(7) DEFAULT '#b77ee6'
);

-- Création de la table G_KPV_evenement
CREATE TABLE IF NOT EXISTS G_KPV_evenement
(
    id_evenement INT AUTO_INCREMENT PRIMARY KEY,
    nom_evenement VARCHAR(255) NOT NULL,
    date_evenement DATE NOT NULL,
    heure_evenement TIME NULL,
    duree_evenement INT NULL, -- durée en minutes
    journee_evenement BOOLEAN NOT NULL DEFAULT FALSE,
    description_evenement TEXT,
    icone_evenement VARCHAR(500),
    id_calendrier INT NOT NULL,
    FOREIGN KEY (id_calendrier) REFERENCES G_KPV_calendrier(id_calendrier) ON DELETE CASCADE
);