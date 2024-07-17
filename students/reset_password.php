<?php
session_start();
include('../admin/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = htmlspecialchars($_POST['token']);
    $password = htmlspecialchars($_POST['password']);
    $password_confirm = htmlspecialchars($_POST['password_confirm']);

    if ($password !== $password_confirm) {
        $_SESSION['error_message'] = 'Les mots de passe ne correspondent pas.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ?");
        $stmt->execute([$token]);
        $reset = $stmt->fetch();

        if ($reset && strtotime($reset['created_at']) > strtotime('-1 hour')) {
            $new_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE students SET password = ? WHERE email = ?");
            $stmt->execute([$new_password, $reset['email']]);

            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = ?");
            $stmt->execute([$token]);

            $_SESSION['success_message'] = 'Votre mot de passe a été réinitialisé avec succès.';
            header('Location: login_student_cool.php');
            exit;
        } else {
            $_SESSION['error_message'] = 'Le lien de réinitialisation est invalide ou expiré.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Réinitialiser le mot de passe</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Favicons -->
    <link href="../assets/img/favicon.png" rel="icon">
    <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">

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
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <h2 class="mt-5">Réinitialiser le mot de passe</h2>
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger">
                    <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="" class="pt-5">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
                <div class="mb-3">
                    <label for="password" class="form-label">Nouveau mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="password_confirm" class="form-label">Confirmer le mot de passe</label>
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                </div>
                <button type="submit" class="btn">Réinitialiser le mot de passe</button>
            </form>
        </div>
        <div class="col-md-4"></div>
    </div>
</div>
</body>
</html>
