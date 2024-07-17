<?php
if (isset($_POST['message'])) {
    $message = 'Ce message vous a été envoyé via la page contact de SOFT IP TECHNOLOGY.
    Nom : ' . $_POST['fullname'] . '
    Email : ' . $_POST['email'] . '
    Message : ' . $_POST['message'];

    $retour = mail("contact@softiptechnology.com", $_POST['fullname'], $message, "From: contact@softiptechnology.com" . "\r\n" . "Reply-to: " . $_POST['email']);

    if ($retour) {
        echo '<p style="text-align: center; padding: 5px; font-size: 18px; ">✔️ Votre courriel a été transmis à l\'administration de Soft IP Technology. Vous recevrez une réponse sous peu.</p>';
    } else {
        echo "<p>❌ Une erreur s'est produite lors de l'envoi du mail</p>";
    }
}
?>
