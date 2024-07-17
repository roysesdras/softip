<?php
session_start();
include('../admin/config.php');

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['student_id'])) {
    header('Location: ../students/login_student_cool.php');
    exit;
}

$topic_id = $_GET['id'];

// Récupérer le sujet de discussion
$query = "SELECT * FROM topics WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':id', $topic_id, PDO::PARAM_INT);
$stmt->execute();
$topic = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupérer les discussions pour ce sujet
$query = "SELECT discussions.*, students.username, students.avatar 
          FROM discussions 
          JOIN students ON discussions.student_id = students.id 
          WHERE topic_id = :topic_id ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
$stmt->execute();
$discussions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $topic['title']; ?></title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.css" rel="stylesheet">

    <style>
        .avatar {
            width: 50px; /* Taille de l'avatar */
            height: 50px; /* Taille de l'avatar */
            border-radius: 50%; /* Pour arrondir l'avatar */
            object-fit: cover;
        }
    </style>
</head>
<body>
    <?php include('../navbar.php'); ?>

    <div class="container">
        <!-- <h2><?php //echo $topic['title']; ?></h2>
        <p><?php //echo $topic['description']; ?></p>
        <a href="create_discussion.php?topic_id=<?php //echo $topic_id; ?>" class="btn btn-primary">Ajouter une discussion</a> -->
        <ul class="list-group mt-3">
            <?php foreach ($discussions as $discussion): ?>
                <li class="list-group-item">
                    <div class="d-flex align-items-center">
                        <?php if (!empty($discussion['avatar'])): ?>
                            <img src="<?php echo $discussion['avatar']; ?>" alt="Avatar" class="avatar me-3">
                        <?php else: ?>
                            <img src="../avatar_img/R.jpg" alt="Avatar par défaut" class="avatar me-3">
                        <?php endif; ?>
                        <div>
                            <h5 class="mb-0"><?php echo $discussion['username']; ?></h5>
                            <small><?php echo $discussion['created_at']; ?></small>
                            <p><?php echo $discussion['message']; ?></p>
                            <!-- <a href="discussion.php?id=<?php //echo $discussion['id']; ?>">répondre</a> -->
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>

        <form method="post" action="submit_discussion.php">
            <input type="hidden" name="topic_id" value="<?php echo $topic_id; ?>">
            <div>
                <label for="message">Message:</label>
                <textarea id="summernote" name="message" required></textarea>
            </div>
            <button type="submit">Ajouter</button>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            $('#summernote').summernote({
                height: 200,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        });
    </script>





    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
