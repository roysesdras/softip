<?php
session_start();
include('../admin/config.php');

// Vérifier si l'utilisateur est connecté en tant que formateur
if (!isset($_SESSION['trainer_id'])) {
    header('Location: login_trainer.php');
    exit;
}

$trainer_id = $_SESSION['trainer_id'];

// Vérifier si un ID de quiz est passé dans l'URL
if (isset($_GET['id'])) {
    $quiz_id = $_GET['id'];

    try {
        // Démarrer une transaction pour assurer l'intégrité des données
        $pdo->beginTransaction();

        // Supprimer les réponses associées à ce quiz
        $stmt = $pdo->prepare("DELETE FROM student_answers WHERE question_id IN (SELECT id FROM questions WHERE quiz_id = ?)");
        $stmt->execute([$quiz_id]);

        // Supprimer les questions associées à ce quiz
        $stmt = $pdo->prepare("DELETE FROM questions WHERE quiz_id = ?");
        $stmt->execute([$quiz_id]);

        // Supprimer le quiz
        $stmt = $pdo->prepare("DELETE FROM quizzes WHERE id = ? AND trainer_id = ?");
        $stmt->execute([$quiz_id, $trainer_id]);

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
    echo 'ID du quiz non spécifié.';
    exit;
}
?>
