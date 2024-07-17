<?php
session_start();
include('../admin/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars($_POST['email']);

    $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ?");
    $stmt->execute([$email]);
    $student = $stmt->fetch();

    if ($student) {
        $token = bin2hex(random_bytes(50));
        $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$email, $token]);

        $reset_link = "http://localhost/softip/students/reset_password.php?token=" . $token;
        $subject = "Password Reset Request";
        $message = "Click on this link to reset your password: " . $reset_link;
        $headers = "From: contact@softiptechnology.com";

        mail($email, $subject, $message, $headers);

        $_SESSION['success_message'] = 'Un lien de réinitialisation de mot de passe a été envoyé à votre adresse email.';
        header('Location: forgot_password.php');
        exit;
    } else {
        $_SESSION['error_message'] = 'Aucun compte trouvé avec cette adresse email.';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Mot de passe oublié</title>
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

    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4 pt-5">
            <h2 class="mt-5">Mot de passe oublié</h2>
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
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <button type="submit" class="btn">Envoyer le lien de réinitialisation</button>
            </form>
        </div>
        <div class="col-md-4"></div>
    </div>
</div>
</body>
</html>
