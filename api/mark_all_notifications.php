<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$pdo->query("UPDATE app_notifications SET is_read=1 WHERE is_read=0");

header("Location: ../pages/notifications.php");
exit;
?>
