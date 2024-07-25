<?php
session_start();
include('../admin/config.php');

// Vérifiez si un étudiant, un formateur ou un administrateur est connecté
if (!isset($_SESSION['student_id']) && !isset($_SESSION['trainer_id']) && !isset($_SESSION['admin_id'])) {
    header('Location: ../students/login_student_cool.php');
    exit;
}

$student_id = $_SESSION['student_id'] ?? null; // Récupérer l'ID de l'étudiant, s'il est connecté

// Vérifier si l'étudiant a un abonnement actif
if ($student_id) {
    $query = "SELECT status FROM abonnements WHERE student_id = :student_id AND status = 'Active'";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    $stmt->execute();
    $subscription_active = $stmt->fetchColumn();
    
    if (!$subscription_active) {
        // Rediriger l'étudiant vers la page de souscription s'il n'a pas d'abonnement actif
        header('Location: ../abonnements/subscribe.php');
        exit;
    }
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">

    <style>
                /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            margin: 0;
            padding: 0;
        }

        nav {
            background-color: #033e60;
            color: #fff;
            padding: 10px;
        }

        nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
        }

        nav ul li {
            margin: 0 15px;
        }

        nav ul li a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
        }

        nav ul li a:hover {
            text-decoration: underline;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .container h1 {
            margin-top: 0;
            color: #004080;
        }

        ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        ul li:last-child {
            border-bottom: none;
        }

        ul li a {
            text-decoration: none;
            color: #333;
        }

        ul li a h3 {
            margin: 0;
            font-size: 1.5rem;
        }

        ul li a p {
            margin: 5px 0 0;
            font-size: 1rem;
            color: #666;
        }

    </style>
</head>
<body>
    <nav>
        <ul>

            <?php if (isset($_SESSION['student_id'])): ?>
                <li><a href="../students/student_dashboard.php">Profil</a></li>
                <li><a href="create_topic.php">Créer un sujet</a></li>
                <li><a href="../students/logout.php">Quitter</a></li>

            <?php elseif (isset($_SESSION['trainer_id'])): ?>
                <li><a href="../trainers/trainers.php">Profil</a></li>
                <li><a href="create_topic.php">Créer un sujet</a></li>
                <li><a href="../trainers/logout_trainer.php">Quitter</a></li>

            <?php elseif (isset($_SESSION['admin_id'])): ?>
                <li><a href="../admin/dashboard.php">Tableau de bord Admin</a></li>
                <li><a href="create_topic.php">Créer un sujet</a></li>
                <li><a href="../admin/logout.php">Quitter</a></li>

            <?php else: ?>
                <li><a href="../students/login_student_cool.php">Connexion Étudiant</a></li>
                <li><a href="../trainers/login_trainer.php">Connexion Formateur</a></li>
                <li><a href="../admin/login_admin.php">Connexion Admin</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="container">
        <div class="mb-2 text-center">
            <h1 class="">Sujet de Discussion</h1>
            <p>Cliquer sur un sujet pour participer.</p>
        </div>
   
        <ul >
            <?php foreach ($topics as $topic): ?>
                <li class="mb-3 shadow p-3" style="background-color: #fff; border-radius: 10px;">
                    <a href="topic.php?id=<?php echo $topic['id']; ?>">
                        <h3><?php echo htmlspecialchars($topic['title']); ?></h3>
                        <p><?php echo htmlspecialchars($topic['description']); ?></p>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>


    <?php require_once ('../inclusion/footer_2.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://code.jquery.com/jquery-2.2.4.min.js'></script>
</body>
</html>
