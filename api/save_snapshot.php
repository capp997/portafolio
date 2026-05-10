<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$assets = $pdo->query("SELECT * FROM assets ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
$cost = 0;
$crypto = 0;
$etf = 0;
$aggressive = 0;

foreach($assets as $a){
    $shares = (float)$a['shares'];
    $price = (float)$a['current_price'];
    $avg = (float)$a['avg_cost'];

    $value = $shares * $price;
    $total += $value;
    $cost += $shares * $avg;

    if($a['category'] === 'Crypto'){
        $crypto += $value;
    }

    if(str_contains($a['category'], 'ETF') || $a['category'] === 'Dividendos'){
        $etf += $value;
    }

    if($a['category'] === 'Acción agresiva'){
        $aggressive += $value;
    }
}

$pl = $total - $cost;

$stmt = $pdo->prepare("
    INSERT INTO portfolio_history
    (total_value,total_cost,profit_loss,etf_value,crypto_value,aggressive_value)
    VALUES (?,?,?,?,?,?)
");

$stmt->execute([
    round($total,2),
    round($cost,2),
    round($pl,2),
    round($etf,2),
    round($crypto,2),
    round($aggressive,2)
]);

header("Location: ../pages/historial.php?saved=1");
exit;
?>
