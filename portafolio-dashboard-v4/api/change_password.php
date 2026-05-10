<?php

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";

require_role(['admin']);

$new = $_POST['new_password'] ?? '';

if(strlen($new) >= 8){

    $hash = password_hash($new, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        UPDATE users 
        SET password_hash=? 
        WHERE id=?
    ");

    $stmt->execute([
        $hash,
        $_SESSION['user_id']
    ]);
}

header("Location: ../pages/users.php");
exit;
?>
