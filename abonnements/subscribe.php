<?php
session_start();
include('../admin/config.php');

// Vérifiez si un étudiant est connecté
if (!isset($_SESSION['student_id'])) {
    header('Location: ../students/login_student.php');
    exit;
}

$student_id = $_SESSION['student_id'];

// Récupérer toutes les formations
$query = "SELECT id, titre, price FROM formations";
$stmt = $pdo->query($query);
$formations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Convertir les formations en JSON pour vérification côté client
$formationsJson = json_encode($formations);

// Générer un jeton CSRF
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S'abonner à une formation</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>S'abonner à une formation</h2>
        <form id="subscriptionForm">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <div class="mb-3">
                <label for="formation" class="form-label">Formation:</label>
                <select class="form-control" id="formation" name="formation_id" required>
                    <?php foreach ($formations as $formation): ?>
                        <option value="<?php echo htmlspecialchars($formation['id']); ?>" data-price="<?php echo htmlspecialchars($formation['price']); ?>">
                            <?php echo htmlspecialchars($formation['titre']); ?> - <?php echo htmlspecialchars($formation['price']); ?> FCFA
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="payment_method" class="form-label">Méthode de paiement:</label>
                <select class="form-control" id="payment_method" name="payment_method" required>
                    <option value="KKiaPay">KKiaPay</option>
                    <option value="FedaPay">FedaPay</option>
                </select>
            </div>
            <button type="button" class="btn btn-primary btn-buy">S'abonner</button>
        </form>
    </div>

    <!-- Inclure le script du widget KKiaPay -->
    <script src="https://cdn.kkiapay.me/k.js"></script>

    <!-- Inclure le script du widget FedaPay -->
    <script src="https://cdn.fedapay.com/checkout.js?v=1.1.7"></script>

    <script>
        // Conversion du JSON PHP en objet JavaScript
        const formations = <?php echo $formationsJson; ?>;

            document.querySelector(".btn-buy").addEventListener("click", function (e) {
            e.preventDefault();

            const formationSelect = document.querySelector("#formation");
            const selectedOption = formationSelect.options[formationSelect.selectedIndex];
            const formationId = selectedOption.value;
            const price = selectedOption.getAttribute('data-price');
            const paymentMethod = document.querySelector("#payment_method").value;

            // Vérification côté client de l'existence de la formation
            const formationExists = formations.some(formation => formation.id == formationId);
            
            if (!formationExists) {
                alert("La formation sélectionnée n'existe pas.");
                return;
            }

            if (paymentMethod === 'KKiaPay') {
                // Configuration du widget KKiaPay
                openKkiapayWidget({
                    amount: price,
                    api_key: "9353b69f0b5205b6c13e90a05e5eded2e2ddb1e6", // Remplacez par votre clé API KKiaPay
                    sandbox: false, // Passez à false pour la production
                    phone: "97000000",
                    name: "Nom de l'utilisateur",
                    callback: `http://localhost/softip/abonnements/confirm_payment.php?formation_id=${formationId}&csrf_token=${document.querySelector('input[name="csrf_token"]').value}` // Redirection après paiement réussi
                });
            } else if (paymentMethod === 'FedaPay') {
                FedaPay.init({
                    public_key: "pk_live_BNc4_LofA-VJAjVcku5lCO6O",
                    transaction: {
                        amount: price * 1, // Montant en centimes
                        description: "Abonnement à la formation"
                    },
                    onComplete: ({ reason, transaction }) => {
                        if (transaction && reason === "CHECKOUT COMPLETE" && transaction.status === "approved") {
                            window.location.href = `process_payment.php?transaction_id=${transaction.id}&formation_id=${formationId}&csrf_token=${document.querySelector('input[name="csrf_token"]').value}`;
                        } else {
                            window.location.href = "index.php?id=désolé";
                        }
                    }
                }).open();
            }
        });

    </script>
</body>
</html>
