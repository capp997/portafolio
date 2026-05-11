<?php
require_once __DIR__ . "/../config/menu.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$runs = $pdo->query("
    SELECT *
    FROM scheduler_runs
    ORDER BY created_at DESC
    LIMIT 50
")->fetchAll(PDO::FETCH_ASSOC);

$last = $runs[0] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Scheduler Center</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="../assets/style_v5.css">
<link rel="stylesheet" href="../assets/unified_pages.css">
<link rel="stylesheet" href="../assets/menu_dropdown.css">
<link rel="stylesheet" href="../assets/scheduler.css">
<link rel="stylesheet" href="../assets/global_page_fix.css">
</head>

<body>

<div class="layout">
<?php render_sidebar('scheduler_center', '../'); ?>
<main class="content">

<section class="scheduler-hero">
<div>
<p>Auto Scheduler</p>
<h1>Cron System</h1>
<span>Automatiza precios, señales, notificaciones y análisis IA.</span>
</div>

<div class="scheduler-status">
<span>Última ejecución</span>
<strong><?= $last ? date("M d, h:i A", strtotime($last['created_at'])) : "Nunca" ?></strong>
</div>
</section>

<section class="scheduler-grid">
<div class="scheduler-card">
<span>Estado</span>
<h2 class="green">Listo</h2>
</div>

<div class="scheduler-card">
<span>Registros</span>
<h2><?=count($runs)?></h2>
</div>

<div class="scheduler-card">
<span>Motores</span>
<h2>4</h2>
</div>
</section>

<section class="panel">
<div class="table-header">
<h2>URL para Render Cron</h2>
<span>Protegida con token</span>
</div>

<div class="cron-box">
https://TU_RENDER_URL/api/cron_runner.php?token=TU_CRON_SECRET
</div>

<p class="scheduler-note">
En Render crea un Cron Job apuntando a esa URL. El token debe coincidir con la variable CRON_SECRET.
</p>
</section>

<section class="panel">
<div class="table-header">
<h2>Historial del Scheduler</h2>
<span>Últimos 50 logs</span>
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
<?php foreach($runs as $r): ?>
<tr>
<td><b><?=$r['run_type']?></b></td>
<td><?=$r['status']?></td>
<td><?=htmlspecialchars($r['details'])?></td>
<td><?=date("M d, h:i A", strtotime($r['created_at']))?></td>
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
