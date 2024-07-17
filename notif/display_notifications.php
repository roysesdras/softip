<?php
session_start();
include('../admin/config.php');

if (!isset($_SESSION['student_id'])) {
    header('Location: login_student.php');
    exit;
}

$student_id = $_SESSION['student_id'];

$stmt = $pdo->prepare("
    SELECT * FROM notifications WHERE student_id = ? ORDER BY created_at DESC
");
$stmt->execute([$student_id]);
$notifications = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Notifications</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Vos Notifications</h2>

        <div class="list-group">
            <?php foreach ($notifications as $notification): ?>
                <div class="list-group-item <?php echo $notification['is_read'] ? '' : 'list-group-item-warning'; ?>">
                    <p class="mb-1"><?php echo htmlspecialchars($notification['message']); ?></p>
                    <small class="text-muted"><?php echo htmlspecialchars($notification['created_at']); ?></small>
                </div>
            <?php endforeach; ?>
        </div>

        <a href="student_dashboard.php" class="btn btn-secondary mt-4">Retour au Tableau de Bord</a>
    </div>
</body>
</html>
