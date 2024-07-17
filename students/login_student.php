<?php
session_start();
include('../admin/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM students WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $student = $stmt->fetch();

    if ($student && password_verify($password, $student['password'])) {
        $_SESSION['student_id'] = $student['id'];
        $_SESSION['student_username'] = $student['username'];
        $_SESSION['student_email'] = $student['email'];
        $_SESSION['student_phone'] = $student['phone_number'];
        header('Location: inscription_formation.php'); // Redirection vers le tableau de bord des étudiants
        exit; // Arrête l'exécution du script après la redirection
    } else {
        $_SESSION['error_message'] = 'Nom d\'utilisateur ou mot de passe incorrect';
        header('Location: login_student.php');
        exit; // Arrête l'exécution du script après la redirection
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <script src="../assets/js/color-modes.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Favicons -->
    <link href="../assets/img/favicon.png" rel="icon">
    <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- meta for og.graph -->
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="sternaafrica" />
    <title>Connexion Étudiant</title>

    <!-- all css -->
    <!-- <link rel="canonical" href="https://sternaafrica.org/"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">
    <link rel="stylesheet" href="../assets/style.css">
    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"> -->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>

        .btn{
            background-color: #033e60;
            color: #ffffff;
            padding: 5px;
            border          : solid transparent;
            font-size: 16px;
            border-radius: 0px;
        }
        .btn:hover{
            background-color: transparent;
            color: #033e60;
            padding: 5px;
            border          : solid 1px #033e60;
        }

        form{
            border-radius: 5px;
        }

        a{
            color: #033e60;
            text-decoration: none;
        }
    </style>

</head>
<body>
    <div class="container">
        <div class="row pt-5">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <h2 class="mt-5 text-center">Connexion Étudiant</h2>
                <p class="text-center">Entrée vos identifiants de connexion</p>
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger">
                        <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="" class="pt-3">
                    <div class="mb-3">
                        <label for="username" class="form-label">Nom d'utilisateur ou Email</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Se connecter</button>
                </form>
                <div class="mt-3">
                    <a href="forgot_password.php">mot de passe oublier ?</a> <br>
                    <p>vous n'avez pas de compte, <a href="register.php">créez-en un</a></p>
                </div>
            </div>
            <div class="col-md-4"></div>
        </div>
    </div>



</body>
</html>
