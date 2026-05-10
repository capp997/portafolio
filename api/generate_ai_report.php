<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$assets = $pdo->query("SELECT * FROM assets")->fetchAll(PDO::FETCH_ASSOC);

$totalValue = 0;
$crypto = 0;
$stocks = 0;

foreach($assets as $a){
    $value = ((float)$a['quantity']) * ((float)$a['current_price']);
    $totalValue += $value;

    $ticker = strtoupper($a['ticker']);

    if(in_array($ticker, ['BTC','ETH','DOGE'])){
        $crypto += $value;
    } else {
        $stocks += $value;
    }
}

$cryptoPct = $totalValue > 0 ? ($crypto / $totalValue) * 100 : 0;
$stocksPct = $totalValue > 0 ? ($stocks / $totalValue) * 100 : 0;

$score = 50;
$risk = "Moderado";
$recommendation = "Portafolio balanceado.";

if($cryptoPct > 60){
    $score -= 15;
    $risk = "Alto";
    $recommendation = "Demasiada exposición a criptomonedas.";
}

if($stocksPct > 70){
    $score += 10;
}

if(count($assets) >= 5){
    $score += 15;
}

if(count($assets) <= 2){
    $score -= 10;
}

if($score >= 80){
    $recommendation = "Excelente diversificación y balance.";
} elseif($score >= 65){
    $recommendation = "Buen portafolio con margen de mejora.";
}

$diversification = min(100, count($assets) * 15);

$stmt = $pdo->prepare("
INSERT INTO portfolio_ai_reports(
portfolio_score,
risk_level,
diversification_score,
recommendation
)
VALUES(?,?,?,?)
");

$stmt->execute([
    $score,
    $risk,
    $diversification,
    $recommendation
]);

header("Location: ../pages/ai_portfolio_advisor.php?generated=1");
exit;
?>
