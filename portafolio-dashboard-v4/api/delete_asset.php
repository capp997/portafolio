<?php
require_once __DIR__ . '/../config/db.php';

$id = $_POST['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM assets WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: ../pages/activos.php');
exit;
?>
