<?php
require_once __DIR__ . "/../config/menu.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$action = trim($_GET['action'] ?? '');
$user = trim($_GET['user'] ?? '');

$where = [];
$params = [];

if($action){
    $where[] = "action ILIKE ?";
    $params[] = "%".$action."%";
}

if($user){
    $where[] = "username ILIKE ?";
    $params[] = "%".$user."%";
}

$sql = "SELECT * FROM activity_logs";

if(count($where)){
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY created_at DESC LIMIT 200";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = $pdo->query("SELECT COUNT(*) FROM activity_logs")->fetchColumn();

$today = $pdo->query("
    SELECT COUNT(*)
    FROM activity_logs
    WHERE created_at >= CURRENT_DATE
")->fetchColumn();

$loginCount = $pdo->query("
    SELECT COUNT(*)
    FROM activity_logs
    WHERE action ILIKE '%login%'
")->fetchColumn();

function actionClass($a){
    if(str_contains($a, 'delete')) return 'danger';
    if(str_contains($a, 'login')) return 'success';
    if(str_contains($a, 'update') || str_contains($a, 'edit')) return 'info';
    if(str_contains($a, 'password')) return 'warning';
    return 'default';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Activity Logs</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../assets/style_v5.css">
<link rel="stylesheet" href="../assets/unified_pages.css">
<link rel="stylesheet" href="../assets/menu_dropdown.css">
<link rel="stylesheet" href="../assets/activity_logs.css">
<link rel="stylesheet" href="../assets/menu_unified_full.css">
</head>
<body>

<div class="layout">

<?php render_sidebar('activity_logs', '../'); ?>

<main class="content">

<section class="audit-hero">
<div>
<p>Admin Security</p>
<h1>Activity Logs / Auditoría</h1>
<span>Monitorea usuarios, acciones críticas, IP y navegador.</span>
</div>

<a href="../api/export_activity_logs.php">Export CSV</a>
</section>

<section class="audit-cards">
<div><span>Total logs</span><h2><?=$total?></h2></div>
<div><span>Hoy</span><h2><?=$today?></h2></div>
<div><span>Logins</span><h2><?=$loginCount?></h2></div>
</section>

<section class="audit-filter">
<form method="GET">
<input type="text" name="user" placeholder="Buscar usuario..." value="<?=htmlspecialchars($user)?>">
<input type="text" name="action" placeholder="Buscar acción..." value="<?=htmlspecialchars($action)?>">
<button>Filtrar</button>
<a href="activity_logs.php">Limpiar</a>
</form>
</section>

<section class="panel">
<div class="table-header">
<h2>Últimos eventos</h2>
<span><?=count($logs)?> mostrados</span>
</div>

<div class="table-wrap">
<table>
<thead>
<tr>
<th>Fecha</th>
<th>Usuario</th>
<th>Acción</th>
<th>Entidad</th>
<th>Detalles</th>
<th>IP</th>
</tr>
</thead>
<tbody>
<?php foreach($logs as $l): ?>
<tr>
<td><?=date("M d, Y h:i A", strtotime($l['created_at']))?></td>
<td><b><?=htmlspecialchars($l['username'] ?? 'system')?></b></td>
<td><span class="action-pill <?=actionClass($l['action'])?>"><?=htmlspecialchars($l['action'])?></span></td>
<td><?=htmlspecialchars(($l['entity'] ?? '').(($l['entity_id'] ?? '') ? ' #'.$l['entity_id'] : ''))?></td>
<td><?=htmlspecialchars($l['details'] ?? '')?></td>
<td><?=htmlspecialchars($l['ip_address'] ?? '')?></td>
</tr>
<?php endforeach; ?>

<?php if(count($logs)===0): ?>
<tr><td colspan="6">No hay logs.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</section>

</main>
</div>

<script src="../assets/menu_dropdown.js"></script>
</body>
</html>
