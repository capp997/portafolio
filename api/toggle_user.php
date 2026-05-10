<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";
require_role(['admin']);

$id = $_POST['id'] ?? null;

if($id){
    $stmt = $pdo->prepare("UPDATE users SET is_active = IF(is_active=1,0,1) WHERE id=? AND username <> 'admin'");
    $stmt->execute([$id]);
}

header("Location: ../pages/users.php");
exit;
?>
