<?php
session_start();
include('../admin/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = htmlspecialchars($_POST['titre']);
    $description = htmlspecialchars($_POST['description']);
    $image_url = htmlspecialchars($_POST['image_url']);
    $price = htmlspecialchars($_POST['price']);
    
    // Validation des données
    $errors = [];
    if (empty($titre)) {
        $errors[] = 'Le titre est requis.';
    }
    if (empty($description)) {
        $errors[] = 'La description est requise.';
    }
    if (empty($image_url) || !filter_var($image_url, FILTER_VALIDATE_URL)) {
        $errors[] = 'L\'URL de l\'image est invalide.';
    }
    if (empty($price) || !is_numeric($price)) {
        $errors[] = 'Le prix doit être un nombre valide.';
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO formations (titre, description, image_url, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$titre, $description, $image_url, $price]);
            $_SESSION['message'] = 'Formation ajoutée avec succès';
        } catch (PDOException $e) {
            $_SESSION['message'] = 'Erreur lors de l\'ajout de la formation : ' . $e->getMessage();
        }
        header('Location: ../admin/dashboard.php');
        exit;
    } else {
        $_SESSION['message'] = 'Erreurs : ' . implode(', ', $errors);
        header('Location: ../admin/dashboard.php');
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter Formation</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Ajouter Formation</h2>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="titre" class="form-label">Titre</label>
                <input type="text" class="form-control" id="titre" name="titre" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="image_url" class="form-label">URL de l'image</label>
                <input type="url" class="form-control" id="image_url" name="image_url" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Prix (FCFA)</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" required>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </form>
    </div>
</body>
</html>
