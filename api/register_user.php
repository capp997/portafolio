<?php
require_once __DIR__ . "/../config/db.php";

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if(!$username || !$password || !$confirm){
    header("Location: ../register.php?error=missing");
    exit;
}

if(strlen($username) < 3){
    header("Location: ../register.php?error=username");
    exit;
}

if(strlen($password) < 6){
    header("Location: ../register.php?error=password");
    exit;
}

if($password !== $confirm){
    header("Location: ../register.php?error=match");
    exit;
}

if($email && !filter_var($email, FILTER_VALIDATE_EMAIL)){
    header("Location: ../register.php?error=email");
    exit;
}

try{
    $check = $pdo->prepare("SELECT id FROM users WHERE username=? OR email=? LIMIT 1");
    $check->execute([$username, $email ?: null]);

    if($check->fetch()){
        header("Location: ../register.php?error=exists");
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

    header("Location: ../login.php?registered=1");
    exit;

}catch(Exception $e){
    header("Location: ../register.php?error=db");
    exit;
}
?>
