<?php
session_start();
include('../admin/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = htmlspecialchars($_POST['description']);

    $stmt = $pdo->prepare("INSERT INTO annonces (description) VALUES (?)");
    $stmt->execute([$description]);

    $_SESSION['message'] = 'Annonce ajoutée avec succès';
    header('Location: ../admin/dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter Annonce</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Ajouter Annonce</h2>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="description" class="form-label">Description de l'annonce</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </form>
    </div>
</body>
</html>
