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
    function is_subscribed($pdo, $student_id) {
        $query = "SELECT COUNT(*) FROM abonnements WHERE student_id = :student_id AND status = 'Active'";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
?>