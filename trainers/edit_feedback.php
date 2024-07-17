<?php
session_start();
include('../admin/config.php');

// if (!isset($_SESSION['trainer_id'])) {
//     header('Location: login_trainer.php');
//     exit;
// }

$trainer_id = $_SESSION['trainer_id'];

if (!isset($_GET['id'])) {
    header('Location: trainers.php');
    exit;
}

$feedback_id = $_GET['id'];

// Récupérer le feedback à modifier
$stmt_feedback = $pdo->prepare("
    SELECT feedbacks.*, students.username AS student_username, formations.titre AS session_title
    FROM feedbacks
    INNER JOIN students ON feedbacks.student_id = students.id
    INNER JOIN sessions ON feedbacks.session_id = sessions.id
    INNER JOIN formations ON sessions.formation_id = formations.id
    WHERE feedbacks.id = ?
");
$stmt_feedback->execute([$feedback_id]);
$feedback = $stmt_feedback->fetch();

if (!$feedback) {
    header('Location: trainers.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_feedback = $_POST['feedback'];
    $new_rating = $_POST['rating'];

    $stmt = $pdo->prepare("UPDATE feedbacks SET feedback = ?, rating = ? WHERE id = ?");
    $stmt->execute([$new_feedback, $new_rating, $feedback_id]);

    $_SESSION['success_message'] = 'Feedback modifié avec succès';
    header('Location: trainers.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un Feedback</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Modifier un Feedback</h2>
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="student_id" class="form-label">Étudiant</label>
                <input type="text" class="form-control" id="student_id" value="<?php echo htmlspecialchars($feedback['student_username']); ?>" disabled>
            </div>
            <div class="mb-3">
                <label for="session_title" class="form-label">Session</label>
                <input type="text" class="form-control" id="session_title" value="<?php echo htmlspecialchars($feedback['session_title']); ?>" disabled>
            </div>
            <div class="mb-3">
                <label for="rating" class="form-label">Note</label>
                <select class="form-select" id="rating" name="rating" required>
                    <option value="1" <?php echo ($feedback['rating'] == 1) ? 'selected' : ''; ?>>1</option>
                    <option value="2" <?php echo ($feedback['rating'] == 2) ? 'selected' : ''; ?>>2</option>
                    <option value="3" <?php echo ($feedback['rating'] == 3) ? 'selected' : ''; ?>>3</option>
                    <option value="4" <?php echo ($feedback['rating'] == 4) ? 'selected' : ''; ?>>4</option>
                    <option value="5" <?php echo ($feedback['rating'] == 5) ? 'selected' : ''; ?>>5</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="feedback" class="form-label">Feedback</label>
                <textarea class="form-control" id="feedback" name="feedback" rows="4" required><?php echo htmlspecialchars($feedback['feedback']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Modifier Feedback</button>
        </form>
        <a href="trainers.php" class="btn btn-secondary mt-3">Retour</a>
    </div>
</body>
</html>
