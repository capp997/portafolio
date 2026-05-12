<?php
session_start();

if(isset($_SESSION["user_id"])){
    header("Location: /index_v5.php");
    exit;
}

header("Location: /login.php");
exit;
?>
