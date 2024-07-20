<?php
session_start();
include('../admin/config.php');

if (!isset($_SESSION['trainer_id'])) {
    header('Location: ../trainers/login_trainer.php');
    exit;
}

$trainer_id = $_SESSION['trainer_id'];

// Vérifier si l'objet PDO est bien créé
if (!$pdo) {
    die("Erreur de connexion à la base de données");
}

try {
    // Récupérer les cours ajoutés par le formateur
    $stmt = $pdo->prepare("SELECT * FROM cours WHERE formateur_id = ?");
    $stmt->execute([$trainer_id]);
    $courses = $stmt->fetchAll();

    if ($courses) {
        $hasCourses = true;
    } else {
        $hasCourses = false;
    }

} catch (PDOException $e) {
    // Gérer les erreurs PDO
    $error = "Erreur PDO : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>gérées - Cours</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.9.0/font/bootstrap-icons.min.css">
    <link href="../assets/img/favicon.png" rel="icon">
    <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <style>
        body {
            background-color: #f8f9fa;
            color: #333;
        }
        .container {
            margin-top: 30px;
        }
        .course-item {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .course-item img {
            max-width: 100%;
            border-radius: 5px;
        }
        .course-item h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        .course-item p {
            font-size: 1rem;
            margin-bottom: 10px;
        }
        .course-item a {
            color: #ffffff;
            text-decoration: none;
        }
        .course-item a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
            <h1 class="mb-4">Mes Cours</h1>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if ($hasCourses): ?>
                    <?php foreach ($courses as $course): ?>
                        <div class="course-item">
                            <h3><?php echo ($course['nom']); ?></h3>
                            
                            <img src="../avatar_img/<?php echo htmlspecialchars($course['image']); ?>" alt="Image du cours">

                            <p><?php echo ($course['description']); ?></p>
                            <div class="mt-2">
                                <a href="modifier_cours.php?id=<?php echo $course['id']; ?>" class="btn btn-warning btn-sm">Modifier</a>
                                <a href="supprimer_cours.php?id=<?php echo $course['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce cours ?');">Supprimer</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info" role="alert">
                        Aucun cours trouvé.
                    </div>
                <?php endif; ?>
                <div class="quitter mb-4">
                    <a href="../trainers/trainers.php" style="font-size: 20px;">Quitter</a>
                </div>
            </div>
            <div class="col-md-3"></div>
        </div>
        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
