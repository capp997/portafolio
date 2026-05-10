<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$items = $pdo->query("SELECT * FROM app_notifications ORDER BY is_read ASC, created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$unread = $pdo->query("SELECT COUNT(*) FROM app_notifications WHERE is_read=0")->fetchColumn();

function nClass($type){
    if($type === 'buy') return 'notice-buy';
    if($type === 'sell') return 'notice-sell';
    if($type === 'dividend') return 'notice-dividend';
    if($type === 'rebalance') return 'notice-rebalance';
    return 'notice-info';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Notificaciones</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../assets/style_v5.css">
<link rel="stylesheet" href="../assets/unified_pages.css">
<link rel="stylesheet" href="../assets/menu_dropdown.css">
<link rel="stylesheet" href="../assets/notifications.css">
</head>
<body>
<div class="layout">
<aside class="sidebar">
<div>
<div class="brand"><div class="logo">🔔</div><div><h1>Notificaciones</h1><p>App Center</p></div></div>
<nav class="premium-menu">
<a href="../index_v5.php">🏠 Dashboard</a>
<a class="active" href="notifications.php">🔔 Notificaciones <?= $unread>0 ? "($unread)" : "" ?></a>
<a href="centro_alertas.php">Centro Alertas</a>
<a href="dividend_tracker.php">Dividend Tracker</a>
<a href="ai_insights.php">AI Insights</a>
</nav>
</div>
<div class="sidebar-footer">
<a href="../api/generate_notifications.php">Generar notificaciones</a>
<a href="../api/logout.php">Cerrar sesión</a>
</div>
</aside>

<main class="content">
<section class="notify-hero">
<div><p class="notify-label">Notification Center</p><h1>Centro de notificaciones</h1><p>Alertas internas desde señales, dividendos y rutinas.</p></div>
<div class="notify-count"><span>No leídas</span><strong><?= $unread ?></strong></div>
</section>

<?php if(isset($_GET['generated'])): ?>
<div class="notify-success">Notificaciones actualizadas ✅</div>
<?php endif; ?>

<section class="action-bar">
<a class="action-btn green" href="../api/generate_notifications.php">🔄 Generar ahora</a>
<form action="../api/mark_all_notifications.php" method="POST"><button class="action-btn dark">✅ Marcar todas leídas</button></form>
</section>

<section class="notification-grid">
<?php foreach($items as $n): ?>
<div class="notification-card <?= nClass($n['type']) ?> <?= $n['is_read'] ? 'read' : '' ?>">
<div class="notification-top">
<div><strong><?=htmlspecialchars($n['title'])?></strong><span><?=htmlspecialchars($n['type'])?></span></div>
<small><?=date("M d, h:i A", strtotime($n['created_at']))?></small>
</div>
<p><?=htmlspecialchars($n['message'])?></p>
<?php if(!$n['is_read']): ?>
<form action="../api/mark_notification_read.php" method="POST">
<input type="hidden" name="id" value="<?=$n['id']?>">
<button>Marcar leída</button>
</form>
<?php else: ?>
<div class="read-label">Leída</div>
<?php endif; ?>
</div>
<?php endforeach; ?>
<?php if(count($items)===0): ?>
<div class="notify-empty">No hay notificaciones. Presiona “Generar ahora”.</div>
<?php endif; ?>
</section>
</main>
</div>
<script src="../assets/menu_dropdown.js"></script>
</body>
</html>
