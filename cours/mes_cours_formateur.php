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
        echo "Cours trouvés :<br>";
        foreach ($courses as $course) {
            echo "<h3>" . htmlspecialchars($course['nom']) . "</h3>";
            echo "<p>" . htmlspecialchars($course['description']) . "</p>";
            echo "<img src='../avatar_img/" . htmlspecialchars($course['image']) . "' alt='Image du cours' style='max-width: 200px;'><br>";
        }
    } else {
        echo "Aucun cours trouvé pour ce formateur.";
    }

} catch (PDOException $e) {
    // Gérer les erreurs PDO
    echo "Erreur PDO : " . $e->getMessage();
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
                <h3><?php echo htmlspecialchars($course['nom']); ?></h3>
                <p><?php echo htmlspecialchars($course['description']); ?></p>
                <img src="../avatar_img/<?php echo htmlspecialchars($course['image']); ?>" alt="Image du cours" style="max-width: 200px;">
                <a href="modifier_cours.php?id=<?php echo $course['id']; ?>">Modifier</a> |
                <a href="supprimer_cours.php?id=<?php echo $course['id']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce cours ?');">Supprimer</a>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Aucun cours trouvé.</p>
    <?php endif; ?>
</body>
</html>
