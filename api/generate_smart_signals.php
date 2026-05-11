<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$assets = $pdo->query("SELECT * FROM assets ORDER BY ticker ASC")->fetchAll(PDO::FETCH_ASSOC);
$generated = 0;

foreach($assets as $a){

    $assetId = $a['id'];
    $ticker = strtoupper(trim($a['ticker']));
    $current = (float)$a['current_price'];
    $base = (float)$a['base_price'];
    $avg = (float)$a['avg_cost'];

    $history = $pdo->prepare("
        SELECT price, created_at
        FROM price_history
        WHERE asset_id=?
        ORDER BY created_at DESC
        LIMIT 10
    ");
    $history->execute([$assetId]);
    $prices = $history->fetchAll(PDO::FETCH_ASSOC);

    $oldPrice = null;
    if(count($prices) >= 2){
        $oldPrice = (float)$prices[count($prices)-1]['price'];
    }

    $momentum = 0;
    if($oldPrice && $oldPrice > 0){
        $momentum = (($current - $oldPrice) / $oldPrice) * 100;
    }

    $trendScore = 50;
    if($momentum > 5) $trendScore = 80;
    elseif($momentum > 2) $trendScore = 65;
    elseif($momentum < -5) $trendScore = 20;
    elseif($momentum < -2) $trendScore = 35;

    $riskScore = 30;

    if(in_array($ticker, ['BTC','ETH','DOGE'])){
        $riskScore = 75;
    }

    if(in_array($ticker, ['NVDA','PLTR'])){
        $riskScore = 65;
    }

    if(in_array($ticker, ['VOO','SCHD','QQQ'])){
        $riskScore = 35;
    }

    $signal = "HOLD";
    $confidence = 50;
    $note = "Sin señal fuerte. Mantener observación.";

    if($base > 0){
        if($current <= $base * 0.90){
            $signal = "BUY FUERTE";
            $confidence = 85;
            $note = "$ticker está 10% o más debajo del precio base. Posible oportunidad fuerte.";
        } elseif($current <= $base * 0.95){
            $signal = "BUY";
            $confidence = 70;
            $note = "$ticker está 5% o más debajo del precio base. Entrada ligera posible.";
        } elseif($current >= $base * 1.20){
            $signal = "SELL FUERTE";
            $confidence = 80;
            $note = "$ticker está 20% o más arriba del precio base. Considera tomar ganancias.";
        } elseif($current >= $base * 1.10){
            $signal = "SELL";
            $confidence = 65;
            $note = "$ticker está 10% o más arriba del precio base. Revisa toma parcial.";
        }
    }

    if($momentum > 7 && str_contains($signal, "BUY")){
        $confidence -= 10;
        $note .= " Ojo: momentum alto, evita perseguir precio.";
    }

    if($momentum < -7 && str_contains($signal, "SELL")){
        $confidence -= 10;
        $note .= " Ojo: caída fuerte, evita vender por pánico.";
    }

    $confidence = max(0, min(100, $confidence));

    $stmt = $pdo->prepare("
        INSERT INTO smart_signals(
            asset_id,ticker,signal,confidence,momentum,trend_score,risk_score,note
        )
        VALUES(?,?,?,?,?,?,?,?)
    ");

    $stmt->execute([
        $assetId,
        $ticker,
        $signal,
        $confidence,
        round($momentum,2),
        $trendScore,
        $riskScore,
        $note
    ]);

    $generated++;
}

header("Location: ../pages/smart_signals.php?generated=1");
exit;
?>
