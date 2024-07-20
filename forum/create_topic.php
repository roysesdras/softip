<?php
session_start();
include('../admin/config.php');

// Vérifiez si un étudiant, un formateur ou un administrateur est connecté
if (!isset($_SESSION['student_id']) && !isset($_SESSION['trainer_id']) && !isset($_SESSION['admin_id'])) {
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .container {
            margin-top: 50px;
            max-width: 500px;;
        }
        .container h2 {
            margin-top: 30px;
            margin-bottom: 20px;
            color: #033e60;
            text-align: center;
        }
        .form-label {
            font-weight: bold;
        }
        .form-control {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
        }
        .btn-primary {
            background-color: #033e60;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-primary:hover {
            background-color: #033e60;
        }
    </style>
</head>
<body>

    <div class="container">
        <form method="post" action="submit_topic.php" class=" ">
        <h2>Créer un Nouveau Sujet</h2>
            <div class="mb-3">
                <label for="title" class="form-label">Titre:</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <textarea class="form-control" id="description" name="description" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Créer</button>
        </form>
    </div>

    <?php require_once ('../inclusion/footer_2.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
</body>
</html>
