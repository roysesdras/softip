<?php
session_start();
include('../admin/config.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['trainer_id'])) {
    header('Location: ../trainers/login_trainer.php');
    exit;
}

$formateur_id = $_SESSION['trainer_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier si toutes les données sont présentes
    if (empty($_POST['nom']) || empty($_POST['description']) || empty($_POST['duree']) || empty($_POST['niveau']) || empty($_POST['formation_id']) || empty($_POST['formateur_id'])) {
        echo "Tous les champs doivent être remplis.";
        exit;
    }

    // Informations du cours
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $duree = $_POST['duree'];
    $niveau = $_POST['niveau'];
    $formation_id = $_POST['formation_id'];

    // Traitement de l'image
    $image = $_FILES['image']['name'];
    $target_dir = "../avatar_img/";
    $target_file = $target_dir . basename($image);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Vérifier le type de fichier
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false) {
        echo "Le fichier n'est pas une image.";
        $uploadOk = 0;
    }

    // Vérifier la taille du fichier
    if ($_FILES["image"]["size"] > 500000) { // 500 KB
        echo "Le fichier est trop volumineux.";
        $uploadOk = 0;
    }

    // Vérifier le format de fichier
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Seules les images JPG, JPEG, PNG & GIF sont autorisées.";
        $uploadOk = 0;
    }

    // Vérifier si $uploadOk est défini sur 0 par une erreur
    if ($uploadOk == 0) {
        echo "Le fichier n'a pas été téléchargé.";
    } else {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            echo "Le fichier " . htmlspecialchars(basename($image)) . " a été téléchargé.";
        } else {
            echo "Désolé, une erreur s'est produite lors du téléchargement de votre fichier.";
        }
    }

    if ($uploadOk == 1) {
        try {
            // Insertion des données du cours dans la base de données
            $stmt = $pdo->prepare("INSERT INTO cours (nom, image, description, duree, niveau, formation_id, formateur_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $image, $description, $duree, $niveau, $formation_id, $formateur_id]);

            // Récupérer l'ID du dernier cours inséré
            $course_id = $pdo->lastInsertId();

            // Insertion des étapes du cours
            if (!empty($_POST['step_number']) && !empty($_POST['content'])) {
                $step_numbers = $_POST['step_number'];
                $contents = $_POST['content'];

                $stmt_steps = $pdo->prepare("INSERT INTO course_steps (course_id, step_number, content) VALUES (?, ?, ?)");
                foreach ($step_numbers as $index => $step_number) {
                    $content = $contents[$index];
                    $stmt_steps->execute([$course_id, $step_number, $content]);
                }
            }

            echo "Le cours et les étapes ont été ajoutés avec succès.";
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Ajouter un cours</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.9.0/font/bootstrap-icons.min.css">
    <link href="../assets/img/favicon.png" rel="icon">
    <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            color: #495057;
        }

        .container {
            margin-top: 20px;
        }

        .form-control, .form-select {
            border-radius: 0.375rem;
        }

        .form-control:focus, .form-select:focus {
            box-shadow: none;
            border-color: #0069d9;
        }

        .btn-custom {
            background-color: #0069d9;
            color: #ffffff;
            border: none;
            border-radius: 0.375rem;
            padding: 10px 20px;
        }

        .btn-custom:hover {
            background-color: #0056b3;
        }

        .shadow-box {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            background-color: #ffffff;
            border-radius: 0.375rem;
        }

        .step-container {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #f8f9fa;
        }

        .step-container label {
            font-weight: bold;
        }

        .back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #0069d9;
            color: #ffffff;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .back-to-top.active {
            opacity: 1;
        }

        .back-to-top i {
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Ajouter un cours</h1>
        <form action="" method="post" enctype="multipart/form-data" class="shadow-box mb-4">
            <!-- Informations du cours -->
            <div class="mb-3">
                <label for="nom" class="form-label">Titre du cours :</label>
                <input type="text" id="nom" name="nom" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Image :</label>
                <input class="form-control" type="file" id="image" name="image" accept="image/*" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description :</label>
                <textarea id="description" class="form-control" name="description" required></textarea>
            </div>

            <div class="mb-3">
                <label for="duree" class="form-label">Durée (en heures) :</label>
                <input type="number" id="duree" name="duree" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="niveau" class="form-label">Niveau :</label>
                <select id="niveau" name="niveau" class="form-select" required>
                    <option value="Débutant">Débutant</option>
                    <option value="Intermédiaire">Intermédiaire</option>
                    <option value="Avancé">Avancé</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="formation_id" class="form-label">Formation :</label>
                <select id="formation_id" name="formation_id" class="form-select" required>
                    <?php
                        include('../admin/config.php');
                        $stmt = $pdo->query("SELECT id, titre FROM formations");
                        while ($row = $stmt->fetch()) {
                            echo "<option value=\"" . htmlspecialchars($row['id']) . "\">" . htmlspecialchars($row['titre']) . "</option>";
                        }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="formateur_id" class="form-label">Formateur :</label>
                <select id="formateur_id" name="formateur_id" class="form-select" required>
                    <?php
                        $stmt = $pdo->query("SELECT id, username FROM trainers");
                        while ($row = $stmt->fetch()) {
                            echo "<option value=\"" . htmlspecialchars($row['id']) . "\">" . htmlspecialchars($row['username']) . "</option>";
                        }
                    ?>
                </select>
            </div>

            <h2>Ajouter les étapes du cours</h2>
            <div id="steps-container">
                <div class="step-container">
                    <div class="mb-3">
                        <label for="step_number_1" class="form-label">Numéro de l'étape :</label>
                        <input type="number" id="step_number_1" name="step_number[]" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="content_1" class="form-label">Contenu de l'étape :</label>
                        <textarea id="content_1" name="content[]" class="form-control" required></textarea>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-warning mb-3" onclick="addStep()">Ajouter une autre étape</button>
            <button type="submit" class="btn btn-success mb-3">Enrégistré</button>
        </form>
        <div class="quitter mb-4">
            <a href="../trainers/trainers.php" style="font-size: 20px;">Quitter</a>
        </div>
    </div>

   
    <div class="back-to-top">
        <i class="bi bi-arrow-up-short"></i>
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

        let stepCount = 1;

        function addStep() {
            stepCount++;
            const container = document.getElementById('steps-container');
            const newStep = document.createElement('div');
            newStep.classList.add('step-container');
            newStep.innerHTML = `
                <div class="mb-3">
                    <label for="step_number_${stepCount}" class="form-label">Numéro de l'étape :</label>
                    <input type="number" id="step_number_${stepCount}" name="step_number[]" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="content_${stepCount}" class="form-label">Contenu de l'étape :</label>
                    <textarea id="content_${stepCount}" name="content[]" class="form-control" required></textarea>
                </div>
            `;
            container.appendChild(newStep);

            // Initialize Summernote for the newly added textarea
            $(`#content_${stepCount}`).summernote({
                placeholder: 'Écrire le contenu de l\'étape',
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
        }

        // Back to top button functionality
        let backToTopButton = document.querySelector('.back-to-top');

        window.addEventListener('scroll', () => {
            if (window.scrollY > 100) {
                backToTopButton.classList.add('active');
            } else {
                backToTopButton.classList.remove('active');
            }
        });

        backToTopButton.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({top: 0, behavior: 'smooth'});
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
