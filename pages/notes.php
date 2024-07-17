<?php
session_start();
include('../admin/config.php');

if (!isset($_SESSION['student_id'])) {
    header('Location: login_student.php');
    exit;
}

$student_id = $_SESSION['student_id'];
$query = "SELECT quiz_id, grade FROM grades WHERE student_id = :student_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
$stmt->execute();
$grades = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Préparer les données pour le graphique
$labels = [];
$data = [];
$badges = [];
foreach ($grades as $grade) {
    $labels[] = 'Quiz ' . $grade['quiz_id'];
    $data[] = $grade['grade'];
    if ($grade['grade'] >= 90) {
        $badges[] = 'Gold';
    } elseif ($grade['grade'] >= 75) {
        $badges[] = 'Silver';
    } else {
        $badges[] = 'Bronze';
    }
}

// Convertir les tableaux PHP en JSON pour les utiliser en JavaScript
$labels_json = json_encode($labels);
$data_json = json_encode($data);
$badges_json = json_encode($badges);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualisation des Notes et Progrès des Étudiants</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.css" integrity="sha512-AJ7Y9+L8xJ/Gw5lSfn6fYrcsZ7r1qozuHe6V3XcmjMREuAZiz1oq1Jk6P7CRgE9Eg9ecGWmT5E7b9V6YpTF7Rg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="styles.css">
    <script>
    function showReplyForm(parent_comment_id) {
        var form = document.getElementById('replyForm_' + parent_comment_id);
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
        } else {
            form.style.display = 'none';
        }
    }

    function submitComment(event) {
        event.preventDefault();
        var form = event.target;
        var formData = new FormData(form);
        
        fetch('submit_comment.php', {
            method: 'POST',
            body: formData
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  // Ajouter le commentaire à la liste sans recharger la page
                  var commentList = document.getElementById('commentList');
                  var newComment = document.createElement('li');
                  newComment.innerHTML = data.comment.created_at + ": " + data.comment.comment;
                  commentList.insertBefore(newComment, commentList.firstChild);
                  form.reset();
              } else {
                  alert('Erreur lors de l\'envoi du commentaire.');
              }
          }).catch(error => console.error('Erreur:', error));
    }
    </script>
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container">
        <h2>Visualisation des Notes et Progrès des Étudiants</h2>
        <div>
            <h3>Vos Notes</h3>
            <ul>
            <?php foreach ($grades as $grade): ?>
                <li>Quiz <?php echo $grade['quiz_id']; ?>: <?php echo $grade['grade']; ?> - Badge: 
                    <?php 
                    $badge_index = $grade['quiz_id'] - 1;
                    if (isset($badges[$badge_index])) {
                        echo $badges[$badge_index];
                    } else {
                        echo 'Aucun badge défini';
                    }
                    ?>
                </li>
            <?php endforeach; ?>

            </ul>
        </div>

        <canvas id="myChart" width="400" height="400"></canvas>

        

        <div>
            <h3>Notifications</h3>
            <ul>
                <?php
                $query = "SELECT message, created_at FROM notif_all WHERE student_id = :student_id AND is_read = 0 ORDER BY created_at DESC";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
                $stmt->execute();
                $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($notifications as $notification):
                ?>
                    <li>
                        <?php echo $notification['message']; ?> 
                        <p><?php echo $notification['created_at']; ?></p> 
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js" integrity="sha512-7nYcN3mBHz1d8Sd0w05nyFf9Q1GspCJZ9CDmlMdwR5yUT9Q8Cyq6pN+f0C3WCrhucUJofR0CyyA1leLmrHGWCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        var ctx = document.getElementById('myChart').getContext('2d');
        var labels = <?php echo $labels_json; ?>;
        var data = <?php echo $data_json; ?>;
        
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Scores',
                    data: data,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
