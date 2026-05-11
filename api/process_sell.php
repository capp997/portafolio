<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$asset_id = $_POST['asset_id'] ?? null;
$sell_date = $_POST['sell_date'] ?? date("Y-m-d");
$shares_sold = (float)($_POST['shares_sold'] ?? 0);
$sell_price = (float)($_POST['sell_price'] ?? 0);
$note = trim($_POST['note'] ?? '');

if(!$asset_id || $shares_sold <= 0 || $sell_price <= 0){
    header("Location: ../pages/sell.php?error=invalid");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM assets WHERE id=? LIMIT 1");
$stmt->execute([$asset_id]);
$asset = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$asset){
    header("Location: ../pages/sell.php?error=asset");
    exit;
}

$currentShares = (float)$asset['shares'];

if($shares_sold > $currentShares){
    header("Location: ../pages/sell.php?error=shares");
    exit;
}

$ticker = strtoupper($asset['ticker']);
$avg_cost = (float)$asset['avg_cost'];

$proceeds = $shares_sold * $sell_price;
$cost_basis = $shares_sold * $avg_cost;
$realized_pl = $proceeds - $cost_basis;

try{
    $pdo->beginTransaction();

    $insert = $pdo->prepare("
        INSERT INTO sales(
            asset_id,ticker,sell_date,shares_sold,sell_price,avg_cost,
            proceeds,cost_basis,realized_pl,note
        )
        VALUES(?,?,?,?,?,?,?,?,?,?)
    ");

    $insert->execute([
        $asset_id,$ticker,$sell_date,$shares_sold,$sell_price,$avg_cost,
        $proceeds,$cost_basis,$realized_pl,$note
    ]);

    $newShares = $currentShares - $shares_sold;

    $update = $pdo->prepare("UPDATE assets SET shares=? WHERE id=?");
    $update->execute([$newShares, $asset_id]);

    $pdo->commit();

    header("Location: ../pages/sell.php?success=1");
    exit;

}catch(Exception $e){
    $pdo->rollBack();
    header("Location: ../pages/sell.php?error=db");
    exit;
}
?>
