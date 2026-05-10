<?php
require_once __DIR__ . '/../config/db.php';

$ticker = strtoupper(trim($_POST['ticker'] ?? ''));
$name = trim($_POST['name'] ?? '');
$category = trim($_POST['category'] ?? 'Otra');
$shares = $_POST['shares'] ?? 0;
$avg_cost = $_POST['avg_cost'] ?? 0;
$current_price = $_POST['current_price'] ?? 0;
$base_price = $_POST['base_price'] ?? 0;
$target_percent = $_POST['target_percent'] ?? 0;

if ($ticker && $name) {
    $stmt = $pdo->prepare("INSERT INTO assets(ticker, name, category, shares, avg_cost, current_price, base_price, target_percent) VALUES(?,?,?,?,?,?,?,?)");
    $stmt->execute([$ticker, $name, $category, $shares, $avg_cost, $current_price, $base_price, $target_percent]);
}

header('Location: ../pages/activos.php');
exit;
?>
