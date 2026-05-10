<?php
session_start();

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

/*
    CAMBIA ESTOS DATOS:
    Usuario inicial: admin
    Contraseña inicial: 1234
*/
$valid_user = "admin";
$valid_pass = "1234";

if ($username === $valid_user && $password === $valid_pass) {
    $_SESSION['logged_in'] = true;
    $_SESSION['username'] = $username;
    header("Location: ../index.php");
    exit;
}

header("Location: ../login.php?error=1");
exit;
?>
