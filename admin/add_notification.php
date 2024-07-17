<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $_POST['message'];

    // Récupérer tous les étudiants
    $stmt_students = $pdo->query("SELECT id FROM students");
    $students = $stmt_students->fetchAll();

    foreach ($students as $student) {
        $student_id = $student['id'];

        // Ajouter la notification pour chaque étudiant
        $stmt = $pdo->prepare("INSERT INTO notifications (student_id, message, is_read, created_at) VALUES (?, ?, FALSE, NOW())");
        $stmt->execute([$student_id, $message]);
    }

    // Redirection vers la page des notifications après l'ajout
    header('Location: dashboard.php#notif');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter Notification</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-2">
        <h3>Ajouter une Notification</h3>
        <form action="" method="POST">
            <div class="mb-3">
                <label for="message" class="form-label">Message</label>
                <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </form>
    </div>
</body>
</html>