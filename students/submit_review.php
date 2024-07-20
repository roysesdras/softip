<?php
session_start();
include('../admin/config.php');

// Assurez-vous que les données sont envoyées par méthode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_id = $_POST['course_id'];
    $student_id = $_POST['student_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    try {
        // Vérifiez si le cours et l'étudiant existent
        $courseCheck = $pdo->prepare("SELECT id FROM cours WHERE id = ?");
        $courseCheck->execute([$course_id]);
        $courseExists = $courseCheck->fetch();

        $studentCheck = $pdo->prepare("SELECT id FROM students WHERE id = ?");
        $studentCheck->execute([$student_id]);
        $studentExists = $studentCheck->fetch();

        if ($courseExists && $studentExists) {
            // Insérer l'avis
            $stmt = $pdo->prepare("INSERT INTO reviews (course_id, student_id, rating, comment) VALUES (?, ?, ?, ?)");
            $stmt->execute([$course_id, $student_id, $rating, $comment]);
            echo "Avis ajouté avec succès.";
        } else {
            echo "Erreur : Cours ou étudiant n'existe pas.";
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
} else {
    echo "Le formulaire n'a pas été soumis correctement.";
}
?>
