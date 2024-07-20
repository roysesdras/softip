<?php
session_start();
include('../admin/config.php');

if (!isset($_SESSION['trainer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé.']);
    exit;
}

$trainer_id = $_SESSION['trainer_id'];
$course_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$course_id) {
    echo json_encode(['success' => false, 'message' => 'Identifiant du cours manquant.']);
    exit;
}

// Vérifier si l'objet PDO est bien créé
if (!$pdo) {
    echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données']);
    exit;
}

try {
    // Vérifier que le cours appartient bien au formateur
    $stmt_check = $pdo->prepare("SELECT * FROM cours WHERE id = ? AND formateur_id = ?");
    $stmt_check->execute([$course_id, $trainer_id]);
    $course = $stmt_check->fetch();

    if (!$course) {
        echo json_encode(['success' => false, 'message' => 'Cours non trouvé ou vous n\'avez pas la permission de le supprimer.']);
        exit;
    }

    // Supprimer les étapes associées au cours
    $stmt_delete_steps = $pdo->prepare("DELETE FROM course_steps WHERE course_id = ?");
    $stmt_delete_steps->execute([$course_id]);

    // Supprimer le cours de la base de données
    $stmt_delete_course = $pdo->prepare("DELETE FROM cours WHERE id = ?");
    $stmt_delete_course->execute([$course_id]);

    // Supprimer l'image associée si elle existe
    $image_path = '../avatar_img/' . $course['image'];
    if (file_exists($image_path)) {
        unlink($image_path);
    }

    echo json_encode(['success' => true, 'message' => 'Le cours a été supprimé avec succès.']);
} catch (PDOException $e) {
    // Gérer les erreurs PDO
    echo json_encode(['success' => false, 'message' => 'Erreur PDO : ' . $e->getMessage()]);
}
?>
