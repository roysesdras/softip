<?php
session_start();
include('../admin/config.php');

if (!isset($_SESSION['student_id'])) {
    header('Location: login_student.php');
    exit;
}

$student_id = $_SESSION['student_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quiz_id = isset($_POST['quiz_id']) ? $_POST['quiz_id'] : null;
    $answers = isset($_POST['answers']) ? $_POST['answers'] : [];

    if (!$quiz_id) {
        echo "Le quiz_id est manquant.";
        exit;
    }

    // Vérifier si le quiz_id existe dans la base de données
    $stmt_check_quiz = $pdo->prepare("SELECT id FROM quizzes WHERE id = ?");
    $stmt_check_quiz->execute([$quiz_id]);
    if ($stmt_check_quiz->rowCount() == 0) {
        echo "Le quiz_id spécifié n'existe pas.";
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Enregistrer les réponses de l'étudiant
        foreach ($answers as $question_id => $answer) {
            $stmt = $pdo->prepare("
                INSERT INTO student_answers (student_id, quiz_id, question_id, answer)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE answer = VALUES(answer)
            ");
            $stmt->execute([$student_id, $quiz_id, $question_id, $answer]);
        }

        // Calculer et enregistrer le score du quiz pour l'étudiant
        $score = calculateQuizScore($quiz_id, $student_id, $pdo);

        $stmt_score = $pdo->prepare("
            INSERT INTO quiz_answers (student_id, quiz_id, question_id, answer, score)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE score = VALUES(score)
        ");
        $stmt_score->execute([$student_id, $quiz_id, $question_id, $answer, $score]);

        $pdo->commit();
        header('Location: student_dashboard.php?submission=success');
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Erreur lors de l'enregistrement des réponses : " . $e->getMessage();
    }
}

function calculateQuizScore($quiz_id, $student_id, $pdo) {
    // Récupérer les réponses correctes pour chaque question du quiz
    $stmt = $pdo->prepare("
        SELECT q.id, q.correct_option
        FROM questions q
        WHERE q.quiz_id = ?
    ");

    $stmt->execute([$quiz_id]);
    $questions = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // Récupérer les réponses de l'étudiant pour chaque question du quiz
    $stmt = $pdo->prepare("
        SELECT sa.question_id, sa.answer
        FROM student_answers sa
        WHERE sa.quiz_id = ? AND sa.student_id = ?
    ");
    $stmt->execute([$quiz_id, $student_id]);
    $student_answers = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // Calculer le score du quiz
    $score = 0;
    foreach ($questions as $question_id => $correct_option) {
        if (isset($student_answers[$question_id]) && $student_answers[$question_id] == $correct_option) {
            $score++;
        }
    }

    // Insérer le score dans la table grades
    $stmt = $pdo->prepare("INSERT INTO grades (quiz_id, student_id, grade, session_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$quiz_id, $student_id, $score, $session_id]);

    return $score;
}
?>
