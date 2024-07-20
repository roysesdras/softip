<?php
session_start();
include('../admin/config.php');

// Vérifiez si un étudiant, un formateur ou un administrateur est connecté
if (!isset($_SESSION['student_id']) && !isset($_SESSION['trainer_id']) && !isset($_SESSION['admin_id'])) {
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
$query = "SELECT discussions.*, 
                 students.username AS student_username, students.avatar AS student_avatar, 
                 trainers.username AS trainer_username, trainers.avatar AS trainer_avatar, 
                 administrators.username AS admin_username
          FROM discussions 
          LEFT JOIN students ON discussions.student_id = students.id 
          LEFT JOIN trainers ON discussions.trainer_id = trainers.id 
          LEFT JOIN administrators ON discussions.admin_id = administrators.id 
          WHERE topic_id = :topic_id 
          ORDER BY created_at DESC";
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
    <title>discussion : <?php echo htmlspecialchars($topic['title']); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            color: #333;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 20px;
            max-width: 800px;
        }
        .topic-title {
            margin-bottom: 15px;
            font-size: 1.5rem;
            font-weight: normal;
            color: #033e60;
        }
        .discussion-item {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
        }
        .discussion-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .discussion-header h5 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: bold;
        }
        .discussion-header small {
            color: #6c757d;
        }
        .discussion-body {
            font-size: 1rem;
            line-height: 1.5;
        }
        .btn-primary {
            background-color: #033e60;
            border: none;
            padding: 10px 15px;
            font-size: 1rem;
            border-radius: 5px;
            text-transform: uppercase;
            font-weight: bold;
            cursor: pointer;
        }
        .btn-primary:hover {
            background-color: #fff;
            color: #033e60;
            border: solid 1px #033e60;
        }
        .form-group {
            margin-top: 20px;
        }
        .form-group label {
            font-weight: bold;
        }
        .form-group textarea {
            width: 100%;
            height: 200px;
        }
        ul li{
            list-style: none;
        }
       a {
            font-size: 20px;
            margin-bottom: 1.5em;
            text-decoration: none;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="topic-title"><?php echo htmlspecialchars($topic['title']); ?></div>

        <ul class="list-group">
            <?php foreach ($discussions as $discussion): ?>
                <li class="discussion-item">
                    <div class="discussion-header">
                        <?php 
                        $username = '';
                        $avatar = '';
                        
                        if (!empty($discussion['student_username'])) {
                            $username = $discussion['student_username'];
                            $avatar = $discussion['student_avatar'];
                        } elseif (!empty($discussion['trainer_username'])) {
                            $username = $discussion['trainer_username'];
                            $avatar = $discussion['trainer_avatar'];
                        } elseif (!empty($discussion['admin_username'])) {
                            $username = $discussion['admin_username'];
                            $avatar = '';  // Les administrateurs n'ont pas d'avatar
                        }

                        if (!empty($avatar)): ?>
                            <img src="<?php echo htmlspecialchars($avatar); ?>" alt="Avatar" class="avatar">
                        <?php else: ?>
                            <img src="../avatar_img/default_avatar.png" alt="Avatar par défaut" class="avatar">
                        <?php endif; ?>
                        <div>
                            <h5><?php echo htmlspecialchars($username); ?></h5>
                            <small><?php echo htmlspecialchars($discussion['created_at']); ?></small>
                        </div>
                    </div>
                    <div class="discussion-body">
                        <?php echo ($discussion['message']); ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>

        <form method="post" action="submit_discussion.php" class="form-group">
            <input type="hidden" name="topic_id" value="<?php echo htmlspecialchars($topic_id); ?>">
            <label for="message">Ajouter un commentaire :</label>
            <textarea id="summernote" name="message" required></textarea>
            <button type="submit" class="btn btn-primary mt-2 mb-2">Ajouter</button>
        </form>
            <?php if (isset($_SESSION['student_id'])): ?>
               
                <p class="mb-4">
                    <!-- <a href="create_topic.php">Créer un sujet</a> /  -->
                    <a href="../admin/logout.php">Quitter la discussion</a>
                </p>

            <?php elseif (isset($_SESSION['trainer_id'])): ?>
              
                <p class="mb-4">
                    <!-- <a href="create_topic.php">Créer un sujet</a> /  -->
                    <a href="../admin/logout.php">Quitter la discussion</a>
                </p>

            <?php elseif (isset($_SESSION['admin_id'])): ?>
               
                <p class="mb-4">
                    <!-- <a href="create_topic.php">Créer un sujet</a> /  -->
                    <a href="../admin/logout.php">Quitter la discussion</a>
                </p>

            <?php else: ?>
                <li><a href="../students/login_student_cool.php">Connexion Étudiant</a></li>
                <li><a href="../trainers/login_trainer.php">Connexion Formateur</a></li>
                <li><a href="../admin/login_admin.php">Connexion Admin</a></li>
            <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
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

    <?php require_once ('../inclusion/footer_2.php'); ?>
</body>
</html>
