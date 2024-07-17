<?php
session_start();
include('../admin/config.php');

if (!isset($_SESSION['trainer_id'])) {
    header('Location: login_trainer.php');
    exit;
}

$trainer_id = $_SESSION['trainer_id'];

// Récupérer les formations disponibles
$stmt_formations = $pdo->prepare("SELECT id, titre FROM formations");
$stmt_formations->execute();
$formations = $stmt_formations->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $formation_id = $_POST['formation_id'];

    try {
        // Préparer et exécuter l'insertion du quiz
        $stmt = $pdo->prepare("INSERT INTO quizzes (titre, description, formation_id, trainer_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$titre, $description, $formation_id, $trainer_id]);

        // Récupérer l'id du quiz nouvellement créé
        $quiz_id = $pdo->lastInsertId();

        // Rediriger avec le quiz_id pour pouvoir l'utiliser dans un autre script
        header('Location: create_quiz.php?quiz_created=true&quiz_id=' . $quiz_id);
        exit;
    } catch (PDOException $e) {
        echo 'Erreur : ' . $e->getMessage();
    }
}

// // Ajout d'un nouveau quiz
// $query = "INSERT INTO quizzes (title, description) VALUES (:title, :description)";
// $stmt = $pdo->prepare($query);
// $stmt->bindParam(':title', $title, PDO::PARAM_STR);
// $stmt->bindParam(':description', $description, PDO::PARAM_STR);
// $stmt->execute();

// // Récupérer les ID des étudiants
// $query = "SELECT id FROM students";
// $stmt = $pdo->prepare($query);
// $stmt->execute();
// $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// // Ajouter une notification pour chaque étudiant
// foreach ($students as $student) {
//     $message = "Un nouveau quiz est disponible : " . $title;
//     $query = "INSERT INTO notifications (student_id, message) VALUES (:student_id, :message)";
//     $stmt = $pdo->prepare($query);
//     $stmt->bindParam(':student_id', $student['id'], PDO::PARAM_INT);
//     $stmt->bindParam(':message', $message, PDO::PARAM_STR);
//     $stmt->execute();
// }


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un Quiz</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.9.0/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Créer un Quiz</h2>

        <!-- Afficher le message de succès si présent -->
        <?php if (isset($_GET['quiz_created']) && $_GET['quiz_created'] == 'true'): ?>
            <div class="alert alert-success" role="alert">Le quiz a été créé avec succès !</div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label for="titre" class="form-label">Titre:</label>
                <input type="text" id="titre" name="titre" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <textarea id="description" name="description" class="form-control" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="formation_id" class="form-label">Formation:</label>
                <select id="formation_id" name="formation_id" class="form-select" required>
                    <?php foreach ($formations as $formation): ?>
                        <option value="<?php echo $formation['id']; ?>">
                            <?php echo htmlspecialchars($formation['titre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Créer le Quiz</button>
        </form>

        <a href="../trainers/trainers.php" class="btn btn-secondary mt-4">Retour au Tableau de Bord</a>
    </div>
</body>
</html>
