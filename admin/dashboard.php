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

// Récupérer les étudiants avec les formations auxquelles ils sont inscrits
$stmt_students = $pdo->query("
    SELECT students.id, students.username, students.email, students.phone_number, students.avatar, GROUP_CONCAT(formations.titre SEPARATOR ', ') AS formations
    FROM students
    LEFT JOIN inscriptions ON students.id = inscriptions.user_id
    LEFT JOIN formations ON inscriptions.formation_id = formations.id
    GROUP BY students.id
");
$students = $stmt_students->fetchAll();

// Récupérer les formateurs
$stmt_trainers = $pdo->query("SELECT * FROM trainers");
$trainers = $stmt_trainers->fetchAll();

/// Récupérer les notifications pour les étudiants
$stmt_notifications = $pdo->query("
    SELECT notifications.id, notifications.student_id, notifications.message, notifications.is_read, notifications.created_at, students.username AS student_username
    FROM notifications
    INNER JOIN students ON notifications.student_id = students.id
    ORDER BY notifications.created_at DESC
");
$notifications = $stmt_notifications->fetchAll();

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.9.0/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chart.js/4.3.0/chart.min.css">

    <!-- Favicons -->
    <link href="../assets/img/favicon.png" rel="icon">
    <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <link rel="stylesheet" href="../assets/style.css">

</head>   
    <body>
        <div class="container">
            <h3>Admin Dashboard</h3>
                <div class="row">
                    <div class="col-lg-12 mb-4 shadow p-0">
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
                <div class="col-lg-12 mb-4 shadow p-0">
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
                                            <th>Formations</th> <!-- Nouvelle colonne pour les formations -->
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
                                                
                                                <td><?php echo htmlspecialchars($student['formations']); ?></td> <!-- Affichage des formations -->

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


                <div class="row">
                    <div class="col-lg-6 mb-4 ">
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

                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Notifications</h3>
                        </div>
                        <div class="card-body" id="notif">
                            <a href="add_notification.php" class="btn btn-primary ">Ajouter Notification</a>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Message</th>
                                            <th>Est Lue</th>
                                            <th>Créé À</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($notifications as $notification): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($notification['id']); ?></td>
                                                <td><?php echo htmlspecialchars($notification['message']); ?></td>
                                                <td><?php echo $notification['is_read'] ? 'Oui' : 'Non'; ?></td>
                                                <td><?php echo htmlspecialchars($notification['created_at']); ?></td>
                                                <td>
                                                    <a href="edit_notification.php?id=<?php echo $notification['id']; ?>" class="btn btn-warning me-2 mb-2"><i class="bi bi-pencil-square"></i></a>
                                                    <a href="delete_notification.php?id=<?php echo $notification['id']; ?>" class="btn btn-danger"><i class="bi bi-trash"></i></a>
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
                <div class="col-lg-12 mb-4 shadow p-0">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Formateurs</h3>
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
                                            <th>Biographie</th>
                                            <th>Avatar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($trainers as $trainer): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($trainer['id']); ?></td>
                                                <td><?php echo htmlspecialchars($trainer['username']); ?></td>
                                                <td><?php echo htmlspecialchars($trainer['email']); ?></td>
                                                <td><?php echo htmlspecialchars($trainer['phone_number']); ?></td>
                                                <td><?php echo htmlspecialchars($trainer['bio']); ?></td>
                                                <td><img src="<?php echo htmlspecialchars($trainer['avatar']); ?>" alt="Avatar" style="max-width: 50px;"></td>
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



        <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
            <i class="bi bi-arrow-up-short"></i>
        </a>

        <script>
        // Sélectionne l'élément .back-to-top
        let backToTopButton = document.querySelector('.back-to-top');

        // Ajoute un écouteur d'événement au défilement de la fenêtre
        window.addEventListener('scroll', () => {
            if (window.scrollY > 100) {
            backToTopButton.classList.add('active');
            } else {
            backToTopButton.classList.remove('active');
            }
        });

        // Ajoute un écouteur d'événement pour cliquer sur le bouton
        backToTopButton.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({top: 0, behavior: 'smooth'});
        });
        </script>



        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src='https://code.jquery.com/jquery-2.2.4.min.js'></script>
    </body>
</html>
