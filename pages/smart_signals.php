<?php
require_once __DIR__ . "/../config/menu.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$signals = $pdo->query("
    SELECT DISTINCT ON (ticker) *
    FROM smart_signals
    ORDER BY ticker, created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

$all = $pdo->query("
    SELECT *
    FROM smart_signals
    ORDER BY created_at DESC
    LIMIT 50
")->fetchAll(PDO::FETCH_ASSOC);

function signalClass($s){
    if(str_contains($s, 'BUY')) return 'sig-buy';
    if(str_contains($s, 'SELL')) return 'sig-sell';
    return 'sig-hold';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Smart Signals</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="../assets/style_v5.css">
<link rel="stylesheet" href="../assets/unified_pages.css">
<link rel="stylesheet" href="../assets/menu_dropdown.css">
<link rel="stylesheet" href="../assets/smart_signals.css">
<link rel="stylesheet" href="../assets/global_page_fix.css">
<link rel="stylesheet" href="../assets/menu_unified_full.css">
</head>

<body>

<div class="layout">
<?php render_sidebar('smart_signals', '../'); ?>
<main class="content">

<section class="signals-hero">
<div>
<p>Smart Signals Engine</p>
<h1>Señales inteligentes BUY / HOLD / SELL</h1>
<span>Basado en precio base, momentum, riesgo y tendencia.</span>
</div>

<a class="signals-btn" href="../api/generate_smart_signals.php">Generar señales</a>
</section>

<?php if(isset($_GET['generated'])): ?>
<div class="signals-success">Señales generadas correctamente ✅</div>
<?php endif; ?>

<section class="signals-grid">
<?php foreach($signals as $s): ?>
<div class="signal-card <?=signalClass($s['signal'])?>">
<div class="signal-top">
<strong><?=$s['ticker']?></strong>
<span><?=$s['signal']?></span>
</div>

<div class="signal-score">
<div>
<small>Confianza</small>
<b><?=$s['confidence']?>%</b>
</div>
<div>
<small>Momentum</small>
<b><?=number_format($s['momentum'],2)?>%</b>
</div>
</div>

<div class="signal-bars">
<label>Trend</label>
<div><span style="width:<?=$s['trend_score']?>%;"></span></div>

<label>Risk</label>
<div><span class="risk" style="width:<?=$s['risk_score']?>%;"></span></div>
</div>

<p><?=htmlspecialchars($s['note'])?></p>
</div>
<?php endforeach; ?>

<?php if(count($signals)===0): ?>
<div class="empty-signals">No hay señales todavía. Presiona “Generar señales”.</div>
<?php endif; ?>
</section>

<section class="panel">
<div class="table-header">
<h2>Historial reciente de señales</h2>
<span>Últimos 50 registros</span>
</div>

<div class="table-wrap">
<table>
<thead>
<tr>
<th>Ticker</th>
<th>Señal</th>
<th>Confianza</th>
<th>Momentum</th>
<th>Riesgo</th>
<th>Fecha</th>
</tr>
</thead>
<tbody>
<?php foreach($all as $s): ?>
<tr>
<td><b><?=$s['ticker']?></b></td>
<td><span class="<?=signalClass($s['signal'])?> mini-sig"><?=$s['signal']?></span></td>
<td><?=$s['confidence']?>%</td>
<td><?=number_format($s['momentum'],2)?>%</td>
<td><?=$s['risk_score']?>%</td>
<td><?=date("M d, h:i A", strtotime($s['created_at']))?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</section>

</main>
</div>

<script src="../assets/menu_dropdown.js"></script>
</body>
</html>
