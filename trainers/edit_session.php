<?php
session_start();
include('../admin/config.php');

// Vérifier si l'utilisateur est un formateur
// if (!isset($_SESSION['trainer_id']) || $_SESSION['role'] != 'trainer') {
//     header('Location: login_trainer.php');
//     exit;
// }

// Récupérer les formations disponibles
$stmt_formations = $pdo->query("SELECT id, titre FROM formations");
$formations = $stmt_formations->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $session_id = $_POST['session_id'];
    $formation_id = $_POST['formation_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = $_POST['location'];

    $stmt = $pdo->prepare("UPDATE sessions SET formation_id = ?, date = ?, time = ?, location = ? WHERE id = ?");
    $stmt->execute([$formation_id, $date, $time, $location, $session_id]);

    $_SESSION['success_message'] = 'Session modifiée avec succès';
    header('Location: trainers.php');
    exit;
} else {
    $session_id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM sessions WHERE id = ?");
    $stmt->execute([$session_id]);
    $session = $stmt->fetch();

    if (!$session) {
        echo "Session non trouvée.";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier une Session</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Modifier une Session</h2>
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="hidden" name="session_id" value="<?php echo $session['id']; ?>">
            <div class="mb-3">
                <label for="formation_id" class="form-label">Formation</label>
                <select class="form-select" id="formation_id" name="formation_id" required>
                    <?php foreach ($formations as $formation): ?>
                        <option value="<?php echo $formation['id']; ?>" <?php echo ($formation['id'] == $session['formation_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($formation['titre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" class="form-control" id="date" name="date" value="<?php echo $session['date']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="time" class="form-label">Heure</label>
                <input type="time" class="form-control" id="time" name="time" value="<?php echo $session['time']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="location" class="form-label">Lieu</label>
                <input type="text" class="form-control" id="location" name="location" value="<?php echo $session['location']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Modifier Session</button>
        </form>
        <a href="trainers.php" class="btn btn-secondary mt-3">Retour</a>
    </div>
</body>
</html>
