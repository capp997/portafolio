<?php
require_once __DIR__ . "/../config/menu.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$pdo->query("
INSERT INTO dividend_settings(asset_id, annual_dividend_per_share, payment_frequency, next_pay_date, notes)
SELECT id, 0, 'Quarterly', NULL, ''
FROM assets
WHERE id NOT IN (SELECT asset_id FROM dividend_settings)
");

$rows = $pdo->query("
SELECT 
    a.id,
    a.ticker,
    a.name,
    a.category,
    a.shares,
    a.current_price,
    ds.annual_dividend_per_share,
    ds.payment_frequency,
    ds.next_pay_date,
    ds.notes
FROM assets a
LEFT JOIN dividend_settings ds ON ds.asset_id = a.id
ORDER BY 
    CASE 
        WHEN a.ticker='SCHD' THEN 1
        WHEN a.ticker='VOO' THEN 2
        WHEN a.ticker='QQQ' THEN 3
        ELSE 4
    END,
    a.ticker ASC
")->fetchAll(PDO::FETCH_ASSOC);

$totalValue = 0;
$totalAnnualDiv = 0;

foreach($rows as $r){
    $value = (float)$r['shares'] * (float)$r['current_price'];
    $annual = (float)$r['shares'] * (float)$r['annual_dividend_per_share'];
    $totalValue += $value;
    $totalAnnualDiv += $annual;
}

$totalMonthlyDiv = $totalAnnualDiv / 12;
$portfolioYield = $totalValue > 0 ? ($totalAnnualDiv / $totalValue) * 100 : 0;

$monthlyGoal = 100;
$goalProgress = min(100, $monthlyGoal > 0 ? ($totalMonthlyDiv / $monthlyGoal) * 100 : 0);

function money($n){
    return '$'.number_format((float)$n,2);
}

$chartLabels = [];
$chartValues = [];
foreach($rows as $r){
    $annualIncome = (float)$r['shares'] * (float)$r['annual_dividend_per_share'];
    if($annualIncome > 0){
        $chartLabels[] = $r['ticker'];
        $chartValues[] = round($annualIncome,2);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Dividend Tracker Premium</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="../assets/style_v5.css">
<link rel="stylesheet" href="../assets/unified_pages.css">
<link rel="stylesheet" href="../assets/dividend_tracker.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="../assets/action_buttons.css">
<link rel="stylesheet" href="../assets/mobile_premium.css">
<link rel="stylesheet" href="../assets/menu_dropdown.css">
<link rel="stylesheet" href="../assets/sidebar_buttons_fix.css">
</head>

<body>

<div class="layout">

<?php render_sidebar('dividend_tracker', '../'); ?>

<main class="content">

<section class="dividend-hero">
    <div>
        <p class="dividend-label">Passive Income</p>
        <h1><?=money($totalMonthlyDiv)?> / mes</h1>
        <div class="dividend-sub">
            Estimado anual: <b><?=money($totalAnnualDiv)?></b> · Yield: <b><?=number_format($portfolioYield,2)?>%</b>
        </div>
    </div>

    <div class="goal-card-income">
        <span>Meta mensual</span>
        <strong><?=money($monthlyGoal)?></strong>
        <div class="income-progress">
            <div style="width:<?=$goalProgress?>%;"></div>
        </div>
        <small><?=number_format($goalProgress,1)?>% completado</small>
    </div>
</section>

<section class="cards-grid">
    <div class="card premium-glow"><span>Dividendos mensuales</span><h3 class="green"><?=money($totalMonthlyDiv)?></h3></div>
    <div class="card"><span>Dividendos anuales</span><h3><?=money($totalAnnualDiv)?></h3></div>
    <div class="card"><span>Yield estimado</span><h3 class="green"><?=number_format($portfolioYield,2)?>%</h3></div>
    <div class="card"><span>Meta $100/mes</span><h3><?=number_format($goalProgress,1)?>%</h3></div>
</section>

<section class="middle-grid">
    <div class="panel">
        <div class="panel-title"><h2>Income por activo</h2><span>Annual dividends</span></div>
        <div class="div-chart-wrap"><canvas id="dividendChart"></canvas></div>
    </div>

    <div class="panel">
        <div class="panel-title"><h2>Lectura rápida</h2><span>Income Strategy</span></div>
        <div class="income-insights">
            <div><span>Rey de dividendos</span><strong>SCHD</strong><p>ETF principal para construir ingresos pasivos.</p></div>
            <div><span>Crecimiento + dividendo</span><strong>VOO</strong><p>Menos yield que SCHD, pero mejor crecimiento largo plazo.</p></div>
            <div><span>Dividendos bajos</span><strong>QQQ</strong><p>Más crecimiento tecnológico que income.</p></div>
        </div>
    </div>
</section>

<section class="panel">
    <div class="table-header"><h2>Configuración de dividendos</h2><span>Actualiza dividendos por acción manualmente</span></div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Activo</th><th>Shares</th><th>Dividendo anual/share</th><th>Frecuencia</th><th>Próximo pago</th><th>Ingreso anual</th><th>Ingreso mensual</th><th>Guardar</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($rows as $r):
                $shares = (float)$r['shares'];
                $annualPerShare = (float)$r['annual_dividend_per_share'];
                $annualIncome = $shares * $annualPerShare;
                $monthlyIncome = $annualIncome / 12;
            ?>
                <tr>
                    <form action="../api/update_dividend_setting.php" method="POST">
                    <td>
                        <div class="asset-cell">
                            <div class="asset-icon"><?=substr($r['ticker'],0,1)?></div>
                            <div><b><?=$r['ticker']?></b><small><?=$r['name']?></small></div>
                        </div>
                        <input type="hidden" name="asset_id" value="<?=$r['id']?>">
                    </td>

                    <td><?=number_format($shares,4)?></td>
                    <td><input type="number" step="0.0001" name="annual_dividend_per_share" value="<?=$r['annual_dividend_per_share']?>"></td>
                    <td>
                        <select name="payment_frequency">
                            <option value="Monthly" <?=$r['payment_frequency']=='Monthly'?'selected':''?>>Monthly</option>
                            <option value="Quarterly" <?=$r['payment_frequency']=='Quarterly'?'selected':''?>>Quarterly</option>
                            <option value="Annual" <?=$r['payment_frequency']=='Annual'?'selected':''?>>Annual</option>
                            <option value="None" <?=$r['payment_frequency']=='None'?'selected':''?>>None</option>
                        </select>
                    </td>
                    <td><input type="date" name="next_pay_date" value="<?=$r['next_pay_date']?>"></td>
                    <td class="green"><?=money($annualIncome)?></td>
                    <td><?=money($monthlyIncome)?></td>
                    <td><button>Guardar</button></td>
                    </form>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="panel">
    <div class="table-header"><h2>Calendario de próximos pagos</h2><span>Dividend calendar</span></div>
    <div class="calendar-grid">
        <?php 
        $hasDate = false;
        foreach($rows as $r): 
            if(!$r['next_pay_date']) continue;
            $hasDate = true;
        ?>
            <div class="calendar-card">
                <strong><?=$r['ticker']?></strong>
                <span><?=date("M d, Y", strtotime($r['next_pay_date']))?></span>
                <small><?=$r['payment_frequency']?></small>
            </div>
        <?php endforeach; ?>

        <?php if(!$hasDate): ?>
            <div class="calendar-empty">No tienes próximos pagos configurados todavía.</div>
        <?php endif; ?>
    </div>
</section>

</main>
</div>

<script>
new Chart(document.getElementById('dividendChart'), {
    type: 'doughnut',
    data: {
        labels: <?=json_encode($chartLabels)?>,
        datasets: [{
            data: <?=json_encode($chartValues)?>,
            borderWidth: 0,
            cutout: '68%'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { labels: { color: '#fff' } }
        }
    }
});
</script>

<script src="../assets/mobile_premium.js"></script>
<script src="../assets/menu_dropdown.js"></script>
</body>
</html>
