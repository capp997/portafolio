<?php
require_once __DIR__ . "/auth.php";

function page_start($title, $active){
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title><?= $title ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="../assets/style_v5.css">
<link rel="stylesheet" href="../assets/unified_pages.css">
<link rel="stylesheet" href="../assets/sidebar_buttons_fix.css">
<link rel="stylesheet" href="../assets/menu_dropdown.css">
<link rel="stylesheet" href="../assets/mobile_premium.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<div class="layout">

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

            <a class="<?= $active=='dashboard'?'active':'' ?>" href="../index_v5.php">🏠 Dashboard</a>
            <a class="<?= $active=='activos'?'active':'' ?>" href="activos.php">📊 Activos</a>

            <div class="menu-group <?= in_array($active, ['alertas','centro_alertas','ai_insights']) ? 'open' : '' ?>">
                <button type="button" class="menu-parent">
                    <span>🔔 Alertas</span>
                    <span class="chevron">⌄</span>
                </button>
                <div class="submenu">
                    <a class="<?= $active=='alertas'?'active':'' ?>" href="alertas.php">Alertas</a>
                    <a class="<?= $active=='centro_alertas'?'active':'' ?>" href="centro_alertas.php">Centro Alertas</a>
                    <a class="<?= $active=='ai_insights'?'active':'' ?>" href="ai_insights.php">AI Insights</a>
                </div>
            </div>

            <a class="<?= $active=='compras'?'active':'' ?>" href="compras.php">🛒 Compras</a>

            <div class="menu-group <?= in_array($active, ['dividendos','dividend_tracker']) ? 'open' : '' ?>">
                <button type="button" class="menu-parent">
                    <span>💰 Dividendos</span>
                    <span class="chevron">⌄</span>
                </button>
                <div class="submenu">
                    <a class="<?= $active=='dividendos'?'active':'' ?>" href="dividendos.php">Dividendos</a>
                    <a class="<?= $active=='dividend_tracker'?'active':'' ?>" href="dividend_tracker.php">Dividend Tracker</a>
                </div>
            </div>

            <a class="<?= $active=='rebalanceo'?'active':'' ?>" href="rebalanceo.php">⚖️ Rebalanceo</a>
            <a class="<?= $active=='rutinas'?'active':'' ?>" href="rutinas.php">🗓️ Rutinas</a>
            <a class="<?= $active=='metas'?'active':'' ?>" href="metas.php">🎯 Metas</a>

            <div class="menu-group <?= in_array($active, ['historial','historial_avanzado']) ? 'open' : '' ?>">
                <button type="button" class="menu-parent">
                    <span>📈 Historial</span>
                    <span class="chevron">⌄</span>
                </button>
                <div class="submenu">
                    <a class="<?= $active=='historial'?'active':'' ?>" href="historial.php">Historial</a>
                    <a class="<?= $active=='historial_avanzado'?'active':'' ?>" href="historial_avanzado.php">Historial Pro</a>
                </div>
            </div>

            <a class="<?= $active=='users'?'active':'' ?>" href="users.php">👤 Usuarios</a>

        </nav>
    </div>

    <div class="sidebar-footer">
        <a href="../api/update_prices.php?redirect=../index_v5.php">📈 Actualizar precios</a>
        <a href="../api/save_snapshot.php">💾 Guardar snapshot</a>
        <a href="../api/scan_alerts.php">🔔 Escanear alertas</a>
        <a href="../api/smart_dividend_engine.php">💰 Smart Dividend Engine</a>
        <a href="../api/logout.php">Cerrar sesión</a>
    </div>

</aside>

<main class="content">
<?php
}

function page_end(){
?>
</main>
</div>

<script src="../assets/menu_dropdown.js"></script>
<script src="../assets/mobile_premium.js"></script>
<script src="../assets/app.js"></script>
</body>
</html>
<?php
}
?>
