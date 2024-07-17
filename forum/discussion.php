<?php
session_start();
include('../admin/config.php');

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['student_id'])) {
    header('Location: ../students/login_student_cool.php');
    exit;
}

$discussion_id = $_GET['id'];

// Récupérer la discussion
$query = "SELECT discussions.*, students.username, students.avatar 
          FROM discussions 
          JOIN students ON discussions.student_id = students.id 
          WHERE discussions.id = :id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':id', $discussion_id, PDO::PARAM_INT);
$stmt->execute();
$discussion = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupérer les réponses pour cette discussion
$query = "SELECT replies.*, students.username, students.avatar 
          FROM replies 
          JOIN students ON replies.student_id = students.id 
          WHERE discussion_id = :discussion_id ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':discussion_id', $discussion_id, PDO::PARAM_INT);
$stmt->execute();
$replies = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discussion</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        .avatar-img {
            width: 40px; /* Ajustez la taille de l'avatar selon vos besoins */
            height: 40px; /* Ajustez la taille de l'avatar selon vos besoins */
            object-fit: cover;
        }
    </style>

</head>
<body>
   

    <div class="container">
        <h2>Discussion</h2>
        <div>
            <img src="<?php echo $discussion['avatar']; ?>" alt="Avatar" class="avatar avatar-img rounded-circle me-2">
            <strong><?php echo $discussion['username']; ?></strong> - <?php echo $discussion['created_at']; ?>
            <p><?php echo $discussion['message']; ?></p>
        </div>
        <h3>Réponses</h3>
        <ul>
            <?php foreach ($replies as $reply): ?>
                <li>
                    <img src="<?php echo $reply['avatar']; ?>" alt="Avatar" class="avatar avatar-img rounded-circle me-2">
                    <strong><?php echo $reply['username']; ?></strong> - <?php echo $reply['created_at']; ?>
                    <p><?php echo $reply['message']; ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
        <h3>Ajouter une réponse</h3>
        <form method="post" action="submit_reply.php">
            <input type="hidden" name="discussion_id" value="<?php echo $discussion_id; ?>">
            <textarea id="summernote" name="reply_message"></textarea>
            <button type="submit">Envoyer</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.js"></script>
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
</body>
</html>
