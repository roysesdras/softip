<?php
session_start();
include('../admin/config.php');

if (!isset($_SESSION['student_id'])) {
    header('Location: login_student.php');
    exit;
}

$student_id = $_SESSION['student_id'];
$course_id = isset($_GET['course_id']) ? $_GET['course_id'] : null;

if (!$course_id) {
    die("Identifiant du cours manquant.");
}

// Vérifier si l'objet PDO est bien créé
if (!$pdo) {
    die("Erreur de connexion à la base de données");
}

// Récupérer les détails du cours
$stmt_course = $pdo->prepare("SELECT * FROM cours WHERE id = ?");
$stmt_course->execute([$course_id]);
$course = $stmt_course->fetch();

if (!$course) {
    die("Cours non trouvé.");
}

// Récupérer les étapes du cours
$stmt_steps = $pdo->prepare("SELECT * FROM course_steps WHERE course_id = ? ORDER BY step_number ASC");
$stmt_steps->execute([$course_id]);
$steps = $stmt_steps->fetchAll();

// Vérifier si des étapes ont été trouvées
if ($steps === false || empty($steps)) {
    die("Aucune étape trouvée pour ce cours.");
}

$total_steps = count($steps);
$current_step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$current_step = max(1, min($current_step, $total_steps));

// Assurer que la clé existe dans le tableau
$step_content = isset($steps[$current_step - 1]) ? $steps[$current_step - 1]['content'] : 'Contenu non disponible.';

$progress = ($total_steps > 0) ? ($current_step / $total_steps) * 100 : 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($course['nom']) ?> - Cours</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.9.0/font/bootstrap-icons.min.css">
    <link href="../assets/img/favicon.png" rel="icon">
    <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">
   

    <style>
        body {
            background-color: #f8f9fa;
            color: #333;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 30px;
        }
        .course-header {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .progress-bar {
            width: 100%;
            background-color: #f3f3f3;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .progress-bar-fill {
            height: 20px;
            background-color: #4caf50;
            width: <?= round($progress, 2) ?>%;
            text-align: center;
            color: white;
            line-height: 20px;
        }

        .course-content {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .navigation-links {
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .form-container {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .form-container textarea {
            width: 100%;
            height: 100px;
            margin-bottom: 10px;
        }
        .review {
            background: #fff;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .review h3 {
            margin: 0;
            font-size: 1.1rem;
        }
        .review p {
            margin: 5px 0;
        }
        .review .username {
            font-weight: bold;
        }
        
        #response {
            margin-top: 20px;
        }

    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('form').on('submit', function(e) {
                e.preventDefault(); // Empêche la soumission normale du formulaire

                $.ajax({
                    url: 'submit_review.php', // URL du script PHP qui traite les avis
                    type: 'POST',
                    data: $(this).serialize(), // Sérialise les données du formulaire
                    success: function(response) {
                        // Affiche la réponse du script PHP dans une div
                        $('#response').html(response);
                    },
                    error: function() {
                        $('#response').html('Une erreur est survenue.');
                    }
                });
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-1"></div>
            <!-- section cours lecture -->
            <div class="col-md-8">
                <div class="course-header">
                    <h1><?= htmlspecialchars($course['nom']) ?></h1>
                    <div class="progress-bar">
                        <div class="progress-bar-fill"><?= round($progress, 2) ?>%</div>
                    </div>
                </div>
                
                <div class="course-content">
                    <?= nl2br($step_content) ?>
                </div>
                <div class="navigation-links ">
                    <?php if ($current_step > 1): ?>
                        <a href="view_course.php?course_id=<?= htmlspecialchars($course_id) ?>&step=<?= $current_step - 1 ?>" class="btn btn-sm btn-outline-secondary">Précédent</a>
                    <?php endif; ?>
                    <?php if ($current_step < $total_steps): ?>
                        <a href="view_course.php?course_id=<?= htmlspecialchars($course_id) ?>&step=<?= $current_step + 1 ?>" class="btn btn-sm btn-success">Suivant</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- section avis des etudiants -->
            <div class="col-md-3 ">
                <div class="position-sticky form-container review" style="top: 2rem">
                    <h3>Donnez votre avis et notez le cours</h3>
                    <form>
                        <input type="hidden" name="course_id" value="<?= htmlspecialchars($course_id) ?>"> <!-- Identifiant du cours -->
                        <input type="hidden" name="student_id" value="<?= htmlspecialchars($student_id) ?>"> <!-- Identifiant de l'étudiant -->
                        <div class="mb-3">
                            <label for="rating">Note (1-5):</label>
                            <input type="number" name="rating" min="1" max="5" required class="form-control">
                        </div>
                        
                        <div class="mb-3">
                            <label for="comment">Commentaire:</label>
                            <textarea name="comment" required class="form-control"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-outline-primary btn-sm">Ajouter un avis</button>
                    </form>
                </div>
                <div id="response"></div> <!-- Zone pour afficher les messages de réponse -->
            </div>

        </div>
    </div>
    

    
    <?php require_once ('../inclusion/footer_2.php'); ?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

