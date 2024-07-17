<?php
session_start();
include('../admin/config.php');

// Vérifier si l'utilisateur est authentifié en tant qu'administrateur ou formateur
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'formateur')) {
    header('Location: ../admin/login.php');
    exit;
}

// Récupérer les ressources avec les informations de formation
$stmt_resources = $pdo->query("SELECT r.id, r.titre, r.description, r.lien, f.titre AS formation_titre FROM resources r
                               LEFT JOIN formations f ON r.formation_id = f.id");
$resources = $stmt_resources->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Ressources</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.9.0/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Gestion des Ressources</h2>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php
                echo $_SESSION['success_message'];
                unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>

        <a href="add_resource.php" class="btn btn-success mb-3">Ajouter une Ressource</a>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Description</th>
                        <th>Lien</th>
                        <th>Formation Associée</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resources as $resource): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($resource['titre']); ?></td>
                            <td><?php echo htmlspecialchars($resource['description']); ?></td>
                            <td><a href="<?php echo htmlspecialchars($resource['lien']); ?>" target="_blank">Accéder à la Ressource</a></td>
                            <td><?php echo htmlspecialchars($resource['formation_titre']); ?></td>
                            <td>
                                <a href="edit_resources.php?id=<?php echo $resource['id']; ?>" class="btn btn-warning me-2 mb-2" title="Modifier"><i class="bi bi-pencil-square"></i></a>
                                <a href="delete_resources.php?id=<?php echo $resource['id']; ?>" class="btn btn-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette ressource ?');"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <a href="../admin/dashboard.php" class="btn btn-secondary mt-4">Retour au Tableau de Bord</a>
    </div>
</body>
</html>
