<?php
session_start();
include('../admin/config.php');

// Vérifiez si un étudiant est connecté
if (!isset($_SESSION['student_id'])) {
    header('Location: ../students/login_student.php');
    exit;
}

$student_id = $_SESSION['student_id'];

// Vérifier et valider les paramètres GET
if (!isset($_GET['transaction_id']) || !isset($_GET['formation_id']) || !isset($_GET['csrf_token'])) {
    die("ID de transaction, ID de formation ou jeton CSRF manquant.");
}

$transaction_id = filter_var($_GET['transaction_id'], FILTER_SANITIZE_STRING);
$formation_id = filter_var($_GET['formation_id'], FILTER_SANITIZE_NUMBER_INT);
$csrf_token = filter_var($_GET['csrf_token'], FILTER_SANITIZE_STRING);

// Vérifier le jeton CSRF
if ($csrf_token !== $_SESSION['csrf_token']) {
    die("Jeton CSRF invalide.");
}

// Récupérer le montant depuis la table des formations
$query = "SELECT price FROM formations WHERE id = :formation_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':formation_id', $formation_id, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    $price = $result['price'];
} else {
    die("Formation non trouvée.");
}

// Préparer les détails de la transaction
$start_date = date('Y-m-d');
$end_date = date('Y-m-d', strtotime('+30 days'));
$status = 'Actif';
$payment_method = 'KKiaPay';  // Vous pouvez adapter cela si vous avez d'autres méthodes de paiement
$transaction_details = "Paiement réussi via KKiaPay";

// Utiliser une transaction pour insérer les données
try {
    $pdo->beginTransaction();
    
    $query = "INSERT INTO abonnements (student_id, formation_id, start_date, end_date, price, status, payment_method, transaction_details, auto_renewal, notifications_sent) 
              VALUES (:student_id, :formation_id, :start_date, :end_date, :price, :status, :payment_method, :transaction_details, 0, 0)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    $stmt->bindParam(':formation_id', $formation_id, PDO::PARAM_INT);
    $stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
    $stmt->bindParam(':end_date', $end_date, PDO::PARAM_STR);
    $stmt->bindParam(':price', $price, PDO::PARAM_STR);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':payment_method', $payment_method, PDO::PARAM_STR);
    $stmt->bindParam(':transaction_details', $transaction_details, PDO::PARAM_STR);

    if ($stmt->execute()) {
        $pdo->commit();
        echo "Abonnement créé avec succès!";
    } else {
        $pdo->rollBack();
        echo "Erreur lors de la création de l'abonnement.";
    }
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Erreur : " . $e->getMessage();
}

// Vérifier si l'utilisateur est déjà inscrit à cette formation
$stmt = $pdo->prepare("SELECT * FROM inscriptions WHERE user_id = ? AND formation_id = ?");
$stmt->execute([$student_id, $formation_id]);
$inscription = $stmt->fetch();

if ($inscription) {
    $_SESSION['message'] = 'Vous êtes déjà inscrit à cette formation.';
} else {
    // Inscrire l'utilisateur à la formation
    $stmt = $pdo->prepare("INSERT INTO inscriptions (user_id, formation_id) VALUES (?, ?)");
    $stmt->execute([$student_id, $formation_id]);

    // Préparer et envoyer l'email de confirmation
    require 'vendor/autoload.php';  // Inclure le fichier autoload de Composer
    $mail = new PHPMailer(true);

    try {
        // Configuration de PHPMailer (à remplacer avec vos paramètres)
        $mail->isSMTP();
        $mail->Host = 'smtp.example.com'; // Remplacez par votre serveur SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'votre_email@example.com'; // Remplacez par votre adresse email
        $mail->Password = 'votre_mot_de_passe'; // Remplacez par votre mot de passe email
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
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
    } catch (Exception $e) {
        $_SESSION['message'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

    // Définir un en-tête de rafraîchissement de 2 secondes pour rediriger vers la page des formations
    header('refresh:2;url=index.php');
    exit;
}

// Récupérer la liste des formations
$stmt = $pdo->query("SELECT * FROM formations");
$formations = $stmt->fetchAll();
?>
