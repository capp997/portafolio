<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";
require_once __DIR__.'/../config/layout.php';
page_start('AI Insights','ai_insights'); 

$assets = $pdo->query("SELECT * FROM assets ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
$cost = 0;
$crypto = 0;
$etf = 0;
$aggressive = 0;
$dividendBase = 0;

$insights = [];
$watchlist = [];

foreach($assets as $a){
    $shares = (float)$a['shares'];
    $price = (float)$a['current_price'];
    $avg = (float)$a['avg_cost'];
    $value = $shares * $price;
    $base = (float)$a['base_price'];

    $total += $value;
    $cost += $shares * $avg;

    if($a['category'] === 'Crypto') $crypto += $value;
    if(str_contains($a['category'], 'ETF') || $a['category'] === 'Dividendos') $etf += $value;
    if($a['category'] === 'Acción agresiva') $aggressive += $value;
    if($a['ticker'] === 'SCHD' || $a['ticker'] === 'VOO') $dividendBase += $value;

    $pct = $total > 0 ? 0 : 0;

    if($base > 0 && $price > 0){
        if($price <= $base * 0.90){
            $watchlist[] = [
                "ticker"=>$a['ticker'],
                "signal"=>"BUY FUERTE",
                "text"=>"Está 10% o más debajo del precio base."
            ];
        } elseif($price <= $base * 0.95){
            $watchlist[] = [
                "ticker"=>$a['ticker'],
                "signal"=>"BUY",
                "text"=>"Está 5% o más debajo del precio base."
            ];
        } elseif($price >= $base * 1.20){
            $watchlist[] = [
                "ticker"=>$a['ticker'],
                "signal"=>"SELL FUERTE",
                "text"=>"Está 20% o más arriba del precio base."
            ];
        } elseif($price >= $base * 1.10){
            $watchlist[] = [
                "ticker"=>$a['ticker'],
                "signal"=>"SELL",
                "text"=>"Está 10% o más arriba del precio base."
            ];
        }
    }
}

$pl = $total - $cost;
$plPct = $cost > 0 ? ($pl / $cost) * 100 : 0;
$cryptoPct = $total > 0 ? ($crypto / $total) * 100 : 0;
$etfPct = $total > 0 ? ($etf / $total) * 100 : 0;
$aggressivePct = $total > 0 ? ($aggressive / $total) * 100 : 0;
$dividendPct = $total > 0 ? ($dividendBase / $total) * 100 : 0;

$riskScore = 0;
$riskNotes = [];

if($cryptoPct > 15){
    $riskScore += 30;
    $riskNotes[] = "Crypto está alto para un portafolio controlado.";
} elseif($cryptoPct > 8){
    $riskScore += 18;
    $riskNotes[] = "Crypto está en zona agresiva moderada.";
}

if($aggressivePct > 20){
    $riskScore += 25;
    $riskNotes[] = "Acciones agresivas están elevadas.";
} elseif($aggressivePct > 10){
    $riskScore += 15;
    $riskNotes[] = "Tienes exposición agresiva razonable.";
}

if($etfPct < 50){
    $riskScore += 20;
    $riskNotes[] = "La base ETF podría ser más fuerte.";
}

if($plPct < -5){
    $riskScore += 15;
    $riskNotes[] = "El portafolio está en pérdida relevante.";
}

if($riskScore < 25){
    $riskLabel = "Bajo / Controlado";
    $riskClass = "green";
} elseif($riskScore < 55){
    $riskLabel = "Moderado";
    $riskClass = "orange";
} else {
    $riskLabel = "Alto";
    $riskClass = "red";
}

if($cryptoPct > 12){
    $insights[] = "No aumentaría crypto ahora. Está tomando peso importante dentro del portafolio.";
}

if($etfPct < 60){
    $insights[] = "Sería inteligente fortalecer la base con VOO, SCHD o QQQ.";
}

if($dividendPct < 30){
    $insights[] = "Si tu meta es vivir del portafolio, conviene aumentar gradualmente SCHD/VOO.";
}

if($pl > 0){
    $insights[] = "Vas positivo. Considera proteger ganancias y no perseguir precios altos.";
} else {
    $insights[] = "Si estás negativo, enfócate en entradas pequeñas y consistentes, no en compras impulsivas.";
}

if(count($watchlist) === 0){
    $insights[] = "No hay señales fuertes de BUY/SELL ahora. HOLD también es una decisión.";
}

function money($n){
    return '$'.number_format((float)$n,2);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>AI Insights</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="../assets/style_v5.css">
<link rel="stylesheet" href="../assets/unified_pages.css">
<link rel="stylesheet" href="../assets/ai_insights.css">
</head>

<body>

<div class="layout">

<!--<aside class="sidebar">
    <div>
        <div class="brand">
            <div class="logo">🧠</div>
            <div>
                <h1>AI Insights</h1>
                <p>Portfolio Brain</p>
            </div>
        </div>

        <nav>
            <a href="../index_v5.php">Dashboard</a>
            <a href="activos.php">Activos</a>
            <a href="centro_alertas.php">Centro Alertas</a>
            <a href="dividend_tracker.php">Dividend Tracker</a>
            <a href="historial_avanzado.php">Historial Pro</a>
            <a class="active" href="ai_insights.php">AI Insights</a>
            <a href="metas.php">Metas</a>
        </nav>
    </div>

    <div class="sidebar-footer">
        <a class="update-btn" href="../api/update_prices.php?redirect=../pages/ai_insights.php">Actualizar precios</a>
        <a class="logout-btn" href="../api/logout.php">Cerrar sesión</a>
    </div>
</aside>-->

<main class="content">

<section class="ai-hero">
    <div>
        <p class="ai-label">Portfolio Intelligence</p>
        <h1>Análisis inteligente del portafolio</h1>
        <p>Lectura automática usando tus precios, asignación, metas y riesgo actual.</p>
    </div>

    <div class="risk-card">
        <span>Riesgo actual</span>
        <strong class="<?=$riskClass?>"><?=$riskLabel?></strong>
        <div class="risk-meter">
            <div style="width:<?=min(100,$riskScore)?>%;"></div>
        </div>
        <small>Score: <?=$riskScore?> / 100</small>
    </div>
</section>

<section class="cards-grid">
    <div class="card premium-glow">
        <span>Valor total</span>
        <h3><?=money($total)?></h3>
    </div>

    <div class="card">
        <span>Ganancia/Pérdida</span>
        <h3 class="<?=$pl>=0?'green':'red'?>"><?=money($pl)?></h3>
    </div>

    <div class="card">
        <span>Crypto</span>
        <h3 class="orange"><?=number_format($cryptoPct,1)?>%</h3>
    </div>

    <div class="card">
        <span>ETF Base</span>
        <h3 class="green"><?=number_format($etfPct,1)?>%</h3>
    </div>
</section>

<section class="ai-grid">

<div class="panel">
    <div class="panel-title">
        <h2>Recomendaciones principales</h2>
        <span>AI Rules</span>
    </div>

    <div class="insight-list">
        <?php foreach($insights as $i): ?>
            <div class="insight-card">💡 <?=htmlspecialchars($i)?></div>
        <?php endforeach; ?>
    </div>
</div>

<div class="panel">
    <div class="panel-title">
        <h2>Notas de riesgo</h2>
        <span>Risk</span>
    </div>

    <div class="insight-list">
        <?php if(count($riskNotes)>0): ?>
            <?php foreach($riskNotes as $n): ?>
                <div class="risk-note">⚠️ <?=htmlspecialchars($n)?></div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="insight-card">✅ El riesgo se ve controlado por ahora.</div>
        <?php endif; ?>
    </div>
</div>

</section>

<section class="panel">
    <div class="table-header">
        <h2>Watchlist inteligente</h2>
        <span>BUY / SELL signals</span>
    </div>

    <div class="ai-watch-grid">
        <?php foreach($watchlist as $w): ?>
            <div class="watch-card">
                <strong><?=$w['ticker']?></strong>
                <span class="<?=str_contains($w['signal'],'BUY')?'buy-tag':'sell-tag'?>">
                    <?=$w['signal']?>
                </span>
                <p><?=htmlspecialchars($w['text'])?></p>
            </div>
        <?php endforeach; ?>

        <?php if(count($watchlist)===0): ?>
            <div class="watch-empty">No hay señales fuertes ahora. Mantén disciplina y revisa alertas.</div>
        <?php endif; ?>
    </div>
</section>

<section class="panel">
    <div class="table-header">
        <h2>Distribución analizada</h2>
        <span>Allocation</span>
    </div>

    <div class="allocation-ai-grid">
        <div>
            <span>ETF / Base</span>
            <div class="ai-bar"><div style="width:<?=$etfPct?>%;"></div></div>
            <b><?=number_format($etfPct,1)?>%</b>
        </div>

        <div>
            <span>Crypto</span>
            <div class="ai-bar orange-bar"><div style="width:<?=$cryptoPct?>%;"></div></div>
            <b><?=number_format($cryptoPct,1)?>%</b>
        </div>

        <div>
            <span>Acciones agresivas</span>
            <div class="ai-bar blue-bar"><div style="width:<?=$aggressivePct?>%;"></div></div>
            <b><?=number_format($aggressivePct,1)?>%</b>
        </div>

        <div>
            <span>Base dividendos</span>
            <div class="ai-bar"><div style="width:<?=$dividendPct?>%;"></div></div>
            <b><?=number_format($dividendPct,1)?>%</b>
        </div>
    </div>
</section>

</main>
</div>

</body>
</html>
