<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Récupérer les formations
$stmt_formations = $pdo->query("SELECT * FROM formations");
$formations = $stmt_formations->fetchAll();

// Récupérer les ressources
$stmt_resources = $pdo->query("SELECT resources.*, formations.titre AS formation_titre FROM resources INNER JOIN formations ON resources.formation_id = formations.id");
$resources = $stmt_resources->fetchAll();

// Récupérer les sessions
$stmt_sessions = $pdo->query("SELECT sessions.*, formations.titre AS formation_titre, trainers.username AS trainer_username FROM sessions INNER JOIN formations ON sessions.formation_id = formations.id INNER JOIN trainers ON sessions.trainer_id = trainers.id");
$sessions = $stmt_sessions->fetchAll();

// Récupérer les notes
$stmt_grades = $pdo->query("SELECT grades.*, students.username AS student_username, sessions.date AS session_date FROM grades INNER JOIN students ON grades.student_id = students.id INNER JOIN sessions ON grades.session_id = sessions.id");
$grades = $stmt_grades->fetchAll();

// Récupérer les annonces
$stmt_annonces = $pdo->query("SELECT * FROM annonces");
$annonces = $stmt_annonces->fetchAll();

// Récupérer les étudiants
$stmt_students = $pdo->query("SELECT * FROM students");
$students = $stmt_students->fetchAll();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.9.0/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chart.js/4.3.0/chart.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            color: #343a40;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #28a745; /* Couleur verte */
            color: #fff;
            border-bottom: 2px solid #1e7e34;
        }
        .card-body {
            background-color: #fff;
            padding: 1.5rem;
        }
        .card-title {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }
        .card-text {
            font-size: 0.875rem;
            color: #6c757d;
        }
        .btn {
            font-size: 0.875rem;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .btn-primary {
            background-color: #28a745; /* Couleur verte */
            border-color: #28a745;
        }
        .btn-primary:hover {
            background-color: #1e7e34;
            border-color: #1e7e34;
        }
        .btn-success {
            background-color: #ffc107; /* Couleur jaune */
            border-color: #ffc107;
        }
        .btn-success:hover {
            background-color: #e0a800;
            border-color: #d39e00;
        }
        .btn-warning {
            background-color: #17a2b8; /* Couleur cyan */
            border-color: #17a2b8;
        }
        .btn-warning:hover {
            background-color: #138496;
            border-color: #117a8b;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
        .chart-container {
            position: relative;
            height: 300px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Tableau de Bord Admin</h2>

        <div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Formations</h3>
            </div>
            <div class="card-body">
                <a href="../formations/add_formation.php" class="btn btn-primary mb-3">Ajouter Formation</a>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Titre</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($formations as $formation): ?>
                                <tr>
                                    <td><img src="<?php echo $formation['image_url']; ?>" alt="<?php echo $formation['titre']; ?>" style="max-width: 100px;"></td>
                                    <td><?php echo htmlspecialchars($formation['titre']); ?></td>
                                    <td>
                                        <?php 
                                            $fullDescription = $formation['description'];
                                            $shortDescription = substr(strip_tags($fullDescription), 0, 500);
                                        ?>
                                        <span class="short-description"><?php echo $shortDescription; ?></span>
                                        <?php if (strlen(strip_tags($fullDescription)) > 500): ?>
                                            <span class="ellipsis">...</span>
                                            <span class="full-description" style="display: none;"><?php echo substr($fullDescription, 500); ?></span>
                                            <a href="#" class="toggle-description">Voir plus</a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="../formations/edit_formation.php?id=<?php echo $formation['id']; ?>" class="btn btn-warning me-2 mb-2"><i class="bi bi-pencil-square"></i></a>
                                        <a href="../formations/delete_formation.php?id=<?php echo $formation['id']; ?>" class="btn btn-danger"><i class="bi bi-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var toggleLinks = document.querySelectorAll('.toggle-description');

    toggleLinks.forEach(function(link) {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            var fullDescription = this.previousElementSibling;
            var ellipsis = fullDescription.previousElementSibling;
            var shortDescription = ellipsis.previousElementSibling;

            if (fullDescription.style.display === 'none') {
                fullDescription.style.display = 'inline';
                ellipsis.style.display = 'none';
                this.textContent = 'Voir moins';
            } else {
                fullDescription.style.display = 'none';
                ellipsis.style.display = 'inline';
                this.textContent = 'Voir plus';
            }
        });
    });
});
</script>


        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Ressources</h3>
                    </div>
                    <div class="card-body">
                        <a href="../resources/resources.php" class="btn btn-primary mb-3">Gérer Ressources</a>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Formation</th>
                                        <th>Titre</th>
                                        <th>Description</th>
                                        <th>Lien</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($resources as $resource): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($resource['formation_titre']); ?></td>
                                            <td><?php echo htmlspecialchars($resource['titre']); ?></td>
                                            <td><?php echo htmlspecialchars($resource['description']); ?></td>
                                            <td><a href="<?php echo htmlspecialchars($resource['lien']); ?>" target="_blank">Accéder à la Ressource</a></td>
                                            <td>
                                                <a href="../resources/edit_resources.php?id=<?php echo $resource['id']; ?>" class="btn btn-warning me-2 mb-2"><i class="bi bi-pencil-square"></i></a>
                                                <a href="../resources/delete_resources.php?id=<?php echo $resource['id']; ?>" class="btn btn-danger"><i class="bi bi-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Sessions</h3>
                    </div>
                    <div class="card-body">
                        <a href="../trainers/add_session.php" class="btn btn-primary mb-3">Ajouter Session</a>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Formation</th>
                                        <th>Formateur</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($sessions as $session): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($session['date']); ?></td>
                                            <td><?php echo htmlspecialchars($session['formation_titre']); ?></td>
                                            <td><?php echo htmlspecialchars($session['trainer_username']); ?></td>
                                            <td>
                                                <a href="../trainers/edit_session.php?id=<?php echo $session['id']; ?>" class="btn btn-warning me-2 mb-2"><i class="bi bi-pencil-square"></i></a>
                                                <a href="../trainers/delete_session.php?id=<?php echo $session['id']; ?>" class="btn btn-danger"><i class="bi bi-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Annonces</h3>
                    </div>
                    <div class="card-body">
                        <a href="../annonces/add_annonce.php" class="btn btn-primary mb-3">Ajouter Annonce</a>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <!-- <th>Titre</th> -->
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($annonces as $annonce): ?>
                                        <tr>
                                            <!-- <td><?php //echo htmlspecialchars($annonce['titre']); ?></td> -->
                                            <td><?php echo htmlspecialchars($annonce['description']); ?></td>
                                            <td>
                                                <a href="../annonces/edit_annonce.php?id=<?php echo $annonce['id']; ?>" class="btn btn-warning me-2 mb-2"><i class="bi bi-pencil-square"></i></a>
                                                <a href="../annonces/delete_annonce.php?id=<?php echo $annonce['id']; ?>" class="btn btn-danger"><i class="bi bi-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Étudiants</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nom d'utilisateur</th>
                                        <th>Email</th>
                                        <th>Numéro de téléphone</th>
                                        <th>Avatar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $student): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($student['id']); ?></td>
                                            <td><?php echo htmlspecialchars($student['username']); ?></td>
                                            <td><?php echo htmlspecialchars($student['email']); ?></td>
                                            <td><?php echo htmlspecialchars($student['phone_number']); ?></td>
                                            <td><img src="<?php echo htmlspecialchars($student['avatar']); ?>" alt="Avatar" style="max-width: 50px;"></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.min.js"></script>
    <script>
        // Chart.js example for formations
        const formationsChartCtx = document.getElementById('formationsChart').getContext('2d');
        const formationsChart = new Chart(formationsChartCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($formations, 'titre')); ?>,
                datasets: [{
                    label: 'Nombre de Formations',
                    data: <?php echo json_encode(array_map(function($formation) { return count($formation); }, $formations)); ?>,
                    backgroundColor: '#28a745',
                    borderColor: '#1e7e34',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Chart.js example for sessions
        const sessionsChartCtx = document.getElementById('sessionsChart').getContext('2d');
        const sessionsChart = new Chart(sessionsChartCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($sessions, 'date')); ?>,
                datasets: [{
                    label: 'Sessions',
                    data: <?php echo json_encode(array_map(function($session) { return count($session); }, $sessions)); ?>,
                    backgroundColor: 'rgba(0, 123, 255, 0.2)',
                    borderColor: '#007bff',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
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
