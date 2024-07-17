<?php
session_start();
include('../admin/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM trainers WHERE username = ?");
    $stmt->execute([$username]);
    $trainer = $stmt->fetch();

    if ($trainer && password_verify($password, $trainer['password'])) {
        $_SESSION['trainer_id'] = $trainer['id'];
        header('Location: trainers.php');
        exit;
    } else {
        $error = 'Nom d\'utilisateur ou mot de passe incorrect';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Formateur</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <!-- Favicons -->
    <link href="../assets/img/favicon.png" rel="icon">
    <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    
    <link rel="stylesheet" href="../assets/style.css">
    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">

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
    <div class="container pt-5">
        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4 pt-5">
            <h2>Connexion Formateur</h2>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Nom d'utilisateur</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn">Se connecter</button>
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
