<?php
session_start();
include('../admin/config.php');
include('../admin/functions.php'); // Assurez-vous d'inclure le fichier contenant la fonction is_subscribed.php

// Vérifiez si un étudiant est connecté
if (!isset($_SESSION['student_id'])) {
    header('Location: ../students/login_student.php');
    exit;
}

$student_id = $_SESSION['student_id'];

// Vérifiez si l'étudiant est abonné
if (!is_subscribed($pdo, $student_id)) {
    header('Location: ../abonnements/subscribe.php'); // Redirige vers la page de souscription si pas abonné
    exit;
}

// Récupérer l'abonnement actuel de l'étudiant
$query = "SELECT * FROM abonnements WHERE student_id = :student_id AND status = 'Active'";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
$stmt->execute();
$current_subscription = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupérer les formations disponibles
$query = "SELECT id, titre, price FROM formations";
$stmt = $pdo->query($query);
$formations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Générer un jeton CSRF
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réabonnement</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Réabonnement</h2>
        <?php if ($current_subscription): ?>
            <div class="alert alert-info">
                <p>Vous êtes actuellement abonné à la formation : <strong><?php echo htmlspecialchars($current_subscription['formation_id']); ?></strong></p>
                <p>Date de début : <?php echo htmlspecialchars($current_subscription['start_date']); ?></p>
                <p>Date de fin : <?php echo htmlspecialchars($current_subscription['end_date']); ?></p>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                <p>Vous n'avez pas d'abonnement actif.</p>
            </div>
        <?php endif; ?>

        <form id="reSubscriptionForm">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            
            <div class="mb-3">
                <label for="formation" class="form-label">Choisissez une Formation :</label>
                <select class="form-control" id="formation" name="formation_id" required>
                    <?php foreach ($formations as $formation): ?>
                        <option value="<?php echo htmlspecialchars($formation['id']); ?>" data-price="<?php echo htmlspecialchars($formation['price']); ?>">
                            <?php echo htmlspecialchars($formation['titre']); ?> - <?php echo htmlspecialchars($formation['price']); ?> FCFA
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="payment_method" class="form-label">Méthode de paiement :</label>
                <select class="form-control" id="payment_method" name="payment_method" required>
                    <option value="KKiaPay">KKiaPay</option>
                    <option value="FedaPay">FedaPay</option>
                </select>
            </div>

            <button type="button" class="btn btn-primary btn-resubscribe">Réabonner</button>
        </form>
    </div>

    <!-- Inclure les scripts de paiement -->
    <script src="https://cdn.kkiapay.me/k.js"></script>
    <script src="https://cdn.fedapay.com/checkout.js?v=1.1.7"></script>

    <script>
        document.querySelector(".btn-resubscribe").addEventListener("click", function (e) {
            e.preventDefault();

            const formationSelect = document.querySelector("#formation");
            const selectedOption = formationSelect.options[formationSelect.selectedIndex];
            const formationId = selectedOption.value;
            const price = selectedOption.getAttribute('data-price');
            const paymentMethod = document.querySelector("#payment_method").value;
            const csrfToken = document.querySelector('input[name="csrf_token"]').value;

            if (paymentMethod === 'KKiaPay') {
                openKkiapayWidget({
                    amount: price,
                    api_key: "9353b69f0b5205b6c13e90a05e5eded2e2ddb1e6",
                    sandbox: false,
                    phone: "97000000",
                    name: "Nom de l'utilisateur",
                    callback: `http://localhost/softip/abonnements/re_confirm_payment.php?formation_id=${formationId}&csrf_token=${csrfToken}`
                });
            } else if (paymentMethod === 'FedaPay') {
                FedaPay.init({
                    public_key: "pk_live_BNc4_LofA-VJAjVcku5lCO6O",
                    transaction: {
                        amount: price * 1,
                        description: "Réabonnement à la formation"
                    },
                    onComplete: ({ reason, transaction }) => {
                        if (transaction && reason === "CHECKOUT COMPLETE" && transaction.status === "approved") {
                            window.location.href = `re_confirm_payment.php?transaction_id=${transaction.id}&formation_id=${formationId}&csrf_token=${csrfToken}`;
                        } else {
                            window.location.href = "re_subscribe.php?id=désolé";
                        }
                    }
                }).open();
            }
        });
    </script>
</body>
</html>
