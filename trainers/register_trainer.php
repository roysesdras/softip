<?php
session_start();
include('../admin/config.php');

// Traitement du formulaire d'enregistrement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = htmlspecialchars($_POST['email']);
    $phone_number = htmlspecialchars($_POST['phone_number']);
    $bio = htmlspecialchars($_POST['bio']);

    // Vérifier si le nom d'utilisateur existe déjà
    $stmt = $pdo->prepare("SELECT * FROM trainers WHERE username = ?");
    $stmt->execute([$username]);
    $existing_trainer = $stmt->fetch();

    if ($existing_trainer) {
        $_SESSION['error_message'] = 'Nom d\'utilisateur déjà pris. Veuillez en choisir un autre.';
    } else {
        // Gestion de l'avatar par défaut
        $default_avatar = '../avatar_img/O.jpg'; // Chemin de l'avatar par défaut

        // Insérer le nouveau formateur dans la base de données avec avatar, numéro de téléphone et biographie
        $stmt = $pdo->prepare("INSERT INTO trainers (username, password, email, phone_number, bio, avatar) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$username, $password, $email, $phone_number, $bio, $default_avatar]);

        // Gestion de l'upload de l'avatar
        if (!empty($_FILES['avatar']['name'])) {
            $avatar_path = '../avatar_img/' . basename($_FILES['avatar']['name']);
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $avatar_path)) {
                // Avatar téléchargé avec succès, mettre à jour le chemin dans la base de données
                $stmt_update_avatar = $pdo->prepare("UPDATE trainers SET avatar = ? WHERE username = ?");
                $stmt_update_avatar->execute([$avatar_path, $username]);
            } else {
                // Échec de l'upload de l'avatar
                $_SESSION['error_message'] = 'Erreur lors du téléchargement de l\'avatar.';
            }
        }

        $_SESSION['success_message'] = 'Enregistrement réussi. Vous pouvez maintenant vous connecter.';
        header('Location: login_trainer.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Enregistrement Formateur</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Enregistrement Formateur</h2>
        
        <?php
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
            unset($_SESSION['error_message']);
        }
        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
            unset($_SESSION['success_message']);
        }
        ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="username" class="form-label">Nom d'utilisateur</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="phone_number" class="form-label">Numéro de téléphone</label>
                <input type="text" class="form-control" id="phone_number" name="phone_number">
            </div>
            <div class="mb-3">
                <label for="bio" class="form-label">Biographie</label>
                <textarea class="form-control" id="bio" name="bio" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label for="avatar" class="form-label">Avatar</label>
                <input type="file" class="form-control" id="avatar" name="avatar">
            </div>
            <button type="submit" class="btn btn-primary">S'enregistrer</button>
        </form>
    </div>
</body>
</html>
