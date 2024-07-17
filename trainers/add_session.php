<?php
session_start();
include('../admin/config.php');

// // Vérifier si l'utilisateur est un formateur
// if (!isset($_SESSION['trainer_id']) || $_SESSION['role'] != 'trainer') {
//     header('Location: login_trainer.php');
//     exit;
// }

// Récupérer les formations disponibles
$stmt_formations = $pdo->query("SELECT id, titre FROM formations");
$formations = $stmt_formations->fetchAll();

// Ajouter une session
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formation_id = $_POST['formation_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = $_POST['location'];
    $trainer_id = $_SESSION['trainer_id'];

    $stmt = $pdo->prepare("INSERT INTO sessions (formation_id, trainer_id, date, time, location) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$formation_id, $trainer_id, $date, $time, $location]);

    $_SESSION['success_message'] = 'Session ajoutée avec succès';
    header('Location: trainers.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une Session</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Ajouter une Session</h2>
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="formation_id" class="form-label">Formation</label>
                <select class="form-select" id="formation_id" name="formation_id" required>
                    <option value="">Sélectionnez une formation</option>
                    <?php foreach ($formations as $formation): ?>
                        <option value="<?php echo $formation['id']; ?>"><?php echo htmlspecialchars($formation['titre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>
            <div class="mb-3">
                <label for="time" class="form-label">Heure</label>
                <input type="time" class="form-control" id="time" name="time" required>
            </div>
            <div class="mb-3">
                <label for="location" class="form-label">Lieu</label>
                <input type="text" class="form-control" id="location" name="location" required>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter Session</button>
        </form>
        <a href="trainers.php" class="btn btn-secondary mt-3">Retour</a>
    </div>
</body>
</html>
