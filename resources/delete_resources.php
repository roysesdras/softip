<?php
session_start();
include('../admin/config.php');

// Vérifier si l'ID de la ressource est passé dans l'URL
if (!isset($_GET['id'])) {
    header('Location: resources.php'); // Redirection vers la page de gestion des ressources
    exit;
}

$id = $_GET['id'];

// Suppression de la ressource dans la base de données
$stmt = $pdo->prepare("DELETE FROM resources WHERE id = ?");
$stmt->execute([$id]);

// Redirection avec un message de confirmation
$_SESSION['success_message'] = 'Ressource supprimée avec succès';
header('Location: ../trainers/trainers.php#resources'); // Rediriger vers la page de gestion des ressources
exit;
?>
