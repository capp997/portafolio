<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$id = $_POST['id'] ?? null;
if($id){
    $stmt = $pdo->prepare("UPDATE smart_alerts SET is_reviewed=1 WHERE id=?");
    $stmt->execute([$id]);
}
header("Location: ../pages/centro_alertas.php");
exit;
?>
