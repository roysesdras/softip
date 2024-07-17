<?php
session_start();
include('../admin/config.php');

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['student_id'])) {
    header('Location: ../students/login_student_cool.php');
    exit;
}

$topic_id = $_POST['topic_id'];
$message = $_POST['message'];
$student_id = $_SESSION['student_id'];

$query = "INSERT INTO discussions (topic_id, student_id, message) VALUES (:topic_id, :student_id, :message)";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':topic_id', $topic_id);
$stmt->bindParam(':student_id', $student_id);
$stmt->bindParam(':message', $message);

if ($stmt->execute()) {
    header('Location: topic.php?id=' . $topic_id);
} else {
    echo "Erreur lors de l'ajout de la discussion.";
}
?>
