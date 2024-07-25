<?php
session_start();
include('../admin/config.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['student_id'])) {
    header('Location: login_student.php');
    exit;
}

$student_id = $_SESSION['student_id'];

// Récupérer les formations auxquelles l'étudiant est inscrit
$stmt = $pdo->prepare("
    SELECT f.id 
    FROM formations f
    INNER JOIN abonnements a ON f.id = a.formation_id 
    WHERE a.student_id = ?
");
$stmt->execute([$student_id]);
$formation_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Récupérer les cours associés aux formations de l'étudiant
if (!empty($formation_ids)) {
    $in_query = implode(',', array_fill(0, count($formation_ids), '?'));
    $stmt_courses = $pdo->prepare("SELECT * FROM cours WHERE formation_id IN ($in_query)");
    $stmt_courses->execute($formation_ids);
    $courses = $stmt_courses->fetchAll();
} else {
    $courses = [];
}
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Cours</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/style.css">
    <link href="../assets/img/favicon.png" rel="icon">
    <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <style>
        .course-item {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .course-item img {
            max-width: 100%;
            border-radius: 5px;
        }
        .course-item h3 {
            font-size: 1.75rem;
            margin-bottom: 10px;
        }
        .course-item p {
            font-size: 1.1rem;
            margin-bottom: 10px;
        }
        .course-item a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        .course-item a:hover {
            text-decoration: underline;
        }
        .alert-info {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Mes Cours</h1>
        <?php if (!empty($courses)): ?>
            <div class="row">
            <?php foreach ($courses as $course): ?>
                <div class="col-md-4">
                    <div class="course-item">
                        <h3><?php echo htmlspecialchars($course['nom']); ?></h3>
                        <img src="../avatar_img/<?php echo htmlspecialchars($course['image']); ?>" alt="Image du cours">
                        <p><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>
                        <a href="view_course.php?course_id=<?php echo $course['id']; ?>">Accéder au cours</a>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info" role="alert">
                Aucun cours disponible pour les formations inscrites.
            </div>
        <?php endif; ?>
    </div>

    <?php require_once ('../inclusion/footer_2.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
