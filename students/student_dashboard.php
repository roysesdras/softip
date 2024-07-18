<?php
session_start();
include('../admin/config.php');

if (!isset($_SESSION['student_id'])) {
    header('Location: login_student.php');
    exit;
}

$student_id = $_SESSION['student_id'];

// Vérifier si l'objet PDO est bien créé
if (!$pdo) {
    die("Erreur de connexion à la base de données");
}

// Récupérer les informations de l'étudiant depuis la base de données
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) {
    header('Location: login_student.php');
    exit;
}

// Récupérer les formations auxquelles l'étudiant est inscrit
$stmt = $pdo->prepare("SELECT formations.id, formations.titre, formations.description FROM formations 
                        INNER JOIN inscriptions ON formations.id = inscriptions.formation_id 
                        WHERE inscriptions.user_id = ?");
$stmt->execute([$student_id]);
$formations = $stmt->fetchAll();

// Récupérer les IDs des formations
$formation_ids = array_column($formations, 'id');

// Récupérer les ressources associées aux formations de l'étudiant
if (!empty($formation_ids)) {
    $in_query = implode(',', array_fill(0, count($formation_ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM resources WHERE formation_id IN ($in_query)");
    $stmt->execute($formation_ids);
    $resources = $stmt->fetchAll();

    // Récupérer les sessions associées aux formations de l'étudiant avec les feedbacks
    $stmt_sessions = $pdo->prepare("
        SELECT sessions.*, formations.titre AS formation_titre, 
               GROUP_CONCAT(feedbacks.feedback SEPARATOR '<br>') AS feedbacks
        FROM sessions
        INNER JOIN formations ON sessions.formation_id = formations.id
        LEFT JOIN feedbacks ON sessions.id = feedbacks.session_id
        WHERE sessions.formation_id IN ($in_query)
        GROUP BY sessions.id, formations.titre
    ");
    $stmt_sessions->execute($formation_ids);
    $sessions = $stmt_sessions->fetchAll();
} else {
    $resources = [];
    $sessions = [];
}

// Récupérer les résultats des quiz filtrés par formation
$stmt_results = $pdo->prepare("
    SELECT quizzes.titre, SUM(IF(questions.correct_option = student_answers.answer, 1, 0)) AS score, MAX(student_answers.submitted_at) AS date_taken
    FROM quizzes
    JOIN questions ON quizzes.id = questions.quiz_id
    LEFT JOIN student_answers ON questions.id = student_answers.question_id AND student_answers.student_id = ?
    WHERE quizzes.formation_id IN ($in_query)
    GROUP BY quizzes.id
");
$stmt_results->execute(array_merge([$student_id], $formation_ids));
$results = $stmt_results->fetchAll();

// Récupérer les quizzes disponibles filtrés par formation
$stmt_quizzes = $pdo->prepare("
    SELECT * FROM quizzes
    WHERE formation_id IN ($in_query)
");
$stmt_quizzes->execute($formation_ids);
$quizzes = $stmt_quizzes->fetchAll();

// Récupérer les questions des quiz pour répondre
$selected_quiz_id = isset($_GET['quiz_id']) ? $_GET['quiz_id'] : (count($quizzes) > 0 ? $quizzes[0]['id'] : null);
if ($selected_quiz_id) {
    $stmt_questions = $pdo->prepare("
        SELECT questions.*, quizzes.titre AS quiz_title 
        FROM questions 
        JOIN quizzes ON questions.quiz_id = quizzes.id
        WHERE quizzes.id = ?
    ");
    $stmt_questions->execute([$selected_quiz_id]);
    $questions = $stmt_questions->fetchAll();
} else {
    $questions = [];
}

// Récupérer les notifications non lues
$stmt_notifications = $pdo->prepare("
    SELECT * FROM notifications WHERE student_id = ? AND is_read = FALSE
");
$stmt_notifications->execute([$student_id]);
$notifications = $stmt_notifications->fetchAll();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <script src="../assets/js/color-modes.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($student['username']); ?> - Tableau de Bord</title>
    <!-- Favicons -->
    <link href="../assets/img/favicon.png" rel="icon">
    <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"> -->
    <!-- <link rel="canonical" href="https://sternaafrica.org/"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">
    <link rel="stylesheet" href="../assets/style.css">
    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">


    <style>
        .btn-group {
            margin-top: 10px;
        }
        .feedback {
            max-height: 100px;
            overflow-y: auto;
        }

        .avatar-img {
            width: 40px; /* Ajustez la taille de l'avatar selon vos besoins */
            height: 40px; /* Ajustez la taille de l'avatar selon vos besoins */
            object-fit: cover;
        }

        .navbar-brand {
            font-size: 1.5rem;
        }

        .navbar-nav .nav-link {
            padding: 10px 20px;
            font-size: 1.1rem;
        }

        .navbar-nav .nav-link.active {
            font-weight: bold;
        }

            /* Style pour rendre la navbar flottante */
        .fixed-top {
            position: fixed;
            width: 100%;
            z-index: 1030;
        }
        .offcanvas-header .btn-close-white {
            filter: invert(1);
        }

        input{
            background-color: #033e60;
            color: #ffffff;
            padding: 5px;
            border          : solid transparent;
            font-size: 16px;
        }
        input:hover{
            background-color: transparent;
            color: #033e60;
            padding: 5px;
            border          : solid 1px #033e60;
        }

        .btn{
            background-color: #5F5B5B;
            color: #ffffff;
            padding: 5px;
            border          : solid transparent;
            font-size: 16px;
            border-radius: 0px;
        }
        .btn:hover{
            background-color: transparent;
            color: #5F5B5B;
            padding: 5px;
            border          : solid 1px #5F5B5B;
        }

    </style>
</head>
<body>
    <!-- Barre de Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="student_dashboard.php">Tableau de Bord</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Menu</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="inscription_formation.php">S'inscrire à une Formation</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Se Déconnecter</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../pages/notes.php">Mes Notes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="access_course.php">Mesc Cours</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../forum/forum.php" target="_blank">Forum</a>
                        </li>
                    </ul>
                    <!-- Inclure l'avatar à droite -->
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item ">
                            <div class="d-flex align-items-center">
                                <?php
                                // Récupérer l'étudiant actuellement connecté (exemple d'utilisation de session)
                                if (isset($_SESSION['student_id'])) {
                                    $student_id = $_SESSION['student_id'];
                                    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
                                    $stmt->execute([$student_id]);
                                    $student = $stmt->fetch(PDO::FETCH_ASSOC);
                                    if ($student && !empty($student['avatar'])) {
                                        echo '<img src="' . $student['avatar'] . '" alt="Avatar" class="avatar-img rounded-circle me-2">';
                                    } else {
                                        echo '<img src="../avatar_img/R.jpg" alt="Avatar par défaut" class="avatar-img rounded-circle me-2">';
                                    }
                                    echo '<span class="text-light">' . htmlspecialchars($student['username']) . '</span>';
                                }
                                ?>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <br><br>

    <div class="container ">
         <!-- Success message -->
         <?php if (isset($_GET['submission']) && $_GET['submission'] == 'success'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Vos réponses au quiz ont été soumises avec succès !
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
 
        <div class="card mt-4 mb-4 shadow p-2">
            <div class="card-body">
                <h3 class="card-title">Bienvenue, <?php echo htmlspecialchars($student['username']); ?></h3>
                <p class="card-text">Email : <?php echo htmlspecialchars($student['email']); ?></p>
                <p class="card-text">Numéro de téléphone : <?php echo htmlspecialchars($student['phone_number']); ?></p>
            </div>

            <?php
                // Récupérer l'étudiant actuellement connecté (exemple d'utilisation de session)
               /* if (isset($_SESSION['student_id'])) {
                    $student_id = $_SESSION['student_id'];
                    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
                    $stmt->execute([$student_id]);
                    $student = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($student && !empty($student['avatar'])) {
                        echo '<img src="' . $student['avatar'] . '" alt="Avatar" class="avatar-img">';
                    } else {
                        echo '<img src="../avatar_img/R.jpg" alt="Avatar par défaut" class="avatar-img">';
                    }
                } */
            ?>

        </div>

        <!-- Section des notifications -->
        <div class="card mb-4 shadow p-2">
            <div class="card-body">
                <h4 class="card-title">Notifications</h4>
                <?php if (count($notifications) > 0): ?>
                    <ul class="list-group">
                        <?php foreach ($notifications as $notification): ?>
                            <li class="list-group-item">
                                <?php echo htmlspecialchars($notification['message']); ?>
                                <span class="badge bg-primary"><?php echo htmlspecialchars($notification['created_at']); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="card-text">Aucune notification pour le moment.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Section des résultats des quiz -->
        <div class="card mb-4 shadow p-2">
            <div class="card-body">
                <h4 class="card-title">Résultats des Quiz</h4>
                <?php if (count($results) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Quiz</th>
                                    <th>Score</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($results as $result): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($result['titre']); ?></td>
                                        <td><?php echo htmlspecialchars($result['score']); ?></td>
                                        <td><?php echo htmlspecialchars($result['date_taken']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="card-text">Aucun résultat de quiz disponible pour le moment.</p>
                <?php endif; ?>
            </div>
        </div>


        <!-- Section des feedbacks des formateurs -->
        <div class="card mb-4 shadow p-2">
            <div class="card-body">
                <h4 class="card-title">Sessions</h4>
                <?php if (count($sessions) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Titre de la Session</th>
                                    <th>Date</th>
                                    <th>Heure</th>
                                    <th>Lieu</th>
                                    <th>Feedback</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sessions as $session): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($session['formation_titre']); ?></td>
                                        <td><?php echo htmlspecialchars($session['date']); ?></td>
                                        <td><?php echo htmlspecialchars($session['time']); ?></td>
                                        <td><?php echo htmlspecialchars($session['location']); ?></td>
                                        <td class="feedback"><?php echo htmlspecialchars($session['feedbacks']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="card-text">Aucun feedback pour le moment.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Section pour répondre aux questions des quiz -->
        <div class="card mb-4 shadow p-2">
            <div class="card-body">
                <h4 class="card-title">Questions des Quiz</h4>
                <form method="GET" action="">
                    <div class="mb-3">
                        <label for="quiz_id" class="form-label">Sélectionnez un Quiz:</label>
                        <select id="quiz_id" name="quiz_id" class="form-select" required onchange="this.form.submit()">
                            <?php foreach ($quizzes as $quiz): ?>
                                <option value="<?php echo $quiz['id']; ?>" <?php echo ($quiz['id'] == $selected_quiz_id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($quiz['titre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>


                <?php if ($selected_quiz_id && count($questions) > 0): ?>
                    <form action="submit_answers.php" method="POST">
                        <input type="hidden" name="quiz_id" value="<?php echo htmlspecialchars($selected_quiz_id); ?>">

                        <?php foreach ($questions as $question): ?>
                            <div class="mb-3">
                                <label class="form-label"><?php echo htmlspecialchars($question['quiz_title']) . " - " . htmlspecialchars($question['question_text']); ?></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="answers[<?php echo $question['id']; ?>]" value="A" required>
                                    <label class="form-check-label"><?php echo htmlspecialchars($question['option_a']); ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="answers[<?php echo $question['id']; ?>]" value="B">
                                    <label class="form-check-label"><?php echo htmlspecialchars($question['option_b']); ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="answers[<?php echo $question['id']; ?>]" value="C">
                                    <label class="form-check-label"><?php echo htmlspecialchars($question['option_c']); ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="answers[<?php echo $question['id']; ?>]" value="D">
                                    <label class="form-check-label"><?php echo htmlspecialchars($question['option_d']); ?></label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <input type="submit" value="Soumettre" />
                    </form>
                <?php else: ?>
                    <p class="card-text">Aucune question de quiz pour le moment.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Section des formations -->
        <div class="card mb-4 shadow p-2">
            <div class="card-body">
                <h4 class="card-title">Vos Formations Inscrites</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($formations as $formation): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($formation['titre']); ?></td>
                                    <td>
                                        <?php 
                                            $fullDescription = $formation['description'];
                                            $shortDescription = substr(strip_tags($fullDescription), 0, 100);
                                        ?>
                                        <span class="short-description"><?php echo $shortDescription; ?></span>
                                        <?php if (strlen(strip_tags($fullDescription)) > 100): ?>
                                            <span class="ellipsis">...</span>
                                            <span class="full-description" style="display: none;"><?php echo substr($fullDescription, 100); ?></span>
                                            <a href="#" class="toggle-description">Voir plus</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var toggleLinks = document.querySelectorAll('.toggle-description');

            toggleLinks.forEach(function(link) {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    var fullDescription = this.previousElementSibling;
                    var ellipsis = fullDescription.previousElementSibling;
                    var shortDescription = ellipsis.previousElementSibling;

                    if (fullDescription.style.display === 'none') {
                        fullDescription.style.display = 'inline';
                        ellipsis.style.display = 'none';
                        this.textContent = 'Voir moins';
                    } else {
                        fullDescription.style.display = 'none';
                        ellipsis.style.display = 'inline';
                        this.textContent = 'Voir plus';
                    }
                });
            });
        });
        </script>

        <!-- Section des ressources -->
        <div class="card mb-2 shadow p-2">
            <div class="card-body">
                <h4 class="card-title">Ressources</h4>
                <?php if (count($resources) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Description</th>
                                    <th>Lien</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($resources as $resource): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($resource['titre']); ?></td>
                                        <td><?php echo htmlspecialchars($resource['description']); ?></td>
                                        <td><a href="<?php echo htmlspecialchars($resource['lien']); ?>" target="_blank">Accéder à la Ressource</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="card-text">Aucune ressource pour le moment.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="btn-group mb-4">
            <a href="logout.php" class="btn">Se Déconnecter</a>
        </div>

    </div>


  
    <div class=" d-md-flex linkFooter">
        <div class="px-3 p-2">
            <p class="p-2">
                &copy; <strong><span>Soft IP Technology</span></strong>. Tout droit réservé 
            </p>
        </div>
    </div>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>

    <script>
      // Sélectionne l'élément .back-to-top
      let backToTopButton = document.querySelector('.back-to-top');

      // Ajoute un écouteur d'événement au défilement de la fenêtre
      window.addEventListener('scroll', () => {
          if (window.scrollY > 100) {
          backToTopButton.classList.add('active');
          } else {
          backToTopButton.classList.remove('active');
          }
      });

      // Ajoute un écouteur d'événement pour cliquer sur le bouton
      backToTopButton.addEventListener('click', (e) => {
          e.preventDefault();
          window.scrollTo({top: 0, behavior: 'smooth'});
      });
    </script>

    <!-- bas / pied de page -->
     <!-- Inclusion de Bootstrap JS (nécessaire pour le offcanvas) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://code.jquery.com/jquery-2.2.4.min.js'></script>
    <script src="./assets/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
