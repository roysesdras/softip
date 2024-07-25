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

// Vérifier l'état de l'abonnement
$stmt = $pdo->prepare("SELECT status FROM abonnements WHERE student_id = ? ORDER BY end_date DESC LIMIT 1");
$stmt->execute([$student_id]);
$subscription = $stmt->fetch(PDO::FETCH_ASSOC);

$is_subscribed = ($subscription && $subscription['status'] === 'Actif');

// Récupérer le nom de l'étudiant
$stmt = $pdo->prepare("SELECT username FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Étudiant non trouvé");
}

$student_name = $student['username'];

// Récupérer les formations auxquelles l'étudiant est inscrit et les détails des abonnements
$stmt = $pdo->prepare("
    SELECT 
        f.id AS formation_id, 
        f.titre, 
        f.description, 
        a.start_date, 
        a.end_date, 
        f.price, 
        a.status, 
        a.transaction_details
    FROM 
        formations f
    INNER JOIN 
        inscriptions i ON f.id = i.formation_id 
    INNER JOIN 
        abonnements a ON i.formation_id = a.formation_id 
    WHERE 
        i.user_id = ?
");
$stmt->execute([$student_id]);
$formations_abonnements = $stmt->fetchAll(PDO::FETCH_ASSOC);

// // Affiche les résultats pour débogage
// echo '<pre>';
// print_r($formations_abonnements);
// echo '</pre>';

// Extraire les IDs des formations
$formation_ids = array_column($formations_abonnements, 'formation_id');

if (!empty($formation_ids)) {
    // Préparer la requête pour récupérer les ressources associées
    $in_query = implode(',', array_fill(0, count($formation_ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM resources WHERE formation_id IN ($in_query)");
    $stmt->execute($formation_ids);
    $resources = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
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
    $sessions = $stmt_sessions->fetchAll(PDO::FETCH_ASSOC);
} else {
    $resources = [];
    $sessions = [];
}

// Récupérer les résultats des quiz
$stmt_results = $pdo->prepare("
    SELECT quizzes.titre, SUM(IF(questions.correct_option = student_answers.answer, 1, 0)) AS score, MAX(student_answers.submitted_at) AS date_taken
    FROM quizzes
    JOIN questions ON quizzes.id = questions.quiz_id
    LEFT JOIN student_answers ON questions.id = student_answers.question_id AND student_answers.student_id = ?
    GROUP BY quizzes.id
");
$stmt_results->execute([$student_id]);
$results = $stmt_results->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les quizzes disponibles
$stmt_quizzes = $pdo->prepare("
    SELECT * FROM quizzes
");
$stmt_quizzes->execute();
$quizzes = $stmt_quizzes->fetchAll(PDO::FETCH_ASSOC);

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
    $questions = $stmt_questions->fetchAll(PDO::FETCH_ASSOC);
} else {
    $questions = [];
}

// Récupérer les notifications non lues
$stmt_notifications = $pdo->prepare("
    SELECT * FROM notifications WHERE student_id = ? AND is_read = FALSE
");
$stmt_notifications->execute([$student_id]);
$notifications = $stmt_notifications->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <script src="../assets/js/color-modes.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord de <?php echo htmlspecialchars($student_name); ?></title>
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
                            <a class="nav-link" href="../abonnements/subcribe.php">S'abonner à une Formation</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Se Déconnecter</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../pages/notes.php">Mes Notes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./access_course.php">Mes Cours</a>
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
            <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
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
        </div>

        <!-- Section des formations inscrites -->
        <div class="card mb-4 shadow p-2">
            <div class="card-body">
                <h4 class="card-title">Vos Formations Inscrites</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Description</th>
                                <th>Date de début</th>
                                <th>Date de fin</th>
                                <th>Prix</th>
                                <th>Statut</th>
                                <th>Détails de la transaction</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($formations_abonnements as $formation): ?>
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
                                            <span class="full-description" style="display: none;"><?php echo htmlspecialchars(substr($fullDescription, 100)); ?></span>
                                            <a href="#" class="toggle-description">Voir plus</a>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($formation['start_date']); ?></td>
                                    <td><?php echo htmlspecialchars($formation['end_date']); ?></td>
                                    <td><?php echo htmlspecialchars(number_format($formation['price'], 2)); ?> €</td>
                                    <td><?php echo htmlspecialchars($formation['status']); ?></td>
                                    <td><?php echo nl2br(htmlspecialchars($formation['transaction_details'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Section des ressources -->
        <?php if ($is_subscribed): ?>
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
        <?php else: ?>
            <div class="card mb-2 shadow p-2">
                <div class="card-body">
                    <h4 class="card-title">Ressources</h4>
                    <p class="card-text">Vous devez être abonné pour accéder aux ressources.</p>
                </div>
            </div>
        <?php endif; ?>


        <!-- Section des sessions et feedbacks -->
        <?php if ($is_subscribed): ?>
            <div class="card mb-2 shadow p-2">
                <div class="card-body">
                    <h4 class="card-title">Sessions et Feedbacks</h4>
                    <?php if (count($sessions) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Session</th>
                                        <th>Formation</th>
                                        <th>Feedbacks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($sessions as $session): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($session['titre']); ?></td>
                                            <td><?php echo htmlspecialchars($session['formation_titre']); ?></td>
                                            <td><?php echo htmlspecialchars($session['feedbacks']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="card-text">Aucune session pour le moment.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="card mb-2 shadow p-2">
                <div class="card-body">
                    <h4 class="card-title">Sessions et Feedbacks</h4>
                    <p class="card-text">Vous devez être abonné pour accéder aux sessions et feedbacks.</p>
                </div>
            </div>
        <?php endif; ?>


        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.toggle-description').forEach(function(link) {
                    link.addEventListener('click', function(event) {
                        event.preventDefault();
                        const fullDescription = this.previousElementSibling;
                        const ellipsis = fullDescription.previousElementSibling;
                        const shortDescription = ellipsis.previousElementSibling;

                        if (fullDescription.style.display === 'none') {
                            fullDescription.style.display = 'inline';
                            ellipsis.style.display = 'none';
                            shortDescription.style.display = 'none';
                            this.textContent = 'Voir moins';
                        } else {
                            fullDescription.style.display = 'none';
                            ellipsis.style.display = 'inline';
                            shortDescription.style.display = 'inline';
                            this.textContent = 'Voir plus';
                        }
                    });
                });
            });
        </script>

        <!-- Section des notifications -->
        <?php if ($is_subscribed): ?>
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
        <?php else: ?>
            <div class="card mb-4 shadow p-2">
                <div class="card-body">
                    <h4 class="card-title">Notifications</h4>
                    <p class="card-text">Vous devez être abonné pour accéder aux notifications.</p>
                </div>
            </div>
        <?php endif; ?>

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

        <!-- Section des Questions des Quiz -->
        <?php if ($is_subscribed): ?>
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
        <?php else: ?>
            <div class="card mb-4 shadow p-2">
                <div class="card-body">
                    <h4 class="card-title">Questions des Quiz</h4>
                    <p class="card-text">Vous devez être abonné pour accéder aux questions des quiz.</p>
                </div>
            </div>
        <?php endif; ?>


        <div class="btn-group mb-4">
            <a href="logout.php" class="btn">Se Déconnecter</a>
        </div>

    </div>


  
    <?php include_once ('../inclusion/footer_2.php'); ?>
     <!-- Inclusion de Bootstrap JS (nécessaire pour le offcanvas) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://code.jquery.com/jquery-2.2.4.min.js'></script>
</body>
</html>
