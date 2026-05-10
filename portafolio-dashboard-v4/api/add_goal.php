<?php
require_once __DIR__ . '/../config/db.php';

$title = trim($_POST['title'] ?? '');
$target_amount = $_POST['target_amount'] ?? 0;
$current_amount = $_POST['current_amount'] ?? 0;
$deadline = $_POST['deadline'] ?: null;

if ($title) {
    $stmt = $pdo->prepare("INSERT INTO goals(title,target_amount,current_amount,deadline) VALUES(?,?,?,?)");
    $stmt->execute([$title, $target_amount, $current_amount, $deadline]);
}

header('Location: ../pages/metas.php');
exit;
?>
