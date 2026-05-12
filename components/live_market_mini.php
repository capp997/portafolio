<?php
/*
LIVE MARKET MINI COMPONENT
Insertar en index_v5.php donde quieras:

<?php include __DIR__ . "/components/live_market_mini.php"; ?>
*/
?>

<section class="live-mini-section">
    <div class="live-mini-header">
        <div>
            <p>Live Market</p>
            <h2>Mercado en vivo</h2>
        </div>
        <a href="pages/live_charts.php">Abrir gráficos</a>
    </div>

    <div class="live-mini-widget">
        <div class="tradingview-widget-container">
          <div class="tradingview-widget-container__widget">
            <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-ticker-tape.js" async>
              {
                "symbols": [
                  {"proName": "FOREXCOM:SPXUSD", "title": "S&P 500"},
                  {"proName": "NASDAQ:IXIC", "title": "Nasdaq"},
                  {"proName": "NASDAQ:NVDA", "title": "NVDA"},
                  {"proName": "BINANCE:BTCUSDT", "title": "BTC"},
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
          </div>
        </div>
    </div>
</section>


