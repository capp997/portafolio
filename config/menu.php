<?php
function render_sidebar($active='dashboard', $base=''){
    // $base = '' for index_v5.php, '../' for files inside /pages
    $isAdmin = (($_SESSION['role'] ?? '') === 'admin');

    $alertPages = ['alertas','centro_alertas','notifications','push_notifications','smart_signals','ai_insights'];
    $tradePages = ['compras','sell'];
    $dividendPages = ['dividendos','dividend_tracker'];
    $analyticsPages = ['advanced_analytics','market_data','ai_portfolio_advisor','ai_finance_chat'];
    $automationPages = ['automation_center','scheduler_center'];
    $historyPages = ['historial','historial_avanzado'];
?>
<aside class="sidebar">
    <div>
        <div class="brand">
            <div class="logo">📈</div>
            <div>
                <h1>Portafolio V5</h1>
                <p>Premium Dashboard</p>
            </div>
        </div>

        <nav class="premium-menu">
            <a class="<?= $active=='dashboard'?'active':'' ?>" href="<?= $base ?>index_v5.php">🏠 Dashboard</a>
            <a class="<?= $active=='activos'?'active':'' ?>" href="<?= $base ?>pages/activos.php">📊 Activos</a>

            <div class="menu-group <?= in_array($active, $tradePages) ? 'open' : '' ?>">
                <button type="button" class="menu-parent">
                    <span>💼 Operaciones</span>
                    <span class="chevron">⌄</span>
                </button>
                <div class="submenu">
                    <a class="<?= $active=='compras'?'active':'' ?>" href="<?= $base ?>pages/compras.php">🛒 Compras</a>
                    <a class="<?= $active=='sell'?'active':'' ?>" href="<?= $base ?>pages/sell.php">💸 Ventas</a>
                </div>
            </div>

            <div class="menu-group <?= in_array($active, $alertPages) ? 'open' : '' ?>">
                <button type="button" class="menu-parent">
                    <span>🔔 Alertas</span>
                    <span class="chevron">⌄</span>
                </button>
                <div class="submenu">
                    <a class="<?= $active=='alertas'?'active':'' ?>" href="<?= $base ?>pages/alertas.php">Alertas</a>
                    <a class="<?= $active=='centro_alertas'?'active':'' ?>" href="<?= $base ?>pages/centro_alertas.php">Centro Alertas</a>
                    <a class="<?= $active=='notifications'?'active':'' ?>" href="<?= $base ?>pages/notifications.php">Notificaciones</a>
                    <a class="<?= $active=='push_notifications'?'active':'' ?>" href="<?= $base ?>pages/push_notifications.php">Push Notifications</a>
                    <a class="<?= $active=='smart_signals'?'active':'' ?>" href="<?= $base ?>pages/smart_signals.php">Smart Signals</a>
                    <a class="<?= $active=='ai_insights'?'active':'' ?>" href="<?= $base ?>pages/ai_insights.php">AI Insights</a>
                </div>
            </div>

            <div class="menu-group <?= in_array($active, $dividendPages) ? 'open' : '' ?>">
                <button type="button" class="menu-parent">
                    <span>💰 Dividendos</span>
                    <span class="chevron">⌄</span>
                </button>
                <div class="submenu">
                    <a class="<?= $active=='dividendos'?'active':'' ?>" href="<?= $base ?>pages/dividendos.php">Dividendos</a>
                    <a class="<?= $active=='dividend_tracker'?'active':'' ?>" href="<?= $base ?>pages/dividend_tracker.php">Dividend Tracker</a>
                </div>
            </div>

            <div class="menu-group <?= in_array($active, $analyticsPages) ? 'open' : '' ?>">
                <button type="button" class="menu-parent">
                    <span>📊 Analytics</span>
                    <span class="chevron">⌄</span>
                </button>
                <div class="submenu">
                    <a class="<?= $active=='advanced_analytics'?'active':'' ?>" href="<?= $base ?>pages/advanced_analytics.php">Advanced Analytics</a>
                    <a class="<?= $active=='market_data'?'active':'' ?>" href="<?= $base ?>pages/market_data.php">Market Data</a>
                    <a class="<?= $active=='ai_portfolio_advisor'?'active':'' ?>" href="<?= $base ?>pages/ai_portfolio_advisor.php">AI Portfolio Advisor</a>
                    <a class="<?= $active=='ai_finance_chat'?'active':'' ?>" href="<?= $base ?>pages/ai_finance_chat.php">AI Finance Chat</a>
                </div>
            </div>

            <div class="menu-group <?= in_array($active, $automationPages) ? 'open' : '' ?>">
                <button type="button" class="menu-parent">
                    <span>⚙️ Automation</span>
                    <span class="chevron">⌄</span>
                </button>
                <div class="submenu">
                    <a class="<?= $active=='automation_center'?'active':'' ?>" href="<?= $base ?>pages/automation_center.php">Automation Center</a>
                    <a class="<?= $active=='scheduler_center'?'active':'' ?>" href="<?= $base ?>pages/scheduler_center.php">Scheduler / Cron</a>
                </div>
            </div>

            <div class="menu-group <?= in_array($active, $historyPages) ? 'open' : '' ?>">
                <button type="button" class="menu-parent">
                    <span>📚 Historial</span>
                    <span class="chevron">⌄</span>
                </button>
                <div class="submenu">
                    <a class="<?= $active=='historial'?'active':'' ?>" href="<?= $base ?>pages/historial.php">Historial</a>
                    <a class="<?= $active=='historial_avanzado'?'active':'' ?>" href="<?= $base ?>pages/historial_avanzado.php">Historial Pro</a>
                </div>
            </div>

            <a class="<?= $active=='rebalanceo'?'active':'' ?>" href="<?= $base ?>pages/rebalanceo.php">⚖️ Rebalanceo</a>
            <a class="<?= $active=='rutinas'?'active':'' ?>" href="<?= $base ?>pages/rutinas.php">🗓️ Rutinas</a>
            <a class="<?= $active=='metas'?'active':'' ?>" href="<?= $base ?>pages/metas.php">🎯 Metas</a>

            <?php if($isAdmin): ?>
            <a class="<?= $active=='users'?'active':'' ?>" href="<?= $base ?>pages/users.php">👤 Usuarios</a>
            <?php endif; ?>
        </nav>
    </div>

    <div class="sidebar-footer">
        <a href="<?= $base ?>api/market_data_engine.php?redirect=<?= $base ?>index_v5.php">📡 Market Data</a>
        <a href="<?= $base ?>api/save_snapshot.php">💾 Guardar snapshot</a>
        <a href="<?= $base ?>api/generate_notifications.php">🔔 Generar notificaciones</a>
        <a href="<?= $base ?>api/smart_dividend_engine.php">💰 Smart Dividend Engine</a>
        <a href="<?= $base ?>api/logout.php">Cerrar sesión</a>
    </div>
</aside>
<?php
}
?>
