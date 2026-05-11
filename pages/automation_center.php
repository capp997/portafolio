<?php
require_once __DIR__ . "/../config/menu.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$logs = $pdo->query("
SELECT *
FROM automation_logs
ORDER BY created_at DESC
LIMIT 50
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Automation Center</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="../assets/style_v5.css">
<link rel="stylesheet" href="../assets/unified_pages.css">
<link rel="stylesheet" href="../assets/menu_dropdown.css">
<link rel="stylesheet" href="../assets/automation_layer.css">
<link rel="stylesheet" href="../assets/global_page_fix.css">
</head>

<body>

<div class="layout">

<?php render_sidebar('automation_center', '../'); ?>
<main class="content">

<section class="automation-hero">
<div>
<p>Automation Layer</p>
<h1>Centro de automatización IA</h1>
<span>Ejecuta market engine, señales, alertas y advisor automáticamente.</span>
</div>

<a class="automation-btn" href="../api/run_automation_layer.php">
Ejecutar ahora
</a>
</section>

<section class="cards-grid">
<div class="card premium-glow">
<span>Motores conectados</span>
<h3>4</h3>
</div>

<div class="card">
<span>Últimos logs</span>
<h3><?=count($logs)?></h3>
</div>

<div class="card">
<span>Estado</span>
<h3 class="green">Activo</h3>
</div>
</section>

<section class="panel">
<div class="table-header">
<h2>Automation Logs</h2>
<span>Últimas ejecuciones</span>
</div>

<div class="table-wrap">
<table>
<thead>
<tr>
<th>Motor</th>
<th>Status</th>
<th>Detalles</th>
<th>Fecha</th>
</tr>
</thead>
<tbody>
<?php foreach($logs as $l): ?>
<tr>
<td><b><?=$l['action_name']?></b></td>
<td><?=$l['status']?></td>
<td><?=$l['details']?></td>
<td><?=date("M d, h:i A", strtotime($l['created_at']))?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</section>

</main>
</div>

<script src="../assets/menu_dropdown.js"></script>
</body>
</html>
