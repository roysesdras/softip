<?php
session_start();
include('../admin/config.php');

// Vérifier si l'utilisateur est un formateur ou un administrateur
if (!isset($_SESSION['trainer_id']) && !isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Récupérer l'identifiant de la notification depuis l'URL
$id = $_GET['id'] ?? null;

if ($id) {
    // Récupérer les détails de la notification
    try {
        $stmt = $pdo->prepare("
            SELECT notifications.*, students.username AS student_username 
            FROM notifications
            INNER JOIN students ON notifications.student_id = students.id
            WHERE notifications.id = ?
        ");
        $stmt->execute([$id]);
        $notification = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo 'Erreur : ' . $e->getMessage();
        $notification = [];
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $student_id = $_POST['student_id'];
        $message = $_POST['message'];

        try {
            $stmt = $pdo->prepare("
                UPDATE notifications 
                SET student_id = ?, message = ?, updated_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$student_id, $message, $id]);

            // Rediriger vers le tableau de bord avec un message de succès
            header('Location: insert_notification.php?notification_updated=true');
            exit;
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
        }
    }

    // Récupérer la liste des étudiants
    try {
        $stmt = $pdo->prepare("SELECT id, username FROM students");
        $stmt->execute();
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo 'Erreur : ' . $e->getMessage();
        $students = [];
    }
} else {
    header('Location: insert_notification.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Éditer une Notification</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Éditer une Notification</h2>

        <form action="" method="POST">
            <div class="mb-3">
                <label for="student_id" class="form-label">Étudiant:</label>
                <select id="student_id" name="student_id" class="form-select" required>
                    <?php foreach ($students as $student): ?>
                        <option value="<?php echo htmlspecialchars($student['id']); ?>" <?php echo $student['id'] == $notification['student_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($student['username']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Message:</label>
                <textarea id="message" name="message" class="form-control" rows="3" required><?php echo htmlspecialchars($notification['message']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Mettre à Jour la Notification</button>
        </form>

        <a href="insert_notification.php" class="btn btn-secondary mt-4">Retour à la Liste des Notifications</a>
    </div>
</body>
</html>
