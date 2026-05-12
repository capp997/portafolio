<?php
require_once __DIR__ . "/config/auth.php";
require_once __DIR__ . "/config/menu.php";
require_once __DIR__.'/config/db.php';

$assets = $pdo->query("SELECT * FROM assets ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
$cost = 0;
$crypto = 0;
$etf = 0;
$aggressive = 0;

$labels = [];
$values = [];

foreach($assets as $a){
    $shares = (float)$a['shares'];
    $price = (float)$a['current_price'];
    $avg = (float)$a['avg_cost'];

    $v = $shares * $price;

    $total += $v;
    $cost += $shares * $avg;

    if($a['category'] == 'Crypto'){
        $crypto += $v;
    }

    if(str_contains($a['category'], 'ETF') || $a['category'] == 'Dividendos'){
        $etf += $v;
    }

    if($a['category'] == 'Acción agresiva'){
        $aggressive += $v;
    }

    if($v > 0){
        $labels[] = $a['ticker'];
        $values[] = round($v,2);
    }
}

$pl = $total - $cost;
$plPercent = $cost > 0 ? ($pl / $cost) * 100 : 0;
$etfPercent = $total > 0 ? ($etf / $total) * 100 : 0;
$cryptoPercent = $total > 0 ? ($crypto / $total) * 100 : 0;

function m($n){
    return '$'.number_format((float)$n,2);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#16a34a">
<title>Dashboard Premium</title>
<link rel="stylesheet" href="assets/style_v5.css">
<link rel="stylesheet" href="assets/auto_refresh.css">
<link rel="stylesheet" href="assets/mobile_premium.css">
<link rel="stylesheet" href="assets/action_buttons.css">
<link rel="stylesheet" href="assets/sidebar_buttons_fix.css">
<link rel="manifest" href="manifest.json">
<link rel="apple-touch-icon" href="assets/icons/icon-192.png">
<link rel="stylesheet" href="assets/pwa.css">
<link rel="stylesheet" href="assets/menu_dropdown.css">
<link rel="stylesheet" href="assets/ai_dashboard_cards.css">
<link rel="stylesheet" href="assets/dashboard_premium_ui.css">
<link rel="stylesheet" href="assets/premium_ui_pack.css">
<link rel="stylesheet" href="assets/live_charts.css">
<script src="assets/mobile_premium.js"></script>
<script src="assets/auto_refresh.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/service-worker.js')
    .then(() => console.log('SW registrado'))
    .catch(err => console.error(err));
}
</script>
<link rel="stylesheet" href="assets/global_page_fix.css">
</head>

<body>

<div class="layout">

<?php render_sidebar('dashboard', ''); ?>

<main class="content">
    <button id="installAppBtn"
        class="install-app-btn">📲 Instalar App</button>
<section class="topbar">
    <div>
        <h2>Bienvenido de nuevo 👋</h2>
        <p>Aquí tienes el resumen actualizado de tu portafolio.</p>
    </div>
    <div class="header-right-tools">

        <div class="header-icon-actions">
            <a class="header-icon-btn green"
               href="api/market_data_engine.php?redirect=../index_v5.php"
               data-tip="Actualizar precios">
                📈
            </a>

            <a class="header-icon-btn blue"
               href="api/save_snapshot.php"
               data-tip="Guardar snapshot">
                💾
            </a>

            <a class="header-icon-btn orange"
               href="api/generate_notifications.php"
               data-tip="Escanear alertas">
                🔔
            </a>

            <a class="header-icon-btn green"
               href="api/smart_dividend_engine.php"
               data-tip="Dividend Engine">
                💰
            </a>

            <a class="header-icon-btn purple"
               href="pages/ai_insights.php"
               data-tip="AI Insights">
                ✨
            </a>
        </div>

        <div class="dashboard-date-pill">
            <?= date("d M Y") ?>
        </div>

    </div>
</section>

<!--<section class="quick-actions-panel">
    <h2>Acciones rápidas</h2>
    <p>Ejecuta funciones principales del sistema con un clic.</p>

    <div class="action-bar">

        <a class="action-btn green"
        href="api/update_prices.php?redirect=../index_v5.php">
        📈 Actualizar precios
        </a>

        <a class="action-btn blue"
        href="api/save_snapshot.php">
        💾 Guardar snapshot
        </a>

        <a class="action-btn orange"
        href="api/scan_alerts.php">
        🔔 Escanear alertas
        </a>

        <a class="action-btn green"
        href="api/smart_dividend_engine.php">
        💰 Smart Dividend Engine
        </a>

        <a class="action-btn dark"
        href="pages/dividend_tracker.php">
        📅 Dividend Tracker
        </a>

        <a class="action-btn dark"
        href="pages/centro_alertas.php">
        🚨 Centro Alertas
        </a>

    </div>
</section>-->
<?php include __DIR__ . "/components/ai_dashboard_cards_premium.php"; ?>
<?php include __DIR__ . "/components/live_market_mini.php"; ?>
<section class="cards-grid">

    <div class="card premium-glow">
        <span>Valor total</span>
        <h3><?=m($total)?></h3>
        <div class="<?= $pl >= 0 ? 'mini-positive' : 'mini-negative' ?>">
            <?= $pl >= 0 ? '▲' : '▼' ?> <?= number_format($plPercent,2) ?>%
        </div>
    </div>

    <div class="card premium-glow">
        <span>Ganancia / Pérdida</span>
        <h3 class="<?=$pl>=0?'green':'red'?>"><?=m($pl)?></h3>
        <div class="<?= $pl >= 0 ? 'mini-positive' : 'mini-negative' ?>">
            <?= $pl >= 0 ? 'Ganando' : 'Perdiendo' ?>
        </div>
    </div>

    <div class="card">
        <span>ETF / Base</span>
        <h3><?=m($etf)?></h3>
        <div class="circle-wrap">
            <div class="circle"><?=number_format($etfPercent,1)?>%</div>
        </div>
    </div>

    <div class="card">
        <span>Crypto</span>
        <h3 class="orange"><?=m($crypto)?></h3>
        <div class="circle-wrap">
            <div class="circle orange-circle"><?=number_format($cryptoPercent,1)?>%</div>
        </div>
    </div>

</section>

<section class="middle-grid">

    <div class="panel">
        <div class="panel-title">
            <h2>Distribución del Portafolio</h2>
            <span>Allocation</span>
        </div>

        <div class="distribution-layout">
            <div class="chart-wrap">
                <canvas id="portfolioChart"></canvas>
            </div>

            <div class="legend-list">
                <?php foreach($assets as $a):
                    $v = (float)$a['shares'] * (float)$a['current_price'];
                    if($v <= 0) continue;
                    $pct = $total > 0 ? ($v / $total) * 100 : 0;
                ?>
                <div class="legend-row">
                    <span><b><?=$a['ticker']?></b></span>
                    <span><?=number_format($pct,1)?>%</span>
                    <span><?=m($v)?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-title">
            <h2>Resumen Financiero</h2>
            <span>Overview</span>
        </div>

        <div class="summary-list">
            <div class="summary-row">
                <span>Valor total</span>
                <strong><?=m($total)?></strong>
            </div>

            <div class="summary-row">
                <span>Ganancia / Pérdida</span>
                <strong class="<?=$pl>=0?'green':'red'?>"><?=m($pl)?></strong>
            </div>

            <div class="summary-row">
                <span>ETFs / Base</span>
                <strong><?=number_format($etfPercent,1)?>%</strong>
            </div>

            <div class="summary-row">
                <span>Crypto</span>
                <strong><?=number_format($cryptoPercent,1)?>%</strong>
            </div>

            <div class="summary-row">
                <span>Acciones agresivas</span>
                <strong><?=m($aggressive)?></strong>
            </div>
        </div>
    </div>

</section>
<?php include __DIR__ . "/components/premium_dashboard_widgets.php"; ?>
<section class="panel">
    <div class="table-header">
        <h2>Resumen por activo</h2>
        <span>Actualizado con precios reales</span>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Activo</th>
                    <th>Categoría</th>
                    <th>Valor actual</th>
                    <th>P/L</th>
                    <th>P/L %</th>
                    <th>Asignación</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach($assets as $a):
                $shares = (float)$a['shares'];
                $price = (float)$a['current_price'];
                $avg = (float)$a['avg_cost'];

                $v = $shares * $price;
                $p = $v - ($shares * $avg);
                $pPct = ($shares * $avg) > 0 ? ($p / ($shares * $avg)) * 100 : 0;
                $pct = $total > 0 ? ($v / $total) * 100 : 0;
            ?>
                <tr>
                    <td>
                        <div class="asset-cell">
                            <div class="asset-icon"><?=substr($a['ticker'],0,1)?></div>
                            <div>
                                <b><?=$a['ticker']?></b>
                                <small><?=$a['name']?></small>
                            </div>
                        </div>
                    </td>

                    <td>
                        <span class="category-pill"><?=htmlspecialchars($a['category'])?></span>
                    </td>

                    <td><?=m($v)?></td>

                    <td class="<?=$p>=0?'green':'red'?>"><?=m($p)?></td>

                    <td class="<?=$pPct>=0?'green':'red'?>">
                        <?= $pPct >= 0 ? '▲' : '▼' ?> <?=number_format(abs($pPct),2)?>%
                    </td>

                    <td>
                        <div class="allocation-wrap">
                            <div class="allocation-bar">
                                <div class="allocation-fill" style="width:<?=$pct?>%;"></div>
                            </div>
                            <span><?=number_format($pct,1)?>%</span>
                        </div>
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
const ctx = document.getElementById('portfolioChart');

new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: <?=json_encode($labels)?>,
        datasets: [{
            data: <?=json_encode($values)?>,
            borderWidth: 0,
            cutout: '68%'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>
<script src="assets/pwa.js"></script>
<script src="assets/menu_dropdown.js"></script>
</body>
</html>
