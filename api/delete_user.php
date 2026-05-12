<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$id = $_POST['id'] ?? null;

if(!$id){
    header("Location: ../pages/users.php?error=missing");
    exit;
}

if((int)$id === (int)($_SESSION['user_id'] ?? 0)){
    header("Location: ../pages/users.php?error=self_delete");
    exit;
}

try{
    $stmt = $pdo->prepare("DELETE FROM users WHERE id=?");
    $stmt->execute([$id]);

    header("Location: ../pages/users.php?msg=deleted");
    exit;

}catch(Exception $e){
    header("Location: ../pages/users.php?error=db");
    exit;
}
?>
