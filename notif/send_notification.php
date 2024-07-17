<?php
include('../admin/config.php');

function send_notification($student_id, $message) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("
            INSERT INTO notifications (student_id, message, is_read, created_at) 
            VALUES (?, ?, 0, NOW())
        ");
        $stmt->execute([$student_id, $message]);
    } catch (PDOException $e) {
        echo 'Erreur : ' . $e->getMessage();
    }
}
?>
