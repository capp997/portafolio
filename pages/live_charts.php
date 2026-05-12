<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$assets = $pdo->query("
    SELECT ticker,name,category,shares,current_price,avg_cost
    FROM assets
    ORDER BY ticker ASC
")->fetchAll(PDO::FETCH_ASSOC);

$defaultTicker = $_GET['ticker'] ?? ($assets[0]['ticker'] ?? 'SPY');

function tvSymbol($ticker){
    $ticker = strtoupper(trim($ticker));

    if($ticker === 'BTC') return 'BINANCE:BTCUSDT';
    if($ticker === 'ETH') return 'BINANCE:ETHUSDT';
    if($ticker === 'DOGE') return 'BINANCE:DOGEUSDT';

    return 'NASDAQ:' . $ticker;
}

function money($n){
    return '$'.number_format((float)$n,2);
}

function pnlClass($shares,$price,$avg){
    $pl = ((float)$shares * (float)$price) - ((float)$shares * (float)$avg);
    return $pl >= 0 ? 'green' : 'red';
}

function pnlValue($shares,$price,$avg){
    return (((float)$shares * (float)$price) - ((float)$shares * (float)$avg));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Live Charts</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="../assets/style_v5.css">
<link rel="stylesheet" href="../assets/unified_pages.css">
<link rel="stylesheet" href="../assets/menu_dropdown.css">
<link rel="stylesheet" href="../assets/live_charts.css">
</head>

<body>

<div class="layout">

<aside class="sidebar">
<div>
<div class="brand">
<div class="logo">📈</div>
<div>
<h1>Live Charts</h1>
<p>TradingView</p>
</div>
</div>

<nav class="premium-menu">
<a href="../index_v5.php">🏠 Dashboard</a>
<a class="active" href="live_charts.php">📈 Live Charts</a>
<a href="advanced_analytics.php">📊 Analytics</a>
<a href="smart_signals.php">🤖 Smart Signals</a>
<a href="market_data.php">📡 Market Data</a>
<a href="ai_insights.php">✨ AI Insights</a>
<a href="report_center.php">📄 Reports</a>
</nav>
</div>

<div class="sidebar-footer">
<a href="../api/market_data_engine.php?redirect=../pages/live_charts.php">📡 Actualizar precios</a>
<a href="../api/logout.php">Cerrar sesión</a>
</div>
</aside>

<main class="content">

<section class="charts-hero">
<div>
<p>TradingView Live Market</p>
<h1>Gráficos live del portafolio</h1>
<span>Visualiza activos, crypto, ETFs y señales con gráficos interactivos.</span>
</div>

<a class="charts-btn" href="../api/market_data_engine.php?redirect=../pages/live_charts.php">Actualizar precios</a>
</section>

<section class="charts-layout">

<div class="watchlist-panel">
<h2>Watchlist</h2>

<div class="watchlist">
<?php foreach($assets as $a): 
$pl = pnlValue($a['shares'],$a['current_price'],$a['avg_cost']);
?>
<a href="?ticker=<?=$a['ticker']?>" class="watch-row <?=$a['ticker']==$defaultTicker?'active':''?>">
    <div>
        <strong><?=$a['ticker']?></strong>
        <small><?=htmlspecialchars($a['name'])?></small>
    </div>

    <div class="watch-price">
        <span><?=money($a['current_price'])?></span>
        <b class="<?=pnlClass($a['shares'],$a['current_price'],$a['avg_cost'])?>">
            <?=money($pl)?>
        </b>
    </div>
</a>
<?php endforeach; ?>
</div>
</div>

<div class="chart-panel">
<div class="chart-top">
<div>
<h2><?=$defaultTicker?> Chart</h2>
<p><?=tvSymbol($defaultTicker)?></p>
</div>

<div class="chart-tabs">
<a href="?ticker=<?=$defaultTicker?>">1D</a>
<a href="?ticker=<?=$defaultTicker?>">1W</a>
<a href="?ticker=<?=$defaultTicker?>">1M</a>
</div>
</div>

<div class="tradingview-widget-container">
  <div id="tradingview_chart"></div>
</div>

</div>

</section>

<section class="market-overview-panel">
<h2>Market Overview</h2>
<div class="tradingview-widget-container">
  <div class="tradingview-widget-container__widget"></div>
</div>
</section>

</main>
</div>

<script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>

<script>
new TradingView.widget({
  "autosize": true,
  "symbol": "<?=tvSymbol($defaultTicker)?>",
  "interval": "D",
  "timezone": "America/Chicago",
  "theme": "dark",
  "style": "1",
  "locale": "en",
  "enable_publishing": false,
  "allow_symbol_change": true,
  "container_id": "tradingview_chart"
});
</script>

<script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-market-overview.js" async>
{
  "colorTheme": "dark",
  "dateRange": "12M",
  "showChart": true,
  "locale": "en",
  "largeChartUrl": "",
  "isTransparent": true,
  "showSymbolLogo": true,
  "showFloatingTooltip": false,
  "width": "100%",
  "height": "500",
  "tabs": [
    {
      "title": "Indices",
      "symbols": [
        {"s": "FOREXCOM:SPXUSD", "d": "S&P 500"},
        {"s": "NASDAQ:IXIC", "d": "Nasdaq"},
        {"s": "DJ:DJI", "d": "Dow Jones"}
      ]
    },
    {
      "title": "Crypto",
      "symbols": [
        {"s": "BINANCE:BTCUSDT", "d": "Bitcoin"},
        {"s": "BINANCE:ETHUSDT", "d": "Ethereum"},
        {"s": "BINANCE:DOGEUSDT", "d": "Dogecoin"}
      ]
    },
    {
      "title": "Stocks",
      "symbols": [
        {"s": "NASDAQ:NVDA", "d": "NVIDIA"},
        {"s": "NASDAQ:TSLA", "d": "Tesla"},
        {"s": "NYSE:KO", "d": "Coca-Cola"},
        {"s": "NYSE:VZ", "d": "Verizon"}
      ]
    }
  ]
}
</script>

<script src="../assets/menu_dropdown.js"></script>
</body>
</html>
