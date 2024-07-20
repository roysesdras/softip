<?php
session_start();
include('../admin/config.php');

if (!isset($_SESSION['trainer_id'])) {
    header('Location: login_trainer.php');
    exit;
}

$trainer_id = $_SESSION['trainer_id'];

try {
    // Récupérer le nom d'utilisateur du formateur connecté
    $stmt_trainer_name = $pdo->prepare("SELECT username FROM trainers WHERE id = ?");
    if ($stmt_trainer_name === false) {
        // Gérer l'erreur de préparation de la requête
        throw new Exception("Erreur de préparation de la requête.");
    }

    $stmt_trainer_name->execute([$trainer_id]);

    // Vérifier si une ligne a été retournée
    if ($stmt_trainer_name->rowCount() > 0) {
        $username = $stmt_trainer_name->fetchColumn();
        // Utiliser $username comme nécessaire
    } else {
        // Gérer le cas où aucun formateur avec cet ID n'est trouvé
        throw new Exception("Aucun formateur trouvé avec l'ID spécifié.");
    }

    // Libérer la ressource du statement
    $stmt_trainer_name = null;

} catch (PDOException $e) {
    // Gérer les erreurs PDO
    echo "Erreur PDO : " . $e->getMessage();
} catch (Exception $e) {
    // Gérer les autres erreurs
    echo "Erreur : " . $e->getMessage();
}


// Récupérer les sessions que le formateur gère
$stmt_sessions = $pdo->prepare("
    SELECT sessions.*, formations.titre AS formation_titre
    FROM sessions
    INNER JOIN formations ON sessions.formation_id = formations.id
    WHERE sessions.trainer_id = ?
");
$stmt_sessions->execute([$trainer_id]);
$sessions = $stmt_sessions->fetchAll();


// Récupérer les ressources
$stmt_resources = $pdo->query("SELECT resources.*, formations.titre AS formation_titre FROM resources INNER JOIN formations ON resources.formation_id = formations.id");
$resources = $stmt_resources->fetchAll();


$trainer_id = $_SESSION['trainer_id']; // Assure-toi que l'ID du formateur est dans la session

// Récupérer les feedbacks des étudiants pour les sessions gérées par le formateur
$stmt_received_feedbacks = $pdo->prepare("
    SELECT feedbacks.*, students.username AS student_username, sessions.title AS session_title
    FROM feedbacks
    INNER JOIN students ON feedbacks.student_id = students.id
    INNER JOIN sessions ON feedbacks.session_id = sessions.id
    WHERE sessions.trainer_id = ?
    ORDER BY feedbacks.created_at DESC
");
$stmt_received_feedbacks->execute([$trainer_id]);
$received_feedbacks = $stmt_received_feedbacks->fetchAll();

// Récupérer les feedbacks que le formateur a donnés avec les informations de session et d'étudiant
$stmt_feedbacks = $pdo->prepare("
    SELECT feedbacks.*, students.username AS student_username, formations.titre AS session_title
    FROM feedbacks
    INNER JOIN students ON feedbacks.student_id = students.id
    INNER JOIN sessions ON feedbacks.session_id = sessions.id
    INNER JOIN formations ON sessions.formation_id = formations.id
    WHERE sessions.trainer_id = ?
");
$stmt_feedbacks->execute([$trainer_id]);
$feedbacks = $stmt_feedbacks->fetchAll();

// Récupérer les quiz créés par le formateur
$stmt_quizzes = $pdo->prepare("
    SELECT quizzes.*, formations.titre AS formation_titre
    FROM quizzes
    INNER JOIN formations ON quizzes.formation_id = formations.id
    WHERE quizzes.trainer_id = ?
");
$stmt_quizzes->execute([$trainer_id]);
$quizzes = $stmt_quizzes->fetchAll();

// Fetch quiz answers
$stmt_answers = $pdo->prepare("
    SELECT quiz_answers.*, students.username AS student_username, quizzes.titre AS quiz_titre
    FROM quiz_answers
    INNER JOIN students ON quiz_answers.student_id = students.id
    INNER JOIN quizzes ON quiz_answers.quiz_id = quizzes.id
    WHERE quizzes.trainer_id = ? AND quiz_answers.archived = 0
");
$stmt_answers->execute([$trainer_id]);
$quiz_answers = $stmt_answers->fetchAll();

// Récupérer les questions créées par le formateur
$stmt_questions = $pdo->prepare("
    SELECT questions.*, quizzes.titre AS quiz_titre
    FROM questions
    INNER JOIN quizzes ON questions.quiz_id = quizzes.id
    WHERE questions.trainer_id = ?
");
$stmt_questions->execute([$trainer_id]);
$questions = $stmt_questions->fetchAll();

// Récupérer les formations disponibles
$stmt_formations = $pdo->prepare("
    SELECT id, titre FROM formations
");
$stmt_formations->execute();
$formations = $stmt_formations->fetchAll();

// Afficher les messages de succès si présents
$success_message = '';
if (isset($_GET['success']) && $_GET['success'] == 'true') {
    $success_message = '<div class="alert alert-success" role="alert">Le quiz a été créé avec succès !</div>';
} elseif (isset($_GET['question_added']) && $_GET['question_added'] == 'true') {
    $success_message = '<div class="alert alert-success" role="alert">La question a été ajoutée avec succès !</div>';
}

// Archivage des réponses
if (isset($_POST['archive_answer'])) {
    $answer_id = $_POST['answer_id'];
    $query = "UPDATE quiz_answers SET archived = 1 WHERE id = ?";
    $stmt = $pdo->prepare($query);
    if ($stmt->execute([$answer_id])) {
        header("Location: trainers.php");
        exit;
    } else {
        echo "Erreur lors de l'archivage de la réponse.";
    }   
}

// Code de restauration pour archivage
if (isset($_POST['restore_answer'])) {
    $answer_id = $_POST['answer_id'];
    $stmt_restore_answer = $pdo->prepare("UPDATE quiz_answers SET archived = 0 WHERE id = ?");
    $stmt_restore_answer->execute([$answer_id]);
    header('Location: trainers.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo isset($username) ? htmlspecialchars($username) : 'Formateur'; ?> - formateur</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.9.0/font/bootstrap-icons.min.css">
    <!-- Favicons -->
    <link href="../assets/img/favicon.png" rel="icon">
    <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <link rel="stylesheet" href="../assets/style.css">


    <script>
        function toggleQuizForm() {
            const form = document.getElementById('quiz-form');
            const button = document.getElementById('toggle-quiz-form-button');
            if (form.style.display === 'none') {
                form.style.display = 'block';
                button.innerText = 'fermer';
            } else {
                form.style.display = 'none';
                button.innerText = 'Créer un Nouveau Quiz';
            }
        }

        function hideQuizForm() {
            document.getElementById('quiz-form').style.display = 'none';
            document.getElementById('toggle-quiz-form-button').innerText = 'Créer un Nouveau Quiz';
        }
    </script>

    <style>
        .btn-group {
            margin-top: 10px;
        }
        /* .feedback {
            max-height: 100px;
            overflow-y: auto;
        } */

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

        .btnb{
            background-color: #033e60;
            color: #ffffff;
            padding: 5px;
            border          : solid transparent;
            font-size: 16px;
            border-radius: 0px;
        }
        .btnb:hover{
            background-color: transparent;
            color: #033e60;
            padding: 5px;
            border          : solid 1px #033e60;
        }

        a{
            text-decoration: none;
        }
    </style>

</head>
<body>

<!-- Barre de Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="student_dashboard.php">Dashboard Formateur</a>
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
                            <a class="nav-link" href="logout_trainer.php">Se Déconnecter</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../cours/insert_cours.php" target="_blanck">Ajouter Cours</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../cours/mes_cours_formateur.php" target="_blanck">Gérer mes Cours</a>
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
                                try {
                                    // Récupérer les informations du formateur
                                    $stmt_trainer = $pdo->prepare("SELECT * FROM trainers WHERE id = ?");
                                    $stmt_trainer->execute([$trainer_id]);
                                    $trainer = $stmt_trainer->fetch(PDO::FETCH_ASSOC);
                                    
                                    // Vérifier si le formateur existe et s'il a un avatar défini
                                    if ($trainer && !empty($trainer['avatar'])) {
                                        echo '<img src="' . $trainer['avatar'] . '" alt="Avatar" class="avatar-img rounded-circle me-2">';
                                    } else {
                                        echo '<img src="../avatar_img/O.jpg" alt="Avatar par défaut" class="avatar-img rounded-circle me-2">';
                                    }
                                    
                                    // Afficher le nom d'utilisateur du formateur
                                    echo '<span class="text-light">' . htmlspecialchars($trainer['username']) . '</span>';

                                } catch (PDOException $e) {
                                    // Gérer les erreurs PDO
                                    echo "Erreur PDO : " . $e->getMessage();
                                }

                            ?>

                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <br>
    <div class="container mt-5">
       
        <h3 class="mb-4">Bienvenue à vous, Mr <?php echo isset($username) ? htmlspecialchars($username) : 'Formateur'; ?></h3>

        <!-- Afficher les messages de succès -->
        <?php if ($success_message): ?>
            <?php echo $success_message; ?>
        <?php endif; ?>

        <!-- Bouton pour ajouter une nouvelle session -->
        <div class="card mb-4 shadow p-2">
            <div class="card-body">
                <h3 class="card-title">Sessions</h3>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Formation</th>
                                <th>Date</th>
                                <th>Heure</th>
                                <th>Lieu</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sessions as $session): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($session['formation_titre']); ?></td>
                                    <td><?php echo htmlspecialchars($session['date']); ?></td>
                                    <td><?php echo htmlspecialchars($session['time']); ?></td>
                                    <td><?php echo htmlspecialchars($session['location']); ?></td>
                                    <td>
                                        <a href="edit_session.php?id=<?php echo $session['id']; ?>" class="btn btn-warning me-2">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="delete_session.php?id=<?php echo $session['id']; ?>" class="btn btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <a href="add_session.php" class="btnb mt-3">
                    <i class="bi bi-plus-circle"></i> Ajouter
                </a>
            </div>
        </div>

        <!-- Affchage des ressources -->
        <div class="card mb-4 shadow p-2">
            <div class="card-body">
                <h3 class="card-title">Ressources</h3>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Formation</th>
                                <th>Titre</th>
                                <th>Description</th>
                                <th>Lien</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resources as $resource): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($resource['formation_titre']); ?></td>
                                    <td><?php echo htmlspecialchars($resource['titre']); ?></td>
                                    <td><?php echo htmlspecialchars($resource['description']); ?></td>
                                    <td><a href="<?php echo htmlspecialchars($resource['lien']); ?>" target="_blank">Accéder à la Ressource</a></td>
                                    <td>
                                        <a href="../resources/edit_resources.php?id=<?php echo $resource['id']; ?>" class="btn btn-warning me-2 mb-2"><i class="bi bi-pencil-square"></i></a>
                                        <a href="../resources/delete_resources.php?id=<?php echo $resource['id']; ?>" class="btn btn-danger"><i class="bi bi-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <a href="../resources/resources.php" class="btnb mt-3">
                    <i class="bi bi-plus-circle"></i> Ajouter
                </a>
            </div>
        </div>

        <!-- Section pour afficher les quiz -->
        <div class="card mb-4 shadow p-2">
            <div class="card-body">
                <h3 class="card-title">Les Quiz</h3>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Description</th>
                                <th>Formation</th>
                                <th>Date de Création</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($quizzes as $quiz): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($quiz['titre']); ?></td>
                                    <td><?php echo htmlspecialchars($quiz['description']); ?></td>
                                    <td><?php echo htmlspecialchars($quiz['formation_titre']); ?></td>
                                    <td><?php echo htmlspecialchars($quiz['created_at']); ?></td>
                                    <td>
                                        <a href="../quiz/edit_quiz.php?id=<?php echo $quiz['id']; ?>" class="btn btn-warning me-2">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="../quiz/delete_quiz.php?id=<?php echo $quiz['id']; ?>" class="btn btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                    <a href="../quiz/create_quiz.php" class="btnb mt-3">
                        <i class="bi bi-plus-circle"></i> Ajouter
                    </a>
            </div>
        </div>

        <!-- Afficher les questions -->
        <div class="card mb-4 shadow p-2">
            <div class="card-body">
                <h3 class="card-title">Les Questions</h3>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Question</th>
                                <th>Quiz</th>
                                <th>Option A</th>
                                <th>Option B</th>
                                <th>Option C</th>
                                <th>Option D</th>
                                <th>Bonne Réponse</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($questions as $question): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($question['question_text']); ?></td>
                                    <td><?php echo htmlspecialchars($question['quiz_titre']); ?></td>
                                    <td><?php echo htmlspecialchars($question['option_a']); ?></td>
                                    <td><?php echo htmlspecialchars($question['option_b']); ?></td>
                                    <td><?php echo htmlspecialchars($question['option_c']); ?></td>
                                    <td><?php echo htmlspecialchars($question['option_d']); ?></td>
                                    <td><?php echo htmlspecialchars($question['correct_option']); ?></td>
                                    <td>
                                        <a href="../quiz/edit_question.php?id=<?php echo $question['id']; ?>" class="btn btn-warning me-2 mb-2">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="../quiz/delete_question.php?id=<?php echo $question['id']; ?>" class="btn btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <a href="../quiz/create_question.php" class="btnb mt-3">
                    <i class="bi bi-plus-circle"></i> Ajouter
                </a>
            </div>
        </div>

        <!-- Affichage des réponses aux quiz -->
        <div class="card mb-4 shadow p-2">
            <div class="card-body">
                <h3 class="card-title">Réponses aux Quiz</h3>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Quiz</th>
                                <th>Étudiant</th>
                                <th>Réponse</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($quiz_answers as $answer): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($answer['quiz_titre']); ?></td>
                                    <td><?php echo htmlspecialchars($answer['student_username']); ?></td>
                                    <td><?php echo htmlspecialchars($answer['answer']); ?></td>
                                    <td><?php echo htmlspecialchars($answer['submitted_at']); ?></td>
                                    <td>
                                        <form action="trainers.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="answer_id" value="<?php echo $answer['id']; ?>">
                                            <button type="submit" name="archive_answer" class="btn btn-danger">
                                                <i class="bi bi-archive"></i> Archiver
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Bouton pour afficher les réponses archivées -->
        <div class="mb-4">
            <button id="toggle-archived-answers-button" class="btnb" onclick="toggleArchivedAnswers()">Afficher les archives</button>
        </div>

        <!-- Section pour afficher les réponses archivées des étudiants aux quiz -->
        <div id="archived-answers-section" class="card mb-4 shadow p-2" style="display: none;">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Étudiant</th>
                                <th>Quiz</th>
                                <th>Question</th>
                                <th>Réponse</th>
                                <th>Date de Soumission</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt_archived_answers = $pdo->prepare("
                                SELECT quiz_answers.*, students.username AS student_username, quizzes.titre AS quiz_titre
                                FROM quiz_answers
                                INNER JOIN students ON quiz_answers.student_id = students.id
                                INNER JOIN quizzes ON quiz_answers.quiz_id = quizzes.id
                                WHERE quizzes.trainer_id = ? AND quiz_answers.archived = 1
                            ");
                            $stmt_archived_answers->execute([$trainer_id]);
                            $archived_answers = $stmt_archived_answers->fetchAll();
                            
                            foreach ($archived_answers as $answer): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($answer['student_username']); ?></td>
                                    <td><?php echo htmlspecialchars($answer['quiz_titre']); ?></td>
                                    <td><?php echo htmlspecialchars($answer['question_id']); ?></td>
                                    <td><?php echo htmlspecialchars($answer['answer']); ?></td>
                                    <td><?php echo htmlspecialchars($answer['submitted_at']); ?></td>
                                    <td>
                                        <form action="trainers.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="answer_id" value="<?php echo $answer['id']; ?>">
                                            <button type="submit" name="restore_answer" class="btn btn-primary">
                                                <i class="bi bi-arrow-counterclockwise"></i> Restaurer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <script>
            function toggleArchivedAnswers() {
                const section = document.getElementById('archived-answers-section');
                const button = document.getElementById('toggle-archived-answers-button');
                if (section.style.display === 'none') {
                    section.style.display = 'block';
                    button.innerText = 'Cacher les archives';
                } else {
                    section.style.display = 'none';
                    button.innerText = 'Afficher les archives';
                }
            }
        </script>

        <!-- Feedbacks reçus -->
        <div class="card mb-4 shadow p-2">
            <div class="card-body">
                <h3 class="card-title">Feedbacks des Étudiants</h3>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Étudiant</th>
                                <th>Session</th>
                                <th>Commentaire</th>
                                <th>Note</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <?php if (empty($received_feedbacks)): ?>
                            <p>Aucun feedback disponible pour l'instant.</p>
                        <?php else: ?>
                            <tbody>
                                <?php foreach ($received_feedbacks as $feedback): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($feedback['student_username']); ?></td>
                                        <td><?php echo htmlspecialchars($feedback['session_title']); ?></td>
                                        <td><?php echo htmlspecialchars($feedback['feedback']); ?></td>
                                        <td><?php echo htmlspecialchars($feedback['rating']); ?></td>
                                        <td><?php echo htmlspecialchars($feedback['created_at']); ?></td>
                                        <td>
                                            <a href="edit_feedback.php?id=<?php echo $feedback['id']; ?>" class="btn btn-warning me-2">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <a href="delete_feedback.php?id=<?php echo $feedback['id']; ?>" class="btn btn-danger">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>

        <!-- Feedbacks donnés -->
        <div class="card mb-4 shadow p-2">
            <div class="card-body">
                <h3 class="card-title">Feedbacks Donnés</h3>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Étudiant</th>
                                <th>Session</th>
                                <th>Commentaire</th>
                                <th>Note</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($feedbacks as $feedback): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($feedback['student_username']); ?></td>
                                    <td><?php echo htmlspecialchars($feedback['session_title']); ?></td>
                                    <td><?php echo htmlspecialchars($feedback['feedback']); ?></td>
                                    <td><?php echo htmlspecialchars($feedback['rating']); ?></td>
                                    <td><?php echo htmlspecialchars($feedback['created_at']); ?></td>
                                    <td>
                                        <a href="edit_feedback.php?id=<?php echo $feedback['id']; ?>" class="btn btn-warning me-2">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="delete_feedback.php?id=<?php echo $feedback['id']; ?>" class="btn btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <a href="add_feedback.php" class="btnb">
                    <i class="bi bi-plus-circle"></i> Ajouter
                </a>
            </div>
        </div>
    </div>



    <?php require_once ('../inclusion/footer_2.php'); ?>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://code.jquery.com/jquery-2.2.4.min.js'></script>
</body>
</html>
