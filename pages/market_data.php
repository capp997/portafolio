<?php
require_once __DIR__ . "/../config/menu.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$latest = $pdo->query("
    SELECT DISTINCT ON (ticker)
        ticker, price, source, created_at
    FROM price_history
    ORDER BY ticker, created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

$totalRecords = $pdo->query("SELECT COUNT(*) FROM price_history")->fetchColumn();

function moneyFlex($n){
    $n = (float)$n;
    if($n < 1) return '$'.rtrim(rtrim(number_format($n,8), '0'), '.');
    return '$'.number_format($n,2);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Market Data</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../assets/style_v5.css">
<link rel="stylesheet" href="../assets/unified_pages.css">
<link rel="stylesheet" href="../assets/menu_dropdown.css">
<link rel="stylesheet" href="../assets/market_data_engine.css">
<link rel="stylesheet" href="../assets/sidebar_buttons_fix.css">
<link rel="stylesheet" href="../assets/mobile_premium.css">
</head>
<body>

<div class="layout">
<?php render_sidebar('market_data', '../'); ?>

<main class="content">

<section class="market-hero">
<div>
<p>Market Intelligence</p>
<h1>Motor de precios reales</h1>
<span>Actualiza precios, registra historial y dispara alertas.</span>
</div>

<a href="../api/market_data_engine.php" class="market-btn">Actualizar ahora</a>
</section>

<section class="cards-grid">
<div class="card premium-glow"><span>Registros históricos</span><h3><?=$totalRecords?></h3></div>
<div class="card"><span>Activos monitoreados</span><h3><?=count($latest)?></h3></div>
<div class="card"><span>Fuente</span><h3 style="font-size:24px;">Finnhub</h3></div>
<div class="card"><span>Estado</span><h3 class="green">Activo</h3></div>
</section>

<section class="panel">
<div class="table-header">
<h2>Últimos precios guardados</h2>
<span>Price History</span>
</div>

<div class="table-wrap">
<table>
<thead>
<tr>
<th>Ticker</th>
<th>Precio</th>
<th>Fuente</th>
<th>Actualizado</th>
</tr>
</thead>
<tbody>
<?php foreach($latest as $r): ?>
<tr>
<td><b><?=$r['ticker']?></b></td>
<td><?=moneyFlex($r['price'])?></td>
<td><?=$r['source']?></td>
<td><?=date("M d, h:i A", strtotime($r['created_at']))?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</section>

</main>
</div>

<script src="../assets/menu_dropdown.js"></script>
<script src="../assets/mobile_premium.js"></script>
</body>
</html>
