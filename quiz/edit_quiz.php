<?php
session_start();
include('../admin/config.php');

if (!isset($_SESSION['trainer_id'])) {
    header('Location: login_trainer.php');
    exit;
}

$trainer_id = $_SESSION['trainer_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quiz_id = $_POST['quiz_id'];
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $formation_id = $_POST['formation_id'];

    try {
        $stmt = $pdo->prepare("
            UPDATE quizzes
            SET titre = ?, description = ?, formation_id = ?
            WHERE id = ? AND trainer_id = ?
        ");
        $stmt->execute([$titre, $description, $formation_id, $quiz_id, $trainer_id]);

        header('Location: ../trainers/trainers.php?success=true');
        exit;
    } catch (PDOException $e) {
        echo 'Erreur : ' . $e->getMessage();
    }
}

if (isset($_GET['id'])) {
    $quiz_id = $_GET['id'];
    $stmt = $pdo->prepare("
        SELECT * FROM quizzes WHERE id = ? AND trainer_id = ?
    ");
    $stmt->execute([$quiz_id, $trainer_id]);
    $quiz = $stmt->fetch();

    if (!$quiz) {
        echo 'Quiz non trouvé.';
        exit;
    }
} else {
    echo 'ID du quiz non spécifié.';
    exit;
}

// Récupérer les formations disponibles
$stmt_formations = $pdo->prepare("
    SELECT id, titre FROM formations
");
$stmt_formations->execute();
$formations = $stmt_formations->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un Quiz</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Modifier le Quiz</h2>

        <form action="" method="POST">
            <input type="hidden" name="quiz_id" value="<?php echo htmlspecialchars($quiz['id']); ?>">
            <div class="mb-3">
                <label for="titre" class="form-label">Titre:</label>
                <input type="text" id="titre" name="titre" class="form-control" value="<?php echo htmlspecialchars($quiz['titre']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <textarea id="description" name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($quiz['description']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="formation_id" class="form-label">Formation:</label>
                <select id="formation_id" name="formation_id" class="form-select" required>
                    <?php foreach ($formations as $formation): ?>
                        <option value="<?php echo $formation['id']; ?>" <?php echo $formation['id'] == $quiz['formation_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($formation['titre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Modifier le Quiz</button>
        </form>

        <a href="trainers.php" class="btn btn-secondary mt-4">Retour au Tableau de Bord</a>
    </div>
</body>
</html>
