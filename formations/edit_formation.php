<?php
session_start();
include('../admin/config.php');

// Vérifier l'ID de formation
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header('Location: ../admin/dashboard.php');
    exit;
}

$id = (int)$_GET['id'];

// Générer un jeton CSRF
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Vérifier le jeton CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['message'] = 'Token CSRF invalide.';
        header('Location: ../admin/dashboard.php');
        exit;
    }

    $titre = htmlspecialchars(trim($_POST['titre']));
    $description = $_POST['description']; // Ne pas échapper ici, Summernote gère le HTML
    $image_url = htmlspecialchars(trim($_POST['image_url']));
    $price = htmlspecialchars(trim($_POST['price']));

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
            $stmt = $pdo->prepare("UPDATE formations SET titre = ?, description = ?, image_url = ?, price = ? WHERE id = ?");
            $stmt->execute([$titre, $description, $image_url, $price, $id]);
            $_SESSION['message'] = 'Formation modifiée avec succès';
        } catch (PDOException $e) {
            $_SESSION['message'] = 'Erreur lors de la modification de la formation : ' . $e->getMessage();
        }
        header('Location: ../admin/dashboard.php');
        exit;
    } else {
        $_SESSION['message'] = 'Erreurs : ' . implode(', ', $errors);
        header('Location: ../admin/dashboard.php');
        exit;
    }
}

$stmt = $pdo->prepare("SELECT * FROM formations WHERE id = ?");
$stmt->execute([$id]);
$formation = $stmt->fetch();

if (!$formation) {
    header('Location: ../admin/dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Formation</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <!-- Favicons -->
    <link href="../assets/img/favicon.png" rel="icon">
    <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Modifier Formation</h2>
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <div class="mb-3">
                <label for="titre" class="form-label">Titre</label>
                <input type="text" class="form-control" id="titre" name="titre" value="<?php echo htmlspecialchars($formation['titre']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" required><?php echo htmlspecialchars($formation['description']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="image_url" class="form-label">URL de l'image</label>
                <input type="text" class="form-control" id="image_url" name="image_url" value="<?php echo htmlspecialchars($formation['image_url']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Prix (FCFA)</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($formation['price']); ?>" required>
            </div>
            <button type="submit" class="btn btn-warning">Mise à jour</button>
        </form>
    </div>

    <!-- Inclure jQuery et Summernote JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#description').summernote({
            placeholder: 'Écrire une description',
            tabsize: 2,
            height: 200,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontsize', ['fontsize']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']],
                ['misc', ['undo', 'redo']],
                ['alignment', ['alignleft', 'aligncenter', 'alignright', 'justify']],
                ['highlight', ['highlight']]
            ]
        });
    });
    </script>


    <?php include_once ('../inclusion/footer_2.php'); ?>
    <!-- Inclusion de Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
