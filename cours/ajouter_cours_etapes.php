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
