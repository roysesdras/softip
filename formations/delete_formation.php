<?php
session_start();
include('../admin/config.php');

if (!isset($_GET['id'])) {
    header('Location: ../admin/dashboard.php');
    exit;
}

$id = $_GET['id'];

try {
    $pdo->beginTransaction();
    
    // Supprimer les inscriptions associées à la formation
    $deleteInscriptions = $pdo->prepare("DELETE FROM inscriptions WHERE formation_id = ?");
    $deleteInscriptions->execute([$id]);
    
    // Supprimer la formation
    $deleteFormation = $pdo->prepare("DELETE FROM formations WHERE id = ?");
    $deleteFormation->execute([$id]);
    
    $pdo->commit();
    
    $_SESSION['message'] = 'Formation supprimée avec succès';
} catch (PDOException $e) {
    $pdo->rollBack();
    $_SESSION['message'] = 'Erreur lors de la suppression de la formation : ' . $e->getMessage();
}

header('Location: ../admin/dashboard.php');
exit;
?>
