<?php
session_start();
include('../admin/config.php');
include('../admin/functions.php');

// Vérifiez si un étudiant est connecté
if (!isset($_SESSION['student_id'])) {
    header('Location: ../students/login_student.php');
    exit;
}

$student_id = $_SESSION['student_id'];

// Vérifier l'abonnement de l'étudiant
$is_subscribed = is_subscribed($pdo, $student_id);

// Si l'étudiant est déjà abonné, rediriger vers la page d'accueil ou tableau de bord
if ($is_subscribed) {
    header('Location: ../students/dashboard.php'); // Rediriger vers la page appropriée
    exit;
}

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
    
    <!-- Favicons -->
    <link href="../assets/img/favicon.png" rel="icon">
    <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <link rel="stylesheet" href="../assets/style.css">
    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin-top: 20px;
            background-color: #fff;
            color: #333;
            border-radius: 5px;
        }
        .btn-buy {
            width: 100%;
            background-color: #033e60;
            color: #fff;  
            font-weight: bold;
            padding: 5px;
            border-radius: 5px; 
        }
        .btn-buy:hover {
            background-color: transparent;
            color: #033e60;
            border: solid 1px #033e60;
        }
        input:hover {
            border: solid 1px #033e60;
        }
        .form-label {
            font-weight: bold;
        }
    </style>

</head>
<body>
    <div class="container mb-4">
        <h2 class="text-center">S'abonner à une formation</h2>
        <form id="subscriptionForm">
            <div class="mb-3">
                <label for="name" class="form-label">Nom Complet:</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($_SESSION['student_username']); ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['student_email']); ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="phone_number" class="form-label">Numéro:</label>
                <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($_SESSION['student_phone']); ?>" readonly>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <div class="mb-3">
                <label for="formation" class="form-label">Sélectionnez une Formation:</label>
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
            <button type="button" class="btn-buy">S'abonner</button>
        </form>
        <div class="texte mt-3">
            <p>
               Votre abonnement vous donne droit à :
               <ul>
                <li>
                    <strong>Accès au cours :</strong> Profitez d'un accès complet à tous les contenus de la formation, incluant les ressources additionnelles.
                </li>

                <li>
                    <strong>Certificats de Réussite :</strong> Recevez un certificat à la fin de votre formation pour valoriser vos compétences.
                </li>

                <li>
                    <strong>Forums de Discussion :</strong> Participez à des forums dynamiques pour échanger avec des formateurs et d'autres étudiants.
                </li>
                <li>
                    <strong>Support Continu :</strong> Bénéficiez d'une assistance technique et pédagogique tout au long de votre parcours.
                </li>
                <li>
                    <strong>Mises à Jour Régulières :</strong> Accédez aux nouvelles mises à jour et ajouts de contenu pour rester à jour avec les dernières avancées.
                </li>
               </ul>
            </p>
        </div>
    </div>

    <!-- Inclure le script du widget KKiaPay -->
    <script src="https://cdn.kkiapay.me/k.js"></script>
    <!-- Inclure le script du widget FedaPay -->
    <script src="https://cdn.fedapay.com/checkout.js?v=1.1.7"></script>

    <script>
        const formations = <?php echo $formationsJson; ?>;

        document.querySelector(".btn-buy").addEventListener("click", function (e) {
            e.preventDefault();

            const formationSelect = document.querySelector("#formation");
            const selectedOption = formationSelect.options[formationSelect.selectedIndex];
            const formationId = selectedOption.value;
            const price = selectedOption.getAttribute('data-price');
            const paymentMethod = document.querySelector("#payment_method").value;
            const csrfToken = document.querySelector('input[name="csrf_token"]').value;

            const formationExists = formations.some(formation => formation.id == formationId);
            if (!formationExists) {
                alert("La formation sélectionnée n'existe pas.");
                return;
            }

            if (paymentMethod === 'KKiaPay') {
                openKkiapayWidget({
                    amount: price,
                    api_key: "b5d6b530365511eca8f5b92f2997955b",
                    sandbox: false,
                    phone: "97000000",
                    name: "Nom de l'utilisateur",
                    callback: `http://localhost/softip/abonnements/confirm_payment.php?formation_id=${formationId}&csrf_token=${csrfToken}`
                });
            } else if (paymentMethod === 'FedaPay') {
                FedaPay.init({
                    public_key: "pk_live_BNc4_LofA-VJAjVcku5lCO6O",
                    transaction: {
                        amount: price * 1,
                        description: "Abonnement à la formation"
                    },
                    onComplete: ({ reason, transaction }) => {
                        if (transaction && reason === "CHECKOUT COMPLETE" && transaction.status === "approved") {
                            window.location.href = `process_payment.php?transaction_id=${transaction.id}&formation_id=${formationId}&csrf_token=${csrfToken}`;
                        } else {
                            window.location.href = "index.php?id=désolé";
                        }
                    }
                }).open();
            }
        });
    </script>

    <?php include_once ('../inclusion/footer_2.php'); ?>
     <!-- Inclusion de Bootstrap JS (nécessaire pour le offcanvas) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://code.jquery.com/jquery-2.2.4.min.js'></script>
</body>
</html>
