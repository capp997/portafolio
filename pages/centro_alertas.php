<?php
require_once __DIR__ . "/../config/menu.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$alerts = $pdo->query("SELECT * FROM smart_alerts ORDER BY is_reviewed ASC, created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$activeCount = $pdo->query("SELECT COUNT(*) FROM smart_alerts WHERE is_reviewed=0")->fetchColumn();

function moneyFlex($n){
    $n = (float)$n;
    if($n < 1) return '$'.rtrim(rtrim(number_format($n,8), '0'), '.');
    return '$'.number_format($n,2);
}
function alertClass($type){
    if(str_contains($type, 'BUY')) return 'smart-buy';
    if(str_contains($type, 'SELL')) return 'smart-sell';
    return 'smart-hold';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Centro de Alertas</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../assets/style_v5.css">
<link rel="stylesheet" href="../assets/unified_pages.css">
<link rel="stylesheet" href="../assets/smart_alerts.css">
<link rel="stylesheet" href="../assets/action_buttons.css">
<link rel="stylesheet" href="../assets/mobile_premium.css">
<link rel="stylesheet" href="../assets/menu_dropdown.css">
<link rel="stylesheet" href="../assets/sidebar_buttons_fix.css">
<link rel="stylesheet" href="../assets/menu_unified_full.css">
</head>
<body>
<div class="layout">
<?php render_sidebar('centro_alertas', '../'); ?>

<main class="content">
<section class="topbar">
    <div>
        <h2>Centro de Alertas Inteligentes 🔔</h2>
        <p>Detecta zonas BUY / SELL usando tus precios base y precios actuales.</p>
    </div>
    <a class="smart-scan-btn" href="../api/scan_alerts.php">Escanear ahora</a>
</section>

<?php if(isset($_GET['scanned'])): ?>
<div class="smart-notice">Escaneo completado ✅</div>
<?php endif; ?>

<section class="cards-grid">
    <div class="card premium-glow"><span>Alertas activas</span><h3><?= $activeCount ?></h3></div>
    <div class="card"><span>Estado</span><h3 class="<?= $activeCount>0 ? 'orange' : 'green' ?>"><?= $activeCount>0 ? 'Revisar' : 'Limpio' ?></h3></div>
    <div class="card"><span>Acción</span><h3 style="font-size:22px;">BUY / SELL</h3></div>
    <div class="card"><span>Sistema</span><h3 class="green">Activo</h3></div>
</section>

<section class="panel">
    <div class="table-header">
        <h2>Alertas detectadas</h2>
        <form action="../api/mark_all_alerts.php" method="POST"><button>Marcar todas revisadas</button></form>
    </div>

    <div class="smart-alert-grid">
        <?php foreach($alerts as $a): ?>
        <div class="smart-alert-card <?= $a['is_reviewed'] ? 'reviewed' : '' ?>">
            <div class="smart-alert-top">
                <div><strong><?= htmlspecialchars($a['ticker']) ?></strong><span class="<?= alertClass($a['alert_type']) ?>"><?= htmlspecialchars($a['alert_type']) ?></span></div>
                <small><?= date("M d, h:i A", strtotime($a['created_at'])) ?></small>
            </div>
            <p><?= htmlspecialchars($a['message']) ?></p>
            <div class="smart-prices">
                <div><span>Actual</span><b><?= moneyFlex($a['price']) ?></b></div>
                <div><span>Base</span><b><?= moneyFlex($a['base_price']) ?></b></div>
            </div>
            <?php if(!$a['is_reviewed']): ?>
            <form action="../api/mark_alert_reviewed.php" method="POST">
                <input type="hidden" name="id" value="<?= $a['id'] ?>">
                <button>Marcar revisada</button>
            </form>
            <?php else: ?>
            <div class="reviewed-label">Revisada</div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>

        <?php if(count($alerts) === 0): ?>
        <div class="smart-empty">No hay alertas todavía. Presiona “Escanear ahora”.</div>
        <?php endif; ?>
    </div>
</section>
</main>
</div>
<script src="../assets/smart_alerts.js"></script>
<script src="../assets/mobile_premium.js"></script>
<script src="../assets/menu_dropdown.js"></script>
</body>
</html>
