<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/api.php';

function getPrice($symbol, $apiKey) {
    $url = "https://finnhub.io/api/v1/quote?symbol={$symbol}&token={$apiKey}";
    $response = @file_get_contents($url);

    if (!$response) {
        return null;
    }

    $data = json_decode($response, true);
    return $data['c'] ?? null;
}

$assets = $pdo->query("SELECT * FROM assets")->fetchAll(PDO::FETCH_ASSOC);
$updated = [];

foreach ($assets as $asset) {
    $ticker = strtoupper(trim($asset['ticker']));
    $price = null;

    if (in_array($ticker, ['VOO','SCHD','QQQ','PLTR','NVDA','TSLA','AAPL','MSFT','AMZN','GOOGL','META'])) {
        $price = getPrice($ticker, $FINNHUB_API_KEY);
    }

    if ($ticker == 'BTC') {
        $price = getPrice('BINANCE:BTCUSDT', $FINNHUB_API_KEY);
    }

    if ($ticker == 'ETH') {
        $price = getPrice('BINANCE:ETHUSDT', $FINNHUB_API_KEY);
    }

    if ($ticker == 'DOGE') {
        $price = getPrice('BINANCE:DOGEUSDT', $FINNHUB_API_KEY);
    }

    if ($price && $price > 0) {
        $stmt = $pdo->prepare("UPDATE assets SET current_price=? WHERE id=?");
        $stmt->execute([$price, $asset['id']]);
        $updated[] = "{$ticker}: {$price}";
    }
}

$redirect = $_GET['redirect'] ?? '';

if ($redirect) {
    header("Location: {$redirect}");
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Precios actualizados</title>
<style>
body{background:#0f172a;color:white;font-family:Arial;padding:30px;}
.box{background:#111827;border:1px solid #334155;border-radius:20px;padding:25px;max-width:700px;}
a{color:#22c55e;font-weight:bold;}
li{margin:8px 0;}
</style>
</head>
<body>
<div class="box">
<h1>Precios actualizados ✅</h1>
<ul>
<?php foreach($updated as $u): ?>
<li><?=htmlspecialchars($u)?></li>
<?php endforeach; ?>
</ul>
<p><a href="../index_v5.php">Volver al Dashboard</a></p>
</div>
</body>
</html>
