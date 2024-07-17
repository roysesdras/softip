<?php
session_start();
include('../admin/config.php');

// Vérifiez si un étudiant, un formateur ou un administrateur est connecté
if (!isset($_SESSION['student_id']) && !isset($_SESSION['trainer_id']) && !isset($_SESSION['admin_id'])) {
    header('Location: ../students/login_student_cool.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    
    // Insérer le sujet dans la base de données
    $stmt = $pdo->prepare("INSERT INTO topics (title, description, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$title, $description]);
    
    $_SESSION['success_message'] = 'Sujet créé avec succès.';
    header('Location: forum.php');
    exit;
}
?>
