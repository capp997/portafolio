// Auto Refresh para Dashboard V5
(function(){
    const REFRESH_KEY = "portfolio_auto_refresh_enabled";
    const INTERVAL_KEY = "portfolio_auto_refresh_interval";
    const DEFAULT_INTERVAL = 300000; // 5 minutos

    let enabled = localStorage.getItem(REFRESH_KEY);
    let intervalMs = parseInt(localStorage.getItem(INTERVAL_KEY) || DEFAULT_INTERVAL, 10);

    if (enabled === null) {
        enabled = "false";
        localStorage.setItem(REFRESH_KEY, enabled);
    }

    function createAutoPanel(){
        const panel = document.createElement("div");
        panel.className = "auto-refresh-panel";

        panel.innerHTML = `
            <div>
                <strong>Auto Refresh</strong>
                <small id="autoRefreshStatus">${enabled === "true" ? "Activo cada 5 min" : "Apagado"}</small>
            </div>

            <div class="auto-actions">
                <button id="toggleAutoRefresh" class="${enabled === "true" ? "auto-on" : "auto-off"}">
                    ${enabled === "true" ? "ON" : "OFF"}
                </button>

                <a href="api/update_prices.php" class="auto-link">Actualizar</a>
                <a href="api/save_snapshot.php" class="auto-link snapshot">Snapshot</a>
            </div>
        `;

        document.body.appendChild(panel);

        document.getElementById("toggleAutoRefresh").addEventListener("click", function(){
            enabled = enabled === "true" ? "false" : "true";
            localStorage.setItem(REFRESH_KEY, enabled);
            location.reload();
        });
    }

    function runAutoRefresh(){
        if(enabled === "true"){
            setTimeout(() => {
                window.location.href = "api/update_prices.php?redirect=../index_v5.php";
            }, intervalMs);
        }
    }

    document.addEventListener("DOMContentLoaded", function(){
        createAutoPanel();
        runAutoRefresh();
    });
})();
