<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$rows = $pdo->query("
    SELECT * FROM portfolio_history
    ORDER BY created_at ASC
")->fetchAll(PDO::FETCH_ASSOC);

$latest = null;
if(count($rows) > 0){
    $latest = $rows[count($rows)-1];
}

$labels = [];
$totalValues = [];
$plValues = [];

foreach($rows as $r){
    $labels[] = date("M d H:i", strtotime($r['created_at']));
    $totalValues[] = (float)$r['total_value'];
    $plValues[] = (float)$r['profit_loss'];
}

function m($n){
    return '$'.number_format((float)$n,2);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Historial del Portafolio</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../assets/style_v5.css">
<link rel="stylesheet" href="../assets/history.css">
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
                <p>Historial</p>
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
            <a class="active" href="historial.php">Historial</a>
            <a class="" href="historial_avanzado.php">Historial Pro</a>
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

<section class="topbar">
    <div>
        <h2>Historial del Portafolio 📈</h2>
        <p>Guarda snapshots para ver cómo crece tu portafolio con el tiempo.</p>
    </div>

    <a class="history-save-btn" href="../api/save_snapshot.php">Guardar snapshot</a>
</section>

<?php if(isset($_GET['saved'])): ?>
<div class="history-alert">Snapshot guardado correctamente ✅</div>
<?php endif; ?>

<section class="cards-grid">
    <div class="card premium-glow">
        <span>Último valor guardado</span>
        <h3><?= $latest ? m($latest['total_value']) : '$0.00' ?></h3>
    </div>

    <div class="card">
        <span>Última ganancia/pérdida</span>
        <h3 class="<?= $latest && $latest['profit_loss'] >= 0 ? 'green' : 'red' ?>">
            <?= $latest ? m($latest['profit_loss']) : '$0.00' ?>
        </h3>
    </div>

    <div class="card">
        <span>Snapshots</span>
        <h3><?= count($rows) ?></h3>
    </div>

    <div class="card">
        <span>Última actualización</span>
        <h3 style="font-size:18px;">
            <?= $latest ? date("M d, h:i A", strtotime($latest['created_at'])) : 'Sin datos' ?>
        </h3>
    </div>
</section>

<section class="panel history-panel">
    <div class="panel-title">
        <h2>Crecimiento histórico</h2>
        <span>Valor total</span>
    </div>

    <div class="history-chart-wrap">
        <canvas id="historyChart"></canvas>
    </div>
</section>

<section class="panel">
    <div class="table-header">
        <h2>Registros guardados</h2>
        <span>Historial manual</span>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Valor total</th>
                    <th>Costo</th>
                    <th>P/L</th>
                    <th>ETF</th>
                    <th>Crypto</th>
                    <th>Agresivo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach(array_reverse($rows) as $r): ?>
                <tr>
                    <td><?= date("M d, Y h:i A", strtotime($r['created_at'])) ?></td>
                    <td><?= m($r['total_value']) ?></td>
                    <td><?= m($r['total_cost']) ?></td>
                    <td class="<?= $r['profit_loss'] >= 0 ? 'green' : 'red' ?>"><?= m($r['profit_loss']) ?></td>
                    <td><?= m($r['etf_value']) ?></td>
                    <td><?= m($r['crypto_value']) ?></td>
                    <td><?= m($r['aggressive_value']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

</main>
</div>

<script>
const ctx = document.getElementById('historyChart');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?=json_encode($labels)?>,
        datasets: [{
            label: 'Valor total',
            data: <?=json_encode($totalValues)?>,
            borderWidth: 3,
            tension: 0.35,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: { color: '#fff' }
            }
        },
        scales: {
            x: {
                ticks: { color: '#94a3b8' },
                grid: { color: 'rgba(148,163,184,.12)' }
            },
            y: {
                ticks: { color: '#94a3b8' },
                grid: { color: 'rgba(148,163,184,.12)' }
            }
        }
    }
});
</script>

<script src="../assets/mobile_premium.js"></script>
</body>
</html>
