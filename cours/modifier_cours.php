<?php
session_start();
include('../admin/config.php');

if (!isset($_SESSION['trainer_id'])) {
    header('Location: ../trainers/login_trainer.php');
    exit;
}

$trainer_id = $_SESSION['trainer_id'];
$course_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$course_id) {
    die("Identifiant du cours manquant.");
}

// Vérifier si l'objet PDO est bien créé
if (!$pdo) {
    die("Erreur de connexion à la base de données");
}

try {
    // Récupérer les détails du cours
    $stmt = $pdo->prepare("SELECT * FROM cours WHERE id = ? AND formateur_id = ?");
    $stmt->execute([$course_id, $trainer_id]);
    $course = $stmt->fetch();

    if (!$course) {
        die("Cours non trouvé ou vous n'avez pas la permission de le modifier.");
    }

    // Traitement du formulaire de modification
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nom = $_POST['nom'];
        $description = $_POST['description'];
        $duree = $_POST['duree'];
        $niveau = $_POST['niveau'];
        $formation_id = $_POST['formation_id'];

        // Traitement de l'image si modifiée
        $image = $course['image'];
        if (!empty($_FILES['image']['name'])) {
            $image = $_FILES['image']['name'];
            $target_dir = "../avatar_img/";
            $target_file = $target_dir . basename($image);

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                echo "Le fichier " . htmlspecialchars(basename($image)) . " a été téléchargé.";
            } else {
                echo "Désolé, une erreur s'est produite lors du téléchargement de votre fichier.";
            }
        }

        // Mise à jour du cours dans la base de données
        $stmt_update = $pdo->prepare("UPDATE cours SET nom = ?, image = ?, description = ?, duree = ?, niveau = ?, formation_id = ? WHERE id = ?");
        $stmt_update->execute([$nom, $image, $description, $duree, $niveau, $formation_id, $course_id]);

        // Gestion des étapes
        if (isset($_POST['step_number'])) {
            $step_numbers = $_POST['step_number'];
            $contents = $_POST['content'];
            
            // Supprimer les étapes existantes
            $stmt_delete_steps = $pdo->prepare("DELETE FROM course_steps WHERE course_id = ?");
            $stmt_delete_steps->execute([$course_id]);

            // Ajouter les nouvelles étapes
            $stmt_steps = $pdo->prepare("INSERT INTO course_steps (course_id, step_number, content) VALUES (?, ?, ?)");
            foreach ($step_numbers as $index => $step_number) {
                $content = $contents[$index];
                $stmt_steps->execute([$course_id, $step_number, $content]);
            }
        }

        echo "Le cours a été modifié avec succès.";
    }

    // Récupérer les formations disponibles
    $stmt_formations = $pdo->prepare("SELECT id, titre FROM formations");
    $stmt_formations->execute();
    $formations = $stmt_formations->fetchAll();

    // Récupérer les étapes du cours
    $stmt_steps = $pdo->prepare("SELECT step_number, content FROM course_steps WHERE course_id = ?");
    $stmt_steps->execute([$course_id]);
    $steps = $stmt_steps->fetchAll();

} catch (PDOException $e) {
    // Gérer les erreurs PDO
    echo "Erreur PDO : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le Cours</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.9.0/font/bootstrap-icons.min.css">
    <link href="../assets/img/favicon.png" rel="icon">
    <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            color: #333;
        }
        .container {
            margin-top: 30px;
        }
        .course-form {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .course-form h1 {
            font-size: 1.75rem;
            margin-bottom: 20px;
        }
        .course-form label {
            font-weight: bold;
        }
        .course-form textarea {
            resize: vertical;
        }
        .step {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #f9f9f9;
        }
        .btn-add-step {
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="course-form mb-4">
            <h1>Modifier le Cours</h1>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="nom" class="form-label">Titre du cours :</label>
                    <input type="text" id="nom" name="nom" class="form-control" value="<?php echo htmlspecialchars($course['nom']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Image :</label>
                    <input type="file" id="image" name="image" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description :</label>
                    <textarea id="description" name="description" class="form-control" required><?php echo htmlspecialchars($course['description']); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="duree" class="form-label">Durée (en heures) :</label>
                    <input type="number" id="duree" name="duree" class="form-control" value="<?php echo htmlspecialchars($course['duree']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="niveau" class="form-label">Niveau :</label>
                    <select id="niveau" name="niveau" class="form-select" required>
                        <option value="Débutant" <?php echo $course['niveau'] == 'Débutant' ? 'selected' : ''; ?>>Débutant</option>
                        <option value="Intermédiaire" <?php echo $course['niveau'] == 'Intermédiaire' ? 'selected' : ''; ?>>Intermédiaire</option>
                        <option value="Avancé" <?php echo $course['niveau'] == 'Avancé' ? 'selected' : ''; ?>>Avancé</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="formation_id" class="form-label">Formation :</label>
                    <select id="formation_id" name="formation_id" class="form-select" required>
                        <?php foreach ($formations as $formation): ?>
                            <option value="<?php echo $formation['id']; ?>" <?php echo $course['formation_id'] == $formation['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($formation['titre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <h2>Étapes du cours</h2>
                <div id="steps-container">
                    <?php foreach ($steps as $step): ?>
                        <div class="step">
                            <div class="mb-3">
                                <label>Numéro de l'étape :</label>
                                <input type="number" name="step_number[]" class="form-control" value="<?php echo htmlspecialchars($step['step_number']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label>Contenu de l'étape :</label>
                                <textarea name="content[]" class="form-control" required><?php echo htmlspecialchars($step['content']); ?></textarea>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
                <script>
                $(document).ready(function() {
                    $('textarea').each(function() {
                        $(this).summernote({
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
                });
                </script>

                <button type="button" id="add-step" class="btn btn-outline-secondary btn-add-step">Ajouter une étape</button><br><br>
                
                <input type="submit" value="Enrégistré les modifications" class="btn btn-warning">
            </form>
        </div>
            <div class="quitter mb-4">
                <a href="../trainers/trainers.php" style="font-size: 20px;">Quitter les modifications</a>
            </div>
    </div>

    <script>
        document.getElementById('add-step').addEventListener('click', function() {
            var container = document.getElementById('steps-container');
            var index = container.children.length + 1;
            var stepDiv = document.createElement('div');
            stepDiv.className = 'step';
            stepDiv.innerHTML = `
                <div class="mb-3">
                    <label>Numéro de l'étape :</label>
                    <input type="number" name="step_number[]" value="${index}" class="form-control" required>
                </div>
                
                <div class="mb-3">
                    <label>Contenu de l'étape :</label>
                    <textarea name="content[]" class="form-control" required></textarea>
                </div>
            `;
            container.appendChild(stepDiv);
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
