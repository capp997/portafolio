<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$id = $_POST['id'] ?? null;
$is_active = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;

if(!$id){
    header("Location: ../pages/users.php?error=missing");
    exit;
}

try{
    $stmt = $pdo->prepare("UPDATE users SET is_active=? WHERE id=?");
    $stmt->execute([$is_active, $id]);

    header("Location: ../pages/users.php?msg=status");
    exit;

}catch(Exception $e){
    header("Location: ../pages/users.php?error=db");
    exit;
}
?>
