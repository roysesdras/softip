<?php
session_start();
include('../admin/config.php');

// Vérifier si la connexion PDO est bien établie
if (!$pdo) {
    die("Erreur de connexion à la base de données");
}

if (!isset($_SESSION['trainer_id'])) {
    header('Location: ../trainers/login_trainer.php');
    exit;
}

$trainer_id = $_SESSION['trainer_id'];

try {
    // Récupérer les cours ajoutés par le formateur
    $stmt = $pdo->prepare("SELECT * FROM cours WHERE formateur_id = ?");
    $stmt->execute([$trainer_id]);
    $courses = $stmt->fetchAll();

    $hasCourses = !empty($courses);

    // Récupérer tous les avis pour un cours spécifique
    $course_id = 2; // Identifiant du cours

    $sql = "SELECT r.rating, r.comment, s.username, r.created_at
            FROM reviews r
            JOIN students s ON r.student_id = s.id
            WHERE r.course_id = :course_id
            ORDER BY r.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['course_id' => $course_id]);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Vérifier s'il y a plus d'avis
    $total_reviews = count($reviews);
    $more_reviews_available = $total_reviews > 5;

} catch (PDOException $e) {
    // Gérer les erreurs PDO
    $error = "Erreur PDO : " . $e->getMessage();
    echo $error;
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
        .reviews-section {
            position: sticky;
            top: 30px; /* Ajustez cette valeur pour le décalage en haut */
        }
        .review {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .review h5 {
            margin-top: 0;
            font-size: 1.25rem;
            margin-bottom: 4px; /* Réduit l'espace sous la note */
        }
        .review p {
            margin: 0 0 0px 0; /* Réduit l'espace entre le commentaire et la date */
            font-size: 1.1rem;
        }
        .hidden {
            display: none;
        }
        .more-reviews {
            cursor: pointer;
            color: #007bff;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-8">
            <h2 class="mb-4">Mes Cours</h2>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($hasCourses): ?>
                <?php foreach ($courses as $course): ?>
                    <div class="course-item" id="course-<?php echo $course['id']; ?>">
                        <h3><?php echo htmlspecialchars($course['nom']); ?></h3>
                        <img src="../avatar_img/<?php echo htmlspecialchars($course['image']); ?>" alt="Image du cours">
                        <p><?php echo nl2br(($course['description'])); ?></p>
                        <div class="mt-2">
                            <a href="modifier_cours.php?id=<?php echo $course['id']; ?>" class="btn btn-warning btn-sm">Modifier</a>
                            <button class="btn btn-danger btn-sm" onclick="deleteCourse(<?php echo $course['id']; ?>)">Supprimer</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info" role="alert">
                    Aucun cours trouvé.
                </div>
            <?php endif; ?>

            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
                function deleteCourse(courseId) {
                    if (confirm('Êtes-vous sûr de vouloir supprimer ce cours ?')) {
                        $.ajax({
                            url: 'supprimer_cours.php',
                            type: 'GET',
                            data: { id: courseId },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    $('#course-' + courseId).remove();
                                    alert(response.message);
                                } else {
                                    alert(response.message);
                                }
                            },
                            error: function() {
                                alert('Une erreur est survenue lors de la suppression.');
                            }
                        });
                    }
                }
            </script>

            </div>
            <div class="col-md-3 reviews-section mb-4">
                <div class="position-sticky" style="top: 2rem">
                    <h4>Notes et avis des étudiants</h4>
                    <?php 
                        if ($reviews) {
                            $review_count = 0;
                            foreach ($reviews as $row) {
                                $review_count++;
                                $hidden_class = ($review_count > 5) ? 'hidden' : '';
                                echo "<div class='review $hidden_class'>";
                                echo "<h5>Note: " . htmlspecialchars($row['rating']) . "/5</h5>";
                                echo "<p><b>Avis:</b> " . htmlspecialchars($row['comment']) . "</p>";
                                echo "<p><b>De:</b> " . htmlspecialchars($row['username']) . " le " . htmlspecialchars($row['created_at']) . "</p>";
                                echo "</div>";
                            }
                            if ($more_reviews_available) {
                                echo "<div class='more-reviews' id='more-reviews'>Voir plus d'avis</div>";
                            }
                        } else {
                            echo "<div class='alert alert-info' role='alert'>Aucun avis pour ce cours.</div>";
                        }
                    ?>
                    <div class="quitter mb-4">
                        <a href="../trainers/trainers.php" style="font-size: 20px;">Quitter</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#more-reviews').click(function() {
                $('.review.hidden').removeClass('hidden');
                $(this).hide(); // Cache le lien "Voir plus"
            });
        });
    </script>



    <?php require_once ('../inclusion/footer_2.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
