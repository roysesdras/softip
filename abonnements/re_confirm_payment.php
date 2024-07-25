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
$payment_method = $_GET['payment_method']; // Le méthode de paiement est passé comme paramètre GET
$transaction_details = "Réabonnement réussi via " . htmlspecialchars($payment_method);

// Utiliser une transaction pour insérer les données
try {
    $pdo->beginTransaction();
    
    if ($payment_method === 'KKiaPay') {
        $query = "UPDATE abonnements SET start_date = :start_date, end_date = :end_date, status = :status, payment_method = :payment_method, transaction_details = :transaction_details 
                  WHERE student_id = :student_id AND formation_id = :formation_id AND status = 'Actif'";
    } else if ($payment_method === 'FedaPay') {
        $query = "UPDATE abonnements SET start_date = :start_date, end_date = :end_date, status = :status, payment_method = :payment_method, transaction_details = :transaction_details 
                  WHERE student_id = :student_id AND formation_id = :formation_id AND status = 'Actif'";
    }

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    $stmt->bindParam(':formation_id', $formation_id, PDO::PARAM_INT);
    $stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
    $stmt->bindParam(':end_date', $end_date, PDO::PARAM_STR);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':payment_method', $payment_method, PDO::PARAM_STR);
    $stmt->bindParam(':transaction_details', $transaction_details, PDO::PARAM_STR);

    if ($stmt->execute()) {
        $pdo->commit();
        echo "Réabonnement réussi!";
    } else {
        $pdo->rollBack();
        echo "Erreur lors du réabonnement.";
    }
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Erreur : " . $e->getMessage();
}
?>
