<?php
session_start();
include('../admin/config.php');

// // Vérifier si l'utilisateur est un formateur
// if (!isset($_SESSION['trainer_id']) || $_SESSION['role'] != 'trainer') {
//     header('Location: login_trainer.php');
//     exit;
// }

$trainer_id = $_SESSION['trainer_id'];

// Récupérer les sessions disponibles
$stmt_sessions = $pdo->prepare("
    SELECT sessions.id, formations.titre 
    FROM sessions
    INNER JOIN formations ON sessions.formation_id = formations.id
    WHERE sessions.trainer_id = ?
");
$stmt_sessions->execute([$trainer_id]);
$sessions = $stmt_sessions->fetchAll();

// Récupérer les étudiants inscrits aux sessions du formateur
$stmt_students = $pdo->prepare("
    SELECT DISTINCT students.id, students.username 
    FROM students
    INNER JOIN inscriptions ON students.id = inscriptions.user_id
    INNER JOIN sessions ON inscriptions.formation_id = sessions.formation_id
    WHERE sessions.trainer_id = ?
");
$stmt_students->execute([$trainer_id]);
$students = $stmt_students->fetchAll();

// Ajouter un feedback
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $session_id = $_POST['session_id'];
    $student_id = $_POST['student_id'];
    $feedback = $_POST['feedback'];
    $rating = $_POST['rating'];

    $stmt = $pdo->prepare("INSERT INTO feedbacks (session_id, student_id, feedback, rating) VALUES (?, ?, ?, ?)");
    $stmt->execute([$session_id, $student_id, $feedback, $rating]);

    $_SESSION['success_message'] = 'Feedback ajouté avec succès';
    header('Location: trainers.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Feedback</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Ajouter un Feedback</h2>
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="session_id" class="form-label">Session</label>
                <select class="form-select" id="session_id" name="session_id" required>
                    <option value="">Sélectionnez une session</option>
                    <?php foreach ($sessions as $session): ?>
                        <option value="<?php echo $session['id']; ?>"><?php echo htmlspecialchars($session['titre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="student_id" class="form-label">Étudiant</label>
                <select class="form-select" id="student_id" name="student_id" required>
                    <option value="">Sélectionnez un étudiant</option>
                    <?php foreach ($students as $student): ?>
                        <option value="<?php echo $student['id']; ?>"><?php echo htmlspecialchars($student['username']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="rating" class="form-label">Note</label>
                <select class="form-select" id="rating" name="rating" required>
                    <option value="">Sélectionnez une note</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="feedback" class="form-label">Feedback</label>
                <textarea class="form-control" id="feedback" name="feedback" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter Feedback</button>
        </form>
        <a href="trainers.php" class="btn btn-secondary mt-3">Retour</a>
    </div>
</body>
</html>
