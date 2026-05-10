<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$rows = $pdo->query("
SELECT * FROM portfolio_history
ORDER BY created_at ASC
")->fetchAll(PDO::FETCH_ASSOC);

$labels = [];
$values = [];

foreach($rows as $r){
    $labels[] = date("M d", strtotime($r['created_at']));
    $values[] = (float)$r['total_value'];
}

$latest = end($values) ?: 0;
$first = reset($values) ?: 0;

$change = $latest - $first;
$changePct = $first > 0 ? ($change / $first) * 100 : 0;

function money($n){
    return '$'.number_format((float)$n,2);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Historial Advanced</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="../assets/style_v5.css">
<link rel="stylesheet" href="../assets/advanced_history.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="../assets/unified_pages.css">
<link rel="stylesheet" href="../assets/action_buttons.css">
<link rel="stylesheet" href="../assets/mobile_premium.css">
</head>

<body>

<div class="layout">

<aside class="sidebar">

<div>
<div class="brand">
<div class="logo">📈</div>

<div>
<h1>Portafolio V5</h1>
<p>Advanced History</p>
</div>
</div>

<nav>
            <a class="" href="../index_v5.php">Dashboard</a>
            <a class="" href="activos.php">Activos</a>
            <a class="" href="alertas.php">Alertas</a>
            <a class="" href="centro_alertas.php">Centro Alertas</a>
            <a class="" href="compras.php">Compras</a>
            <a class="" href="dividendos.php">Dividendos</a>
            <a class="" href="dividend_tracker.php">Dividend Tracker</a>
            <a class="" href="rebalanceo.php">Rebalanceo</a>
            <a class="" href="rutinas.php">Rutinas</a>
            <a class="" href="metas.php">Metas</a>
            <a class="" href="historial.php">Historial</a>
            <a class="active" href="historial_avanzado.php">Historial Pro</a>
        </nav>
</div>

<div class="sidebar-footer">
        <a class="side-btn green" href="../api/update_prices.php?redirect=../index_v5.php">📈 Actualizar precios</a>
        <a class="side-btn blue" href="../api/save_snapshot.php">💾 Guardar snapshot</a>
        <a class="side-btn orange" href="../api/scan_alerts.php">🔔 Escanear alertas</a>
        <a class="side-btn green" href="../api/smart_dividend_engine.php">💰 Dividend Engine</a>
        <a class="side-btn red" href="../api/logout.php">Cerrar sesión</a>
    </div>
</aside>

<main class="content">

<section class="advanced-hero">

<div>
<p class="hero-label">Portfolio Value</p>

<h1><?=money($latest)?></h1>

<div class="hero-change <?=$change>=0?'positive':'negative'?>">
<?= $change>=0 ? '▲' : '▼' ?>
<?=money(abs($change))?>
(<?=number_format(abs($changePct),2)?>%)
</div>
</div>

<div class="range-selector">
<button class="active">1M</button>
<button>3M</button>
<button>6M</button>
<button>1Y</button>
<button>ALL</button>
</div>

</section>

<section class="advanced-chart-panel">

<div class="chart-top">
<div>
<h2>Crecimiento histórico</h2>
<p>Evolución de tu portafolio</p>
</div>

<div class="chart-badge">
Live Portfolio
</div>
</div>

<div class="advanced-chart-wrap">
<canvas id="advancedChart"></canvas>
</div>

</section>

<section class="stats-grid">

<div class="stat-card">
<span>Total actual</span>
<h3><?=money($latest)?></h3>
</div>

<div class="stat-card">
<span>Cambio total</span>
<h3 class="<?=$change>=0?'green':'red'?>">
<?=money($change)?>
</h3>
</div>

<div class="stat-card">
<span>Rendimiento</span>
<h3 class="<?=$change>=0?'green':'red'?>">
<?=number_format($changePct,2)?>%
</h3>
</div>

<div class="stat-card">
<span>Snapshots</span>
<h3><?=count($rows)?></h3>
</div>

</section>

<section class="advanced-table-panel">

<div class="table-header">
<h2>Historial de snapshots</h2>
<span>Timeline</span>
</div>

<div class="table-wrap">

<table>
<thead>
<tr>
<th>Fecha</th>
<th>Valor total</th>
<th>Ganancia/Pérdida</th>
</tr>
</thead>

<tbody>

<?php foreach(array_reverse($rows) as $r): ?>

<tr>
<td><?=date("M d, Y h:i A", strtotime($r['created_at']))?></td>

<td>
<?=money($r['total_value'])?>
</td>

<td class="<?=$r['profit_loss']>=0?'green':'red'?>">
<?=money($r['profit_loss'])?>
</td>
</tr>

<?php endforeach; ?>

</tbody>
</table>

</div>

</section>

</main>
</div>

<script>

const ctx = document.getElementById('advancedChart');

const gradient = ctx.getContext('2d').createLinearGradient(0,0,0,400);

gradient.addColorStop(0,'rgba(34,197,94,.45)');
gradient.addColorStop(1,'rgba(34,197,94,0)');

new Chart(ctx, {

type:'line',

data:{
labels:<?=json_encode($labels)?>,

datasets:[{
label:'Portfolio',
data:<?=json_encode($values)?>,
borderColor:'#22c55e',
backgroundColor:gradient,
fill:true,
tension:.4,
borderWidth:4,
pointRadius:0,
pointHoverRadius:6
}]
},

options:{
responsive:true,
maintainAspectRatio:false,

plugins:{
legend:{
display:false
}
},

scales:{
x:{
ticks:{
color:'#94a3b8'
},
grid:{
display:false
}
},

y:{
ticks:{
color:'#94a3b8'
},
grid:{
color:'rgba(148,163,184,.08)'
}
}
}
}

});

</script>

<script src="../assets/mobile_premium.js"></script>
</body>
</html>
