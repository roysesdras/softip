<?php
session_start();
include('../admin/config.php');

if (!isset($_SESSION['trainer_id'])) {
    header('Location: login_trainer.php');
    exit;
}

if (isset($_GET['id'])) {
    $question_id = $_GET['id'];

    try {
        // Suppression des réponses associées à cette question
        $stmt = $pdo->prepare("DELETE FROM student_answers WHERE question_id = ?");
        $stmt->execute([$question_id]);

        // Suppression de la question
        $stmt = $pdo->prepare("DELETE FROM questions WHERE id = ? AND trainer_id = ?");
        $stmt->execute([$question_id, $_SESSION['trainer_id']]);

        // Rediriger vers le tableau de bord des formateurs avec un message de succès
        header('Location: ../trainers/trainers.php?question_deleted=true');
        exit;

    } catch (PDOException $e) {
        echo 'Erreur : ' . $e->getMessage();
    }
} else {
    echo 'ID de la question manquant.';
}
?>
