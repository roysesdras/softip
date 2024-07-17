<?php
session_start();
include('../admin/config.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['student_id'])) {
    header('Location: login_student.php');
    exit;
}

// Initialiser le message de session
$_SESSION['message'] = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formation_id = $_POST['formation'];
    $user_id = $_SESSION['student_id']; // Utilisez $_SESSION['student_id'] au lieu de $_SESSION['user_id']

    // Vérifier si l'utilisateur est déjà inscrit à cette formation
    $stmt = $pdo->prepare("SELECT * FROM inscriptions WHERE user_id = ? AND formation_id = ?");
    $stmt->execute([$user_id, $formation_id]);
    $inscription = $stmt->fetch();

    if ($inscription) {
        $_SESSION['message'] = 'Vous êtes déjà inscrit à cette formation.';
    } else {
        // Inscrire l'utilisateur à la formation
        $stmt = $pdo->prepare("INSERT INTO inscriptions (user_id, formation_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $formation_id]);

        // Préparer et envoyer l'email de confirmation
        $mail = new PHPMailer(true);
        // Configuration de PHPMailer (à remplacer avec vos paramètres)
        $mail->isSMTP();
        $mail->Host = 'smtp.example.com'; // Remplacez par votre serveur SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'votre_email@example.com'; // Remplacez par votre adresse email
        $mail->Password = 'votre_mot_de_passe'; // Remplacez par votre mot de passe email
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('votre_email@example.com', 'Votre Nom ou Nom de l\'entreprise');
        $mail->addAddress($_SESSION['student_email']);

        $mail->isHTML(true);
        $mail->Subject = 'Confirmation d\'inscription à la formation';
        $mail->Body    = "
            <p>Chèr(e) {$_SESSION['student_username']},</p>
            <p>Nous avons le plaisir de vous informer que votre inscription à la formation a été réussie.</p>
            <p>Pour accéder à votre tableau de bord et commencer à suivre votre formation, veuillez cliquer sur le lien suivant : <a href='http://votre_site.com/student_dashboard.php'>Votre Tableau de Bord</a>.</p>
            <p>Si vous avez des questions ou besoin d'assistance, n'hésitez pas à nous contacter.</p>
            <p>Nous vous souhaitons une excellente formation !</p>
            <p>Cordialement,<br />L'équipe de votre centre de formation</p>
        ";

        if ($mail->send()) {
            $_SESSION['message'] = 'Inscription réussie. Un email de confirmation a été envoyé à votre adresse. Vous serez redirigé vers la page des formations dans 2 secondes.';
        } else {
            $_SESSION['message'] = 'Inscription réussie. Toutefois, l\'envoi de l\'email de confirmation a échoué. Vous serez redirigé vers la page des formations dans 2 secondes.';
        }

        // Définir un en-tête de rafraîchissement de 2 secondes pour rediriger vers la page des formations
        header('refresh:2;url=index.php');
        exit;
    }
}

// Récupérer la liste des formations
$stmt = $pdo->query("SELECT * FROM formations");
$formations = $stmt->fetchAll();
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
    <title>Inscription</title>

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
               <h2 class="mt-3 text-center">Inscription à une Formation</h2>
               <p class="text-center">veuilez bien à remplir tout les champs nécessaire pour votre inscription</p>
                <?php if (!empty($_SESSION['message'])): ?>
                    <div class="alert alert-info">
                        <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="" class="shadow p-2 mb-3">
                    <div class="mb-3">
                        <label for="formation" class="form-label"></label>
                        <select class="form-control" id="formation" name="formation" required>
                            <option value="">Sélectionnez une formation</option>
                            <?php foreach ($formations as $formation): ?>
                                <option value="<?php echo $formation['id']; ?>"><?php echo $formation['titre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom Complet:</label>
                        <input type="text" class="form-control" id="nom" name="nom" value="<?php echo $_SESSION['student_username']; ?>" readonly required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail:</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $_SESSION['student_email']; ?>" readonly required>
                    </div>
                    <div class="mb-3">
                        <label for="telephone" class="form-label">Numéro de téléphone:</label>
                        <input type="tel" class="form-control" id="telephone" name="telephone" value="<?php echo $_SESSION['student_phone']; ?>" readonly required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message supplémentaire</label>
                        <textarea class="form-control" id="message" name="message" rows="3" placeholder="veuillez nous parlés unpeu de vos attentes" required></textarea>
                    </div>
                    <button type="submit" class="btn mb-1">S'inscrire</button>
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
    <script src="../assets/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
