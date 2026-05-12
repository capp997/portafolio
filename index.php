<?php
require_once __DIR__.'/config/db.php';

$assets=$pdo->query("SELECT * FROM assets ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
$total=0;$cost=0;$crypto=0;$etf=0;$aggr=0;
foreach($assets as $a){
    $v=(float)$a['shares']*(float)$a['current_price'];
    $total+=$v;
    $cost+=(float)$a['shares']*(float)$a['avg_cost'];
    if($a['category']=='Crypto')$crypto+=$v;
    if(str_contains($a['category'],'ETF')||$a['category']=='Dividendos')$etf+=$v;
    if($a['category']=='Acción agresiva')$aggr+=$v;
}
$pl=$total-$cost;
function m($n){return '$'.number_format((float)$n,2);} 
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Dashboard V5</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="assets/menu_metas_fix.css">
</head>
<body>
<div class="app">
<aside class="sidebar">
    <div class="brand"><div class="logo">📈</div><div><h2>Portafolio<br>V5</h2><p>Premium Dashboard</p></div></div>
    <nav class="premium-menu">
        <a class="active" href="index.php">🏠 Dashboard</a>
        <a href="pages/activos.php">📊 Activos</a>
        <div class="menu-group"><button type="button" class="menu-parent"><span>💼 Operaciones</span><span class="chevron">⌄</span></button><div class="submenu"><a href="pages/compras.php">🛒 Compras</a><?php if(file_exists(__DIR__.'/pages/sell.php')): ?><a href="pages/sell.php">💸 Ventas / Sell</a><?php endif; ?></div></div>
        <div class="menu-group"><button type="button" class="menu-parent"><span>🔔 Alertas</span><span class="chevron">⌄</span></button><div class="submenu"><a href="pages/alertas.php">🔔 Alertas</a><?php if(file_exists(__DIR__.'/pages/centro_alertas.php')): ?><a href="pages/centro_alertas.php">🚨 Centro Alertas</a><?php endif; ?><?php if(file_exists(__DIR__.'/pages/notifications.php')): ?><a href="pages/notifications.php">📬 Notificaciones</a><?php endif; ?><?php if(file_exists(__DIR__.'/pages/ai_insights.php')): ?><a href="pages/ai_insights.php">✨ AI Insights</a><?php endif; ?></div></div>
        <div class="menu-group"><button type="button" class="menu-parent"><span>💰 Dividendos</span><span class="chevron">⌄</span></button><div class="submenu"><a href="pages/dividendos.php">💵 Dividendos</a><?php if(file_exists(__DIR__.'/pages/dividend_tracker.php')): ?><a href="pages/dividend_tracker.php">📅 Dividend Tracker</a><?php endif; ?></div></div>
        <div class="menu-group"><button type="button" class="menu-parent"><span>📊 Analytics</span><span class="chevron">⌄</span></button><div class="submenu"><?php if(file_exists(__DIR__.'/pages/advanced_analytics.php')): ?><a href="pages/advanced_analytics.php">📈 Advanced Analytics</a><?php endif; ?><?php if(file_exists(__DIR__.'/pages/market_data.php')): ?><a href="pages/market_data.php">📡 Market Data</a><?php endif; ?><?php if(file_exists(__DIR__.'/pages/live_charts.php')): ?><a href="pages/live_charts.php">📉 Live Charts</a><?php endif; ?></div></div>
        <a href="pages/rebalanceo.php">⚖️ Rebalanceo</a>
        <a href="pages/rutinas.php">🗓️ Rutinas</a>
        <a href="pages/metas.php">🎯 Metas</a>
    </nav>
</aside>
<main class="main">
<section class="hero"><div><h1>Dashboard Principal</h1><p>Control completo de tu portafolio, alertas, compras, dividendos y metas.</p></div><div class="pill">Online</div></section>
<section class="cards"><div class="card"><span>Valor total</span><strong><?=m($total)?></strong></div><div class="card"><span>Ganancia/Pérdida</span><strong class="<?=$pl>=0?'green':'red'?>"><?=m($pl)?></strong></div><div class="card"><span>ETF/Base</span><strong class="green"><?=m($etf)?></strong></div><div class="card"><span>Crypto</span><strong class="orange"><?=m($crypto)?></strong></div></section>
<section class="grid2"><div class="panel"><h2>Resumen por activo</h2><div class="table-wrap"><table><thead><tr><th>Activo</th><th>Valor</th><th>P/L</th><th>%</th></tr></thead><tbody><?php foreach($assets as $a):$v=$a['shares']*$a['current_price'];$p=$v-($a['shares']*$a['avg_cost']);$pct=$total>0?$v/$total*100:0;?><tr><td><b><?=$a['ticker']?></b><br><small><?=$a['name']?></small></td><td><?=m($v)?></td><td class="<?=$p>=0?'green':'red'?>"><?=m($p)?></td><td><?=number_format($pct,1)?>%</td></tr><?php endforeach;?></tbody></table></div></div><div class="panel"><h2>Acciones rápidas</h2><p>Actualiza precios, revisa alertas y registra compras para mantener tu portafolio al día.</p><p><a class="btn" href="pages/activos.php">Ir a Activos</a> <a class="btn btn-gray" href="pages/alertas.php">Ver Alertas</a></p></div></section>
</main></div><script src="assets/app.js"></script><script src="assets/menu_metas_fix.js"></script></body></html>
