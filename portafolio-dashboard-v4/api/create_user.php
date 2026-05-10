<?php

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";

require_role(['admin']);

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? 'viewer';

if($username && $password){

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO users(username,password_hash,role,is_active)
        VALUES(?,?,?,1)
    ");

    $stmt->execute([
        $username,
        $hash,
        $role
    ]);
}

header("Location: ../pages/users.php");
exit;
?>