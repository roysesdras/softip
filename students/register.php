<?php
session_start();
include('../admin/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer et sécuriser les données du formulaire
    $username = htmlspecialchars($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone_number = htmlspecialchars($_POST['phone_number']);
    $avatar = null;

    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../avatar_img/';
        $avatarName = $_FILES['avatar']['name'];
        $uploadFile = $uploadDir . basename($avatarName);
    
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadFile)) {
            echo "L'avatar a été téléchargé avec succès.";
            $avatar = $uploadFile;
        } else {
            echo "Erreur lors du téléchargement de l'avatar.";
        }
    } else {
        echo "Aucun avatar téléchargé.";
        // Définir un avatar par défaut si nécessaire
        $avatar = '../avatar_img/R.jpg'; // Assurez-vous que ce fichier existe
    }
    

    // Vérifier si l'utilisateur existe déjà
    $stmt = $pdo->prepare("SELECT * FROM students WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    $existing_user = $stmt->fetch();

    if ($existing_user) {
        $_SESSION['message'] = 'Nom d\'utilisateur ou email déjà utilisé';
    } else {
        // Insérer le nouvel étudiant dans la base de données
        $stmt = $pdo->prepare("INSERT INTO students (username, password, email, phone_number, avatar) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$username, $password, $email, $phone_number, $avatar]);

        $_SESSION['message'] = 'Inscription réussie. Vous pouvez maintenant vous connecter.';
        header('Location: login_student.php');
        exit;
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
    <title>Créer un compte Etudiant</title>

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
    </style>

</head>
<body>
<?php include_once('../inclusion/navbar.php'); ?>
    <div class="container">
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <h2 class="mt-5 text-center">Créer un compte</h2>
                    <p class="text-center">Veuillez à fournir vos informations exact</p>
                        <?php if (isset($_SESSION['message'])): ?>
                            <div class="alert alert-danger">
                                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                            </div>
                        <?php endif; ?>
                        <form method="POST" action="" enctype="multipart/form-data" class="shadow p-2 mb-5">
                            <!-- Vos champs existants -->
                            <div class="mb-3">
                                <label for="username" class="form-label">Nom Complet:</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe:</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone_number" class="form-label">Numéro de téléphone:</label>
                                <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                            </div>
                            <div class="mb-3">
                                <label for="avatar" class="form-label">Sélectionner un Avatar</label>
                                <input type="file" class="form-control" id="avatar" name="avatar">
                            </div>

                            <!-- Bouton de soumission -->
                            <button type="submit" class="btn mb-1">Créer compte</button>
                        </form>
                    </div>
                <div class="col-md-4"></div>
            </div>
        
    </div>




        <div class=" d-md-flex py-4 linkFooter">
            <div class="px-2 copyright">
                <p>
                    &copy; <strong><span>Soft IP Technology</span></strong>. Tout droit réservé 
                </p>
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

    <!-- bas / pied de page -->
    <script src='https://code.jquery.com/jquery-2.2.4.min.js'></script>
    <script src="./assets/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
