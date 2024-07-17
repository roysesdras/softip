<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

if (isset($_GET['id'])) {
    $notification_id = $_GET['id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $message = $_POST['message'];

        // Mettre à jour la notification
        $stmt = $pdo->prepare("UPDATE notifications SET message = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$message, $notification_id]);

        header('Location: dashboard.php#notif');
        exit;
    }

    // Récupérer la notification à modifier
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE id = ?");
    $stmt->execute([$notification_id]);
    $notification = $stmt->fetch();

    if (!$notification) {
        echo "Notification introuvable.";
        exit;
    }
} else {
    echo "ID de notification manquant.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Notification</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-2">
        <h3>Modifier la Notification</h3>
        <form action="" method="POST">
            <div class="mb-3">
                <label for="message" class="form-label">Message</label>
                <textarea class="form-control" id="message" name="message" rows="3" required><?php echo htmlspecialchars($notification['message']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Mettre à Jour</button>
        </form>
    </div>
</body>
</html>
