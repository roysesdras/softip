<?php
session_start();
include('../admin/config.php');

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['student_id'])) {
    header('Location: ../students/login_student_cool.php');
    exit;
}

$title = $_POST['title'];
$description = $_POST['description'];
$created_by = $_SESSION['student_id'];

$query = "INSERT INTO topics (title, description, created_by) VALUES (:title, :description, :created_by)";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':title', $title);
$stmt->bindParam(':description', $description);
$stmt->bindParam(':created_by', $created_by);

if ($stmt->execute()) {
    header('Location: forum.php');
} else {
    echo "Erreur lors de la création du sujet.";
}
?>
