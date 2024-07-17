<?php
session_start();
include('../admin/config.php');

// Vérifier si l'ID de la ressource est passé dans l'URL
if (!isset($_GET['id'])) {
    header('Location: resources.php'); // Redirection vers la page de gestion des ressources
    exit;
}

$id = $_GET['id'];

// Traitement du formulaire de mise à jour de la ressource
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titre = htmlspecialchars($_POST['titre']);
    $description = htmlspecialchars($_POST['description']);
    $lien = htmlspecialchars($_POST['lien']);

    // Mettre à jour la ressource dans la base de données
    $stmt = $pdo->prepare("UPDATE resources SET titre = ?, description = ?, lien = ? WHERE id = ?");
    $stmt->execute([$titre, $description, $lien, $id]);

    // Redirection avec un message de confirmation
    $_SESSION['success_message'] = 'Ressource modifiée avec succès';
    header('Location: resources.php'); // Rediriger vers la page de gestion des ressources
    exit;
}

// Récupérer les détails de la ressource à modifier depuis la base de données
$stmt = $pdo->prepare("SELECT * FROM resources WHERE id = ?");
$stmt->execute([$id]);
$resource = $stmt->fetch();

// Vérifier si la ressource existe
if (!$resource) {
    header('Location: resources.php'); // Redirection vers la page de gestion des ressources
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Ressource</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Modifier Ressource</h2>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="titre" class="form-label">Titre</label>
                <input type="text" class="form-control" id="titre" name="titre" value="<?php echo htmlspecialchars($resource['titre']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" required><?php echo htmlspecialchars($resource['description']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="lien" class="form-label">Lien</label>
                <input type="text" class="form-control" id="lien" name="lien" value="<?php echo htmlspecialchars($resource['lien']); ?>" required>
            </div>
            <button type="submit" class="btn btn-warning">Mise à jour</button>
        </form>
    </div>
</body>
</html>
