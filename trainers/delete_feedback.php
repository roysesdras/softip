<?php
session_start();
include('../admin/config.php');

// // Vérifier si l'utilisateur est un formateur
// if (!isset($_SESSION['trainer_id']) || $_SESSION['role'] != 'trainer') {
//     header('Location: login_trainer.php');
//     exit;
// }

$feedback_id = $_GET['id'];

// Supprimer le feedback
$stmt = $pdo->prepare("DELETE FROM feedbacks WHERE id = ?");
$stmt->execute([$feedback_id]);

$_SESSION['success_message'] = 'Feedback supprimé avec succès';
header('Location: trainers.php');
exit;
