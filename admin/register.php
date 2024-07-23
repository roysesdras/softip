<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO administrators (username, password) VALUES (?, ?)");
    $stmt->execute([$username, $password]);

    $_SESSION['message'] = 'Administrateur ajouté avec succès';
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Inscription Administrateur</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Favicons -->
    <link href="../assets/img/favicon.png" rel="icon">
    <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <h2 class="mt-5">Inscription Administrateur</h2>
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-success">
                        <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                        <a href="login.php">Cliquez ici pour vous connecter</a>
                    </div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">Nom d'utilisateur</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">S'inscrire</button>
                </form>
            </div>
            <div class="col-md-4"></div>
        </div>
    </div>

    <?php require_once ('../inclusion/footer_2.php'); ?>
</body>
</html>
