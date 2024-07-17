<?php
session_start();
include('../admin/config.php');

if (!isset($_GET['id'])) {
    header('Location: ../admin/dashboard.php');
    exit;
}

$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM formations WHERE id = ?");
$stmt->execute([$id]);

$_SESSION['message'] = 'Formation supprimée avec succès';
header('Location: ../admin/dashboard.php');
exit;
?>
