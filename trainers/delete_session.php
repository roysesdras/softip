<?php
session_start();
include('../admin/config.php');

// Vérifier si l'utilisateur est connecté en tant que formateur
if (!isset($_SESSION['trainer_id'])) {
    header('Location: login_trainer.php');
    exit;
}

$trainer_id = $_SESSION['trainer_id'];

// Vérifier si un ID de session est passé dans l'URL
if (isset($_GET['id'])) {
    $session_id = $_GET['id'];

    try {
        // Démarrer une transaction pour assurer l'intégrité des données
        $pdo->beginTransaction();

        // Supprimer les feedbacks associés à cette session
        $stmt = $pdo->prepare("DELETE FROM feedbacks WHERE session_id = ?");
        $stmt->execute([$session_id]);

        // Supprimer la session
        $stmt = $pdo->prepare("DELETE FROM sessions WHERE id = ? AND trainer_id = ?");
        $stmt->execute([$session_id, $trainer_id]);

        // Valider la transaction
        $pdo->commit();

        // Rediriger vers la page des formateurs avec un message de succès
        header('Location: ../trainers/trainers.php?success=true');
        exit;

    } catch (PDOException $e) {
        // Annuler la transaction en cas d'erreur
        $pdo->rollBack();
        echo 'Erreur : ' . $e->getMessage();
    }
} else {
    echo 'ID de la session non spécifié.';
    exit;
}
?>
