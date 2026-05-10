<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$asset_id = $_POST['asset_id'] ?? null;
$annual = $_POST['annual_dividend_per_share'] ?? 0;
$frequency = $_POST['payment_frequency'] ?? 'Quarterly';
$next = $_POST['next_pay_date'] ?: null;

if($asset_id){
    $check = $pdo->prepare("SELECT id FROM dividend_settings WHERE asset_id=?");
    $check->execute([$asset_id]);

    if($check->fetch()){
        $stmt = $pdo->prepare("
            UPDATE dividend_settings
            SET annual_dividend_per_share=?, payment_frequency=?, next_pay_date=?
            WHERE asset_id=?
        ");
        $stmt->execute([$annual, $frequency, $next, $asset_id]);
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO dividend_settings(asset_id, annual_dividend_per_share, payment_frequency, next_pay_date)
            VALUES(?,?,?,?)
        ");
        $stmt->execute([$asset_id, $annual, $frequency, $next]);
    }
}

header("Location: ../pages/dividend_tracker.php");
exit;
?>
