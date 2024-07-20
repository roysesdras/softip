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
    <title><?= htmlspecialchars($course['nom']) ?></title>
    <style>
        .progress-bar {
            width: 100%;
            background-color: #f3f3f3;
        }

        .progress-bar-fill {
            height: 20px;
            background-color: #4caf50;
            width: <?= round($progress, 2) ?>%;
            text-align: center;
            color: white;
        }
    </style>
</head>
<body>
    <h1><?= htmlspecialchars($course['nom']) ?></h1>
    <div class="progress-bar">
        <div class="progress-bar-fill"><?= round($progress, 2) ?>%</div>
    </div>
    <div>
        <?= nl2br(($step_content)) ?>
    </div>
    <div>
        <?php if ($current_step > 1): ?>
            <a href="view_course.php?course_id=<?= htmlspecialchars($course_id) ?>&step=<?= $current_step - 1 ?>">Précédent</a>
        <?php endif; ?>
        <?php if ($current_step < $total_steps): ?>
            <a href="view_course.php?course_id=<?= htmlspecialchars($course_id) ?>&step=<?= $current_step + 1 ?>">Suivant</a>
        <?php endif; ?>
    </div>
</body>
</html>
