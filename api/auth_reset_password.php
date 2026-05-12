<?php
require_once __DIR__ . "/../config/db.php";

$identifier = trim($_POST['identifier'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if(!$identifier || !$password || !$confirm){
    header("Location: ../login.php?mode=forgot&error=missing");
    exit;
}

if(strlen($password) < 6){
    header("Location: ../login.php?mode=forgot&error=password");
    exit;
}

if($password !== $confirm){
    header("Location: ../login.php?mode=forgot&error=match");
    exit;
}

try{
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username=? OR email=? LIMIT 1");
    $stmt->execute([$identifier, $identifier]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$user){
        header("Location: ../login.php?mode=forgot&error=notfound");
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $update = $pdo->prepare("UPDATE users SET password_hash=? WHERE id=?");
    $update->execute([$hash, $user['id']]);

    header("Location: ../login.php?mode=login&success=reset");
    exit;

}catch(Exception $e){
    header("Location: ../login.php?mode=forgot&error=db");
    exit;
}
?>
