<?php
session_start();
include('../admin/config.php');

if (!isset($_GET['id'])) {
    header('Location: ../admin/dashboard.php');
    exit;
}

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titre = htmlspecialchars($_POST['titre']);
    $description = $_POST['description']; // Ne pas échapper ici, Summernote gère le HTML
    $image_url = htmlspecialchars($_POST['image_url']);

    $stmt = $pdo->prepare("UPDATE formations SET titre = ?, description = ?, image_url = ? WHERE id = ?");
    $stmt->execute([$titre, $description, $image_url, $id]);

    $_SESSION['message'] = 'Formation modifiée avec succès';
    header('Location: ../admin/dashboard.php');
    exit;
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

    <!-- Inclure Summernote CSS et JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Modifier Formation</h2>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="titre" class="form-label">Titre</label>
                <input type="text" class="form-control" id="titre" name="titre" value="<?php echo htmlspecialchars($formation['titre']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" required><?php echo $formation['description']; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="image_url" class="form-label">URL de l'image</label>
                <input type="text" class="form-control" id="image_url" name="image_url" value="<?php echo htmlspecialchars($formation['image_url']); ?>" required>
            </div>
            <button type="submit" class="btn btn-warning">Mise à jour</button>
        </form>
    </div>

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

</body>
</html>
