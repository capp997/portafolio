<?php
require_once __DIR__ . "/../config/menu.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$subs = $pdo->query("SELECT * FROM push_subscriptions ORDER BY created_at DESC LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Push Notifications</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="../assets/style_v5.css">
<link rel="stylesheet" href="../assets/unified_pages.css">
<link rel="stylesheet" href="../assets/push_notifications.css">
<link rel="stylesheet" href="../assets/menu_dropdown.css">
<link rel="stylesheet" href="../assets/sidebar_buttons_fix.css">
<link rel="stylesheet" href="../assets/mobile_premium.css">
<link rel="stylesheet" href="../assets/global_page_fix.css">
</head>
<body>
<div class="layout">
<?php render_sidebar('push_notifications', '../'); ?>
<main class="content">
<div class="push-wrap">

<section class="push-hero">
<div>
<p>Push Center</p>
<h1>Notificaciones reales PWA</h1>
<span>Activa permisos y prueba notificaciones del sistema.</span>
</div>
</section>

<section class="push-actions">
<button id="enablePushBtn">🔔 Activar notificaciones</button>
<button id="testPushBtn">✅ Probar notificación</button>
<a href="../pages/notifications.php">Ver notificaciones internas</a>
</section>

<section class="push-panel">
<h2>Dispositivos / permisos recientes</h2>

<div class="table-wrap">
<table>
<thead>
<tr>
<th>ID</th>
<th>Permiso</th>
<th>Activo</th>
<th>Fecha</th>
</tr>
</thead>
<tbody>
<?php foreach($subs as $s): ?>
<tr>
<td><?=$s['id']?></td>
<td><?=$s['permission']?></td>
<td><?=$s['is_active'] ? 'Sí' : 'No'?></td>
<td><?=$s['created_at']?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</section>

<a class="back-btn" href="../index_v5.php">← Volver al Dashboard</a>

</div>

<script src="../assets/push_notifications.js"></script>
<script src="../assets/menu_dropdown.js"></script>
<script src="../assets/mobile_premium.js"></script>
</main>
</div>
<script src="../assets/menu_dropdown.js"></script>
</body>
</html>
