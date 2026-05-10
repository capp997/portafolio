<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function current_user(){
    return [
        "id" => $_SESSION['user_id'] ?? null,
        "username" => $_SESSION['username'] ?? null,
        "role" => $_SESSION['role'] ?? null
    ];
}

function is_admin(){
    return ($_SESSION['role'] ?? '') === 'admin';
}
?>
