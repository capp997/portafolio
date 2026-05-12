<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$id = $_POST['id'] ?? null;
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if(!$id || !$password || !$confirm){
    header("Location: ../pages/users.php?edit=".$id."&error=missing");
    exit;
}

if(strlen($password) < 6 || $password !== $confirm){
    header("Location: ../pages/users.php?edit=".$id."&error=password");
    exit;
}

try{
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("UPDATE users SET password_hash=? WHERE id=?");
    $stmt->execute([$hash, $id]);

    header("Location: ../pages/users.php?msg=password");
    exit;

}catch(Exception $e){
    header("Location: ../pages/users.php?edit=".$id."&error=db");
    exit;
}
?>
