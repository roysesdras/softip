<?php
session_start();
include('../admin/config.php');

// Vérifier l'ID de formation
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $_SESSION['message'] = 'ID de formation invalide.';
    header('Location: ../admin/dashboard.php');
    exit;
}

$id = (int)$_GET['id'];

// Préparer la suppression
try {
    // Commencer une transaction
    $pdo->beginTransaction();

    // Supprimer les inscriptions associées
    $stmt = $pdo->prepare("DELETE FROM inscriptions WHERE formation_id = ?");
    $stmt->execute([$id]);

    // Supprimer la formation
    $stmt = $pdo->prepare("DELETE FROM formations WHERE id = ?");
    $stmt->execute([$id]);

    // Valider la transaction
    $pdo->commit();

    $_SESSION['message'] = 'Formation supprimée avec succès.';
} catch (PDOException $e) {
    // Annuler la transaction en cas d'erreur
    $pdo->rollBack();
    $_SESSION['message'] = 'Erreur lors de la suppression de la formation : ' . $e->getMessage();
}

header('Location: ../admin/dashboard.php');
exit;
