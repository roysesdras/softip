<?php
// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Nettoyer les données soumises
    $full_name = htmlspecialchars($_POST['full_name']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars($_POST['phone']);
    $adresse = htmlspecialchars($_POST['adresse']);
    $formation_choisi = htmlspecialchars($_POST['formation_choisi']);
    $message = htmlspecialchars($_POST['message']);

    // Construire le message
    $message_body = "Nom et Prénom: $full_name\n"; 
    $message_body .= "Adresse: $adresse\n";
    $message_body .= "Email: $email\n";
    $message_body .= "Téléphone: $phone\n";
    $message_body .= "Formation ou domaine d'intérêt: $formation_choisi\n";
    $message_body .= "Message: $message";

    // Valider l'adresse e-mail
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // En-têtes du mail
        $headers = "From: $email\r\n";
        $headers .= "Reply-to: $email\r\n";

        // Envoyer le mail de manière plus sécurisée
        $retour = mail("contact@softiptechnology.com", "Une inscription vient d'être enregistrée. Veuillez vérifier votre boîte mail ou votre page de paiement pour confirmer la réception des fonds.", $message, $headers);

        // Affichez un message de confirmation
        echo '<p style="font-size: 18px; padding: 5em; font-family:calibri;">Votre inscription a bien été enregistrée. Merci!<br>Vous serez redirigé dans quelques instants.</p>';

        // Inclusion du script JavaScript pour le paiement
        echo '<script>
                FedaPay.init({
                    public_key: "pk_live_BNc4_LofA-VJAjVcku5lCO6O",
                    transaction: {
                        amount: 7000, // Modifier le montant à 2000 pour correspondre à 2000 FCFA
                        description: "inscription",
                    },
                    onComplete: ({ reason, transaction }) => {
                        console.log(reason, transaction);

                        // Vérifiez si transaction est défini avant d\'y accéder

                        if (transaction && reason === "CHECKOUT COMPLETE") {
                            if (transaction.status == "approved") {
                                window.location.href = "index.php?id=" + transaction.id; // Redirection vers index.php avec l\'ID de transaction
                            } else {
                                window.location.href = "inscription_form.php?id=désolé"; // Redirection vers la page d\'inscription avec un paramètre d\'erreur
                            }
                        } else {
                            window.location.href = "index.php"; // Redirection vers index.php sans ID de transaction
                        }
                    },
                }).open();
            </script>';
    } else {
        // Si le formulaire n'a pas été soumis correctement
        echo '<p style="font-size: 18px; padding: 5em; font-family:calibri;">Le formulaire n\'a pas été soumis correctement. Veuillez réessayer. <br>
        <a href="https://votre-site-web.de/#inscription_form.php">je m\'inscris</a></p>';
    }

} else {
    // Si le formulaire n'a pas été soumis
    echo '<p style="font-size: 18px; padding: 5em; font-family:calibri;">Le formulaire n\'a pas été soumis correctement. Veuillez réessayer. <br>
    <a href="https://votre-site-web.de/#inscription_form.php">je m\'inscris</a></p>';
}
?>
