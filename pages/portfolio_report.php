<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$assets = $pdo->query("SELECT * FROM assets ORDER BY ticker ASC")->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
$cost = 0;
$crypto = 0;
$etf = 0;
$aggressive = 0;

foreach($assets as $a){
    $value = (float)$a['shares'] * (float)$a['current_price'];
    $assetCost = (float)$a['shares'] * (float)$a['avg_cost'];

    $total += $value;
    $cost += $assetCost;

    $ticker = strtoupper($a['ticker']);

    if(in_array($ticker, ['BTC','ETH','DOGE']) || $a['category'] === 'Crypto'){
        $crypto += $value;
    }elseif(str_contains($a['category'], 'ETF') || $a['category'] === 'Dividendos'){
        $etf += $value;
    }elseif($a['category'] === 'Acción agresiva'){
        $aggressive += $value;
    }
}

$pl = $total - $cost;
$plPct = $cost > 0 ? ($pl / $cost) * 100 : 0;

$salesTotal = 0;
try{
    $salesTotal = $pdo->query("SELECT COALESCE(SUM(realized_pl),0) FROM sales")->fetchColumn();
}catch(Exception $e){}

$annualDiv = 0;
try{
    $divRows = $pdo->query("
        SELECT a.shares, ds.annual_dividend_per_share
        FROM dividend_settings ds
        JOIN assets a ON a.id = ds.asset_id
    ")->fetchAll(PDO::FETCH_ASSOC);

    foreach($divRows as $d){
        $annualDiv += (float)$d['shares'] * (float)$d['annual_dividend_per_share'];
    }
}catch(Exception $e){}

$monthlyDiv = $annualDiv / 12;

$latestSignals = [];
try{
    $latestSignals = $pdo->query("
        SELECT DISTINCT ON (ticker) *
        FROM smart_signals
        ORDER BY ticker, created_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
}catch(Exception $e){}

function money($n){
    return '$'.number_format((float)$n,2);
}

function numberFlex($n){
    return rtrim(rtrim(number_format((float)$n,8), '0'), '.');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Portfolio Report</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="../assets/report_center.css">
<link rel="stylesheet" href="../assets/menu_unified_full.css">
</head>
<body>

<div class="report-wrap">

<div class="report-actions no-print">
<a href="report_center.php">← Volver</a>
<button onclick="window.print()">🖨️ Imprimir / Guardar PDF</button>
</div>

<header class="report-header">
<div>
<h1>Portfolio V5 Report</h1>
<p>Reporte generado: <?=date("M d, Y h:i A")?></p>
</div>
<div class="report-badge">Financial Dashboard</div>
</header>

<section class="report-kpis">
<div>
<span>Valor total</span>
<strong><?=money($total)?></strong>
</div>

<div>
<span>Ganancia/Pérdida</span>
<strong class="<?=$pl>=0?'green':'red'?>"><?=money($pl)?></strong>
<small><?=number_format($plPct,2)?>%</small>
</div>

<div>
<span>P/L realizado</span>
<strong class="<?=$salesTotal>=0?'green':'red'?>"><?=money($salesTotal)?></strong>
</div>

<div>
<span>Dividendos estimados</span>
<strong><?=money($monthlyDiv)?>/mes</strong>
<small><?=money($annualDiv)?> anual</small>
</div>
</section>

<section class="report-section">
<h2>Resumen de exposición</h2>

<div class="exposure-grid">
<div>
<span>ETF/Base</span>
<strong><?=money($etf)?></strong>
</div>
<div>
<span>Crypto</span>
<strong><?=money($crypto)?></strong>
</div>
<div>
<span>Acciones agresivas</span>
<strong><?=money($aggressive)?></strong>
</div>
</div>
</section>

<section class="report-section">
<h2>Activos actuales</h2>

<table>
<thead>
<tr>
<th>Ticker</th>
<th>Nombre</th>
<th>Categoría</th>
<th>Shares</th>
<th>Precio</th>
<th>Valor</th>
<th>P/L</th>
</tr>
</thead>
<tbody>
<?php foreach($assets as $a): 
$value = (float)$a['shares'] * (float)$a['current_price'];
$basis = (float)$a['shares'] * (float)$a['avg_cost'];
$rowPl = $value - $basis;
?>
<tr>
<td><b><?=$a['ticker']?></b></td>
<td><?=htmlspecialchars($a['name'])?></td>
<td><?=htmlspecialchars($a['category'])?></td>
<td><?=numberFlex($a['shares'])?></td>
<td><?=money($a['current_price'])?></td>
<td><?=money($value)?></td>
<td class="<?=$rowPl>=0?'green':'red'?>"><?=money($rowPl)?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</section>

<section class="report-section">
<h2>Últimas señales inteligentes</h2>

<table>
<thead>
<tr>
<th>Ticker</th>
<th>Señal</th>
<th>Confianza</th>
<th>Riesgo</th>
<th>Nota</th>
</tr>
</thead>
<tbody>
<?php foreach($latestSignals as $s): ?>
<tr>
<td><b><?=$s['ticker']?></b></td>
<td><?=$s['signal']?></td>
<td><?=$s['confidence']?>%</td>
<td><?=$s['risk_score']?>%</td>
<td><?=htmlspecialchars($s['note'])?></td>
</tr>
<?php endforeach; ?>

<?php if(count($latestSignals)===0): ?>
<tr><td colspan="5">No hay señales generadas todavía.</td></tr>
<?php endif; ?>
</tbody>
</table>
</section>

<footer class="report-footer">
<p>Este reporte es informativo y no constituye asesoría financiera.</p>
</footer>

</div>

<script src="../assets/menu_dropdown.js"></script>
</body>
</html>
