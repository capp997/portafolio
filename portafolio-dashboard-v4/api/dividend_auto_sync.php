<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/api.php";

function fetchDividendData($symbol, $apiKey){
    $from = date("Y-m-d", strtotime("-30 days"));
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

$assets = $pdo->query("SELECT id,ticker FROM assets ORDER BY ticker")->fetchAll(PDO::FETCH_ASSOC);

$updated = [];

foreach($assets as $a){

    $ticker = strtoupper(trim($a['ticker']));

    if(in_array($ticker,['BTC','ETH','DOGE'])){
        continue;
    }

    $dividend = fetchDividendData($ticker, $FINNHUB_API_KEY);

    if(!$dividend){
        continue;
    }

    $amount = isset($dividend['amount']) ? (float)$dividend['amount'] : 0;
    $payDate = $dividend['payDate'] ?? null;

    $annualEstimate = $amount * 4;

    $exists = $pdo->prepare("SELECT id FROM dividend_settings WHERE asset_id=?");
    $exists->execute([$a['id']]);

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
            $annualEstimate,
            'Quarterly',
            $payDate,
            'Auto synced from Finnhub',
            $a['id']
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
            $a['id'],
            $annualEstimate,
            'Quarterly',
            $payDate,
            'Auto synced from Finnhub'
        ]);
    }

    $updated[] = [
        'ticker'=>$ticker,
        'amount'=>$amount,
        'pay'=>$payDate
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Dividend Auto Sync</title>
<style>
body{background:#020617;color:white;font-family:Arial;padding:40px;}
.box{max-width:900px;margin:auto;background:#0b1220;border:1px solid #1e293b;border-radius:28px;padding:28px;}
.item{background:#020617;border:1px solid #334155;padding:16px;border-radius:18px;margin-bottom:12px;display:flex;justify-content:space-between;gap:12px;}
.tag{background:#052e16;color:#86efac;padding:6px 10px;border-radius:999px;font-size:12px;font-weight:bold;}
a{display:inline-block;margin-top:20px;background:#16a34a;color:white;padding:12px 18px;border-radius:14px;text-decoration:none;font-weight:bold;}
</style>
</head>
<body>
<div class="box">
<h1>Dividend Auto Sync ✅</h1>

<?php if(count($updated)>0): ?>
<?php foreach($updated as $u): ?>
<div class="item">
<div>
<strong><?=$u['ticker']?></strong>
<div>Próximo pago: <?=$u['pay'] ?: 'N/A'?></div>
</div>
<div class="tag">~$<?=number_format($u['amount'],2)?> trimestral</div>
</div>
<?php endforeach; ?>
<?php else: ?>
<p>No se encontraron dividendos automáticos.</p>
<?php endif; ?>

<a href="../pages/dividend_tracker.php">Volver a Dividend Tracker</a>

</div>
</body>
</html>
