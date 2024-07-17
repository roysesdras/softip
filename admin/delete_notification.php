<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

if (isset($_GET['id'])) {
    $notification_id = $_GET['id'];

    // Supprimer la notification
    $stmt = $pdo->prepare("DELETE FROM notifications WHERE id = ?");
    $stmt->execute([$notification_id]);

    header('Location: dashboard.php#notif');
    exit;
} else {
    echo "ID de notification manquant.";
    exit;
}
