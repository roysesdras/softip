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

// Récupérer les formations auxquelles l'étudiant est inscrit
$stmt = $pdo->prepare("SELECT formations.id FROM formations 
                        INNER JOIN inscriptions ON formations.id = inscriptions.formation_id 
                        WHERE inscriptions.user_id = ?");
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
    <title>Mes Cours</title>
</head>
<body>
    <h1>Mes Cours</h1>
    <?php if (!empty($courses)): ?>
        <ul>
        <?php foreach ($courses as $course): ?>
            <li>
                <h3><?php echo ($course['nom']); ?></h3>
                <p><?php echo ($course['description']); ?></p>
                <img src="../avatar_img<?php echo htmlspecialchars($course['image']); ?>" alt="Image du cours" style="max-width: 200px;">
                <a href="view_course.php?course_id=<?php echo $course['id']; ?>">Accéder au cours</a>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Aucun cours disponible pour les formations inscrites.</p>
    <?php endif; ?>
</body>

</html>
