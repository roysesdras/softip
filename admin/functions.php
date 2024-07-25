<?php 
//     // functions.php
// function is_subscribed($pdo, $student_id) {
//     // Préparer la requête pour vérifier l'abonnement actif
//     $stmt = $pdo->prepare("SELECT status FROM abonnements WHERE student_id = ? AND status = 'Active'");
//     $stmt->execute([$student_id]);
    
//     // Vérifier s'il y a un abonnement actif pour l'étudiant
//     return $stmt->rowCount() > 0;
// }   
?>

<?php
// functions.php

function is_subscribed($pdo, $student_id) {
    // Vérifier si l'étudiant a un abonnement actif
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM abonnements 
        WHERE student_id = ? 
        AND status = 'active' 
        AND NOW() BETWEEN start_date AND end_date
    ");
    $stmt->execute([$student_id]);
    $count = $stmt->fetchColumn();

    return $count > 0;
}
?>
