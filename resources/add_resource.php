<?php
session_start();
include('../admin/config.php');

// Affichage des erreurs PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Récupérer les formations disponibles pour le menu déroulant
$stmt = $pdo->query("SELECT id, titre FROM formations ORDER BY titre");
$formations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traiter l'ajout de la ressource
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formation_id = intval($_POST['formation_id']);
    $titre = htmlspecialchars($_POST['titre']);
    $description = htmlspecialchars($_POST['description']);
    $lien = htmlspecialchars($_POST['lien']);

    try {
        $stmt = $pdo->prepare("INSERT INTO resources (formation_id, titre, description, lien) VALUES (?, ?, ?, ?)");
        $stmt->execute([$formation_id, $titre, $description, $lien]);

        $_SESSION['success_message'] = 'Ressource ajoutée avec succès';
        header('Location: resources.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Erreur lors de l\'ajout de la ressource : ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter Ressource</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Ajouter Ressource</h2>
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="formation_id" class="form-label">Formation</label>
                <select class="form-select" id="formation_id" name="formation_id" required>
                    <option value="">Sélectionner une formation</option>
                    <?php foreach ($formations as $formation): ?>
                        <option value="<?php echo htmlspecialchars($formation['id']); ?>">
                            <?php echo htmlspecialchars($formation['titre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="titre" class="form-label">Titre</label>
                <input type="text" class="form-control" id="titre" name="titre" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="lien" class="form-label">Lien</label>
                <input type="url" class="form-control" id="lien" name="lien" required>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </form>
    </div>
</body>
</html>
