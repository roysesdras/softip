<?php
session_start();
include('../admin/config.php');

// Fonction pour générer un token CSRF
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Vérifier le token CSRF
function verifyCSRFToken($token) {
    return hash_equals($_SESSION['csrf_token'], $token);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $_SESSION['message'] = 'Erreur de validation CSRF.';
        header('Location: ../admin/dashboard.php');
        exit;
    }

    $titre = htmlspecialchars($_POST['titre']);
    $description = $_POST['description']; // Ne pas échapper ici, Summernote gère le HTML
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
            $_SESSION['message'] = 'Erreur lors de l\'ajout de la formation. Veuillez réessayer.';
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
    <title>Ajouter - Formation</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <!-- Favicons -->
    <link href="../assets/img/favicon.png" rel="icon">
    <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        
        .btn-buy {          
            background-color: #033e60;
            color: #fff;  
            font-size: 16px;
            padding: 5px;
            border-radius: 5px; 
            border: solid 1px #033e60;
        }
        .btn-buy:hover {
            background-color: transparent;
            color: #033e60;
            border: solid 1px #033e60;
        }
    </style>

</head>
<body>
    <div class="container">
        <h2 class="mt-5">Ajouter Formation</h2>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
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

            <div class="mb-4">
                <button type="submit" class="btn-buy">Ajouter</button>
            </div>
           
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
