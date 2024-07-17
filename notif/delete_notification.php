<?php
session_start();
include('../admin/config.php');

// Vérifier si l'utilisateur est un formateur ou un administrateur
if (!isset($_SESSION['trainer_id']) && !isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Récupérer l'identifiant de la notification depuis l'URL
$id = $_GET['id'] ?? null;

if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM notifications WHERE id = ?");
        $stmt->execute([$id]);

        // Rediriger vers le tableau de bord avec un message de succès
        header('Location: insert_notification.php?notification_deleted=true');
        exit;
    } catch (PDOException $e) {
        echo 'Erreur : ' . $e->getMessage();
    }
} else {
    header('Location: insert_notification.php');
    exit;
}
