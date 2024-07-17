<?php
session_start();
include('../admin/config.php');

if (!isset($_SESSION['trainer_id'])) {
    header('Location: login_trainer.php');
    exit;
}

$trainer_id = $_SESSION['trainer_id'];

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
        // Préparer et exécuter l'insertion de la question
        $stmt = $pdo->prepare("
            INSERT INTO questions (question_text, quiz_id, option_a, option_b, option_c, option_d, correct_option, trainer_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$question_text, $quiz_id, $option_a, $option_b, $option_c, $option_d, $correct_option, $trainer_id]);

        // Rediriger vers le tableau de bord formateur avec un message de succès
        header('Location: ../trainers/trainers.php?question_added=true');
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
    <title>Ajouter une Question</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.9.0/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Ajouter une Question au Quiz</h2>

        <!-- Afficher le message de succès si présent -->
        <?php if (isset($_GET['question_added']) && $_GET['question_added'] == 'true'): ?>
            <div class="alert alert-success" role="alert">La question a été ajoutée avec succès !</div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label for="quiz_id" class="form-label">Quiz:</label>
                <select id="quiz_id" name="quiz_id" class="form-select" required>
                    <?php foreach ($quizzes as $quiz): ?>
                        <option value="<?php echo $quiz['id']; ?>">
                            <?php echo htmlspecialchars($quiz['titre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="question_text" class="form-label">Question:</label>
                <textarea id="question_text" name="question_text" class="form-control" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="option_a" class="form-label">Option A:</label>
                <input type="text" id="option_a" name="option_a" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="option_b" class="form-label">Option B:</label>
                <input type="text" id="option_b" name="option_b" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="option_c" class="form-label">Option C:</label>
                <input type="text" id="option_c" name="option_c" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="option_d" class="form-label">Option D:</label>
                <input type="text" id="option_d" name="option_d" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="correct_option" class="form-label">Option Correcte:</label>
                <select id="correct_option" name="correct_option" class="form-select" required>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter la Question</button>
        </form>

        <a href="../trainers/trainers.php" class="btn btn-secondary mt-4">Retour au Tableau de Bord</a>
    </div>
</body>
</html>
