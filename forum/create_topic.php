<?php
session_start();
include('../admin/config.php');

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['student_id'])) {
    header('Location: ../students/login_student_cool.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Sujet</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
   

    <div class="container">
        <h2>Créer un Nouveau Sujet</h2>
        <form method="post" action="submit_topic.php">
            <div>
                <label for="title">Titre:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div>
                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <button type="submit">Créer</button>
        </form>
    </div>
</body>
</html>
