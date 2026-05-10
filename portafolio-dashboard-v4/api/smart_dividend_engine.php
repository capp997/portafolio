<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/api.php";

/*
SMART DIVIDEND ENGINE
1. Intenta Finnhub.
2. Si no encuentra datos, usa estimados inteligentes.
3. Evita que Dividend Tracker quede vacío.
*/

$manualEstimates = [
    "SCHD" => [
        "annual" => 1.00,
        "frequency" => "Quarterly",
        "next" => "2026-06-25",
        "note" => "Smart estimate: SCHD quarterly dividend"
    ],
    "VOO" => [
        "annual" => 6.80,
        "frequency" => "Quarterly",
        "next" => "2026-06-30",
        "note" => "Smart estimate: VOO quarterly dividend"
    ],
    "QQQ" => [
        "annual" => 2.50,
        "frequency" => "Quarterly",
        "next" => "2026-06-30",
        "note" => "Smart estimate: QQQ quarterly dividend"
    ],
    "NVDA" => [
        "annual" => 0.04,
        "frequency" => "Quarterly",
        "next" => "2026-06-30",
        "note" => "Smart estimate: NVDA low dividend"
    ],
    "PLTR" => [
        "annual" => 0,
        "frequency" => "None",
        "next" => null,
        "note" => "No dividend"
    ],
    "BTC" => [
        "annual" => 0,
        "frequency" => "None",
        "next" => null,
        "note" => "Crypto does not pay dividend"
    ],
    "ETH" => [
        "annual" => 0,
        "frequency" => "None",
        "next" => null,
        "note" => "Crypto does not pay dividend"
    ],
    "DOGE" => [
        "annual" => 0,
        "frequency" => "None",
        "next" => null,
        "note" => "Crypto does not pay dividend"
    ]
];

function fetchDividendData($symbol, $apiKey){

    $from = date("Y-m-d", strtotime("-60 days"));
    $to   = date("Y-m-d", strtotime("+365 days"));

    $url = "https://finnhub.io/api/v1/calendar/dividend?symbol={$symbol}&from={$from}&to={$to}&token={$apiKey}";

    $json = @file_get_contents($url);

    if(!$json){
        return null;
    }

    $data = json_decode($json, true);

    if(!isset($data['dividendCalendar']) || !is_array($data['dividendCalendar'])){
        return null;
    }

    if(count($data['dividendCalendar']) === 0){
        return null;
    }

    return $data['dividendCalendar'][0];
}

function upsertDividendSetting($pdo, $assetId, $annual, $frequency, $nextPayDate, $note){

    $exists = $pdo->prepare("SELECT id FROM dividend_settings WHERE asset_id=?");
    $exists->execute([$assetId]);

    if($exists->fetch()){

        $stmt = $pdo->prepare("
            UPDATE dividend_settings
            SET annual_dividend_per_share=?,
                payment_frequency=?,
                next_pay_date=?,
                notes=?
            WHERE asset_id=?
        ");

        $stmt->execute([
            $annual,
            $frequency,
            $nextPayDate,
            $note,
            $assetId
        ]);

    } else {

        $stmt = $pdo->prepare("
            INSERT INTO dividend_settings(
                asset_id,
                annual_dividend_per_share,
                payment_frequency,
                next_pay_date,
                notes
            )
            VALUES(?,?,?,?,?)
        ");

        $stmt->execute([
            $assetId,
            $annual,
            $frequency,
            $nextPayDate,
            $note
        ]);
    }
}

$assets = $pdo->query("SELECT id,ticker,name FROM assets ORDER BY ticker")->fetchAll(PDO::FETCH_ASSOC);

$results = [];

foreach($assets as $asset){

    $ticker = strtoupper(trim($asset['ticker']));
    $source = "None";
    $annual = 0;
    $frequency = "None";
    $next = null;
    $note = "No dividend data";

    // Crypto and no-dividend assets first
    if(isset($manualEstimates[$ticker]) && $manualEstimates[$ticker]['annual'] == 0){
        $annual = $manualEstimates[$ticker]['annual'];
        $frequency = $manualEstimates[$ticker]['frequency'];
        $next = $manualEstimates[$ticker]['next'];
        $note = $manualEstimates[$ticker]['note'];
        $source = "Smart Estimate";
    } else {

        // Try Finnhub
        $dividend = fetchDividendData($ticker, $FINNHUB_API_KEY);

        if($dividend && isset($dividend['amount']) && (float)$dividend['amount'] > 0){

            $amount = (float)$dividend['amount'];
            $payDate = $dividend['payDate'] ?? null;

            $annual = $amount * 4;
            $frequency = "Quarterly";
            $next = $payDate;
            $note = "Auto synced from Finnhub";
            $source = "Finnhub";

        } elseif(isset($manualEstimates[$ticker])) {

            $annual = $manualEstimates[$ticker]['annual'];
            $frequency = $manualEstimates[$ticker]['frequency'];
            $next = $manualEstimates[$ticker]['next'];
            $note = $manualEstimates[$ticker]['note'];
            $source = "Smart Estimate";

        } else {

            $annual = 0;
            $frequency = "Unknown";
            $next = null;
            $note = "No estimate available";
            $source = "No Data";
        }
    }

    upsertDividendSetting($pdo, $asset['id'], $annual, $frequency, $next, $note);

    $results[] = [
        "ticker" => $ticker,
        "annual" => $annual,
        "frequency" => $frequency,
        "next" => $next,
        "source" => $source
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Smart Dividend Engine</title>
<link rel="stylesheet" href="../assets/smart_dividend_engine.css">
</head>

<body>

<div class="engine-wrap">

<div class="engine-card">

<h1>Smart Dividend Engine ✅</h1>
<p>Se actualizaron dividendos usando API cuando fue posible y estimados inteligentes cuando no hubo datos.</p>

<div class="engine-grid">

<?php foreach($results as $r): ?>

<div class="engine-item">

<div>
<strong><?=$r['ticker']?></strong>
<span><?=$r['source']?></span>
</div>

<div>
<b>$<?=number_format((float)$r['annual'],2)?></b>
<small>annual/share</small>
</div>

<div>
<b><?=$r['frequency']?></b>
<small>frecuencia</small>
</div>

<div>
<b><?=$r['next'] ?: 'N/A'?></b>
<small>próximo pago</small>
</div>

</div>

<?php endforeach; ?>

</div>

<a href="../pages/dividend_tracker.php" class="engine-btn">
Volver a Dividend Tracker
</a>

</div>

</div>

</body>
</html>
