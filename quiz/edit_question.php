<?php
session_start();
include('../admin/config.php');

if (!isset($_SESSION['trainer_id'])) {
    header('Location: login_trainer.php');
    exit;
}

$trainer_id = $_SESSION['trainer_id'];

if (!isset($_GET['id'])) {
    header('Location: trainers.php');
    exit;
}

$question_id = $_GET['id'];

// Récupérer les informations de la question
$stmt = $pdo->prepare("
    SELECT * FROM questions WHERE id = ? AND trainer_id = ?
");
$stmt->execute([$question_id, $trainer_id]);
$question = $stmt->fetch();

if (!$question) {
    header('Location: ../trainers/trainers.php');
    exit;
}

// Récupérer les quiz disponibles créés par le formateur
$stmt_quizzes = $pdo->prepare("
    SELECT id, titre FROM quizzes WHERE trainer_id = ?
");
$stmt_quizzes->execute([$trainer_id]);
$quizzes = $stmt_quizzes->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $question_text = $_POST['question_text'];
    $quiz_id = $_POST['quiz_id'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = $_POST['option_c'];
    $option_d = $_POST['option_d'];
    $correct_option = $_POST['correct_option'];

    try {
        // Préparer et exécuter la mise à jour de la question
        $stmt = $pdo->prepare("
            UPDATE questions SET question_text = ?, quiz_id = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_option = ?
            WHERE id = ? AND trainer_id = ?
        ");
        $stmt->execute([$question_text, $quiz_id, $option_a, $option_b, $option_c, $option_d, $correct_option, $question_id, $trainer_id]);

        // Rediriger vers le tableau de bord formateur avec un message de succès
        header('Location: ../trainers/trainers.php?question_updated=true');
        exit;
    } catch (PDOException $e) {
        echo 'Erreur : ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier une Question</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.9.0/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Modifier une Question</h2>

        <form action="" method="POST">
            <div class="mb-3">
                <label for="quiz_id" class="form-label">Quiz:</label>
                <select id="quiz_id" name="quiz_id" class="form-select" required>
                    <?php foreach ($quizzes as $quiz): ?>
                        <option value="<?php echo $quiz['id']; ?>" <?php echo $quiz['id'] == $question['quiz_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($quiz['titre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="question_text" class="form-label">Question:</label>
                <textarea id="question_text" name="question_text" class="form-control" rows="3" required><?php echo htmlspecialchars($question['question_text']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="option_a" class="form-label">Option A:</label>
                <input type="text" id="option_a" name="option_a" class="form-control" value="<?php echo htmlspecialchars($question['option_a']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="option_b" class="form-label">Option B:</label>
                <input type="text" id="option_b" name="option_b" class="form-control" value="<?php echo htmlspecialchars($question['option_b']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="option_c" class="form-label">Option C:</label>
                <input type="text" id="option_c" name="option_c" class="form-control" value="<?php echo htmlspecialchars($question['option_c']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="option_d" class="form-label">Option D:</label>
                <input type="text" id="option_d" name="option_d" class="form-control" value="<?php echo htmlspecialchars($question['option_d']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="correct_option" class="form-label">Option Correcte:</label>
                <select id="correct_option" name="correct_option" class="form-select" required>
                    <option value="A" <?php echo $question['correct_option'] == 'A' ? 'selected' : ''; ?>>A</option>
                    <option value="B" <?php echo $question['correct_option'] == 'B' ? 'selected' : ''; ?>>B</option>
                    <option value="C" <?php echo $question['correct_option'] == 'C' ? 'selected' : ''; ?>>C</option>
                    <option value="D" <?php echo $question['correct_option'] == 'D' ? 'selected' : ''; ?>>D</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Modifier la Question</button>
        </form>

        <a href="../trainers/trainers.php" class="btn btn-secondary mt-4">Retour au Tableau de Bord</a>
    </div>
</body>
</html>
