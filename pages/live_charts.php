<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$assets = $pdo->query("
    SELECT ticker,name,category,shares,current_price,avg_cost
    FROM assets
    ORDER BY ticker ASC
")->fetchAll(PDO::FETCH_ASSOC);

$defaultTicker = strtoupper($_GET['ticker'] ?? ($assets[0]['ticker'] ?? 'BTC'));

function tvSymbol($ticker){
    $ticker = strtoupper(trim($ticker));

    if($ticker === 'BTC') return 'BINANCE:BTCUSDT';
    if($ticker === 'ETH') return 'BINANCE:ETHUSDT';
    if($ticker === 'DOGE') return 'BINANCE:DOGEUSDT';

    $nyse = ['KO','VZ','T','O','SCHD','VOO','SPY'];
    if(in_array($ticker, $nyse)) return 'NYSE:' . $ticker;

    return 'NASDAQ:' . $ticker;
}

function money($n){
    return number_format((float)$n,2);
}

function moneySymbol($n){
    return '$'.number_format((float)$n,2);
}

function flexNum($n){
    return rtrim(rtrim(number_format((float)$n,8), '0'), '.');
}

function assetValue($a){
    return (float)$a['shares'] * (float)$a['current_price'];
}

function assetPL($a){
    return ((float)$a['shares'] * (float)$a['current_price']) - ((float)$a['shares'] * (float)$a['avg_cost']);
}

function pctChange($a){
    $cost = (float)$a['shares'] * (float)$a['avg_cost'];
    if($cost <= 0) return 0;
    return (assetPL($a) / $cost) * 100;
}

function iconFor($ticker){
    $ticker = strtoupper($ticker);
    if($ticker === 'BTC') return '₿';
    if($ticker === 'ETH') return 'Ξ';
    if($ticker === 'DOGE') return 'Ð';
    if($ticker === 'NVDA') return '🟢';
    if($ticker === 'TSLA') return '🔴';
    if($ticker === 'KO') return '🥤';
    if($ticker === 'VZ') return '📡';
    return '●';
}

$selected = null;
foreach($assets as $a){
    if(strtoupper($a['ticker']) === $defaultTicker){
        $selected = $a;
        break;
    }
}

if(!$selected && count($assets)){
    $selected = $assets[0];
    $defaultTicker = strtoupper($selected['ticker']);
}

$tvDefault = tvSymbol($defaultTicker);

$overview = array_slice($assets, 0, 8);

if(count($overview) < 6){
    $fallback = [
        ['ticker'=>'SPY','name'=>'S&P 500 ETF','current_price'=>530.70,'shares'=>1,'avg_cost'=>532.10,'category'=>'Index'],
        ['ticker'=>'QQQ','name'=>'Nasdaq 100 ETF','current_price'=>462.22,'shares'=>1,'avg_cost'=>459.00,'category'=>'Index'],
        ['ticker'=>'NVDA','name'=>'NVIDIA','current_price'=>1078.91,'shares'=>1,'avg_cost'=>1054.61,'category'=>'Stock'],
        ['ticker'=>'BTC','name'=>'Bitcoin','current_price'=>67245.19,'shares'=>1,'avg_cost'=>66000,'category'=>'Crypto'],
        ['ticker'=>'ETH','name'=>'Ethereum','current_price'=>3214.67,'shares'=>1,'avg_cost'=>3232.93,'category'=>'Crypto'],
        ['ticker'=>'DOGE','name'=>'Dogecoin','current_price'=>0.171,'shares'=>1000,'avg_cost'=>0.172,'category'=>'Crypto'],
    ];

    foreach($fallback as $f){
        $exists = false;
        foreach($overview as $o){
            if(strtoupper($o['ticker']) === strtoupper($f['ticker'])) $exists = true;
        }
        if(!$exists) $overview[] = $f;
    }
}

$selectedPL = $selected ? assetPL($selected) : 0;
$selectedPct = $selected ? pctChange($selected) : 0;
$selectedValue = $selected ? assetValue($selected) : 0;
$selectedPrice = $selected ? (float)$selected['current_price'] : 0;

$signal = "Neutral";
$signalClass = "neutral";
$gaugeDeg = 0;
if($selectedPct > 5){
    $signal = "Compra";
    $signalClass = "buy";
    $gaugeDeg = 38;
}elseif($selectedPct > 1){
    $signal = "Compra ligera";
    $signalClass = "buy";
    $gaugeDeg = 22;
}elseif($selectedPct < -5){
    $signal = "Venta";
    $signalClass = "sell";
    $gaugeDeg = -38;
}elseif($selectedPct < -1){
    $signal = "Venta ligera";
    $signalClass = "sell";
    $gaugeDeg = -22;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Live Charts Premium</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="../assets/live_charts_premium_final.css">
</head>

<body>

<div class="tv-layout">

<aside class="tv-sidebar">
    <div class="tv-brand">
        <div class="tv-logo">📈</div>
        <div>
            <h1>Live Charts</h1>
            <p>TradingView</p>
        </div>
    </div>

    <nav class="tv-nav">
        <a href="../index_v5.php">🏠 Dashboard</a>
        <a class="active" href="live_charts.php">📈 Live Charts</a>
        <a href="advanced_analytics.php">📊 Analytics</a>
        <a href="smart_signals.php">🤖 Smart Signals</a>
        <a href="market_data.php">📡 Market Data</a>
        <a href="ai_insights.php">✨ AI Insights</a>
        <a href="report_center.php">📄 Reports</a>
        <a href="notifications.php">🔔 Alerts <span>3</span></a>
    </nav>

    <div class="market-status-box">
        <h3>● Mercados Abiertos</h3>
        <div><span>NYSE</span><b>Abierto</b></div>
        <div><span>NASDAQ</span><b>Abierto</b></div>
        <div><span>Crypto</span><b>24/7</b></div>
        <div><span>Forex</span><b>Abierto</b></div>
    </div>

    <div class="premium-box">
        <h3>👑 Premium Plan</h3>
        <p>Accede a datos en tiempo real, más indicadores y alertas avanzadas.</p>
        <a href="ai_insights.php">Actualizar ahora</a>
    </div>

    <div class="investor-box">
        <div class="avatar">👤</div>
        <div>
            <strong>Inversionista</strong>
            <span>Plan Premium</span>
        </div>
    </div>
</aside>

<main class="tv-main">

<header class="topbar">
    <form method="GET" class="searchbar">
        <span>🔍</span>
        <input type="text" name="ticker" placeholder="Buscar símbolo (AAPL, BTC, SPY...)" value="<?=htmlspecialchars($defaultTicker)?>">
    </form>

    <div class="top-actions">
        <a href="live_charts.php?ticker=<?=$defaultTicker?>">☆ Mi lista</a>
        <button>USD⌄</button>
        <a href="../api/market_data_engine.php?redirect=../pages/live_charts.php">↻ Auto</a>
        <span class="connected">● Conectado</span>
    </div>
</header>

<section class="market-cards">
<?php foreach(array_slice($overview,0,6) as $o): 
    $rowPL = assetPL($o);
    $rowPct = pctChange($o);
    $isUp = $rowPL >= 0;
?>
<a class="market-card" href="?ticker=<?=$o['ticker']?>">
    <div>
        <span><?=htmlspecialchars($o['ticker'])?></span>
        <strong><?=money($o['current_price'])?></strong>
        <small class="<?=$isUp?'up':'down'?>">
            <?=$isUp?'+':''?><?=money($rowPL)?> (<?=number_format($rowPct,2)?>%)
        </small>
    </div>
    <svg viewBox="0 0 120 44" class="spark <?=$isUp?'spark-up':'spark-down'?>">
        <polyline points="<?=$isUp?'0,35 15,29 30,32 45,21 60,26 75,18 90,13 105,8 120,4':'0,10 15,15 30,13 45,24 60,19 75,27 90,31 105,35 120,38'?>"></polyline>
    </svg>
</a>
<?php endforeach; ?>
</section>

<section class="main-grid">

<div class="left-column">

    <section class="chart-card">
        <div class="card-header">
            <div>
                <h2>Gráfico Principal</h2>
                <p><?=$tvDefault?></p>
            </div>

            <select onchange="location.href='?ticker='+this.value">
                <?php foreach($assets as $a): ?>
                <option value="<?=$a['ticker']?>" <?=$a['ticker']==$defaultTicker?'selected':''?>>
                    <?=$a['ticker']?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="fake-toolbar">
            <span>1m</span><span>5m</span><span>15m</span><span>1h</span><span>4h</span><span>D</span>
            <b>〽 Indicadores</b>
        </div>

        <div class="chart-wrapper">
            <div id="tradingview_chart"></div>
        </div>
    </section>

    <section class="ticker-card">
        <div class="card-header">
            <h2>Ticker Tape</h2>
        </div>

        <div class="ticker-row">
            <?php foreach(array_slice($overview,0,7) as $o): 
                $rowPL = assetPL($o);
                $rowPct = pctChange($o);
            ?>
            <a href="?ticker=<?=$o['ticker']?>" class="ticker-item">
                <span class="ticker-icon"><?=iconFor($o['ticker'])?></span>
                <div>
                    <strong><?=$o['ticker']?></strong>
                    <b><?=money($o['current_price'])?></b>
                    <small class="<?=$rowPL>=0?'up':'down'?>">
                        <?=$rowPL>=0?'+':''?><?=number_format($rowPct,2)?>%
                    </small>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="selected-card">
        <div class="coin-icon"><?=iconFor($defaultTicker)?></div>

        <div class="selected-price">
            <p>Símbolo Seleccionado: <?=$defaultTicker?></p>
            <h2><?=money($selectedPrice)?> <span>USD</span></h2>
            <b class="<?=$selectedPL>=0?'up':'down'?>">
                <?=$selectedPL>=0?'+':''?><?=money($selectedPL)?> (<?=number_format($selectedPct,2)?>%)
            </b>
            <small>● Mercado abierto</small>
        </div>

        <div class="selected-stats">
            <div><span>Valor posición</span><strong><?=moneySymbol($selectedValue)?></strong></div>
            <div><span>Shares</span><strong><?=$selected?flexNum($selected['shares']):'0'?></strong></div>
            <div><span>Avg Cost</span><strong><?=moneySymbol($selected['avg_cost'] ?? 0)?></strong></div>
            <div><span>Categoría</span><strong><?=htmlspecialchars($selected['category'] ?? 'N/A')?></strong></div>
        </div>
    </section>

</div>

<div class="right-column">

    <section class="overview-card">
        <div class="card-header">
            <h2>Market Overview</h2>
            <a href="market_data.php">Ver más</a>
        </div>

        <div class="overview-table">
            <div class="overview-head">
                <span>Símbolo</span><span>Precio</span><span>Cambio</span><span>Cambio%</span><span>Gráfico</span>
            </div>

            <?php foreach(array_slice($overview,0,8) as $o): 
                $rowPL = assetPL($o);
                $rowPct = pctChange($o);
                $isUp = $rowPL >= 0;
            ?>
            <a href="?ticker=<?=$o['ticker']?>" class="overview-row">
                <span><b><?=iconFor($o['ticker'])?></b> <?=$o['ticker']?></span>
                <span><?=money($o['current_price'])?></span>
                <span class="<?=$isUp?'up':'down'?>"><?=$isUp?'+':''?><?=money($rowPL)?></span>
                <span class="<?=$isUp?'up':'down'?>"><?=$isUp?'+':''?><?=number_format($rowPct,2)?>%</span>
                <svg viewBox="0 0 90 26" class="mini-spark <?=$isUp?'spark-up':'spark-down'?>">
                    <polyline points="<?=$isUp?'0,22 15,18 30,20 45,13 60,16 75,8 90,4':'0,5 15,9 30,7 45,13 60,16 75,20 90,22'?>"></polyline>
                </svg>
            </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="technical-card">
        <div class="card-header">
            <h2>Análisis Técnico (<?=$defaultTicker?>)</h2>
            <a href="smart_signals.php">Ver más</a>
        </div>

        <div class="gauge">
            <div class="gauge-arc">
                <div class="needle" style="transform:rotate(<?=$gaugeDeg?>deg);"></div>
                <div class="needle-center"></div>
            </div>

            <h3 class="<?=$signalClass?>"><?=$signal?></h3>

            <div class="gauge-labels">
                <span>Venta Fuerte<br><b>1</b></span>
                <span>Venta<br><b>2</b></span>
                <span>Neutral<br><b>5</b></span>
                <span>Compra<br><b>9</b></span>
                <span>Compra Fuerte<br><b>3</b></span>
            </div>

            <p>Basado en precio actual, costo promedio y señales internas.</p>
        </div>
    </section>

</div>

</section>

<section class="news-bar">
    <strong>✥ Noticias del Mercado</strong>
    <span>• Fed mantiene tasas sin cambios y anticipa recorte en 2024</span>
    <span>• NVIDIA alcanza nuevo máximo histórico impulsado por IA</span>
    <span>• Bitcoin supera zona clave mientras crece el interés institucional</span>
    <a href="ai_insights.php">Ver todas</a>
</section>

</main>

</div>

<script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
<script>
new TradingView.widget({
  "autosize": true,
  "symbol": "<?=$tvDefault?>",
  "interval": "D",
  "timezone": "America/Chicago",
  "theme": "dark",
  "style": "1",
  "locale": "en",
  "enable_publishing": false,
  "hide_top_toolbar": false,
  "hide_side_toolbar": false,
  "allow_symbol_change": true,
  "container_id": "tradingview_chart"
});
</script>

</body>
</html>
