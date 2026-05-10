<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$id = $_POST['id'] ?? null;
if($id){
    $stmt = $pdo->prepare("UPDATE app_notifications SET is_read=1 WHERE id=?");
    $stmt->execute([$id]);
}

header("Location: ../pages/notifications.php");
exit;
?>
