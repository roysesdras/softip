<?php
session_start();
include('../admin/config.php');

// Assure-toi que le formateur est connecté
if (!isset($_SESSION['trainer_id'])) {
    header('Location: login_trainer.php');
    exit;
}

$trainer_id = $_SESSION['trainer_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les informations depuis le formulaire
    $quiz_id = isset($_POST['quiz_id']) ? $_POST['quiz_id'] : null;
    $student_id = isset($_POST['student_id']) ? $_POST['student_id'] : null;
    $question_id = isset($_POST['question_id']) ? $_POST['question_id'] : null;
    $grade = isset($_POST['grade']) ? $_POST['grade'] : null;

    if (!$quiz_id || !$student_id || !$question_id || $grade === null) {
        echo "Des informations nécessaires sont manquantes.";
        exit;
    }

    try {
        // Enregistrer la note dans la base de données
        $stmt = $pdo->prepare("
            INSERT INTO grades (quiz_id, student_id, question_id, grade)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE grade = VALUES(grade)
        ");
        $stmt->execute([$quiz_id, $student_id, $question_id, $grade]);

        header('Location: trainer_dashboard.php?quiz_id=' . $quiz_id);
        exit;
    } catch (Exception $e) {
        echo "Erreur lors de l'enregistrement de la note : " . $e->getMessage();
    }
}
