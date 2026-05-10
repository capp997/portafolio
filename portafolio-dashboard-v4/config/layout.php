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
<link rel="stylesheet" href="../assets/action_buttons.css">
<link rel="stylesheet" href="../assets/mobile_premium.css">
<link rel="stylesheet" href="../assets/sidebar_buttons_fix.css">
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
        <nav>
            <a class="<?= $active=='dashboard'?'active':'' ?>" href="../index_v5.php">Dashboard</a>
            <a class="<?= $active=='activos'?'active':'' ?>" href="activos.php">Activos</a>
            <a class="<?= $active=='alertas'?'active':'' ?>" href="alertas.php">Alertas</a>
            <a class="<?= $active=='centro_alertas'?'active':'' ?>" href="centro_alertas.php">Centro Alertas</a>
            <a class="<?= $active=='compras'?'active':'' ?>" href="compras.php">Compras</a>
            <a class="<?= $active=='dividendos'?'active':'' ?>" href="dividendos.php">Dividendos</a>
            <a class="<?= $active=='dividend_tracker'?'active':'' ?>" href="dividend_tracker.php">Dividend Tracker</a>
            <a class="<?= $active=='rebalanceo'?'active':'' ?>" href="rebalanceo.php">Rebalanceo</a>
            <a class="<?= $active=='rutinas'?'active':'' ?>" href="rutinas.php">Rutinas</a>
            <a class="<?= $active=='metas'?'active':'' ?>" href="metas.php">Metas</a>
            <a class="<?= $active=='historial'?'active':'' ?>" href="historial.php">Historial</a>
            <a class="<?= $active=='historial_avanzado'?'active':'' ?>" href="historial_avanzado.php">Historial Pro</a>
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
<?php
}

function page_end(){
?>
</main>
</div>
<script src="../assets/mobile_premium.js"></script>
<script src="../assets/app.js"></script>
</body>
</html>
<?php
}
?>
