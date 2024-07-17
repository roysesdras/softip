<?php
session_start();
include('../admin/config.php');

if (!isset($_SESSION['student_id'])) {
    header('Location: login_student.php');
    exit;
}

$student_id = $_SESSION['student_id'];

// Vérifier si l'objet PDO est bien créé
if (!$pdo) {
    die("Erreur de connexion à la base de données");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $session_id = $_POST['session_id'];
    $feedback = $_POST['feedback'];

    // Valider et nettoyer les données
    $session_id = filter_var($session_id, FILTER_VALIDATE_INT);
    $feedback = htmlspecialchars($feedback);

    if ($session_id && $feedback) {
        $stmt = $pdo->prepare("INSERT INTO student_feedbacks (session_id, student_id, feedback) VALUES (?, ?, ?)");
        $stmt->execute([$session_id, $student_id, $feedback]);

        header('Location: student_dashboard.php');
        exit;
    } else {
        echo "Données invalides. Veuillez réessayer.";
    }
} else {
    header('Location: student_dashboard.php');
    exit;
}
?>
