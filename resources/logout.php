<?php
session_start();

// Détruire toutes les variables de session
$_SESSION = array();

// Supprimer le cookie de session en le rendant expiré
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 86400, '/');
}

// Détruire la session
session_destroy();

// Redirection vers la page de connexion avec un message de déconnexion
header('Location: ../admin/login_admin.php'); // Remplacez par la page de connexion des administrateurs des ressources
exit;
?>
