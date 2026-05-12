<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$id = $_POST['id'] ?? null;
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$role = $_POST['role'] ?? 'user';
$is_active = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;

if(!$id || !$username){
    header("Location: ../pages/users.php?error=missing");
    exit;
}

if(!in_array($role, ['user','admin'])){
    $role = 'user';
}

if($email && !filter_var($email, FILTER_VALIDATE_EMAIL)){
    header("Location: ../pages/users.php?edit=".$id."&error=missing");
    exit;
}

try{
    $check = $pdo->prepare("
        SELECT id FROM users
        WHERE (username=? OR email=?)
        AND id <> ?
        LIMIT 1
    ");
    $check->execute([$username, $email ?: null, $id]);

    if($check->fetch()){
        header("Location: ../pages/users.php?edit=".$id."&error=exists");
        exit;
    }

    $stmt = $pdo->prepare("
        UPDATE users
        SET username=?, email=?, role=?, is_active=?
        WHERE id=?
    ");

    $stmt->execute([
        $username,
        $email ?: null,
        $role,
        $is_active,
        $id
    ]);

    header("Location: ../pages/users.php?msg=updated");
    exit;

}catch(Exception $e){
    header("Location: ../pages/users.php?edit=".$id."&error=db");
    exit;
}
?>
