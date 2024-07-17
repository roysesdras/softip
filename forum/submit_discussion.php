<?php
session_start();
include('../admin/config.php');

// Vérifiez si un étudiant, un formateur ou un administrateur est connecté
if (!isset($_SESSION['student_id']) && !isset($_SESSION['trainer_id']) && !isset($_SESSION['admin_id'])) {
    header('Location: ../students/login_student_cool.php');
    exit;
}

$topic_id = $_POST['topic_id'];
$message = $_POST['message'];
$user_id = null;
$user_type = null;

if (isset($_SESSION['student_id'])) {
    $user_id = $_SESSION['student_id'];
    $user_type = 'student';
} elseif (isset($_SESSION['trainer_id'])) {
    $user_id = $_SESSION['trainer_id'];
    $user_type = 'trainer';
} elseif (isset($_SESSION['admin_id'])) {
    $user_id = $_SESSION['admin_id'];
    $user_type = 'admin';
}

$query = "INSERT INTO discussions (topic_id, {$user_type}_id, message) VALUES (:topic_id, :user_id, :message)";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindParam(':message', $message, PDO::PARAM_STR);

if ($stmt->execute()) {
    header('Location: topic.php?id=' . $topic_id);
} else {
    echo "Erreur lors de l'ajout de la discussion.";
}
?>
