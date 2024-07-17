<?php
session_start();
include('../admin/config.php');

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['student_id'])) {
    header('Location: ../students/login_student_cool.php');
    exit;
}

// Récupérer les sujets de discussion
$query = "SELECT * FROM topics ORDER BY created_at DESC";
$stmt = $pdo->query($query);
$topics = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container">
        <h2>Forum</h2>
        <a href="create_topic.php" class="btn btn-primary">Créer un nouveau sujet</a>
        <ul>
            <?php foreach ($topics as $topic): ?>
                <li>
                    <a href="topic.php?id=<?php echo $topic['id']; ?>">
                        <h3><?php echo $topic['title']; ?></h3>
                        <p><?php echo $topic['description']; ?></p>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
