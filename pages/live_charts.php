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

    // Algunos tickers pueden ser NYSE; TradingView permite cambio manual.
    $nyse = ['KO','VZ','T','O','SCHD','VOO','SPY'];
    if(in_array($ticker, $nyse)){
        return 'NYSE:' . $ticker;
    }

    return 'NASDAQ:' . $ticker;
}

function money($n){
    return '$'.number_format((float)$n,2);
}

function pnlValue($shares,$price,$avg){
    return (((float)$shares * (float)$price) - ((float)$shares * (float)$avg));
}

function pnlClass($n){
    return $n >= 0 ? 'green' : 'red';
}

$tvDefault = tvSymbol($defaultTicker);
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
<link rel="stylesheet" href="../assets/live_charts_fix.css">
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

<section class="charts-hero fixed-hero">
<div>
<p>TradingView Live Market</p>
<h1>Gráficos live del portafolio</h1>
<span>Visualiza activos, crypto, ETFs y señales con gráficos interactivos.</span>
</div>

<a class="charts-btn" href="../api/market_data_engine.php?redirect=../pages/live_charts.php">Actualizar precios</a>
</section>

<section class="ticker-tape-panel">
<div class="tradingview-widget-container">
  <div class="tradingview-widget-container__widget">
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
  </div>
</div>
</section>

<section class="charts-layout fixed-charts-layout">

<div class="watchlist-panel fixed-watchlist">
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
        <b class="<?=pnlClass($pl)?>">
            <?=money($pl)?>
        </b>
    </div>
</a>
<?php endforeach; ?>
</div>
</div>

<div class="chart-panel fixed-chart-panel">
<div class="chart-top">
<div>
<h2><?=$defaultTicker?> Chart</h2>
<p><?=$tvDefault?></p>
</div>

<div class="chart-tabs">
<a href="?ticker=<?=$defaultTicker?>">D</a>
<a href="?ticker=<?=$defaultTicker?>">W</a>
<a href="?ticker=<?=$defaultTicker?>">M</a>
</div>
</div>

<div class="tv-chart-box">
  <div id="tradingview_chart"></div>
</div>

</div>

</section>

<section class="market-widgets-grid">

<div class="market-widget-card">
<div class="widget-title">
<h2>Symbol Overview</h2>
<p><?=$tvDefault?></p>
</div>

<div class="tradingview-widget-container fixed-symbol-overview">
  <div class="tradingview-widget-container__widget">
    <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-symbol-overview.js" async>
{
  "symbols": [
    [
      "<?=$defaultTicker?>",
      "<?=$tvDefault?>|1D"
    ]
  ],
  "chartOnly": false,
  "width": "100%",
  "height": "420",
  "locale": "en",
  "colorTheme": "dark",
  "autosize": true,
  "showVolume": false,
  "showMA": false,
  "hideDateRanges": false,
  "hideMarketStatus": false,
  "hideSymbolLogo": false,
  "scalePosition": "right",
  "scaleMode": "Normal",
  "fontFamily": "Arial, sans-serif",
  "fontSize": "10",
  "noTimeScale": false,
  "valuesTracking": "1",
  "changeMode": "price-and-percent",
  "chartType": "area",
  "maLineColor": "#2962FF",
  "maLineWidth": 1,
  "maLength": 9,
  "backgroundColor": "rgba(0, 0, 0, 0)",
  "lineWidth": 2,
  "lineType": 0,
  "dateRanges": [
    "1d|1",
    "1m|30",
    "3m|60",
    "12m|1D",
    "60m|1W",
    "all|1M"
  ]
}
</script>
  </div>
</div>
</div>

<div class="market-widget-card">
<div class="widget-title">
<h2>Technical Analysis</h2>
<p>Resumen técnico</p>
</div>

<div class="tradingview-widget-container fixed-technical">
  <div class="tradingview-widget-container__widget">
    <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-technical-analysis.js" async>
      {
        "interval": "1D",
        "width": "100%",
        "isTransparent": true,
        "height": "420",
        "symbol": "<?=$tvDefault?>",
        "showIntervalTabs": true,
        "displayMode": "single",
        "locale": "en",
        "colorTheme": "dark"
      }
    </script>
  </div>
</div>
</div>

</section>
<script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>



<script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-ticker-tape.js" async>
{
  "symbols": [
    {"proName": "FOREXCOM:SPXUSD", "title": "S&P 500"},
    {"proName": "NASDAQ:IXIC", "title": "Nasdaq"},
    {"proName": "NASDAQ:NVDA", "title": "NVDA"},
    {"proName": "BINANCE:BTCUSDT", "title": "BTC"},
    {"proName": "BINANCE:ETHUSDT", "title": "ETH"},
    {"proName": "BINANCE:DOGEUSDT", "title": "DOGE"},
    {"proName": "NYSE:KO", "title": "KO"},
    {"proName": "NYSE:VZ", "title": "VZ"}
  ],
  "showSymbolLogo": true,
  "colorTheme": "dark",
  "isTransparent": true,
  "displayMode": "adaptive",
  "locale": "en"
}
</script>
</main>
</div>



<script src="../assets/menu_dropdown.js"></script>
</body>
</html>
