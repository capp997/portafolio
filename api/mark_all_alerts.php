<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";
$pdo->query("UPDATE smart_alerts SET is_reviewed=1 WHERE is_reviewed=0");
header("Location: ../pages/centro_alertas.php");
exit;
?>
