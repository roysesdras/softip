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
    <title>Modifier le Cours</title>
    <style>
        .step {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Modifier le Cours</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <label for="nom">Nom du cours :</label>
        <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($course['nom']); ?>" required><br><br>

        <label for="image">Image :</label>
        <input type="file" id="image" name="image"><br><br>

        <label for="description">Description :</label>
        <textarea id="description" name="description" required><?php echo htmlspecialchars($course['description']); ?></textarea><br><br>

        <label for="duree">Durée (en heures) :</label>
        <input type="number" id="duree" name="duree" value="<?php echo htmlspecialchars($course['duree']); ?>" required><br><br>

        <label for="niveau">Niveau :</label>
        <select id="niveau" name="niveau" required>
            <option value="Débutant" <?php echo $course['niveau'] == 'Débutant' ? 'selected' : ''; ?>>Débutant</option>
            <option value="Intermédiaire" <?php echo $course['niveau'] == 'Intermédiaire' ? 'selected' : ''; ?>>Intermédiaire</option>
            <option value="Avancé" <?php echo $course['niveau'] == 'Avancé' ? 'selected' : ''; ?>>Avancé</option>
        </select><br><br>

        <label for="formation_id">Formation :</label>
        <select id="formation_id" name="formation_id" required>
            <?php foreach ($formations as $formation): ?>
                <option value="<?php echo $formation['id']; ?>" <?php echo $course['formation_id'] == $formation['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($formation['titre']); ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <h2>Étapes du cours</h2>
        <div id="steps-container">
            <?php foreach ($steps as $step): ?>
                <div class="step">
                    <label>Numéro de l'étape :</label>
                    <input type="number" name="step_number[]" value="<?php echo htmlspecialchars($step['step_number']); ?>" required><br>
                    
                    <label>Contenu de l'étape :</label>
                    <textarea name="content[]" required><?php echo htmlspecialchars($step['content']); ?></textarea><br><br>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="button" id="add-step">Ajouter une étape</button><br><br>
        
        <input type="submit" value="Modifier le cours">
    </form>

    <!-- Script pour ajouter des étapes dynamiquement -->
    <script>
        document.getElementById('add-step').addEventListener('click', function() {
            var container = document.getElementById('steps-container');
            var index = container.children.length + 1;
            var stepDiv = document.createElement('div');
            stepDiv.className = 'step';
            stepDiv.innerHTML = `
                <label>Numéro de l'étape :</label>
                <input type="number" name="step_number[]" value="${index}" required><br>
                
                <label>Contenu de l'étape :</label>
                <textarea name="content[]" required></textarea><br><br>
            `;
            container.appendChild(stepDiv);
        });
    </script>
</body>
</html>
