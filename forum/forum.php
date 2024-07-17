<?php
session_start();
include('../admin/config.php');

// Vérifiez si un étudiant, un formateur ou un administrateur est connecté
if (!isset($_SESSION['student_id']) && !isset($_SESSION['trainer_id']) && !isset($_SESSION['admin_id'])) {
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
<?php
// Démarrer la session si ce n'est pas déjà fait
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
    <nav>
        <ul>
            <li><a href="index.php">Accueil</a></li>
            <li><a href="forum.php">Forum</a></li>
            <!-- Afficher des options spécifiques pour chaque type d'utilisateur connecté -->
            <?php if (isset($_SESSION['student_id'])): ?>
                <li><a href="../students/student_dashboard.php">Profil Étudiant</a></li>
                <li><a href="../students/logout.php">Déconnexion</a></li>
            <?php elseif (isset($_SESSION['trainer_id'])): ?>
                <li><a href="../trainers/trainers.php">Profil Formateur</a></li>
                <li><a href="../trainers/logout_trainer.php">Déconnexion</a></li>
            <?php elseif (isset($_SESSION['admin_id'])): ?>
                <li><a href="../admin/dashboard.php">Tableau de bord Admin</a></li>
                <li><a href="../admin/logout.php">Déconnexion</a></li>
            <?php else: ?>
                <li><a href="../students/login_student_cool.php">Connexion Étudiant</a></li>
                <li><a href="../trainers/login_trainer.php">Connexion Formateur</a></li>
                <li><a href="../admin/login_admin.php">Connexion Admin</a></li>
            <?php endif; ?>
        </ul>
    </nav>

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
