<?php
require_once __DIR__ . '/../config/db.php';

$id = $_POST['id'] ?? null;
$title = trim($_POST['title'] ?? '');
$current_amount = $_POST['current_amount'] ?? 0;
$target_amount = $_POST['target_amount'] ?? 0;
$deadline = $_POST['deadline'] ?: null;

if ($id && $title) {
    $stmt = $pdo->prepare("UPDATE goals SET title=?, current_amount=?, target_amount=?, deadline=? WHERE id=?");
    $stmt->execute([$title, $current_amount, $target_amount, $deadline, $id]);
}

header('Location: ../pages/metas.php');
exit;
?>
