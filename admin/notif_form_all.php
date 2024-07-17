<?php
session_start();
include('../admin/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $titre = $_POST['titre'];
    $message = $_POST['message'];

    // Exemple d'insertion de la notification dans la base de données
    $query = "INSERT INTO notif_all (student_id, message, created_at) VALUES (:student_id, :message, NOW())";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':student_id', $_SESSION['student_id'], PDO::PARAM_INT);
    $stmt->bindParam(':message', $message, PDO::PARAM_STR);

    if ($stmt->execute()) {
        $_SESSION['notification_message'] = "Notification ajoutée avec succès.";
    } else {
        $_SESSION['notification_message'] = "Erreur lors de l'ajout de la notification.";
    }
    
    header('Location: notif_form_all.php');
    exit;
}

// Affichage du message de succès ou d'échec
if (isset($_SESSION['notification_message'])) {
    echo $_SESSION['notification_message'];
    unset($_SESSION['notification_message']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une Notification</title>
</head>
<body>
    <form method="POST" action="">
        <label for="titre">Titre :</label>
        <input type="text" id="titre" name="titre" required><br>

        <label for="message">Message :</label>
        <textarea id="message" name="message" required></textarea><br>

        <button type="submit">Ajouter</button>
    </form>
</body>
</html>
