<?php
require_once __DIR__ . "/../config/menu.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$assets = $pdo->query("SELECT * FROM assets ORDER BY ticker ASC")->fetchAll(PDO::FETCH_ASSOC);

$total = 0; $totalCost = 0; $annualDiv = 0;
$categoryTotals = ["ETF/Base"=>0,"Crypto"=>0,"Agresivas"=>0,"Dividendos"=>0,"Otros"=>0];
$allocationLabels = []; $allocationValues = []; $rows = [];

foreach($assets as $a){
    $shares = (float)$a['shares'];
    $price = (float)$a['current_price'];
    $avg = (float)$a['avg_cost'];
    $value = $shares * $price;
    $cost = $shares * $avg;
    $pl = $value - $cost;
    $plPct = $cost > 0 ? ($pl/$cost)*100 : 0;
    $ticker = strtoupper($a['ticker']);
    $cat = $a['category'];

    $total += $value;
    $totalCost += $cost;

    if(in_array($ticker, ['BTC','ETH','DOGE']) || $cat === 'Crypto') $categoryTotals["Crypto"] += $value;
    elseif(str_contains($cat, 'ETF')) $categoryTotals["ETF/Base"] += $value;
    elseif($cat === 'Acción agresiva') $categoryTotals["Agresivas"] += $value;
    elseif($cat === 'Dividendos') $categoryTotals["Dividendos"] += $value;
    else $categoryTotals["Otros"] += $value;

    if($value > 0){ $allocationLabels[]=$ticker; $allocationValues[]=round($value,2); }

    $rows[] = ["ticker"=>$ticker,"name"=>$a['name'],"category"=>$cat,"value"=>$value,"cost"=>$cost,"pl"=>$pl,"plPct"=>$plPct];
}

$totalPL = $total - $totalCost;
$totalPLPct = $totalCost > 0 ? ($totalPL/$totalCost)*100 : 0;

$gainRows = $rows; usort($gainRows, fn($a,$b)=>$b['plPct']<=>$a['plPct']); $topGainers=array_slice($gainRows,0,5);
$lossRows = $rows; usort($lossRows, fn($a,$b)=>$a['plPct']<=>$b['plPct']); $topLosers=array_slice($lossRows,0,5);

$historyLabels=[]; $historyValues=[];
try{
    $history=$pdo->query("SELECT total_value, created_at FROM portfolio_history ORDER BY created_at ASC LIMIT 120")->fetchAll(PDO::FETCH_ASSOC);
    foreach($history as $h){ $historyLabels[]=date("M d", strtotime($h['created_at'])); $historyValues[]=(float)$h['total_value']; }
}catch(Exception $e){}

try{
    $divRows=$pdo->query("SELECT a.shares, ds.annual_dividend_per_share FROM dividend_settings ds JOIN assets a ON a.id=ds.asset_id")->fetchAll(PDO::FETCH_ASSOC);
    foreach($divRows as $d){ $annualDiv += (float)$d['shares'] * (float)$d['annual_dividend_per_share']; }
}catch(Exception $e){}

$monthlyDiv=$annualDiv/12;
$yield=$total>0?($annualDiv/$total)*100:0;

function money($n){ return '$'.number_format((float)$n,2); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Advanced Analytics</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../assets/style_v5.css">
<link rel="stylesheet" href="../assets/unified_pages.css">
<link rel="stylesheet" href="../assets/menu_dropdown.css">
<link rel="stylesheet" href="../assets/advanced_analytics.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="../assets/sidebar_buttons_fix.css">
<link rel="stylesheet" href="../assets/mobile_premium.css">
</head>
<body>
<div class="layout">
<?php render_sidebar('advanced_analytics', '../'); ?>

<main class="content">
<section class="analytics-hero">
<div><p>Advanced Portfolio Analytics</p><h1>Panel de análisis avanzado</h1><span>Rendimiento, asignación, dividendos y exposición visual.</span></div>
<a href="../api/market_data_engine.php?redirect=../pages/advanced_analytics.php" class="analytics-btn">Actualizar datos</a>
</section>

<section class="analytics-cards">
<div class="analytics-card glow"><span>Valor total</span><h2><?=money($total)?></h2></div>
<div class="analytics-card"><span>Ganancia / Pérdida</span><h2 class="<?=$totalPL>=0?'green':'red'?>"><?=money($totalPL)?></h2><small><?=number_format($totalPLPct,2)?>%</small></div>
<div class="analytics-card"><span>Dividendos estimados</span><h2 class="green"><?=money($monthlyDiv)?>/mes</h2><small><?=money($annualDiv)?> anual</small></div>
<div class="analytics-card"><span>Yield estimado</span><h2><?=number_format($yield,2)?>%</h2></div>
</section>

<section class="analytics-grid">
<div class="analytics-panel"><div class="panel-title"><h2>Allocation por activo</h2><span>Asset allocation</span></div><div class="chart-box"><canvas id="allocationChart"></canvas></div></div>
<div class="analytics-panel"><div class="panel-title"><h2>Distribución por categoría</h2><span>Risk exposure</span></div><div class="chart-box"><canvas id="categoryChart"></canvas></div></div>
</section>

<section class="analytics-panel"><div class="panel-title"><h2>Historial del portafolio</h2><span>Performance</span></div><div class="history-box"><canvas id="historyChart"></canvas></div></section>

<section class="analytics-grid">
<div class="analytics-panel"><div class="panel-title"><h2>Top Gainers</h2><span>Mejor rendimiento</span></div><div class="rank-list">
<?php foreach($topGainers as $r): ?><div class="rank-row"><div><strong><?=$r['ticker']?></strong><small><?=htmlspecialchars($r['name'])?></small></div><span class="green">▲ <?=number_format($r['plPct'],2)?>%</span><b><?=money($r['pl'])?></b></div><?php endforeach; ?>
</div></div>
<div class="analytics-panel"><div class="panel-title"><h2>Top Losers</h2><span>Revisar riesgo</span></div><div class="rank-list">
<?php foreach($topLosers as $r): ?><div class="rank-row"><div><strong><?=$r['ticker']?></strong><small><?=htmlspecialchars($r['name'])?></small></div><span class="red">▼ <?=number_format(abs($r['plPct']),2)?>%</span><b><?=money($r['pl'])?></b></div><?php endforeach; ?>
</div></div>
</section>

<section class="analytics-panel"><div class="panel-title"><h2>Heatmap de activos</h2><span>Visual P/L</span></div><div class="heatmap">
<?php foreach($rows as $r): ?><div class="heat-tile <?=$r['pl']>=0?'heat-green':'heat-red'?>"><strong><?=$r['ticker']?></strong><span><?=money($r['value'])?></span><small><?=number_format($r['plPct'],2)?>%</small></div><?php endforeach; ?>
</div></section>
</main>
</div>

<script>
new Chart(document.getElementById('allocationChart'),{type:'doughnut',data:{labels:<?=json_encode($allocationLabels)?>,datasets:[{data:<?=json_encode($allocationValues)?>,borderWidth:0,cutout:'68%'}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{labels:{color:'#fff'}}}}});
new Chart(document.getElementById('categoryChart'),{type:'bar',data:{labels:<?=json_encode(array_keys($categoryTotals))?>,datasets:[{data:<?=json_encode(array_values($categoryTotals))?>,borderWidth:0}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{ticks:{color:'#94a3b8'},grid:{display:false}},y:{ticks:{color:'#94a3b8'},grid:{color:'rgba(148,163,184,.12)'}}}}});
new Chart(document.getElementById('historyChart'),{type:'line',data:{labels:<?=json_encode($historyLabels)?>,datasets:[{label:'Valor total',data:<?=json_encode($historyValues)?>,borderColor:'#22c55e',backgroundColor:'rgba(34,197,94,.12)',fill:true,tension:.35,borderWidth:3,pointRadius:0}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{labels:{color:'#fff'}}},scales:{x:{ticks:{color:'#94a3b8'},grid:{display:false}},y:{ticks:{color:'#94a3b8'},grid:{color:'rgba(148,163,184,.12)'}}}}});
</script>
<script src="../assets/menu_dropdown.js"></script>
<script src="../assets/mobile_premium.js"></script>
</body>
</html>
