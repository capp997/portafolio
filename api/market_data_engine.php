<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/api.php";

/*
MARKET DATA ENGINE
- Actualiza precios reales
- Guarda historial de precios
- Ejecuta escaneo de alertas después
*/

function getFinnhubQuote($symbol, $apiKey){
    $url = "https://finnhub.io/api/v1/quote?symbol=" . urlencode($symbol) . "&token=" . urlencode($apiKey);
    $json = @file_get_contents($url);

    if(!$json){
        return null;
    }

    $data = json_decode($json, true);

    if(!isset($data['c']) || (float)$data['c'] <= 0){
        return null;
    }

    return (float)$data['c'];
}

function mapSymbol($ticker){
    $ticker = strtoupper(trim($ticker));

    $crypto = [
        "BTC" => "BINANCE:BTCUSDT",
        "ETH" => "BINANCE:ETHUSDT",
        "DOGE" => "BINANCE:DOGEUSDT"
    ];

    return $crypto[$ticker] ?? $ticker;
}

$assets = $pdo->query("SELECT * FROM assets ORDER BY ticker ASC")->fetchAll(PDO::FETCH_ASSOC);

$results = [];
$updated = 0;
$failed = 0;

foreach($assets as $asset){

    $ticker = strtoupper(trim($asset['ticker']));
    $symbol = mapSymbol($ticker);

    $price = getFinnhubQuote($symbol, $FINNHUB_API_KEY);

    if($price && $price > 0){

        $stmt = $pdo->prepare("UPDATE assets SET current_price=? WHERE id=?");
        $stmt->execute([$price, $asset['id']]);

        try{
            $hist = $pdo->prepare("
                INSERT INTO price_history(asset_id,ticker,price,source)
                VALUES(?,?,?,?)
            ");
            $hist->execute([$asset['id'], $ticker, $price, "finnhub"]);
        }catch(Exception $e){}

        $results[] = [
            "ticker" => $ticker,
            "price" => $price,
            "status" => "updated"
        ];

        $updated++;

    } else {

        $results[] = [
            "ticker" => $ticker,
            "price" => null,
            "status" => "failed"
        ];

        $failed++;
    }
}

/* Escanear alertas automáticamente si existe */
$scanPath = __DIR__ . "/scan_alerts.php";
if(file_exists($scanPath)){
    // No redirigimos; solo reutilizamos lógica básica aquí para evitar salida HTML.
    try{
        $freshAssets = $pdo->query("SELECT * FROM assets ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

        foreach($freshAssets as $a){
            $base = (float)$a['base_price'];
            $current = (float)$a['current_price'];
            $ticker = $a['ticker'];

            if($base <= 0 || $current <= 0) continue;

            $type = null;
            $message = null;

            if($current <= $base * 0.90){
                $type = "BUY FUERTE";
                $message = "$ticker cayó 10% o más. Zona de compra fuerte.";
            } elseif($current <= $base * 0.95){
                $type = "BUY";
                $message = "$ticker cayó 5% o más. Zona de compra ligera.";
            } elseif($current >= $base * 1.20){
                $type = "SELL FUERTE";
                $message = "$ticker subió 20% o más. Zona de tomar ganancias fuertes.";
            } elseif($current >= $base * 1.10){
                $type = "SELL";
                $message = "$ticker subió 10% o más. Posible toma parcial.";
            }

            if($type){
                $check = $pdo->prepare("SELECT id FROM smart_alerts WHERE asset_id=? AND alert_type=? AND is_reviewed=0 LIMIT 1");
                $check->execute([$a['id'], $type]);

                if(!$check->fetch()){
                    $insert = $pdo->prepare("
                        INSERT INTO smart_alerts(asset_id,ticker,alert_type,message,price,base_price)
                        VALUES(?,?,?,?,?,?)
                    ");
                    $insert->execute([$a['id'], $ticker, $type, $message, $current, $base]);
                }
            }
        }
    }catch(Exception $e){}
}

$redirect = $_GET['redirect'] ?? '';

if($redirect){
    header("Location: " . $redirect);
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Market Data Engine</title>
<link rel="stylesheet" href="../assets/market_data_engine.css">
</head>
<body>

<div class="engine-wrap">
<div class="engine-card">

<h1>Market Data Engine ✅</h1>
<p>Actualización completada con precios reales y registro histórico.</p>

<div class="engine-summary">
    <div><span>Actualizados</span><strong><?=$updated?></strong></div>
    <div><span>Fallidos</span><strong><?=$failed?></strong></div>
</div>

<div class="engine-list">
<?php foreach($results as $r): ?>
    <div class="engine-row <?=$r['status']=='updated'?'ok':'bad'?>">
        <strong><?=$r['ticker']?></strong>
        <span><?=$r['status']=='updated' ? '$'.number_format($r['price'], 4) : 'No data'?></span>
        <small><?=$r['status']?></small>
    </div>
<?php endforeach; ?>
</div>

<a class="engine-btn" href="../index_v5.php">Volver al Dashboard</a>

</div>
</div>

</body>
</html>
