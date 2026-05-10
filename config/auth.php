<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in(){
    return isset($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function require_login(){
    if (!is_logged_in()) {
        header("Location: /login.php");
        exit;
    }
}

function require_role($roles = []){
    require_login();

    if (!is_array($roles)) {
        $roles = [$roles];
    }

    if (!in_array($_SESSION['role'] ?? '', $roles)) {
        http_response_code(403);
        die("Acceso denegado.");
    }
}

require_login();
?>
