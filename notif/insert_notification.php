<?php
session_start();
include('../admin/config.php');

// Vérifier si l'utilisateur est un formateur ou un administrateur
if (!isset($_SESSION['trainer_id']) && !isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $message = $_POST['message'];

    try {
        $stmt = $pdo->prepare("
            INSERT INTO notifications (student_id, message, is_read, created_at) 
            VALUES (?, ?, 0, NOW())
        ");
        $stmt->execute([$student_id, $message]);

        // Rediriger vers le tableau de bord avec un message de succès
        header('Location: insert_notification.php?notification_added=true');
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

// Récupérer les notifications
try {
    $stmt = $pdo->prepare("
        SELECT notifications.*, students.username AS student_username 
        FROM notifications
        INNER JOIN students ON notifications.student_id = students.id
        ORDER BY notifications.created_at DESC
    ");
    $stmt->execute();
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Erreur : ' . $e->getMessage();
    $notifications = [];
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Envoyer une Notification</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.9.0/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Envoyer une Notification</h2>

        <?php if (isset($_GET['notification_added']) && $_GET['notification_added'] == 'true'): ?>
            <div class="alert alert-success" role="alert">La notification a été ajoutée avec succès !</div>
        <?php endif; ?>

        <?php if (isset($_GET['notification_deleted']) && $_GET['notification_deleted'] == 'true'): ?>
            <div class="alert alert-success" role="alert">La notification a été supprimée avec succès !</div>
        <?php endif; ?>
        <?php if (isset($_GET['notification_updated']) && $_GET['notification_updated'] == 'true'): ?>
            <div class="alert alert-success" role="alert">La notification a été mise à jour avec succès !</div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label for="student_id" class="form-label">Étudiant:</label>
                <select id="student_id" name="student_id" class="form-select" required>
                    <option value="">Sélectionner un étudiant</option>
                    <?php foreach ($students as $student): ?>
                        <option value="<?php echo htmlspecialchars($student['id']); ?>">
                            <?php echo htmlspecialchars($student['username']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Message:</label>
                <textarea id="message" name="message" class="form-control" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Envoyer la Notification</button>
        </form>

        <a href="../admin/dashboard.php" class="btn btn-secondary mt-4">Retour au Tableau de Bord</a>

        <div class="card mt-4">
            <div class="card-body">
                <h3 class="card-title">Notifications</h3>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Étudiant</th>
                                <th>Message</th>
                                <th>Date de Création</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($notifications as $notification): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($notification['student_username']); ?></td>
                                    <td><?php echo htmlspecialchars($notification['message']); ?></td>
                                    <td><?php echo htmlspecialchars($notification['created_at']); ?></td>
                                    <td>
                                        <a href="edit_notification.php?id=<?php echo $notification['id']; ?>" class="btn btn-warning me-2">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="delete_notification.php?id=<?php echo $notification['id']; ?>" class="btn btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
