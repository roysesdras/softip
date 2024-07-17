<?php
session_start();
include('../admin/config.php');

if (!isset($_GET['id'])) {
    header('Location: ../admin/dashboard.php');
    exit;
}

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $description = htmlspecialchars($_POST['description']);

    $stmt = $pdo->prepare("UPDATE annonces SET description = ? WHERE id = ?");
    $stmt->execute([$description, $id]);

    $_SESSION['message'] = 'Annonce modifiée avec succès';
    header('Location: ../admin/dashboard.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM annonces WHERE id = ?");
$stmt->execute([$id]);
$annonce = $stmt->fetch();

if (!$annonce) {
    header('Location: ../admin/dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Annonce</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Modifier Annonce</h2>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($annonce['description']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-warning">mise à jour</button>
            <a href="../admin/dashboard.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</body>
</html>
