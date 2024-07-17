<?php

// Paramètres de connexion à la base de données MySQL
$host = '127.0.0.1';
$db = 'soft_ip';
$user = 'root'; // Votre nom d'utilisateur MySQL
$pass = ''; // Votre mot de passe MySQL
$charset = 'utf8mb4';

// DSN (Data Source Name) pour PDO
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Options PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Activer le mode d'affichage des erreurs PDO
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Récupération des résultats en tableau associatif par défaut
    PDO::ATTR_EMULATE_PREPARES   => false, // Désactiver l'émulation des requêtes préparées pour prévenir les injections SQL
];

// Création de l'objet PDO
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // En cas d'échec de connexion, lancer une exception PDO
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

?>