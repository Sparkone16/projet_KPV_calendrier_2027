<?php
require_once __DIR__ . '/connexion.php';

try {
    $dsn = 'mysql:host=' . $dbConnexion['host'] . ';charset=utf8mb4';

    $pdo = new PDO($dsn, $dbConnexion['user'], $dbConnexion['pass'], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

    $dbName = $dbConnexion['name'];
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbName`");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS G_KPV_calendrier (
            id_calendrier INT AUTO_INCREMENT PRIMARY KEY,
            nom_calendrier VARCHAR(255) NOT NULL,
            description_calendrier TEXT,
            couleur_calendrier VARCHAR(7) DEFAULT '#b77ee6'
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS G_KPV_evenement (
            id_evenement INT AUTO_INCREMENT PRIMARY KEY,
            nom_evenement VARCHAR(255) NOT NULL,
            date_evenement DATE NOT NULL,
            heure_evenement TIME NULL,
            duree_evenement INT NULL,
            journee_evenement BOOLEAN NOT NULL DEFAULT FALSE,
            description_evenement TEXT,
            icone_evenement VARCHAR(500),
            id_calendrier INT NOT NULL,
            FOREIGN KEY (id_calendrier) REFERENCES G_KPV_calendrier(id_calendrier) ON DELETE CASCADE
        )
    ");

} catch (PDOException $e) {
    http_response_code(500);
    die('Erreur de base de données : ' . htmlspecialchars($e->getMessage()));
}