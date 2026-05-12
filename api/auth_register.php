<?php
require_once __DIR__ . "/../config/db.php";

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if(!$username || !$password || !$confirm){
    header("Location: ../login.php?mode=register&error=missing");
    exit;
}

if(strlen($username) < 3){
    header("Location: ../login.php?mode=register&error=invalid");
    exit;
}

if(strlen($password) < 6){
    header("Location: ../login.php?mode=register&error=password");
    exit;
}

if($password !== $confirm){
    header("Location: ../login.php?mode=register&error=match");
    exit;
}

if($email && !filter_var($email, FILTER_VALIDATE_EMAIL)){
    header("Location: ../login.php?mode=register&error=email");
    exit;
}

try{
    $check = $pdo->prepare("SELECT id FROM users WHERE username=? OR email=? LIMIT 1");
    $check->execute([$username, $email ?: null]);

    if($check->fetch()){
        header("Location: ../login.php?mode=register&error=exists");
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO users(username,email,password_hash,role,is_active)
        VALUES(?,?,?,?,1)
    ");

    $stmt->execute([
        $username,
        $email ?: null,
        $hash,
        "user"
    ]);

    header("Location: ../login.php?mode=login&success=registered");
    exit;

}catch(Exception $e){
    header("Location: ../login.php?mode=register&error=db");
    exit;
}
?>
