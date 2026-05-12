<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$totalUsers = 0;
$activeUsers = 0;
$totalLogs = 0;
$todayLogs = 0;
$totalSignals = 0;
$totalInsights = 0;
$totalNotifications = 0;
$totalCron = 0;
$lastCron = null;
$lastInsight = null;
$lastSignal = null;
$lastOpenAiError = null;

try{ $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(); }catch(Exception $e){}
try{ $activeUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE is_active=1")->fetchColumn(); }catch(Exception $e){}
try{ $totalLogs = $pdo->query("SELECT COUNT(*) FROM activity_logs")->fetchColumn(); }catch(Exception $e){}
try{ $todayLogs = $pdo->query("SELECT COUNT(*) FROM activity_logs WHERE created_at >= CURRENT_DATE")->fetchColumn(); }catch(Exception $e){}
try{ $totalSignals = $pdo->query("SELECT COUNT(*) FROM smart_signals")->fetchColumn(); }catch(Exception $e){}
try{ $totalInsights = $pdo->query("SELECT COUNT(*) FROM ai_insights")->fetchColumn(); }catch(Exception $e){}
try{ $totalNotifications = $pdo->query("SELECT COUNT(*) FROM app_notifications")->fetchColumn(); }catch(Exception $e){}
try{ $totalCron = $pdo->query("SELECT COUNT(*) FROM scheduler_runs")->fetchColumn(); }catch(Exception $e){}

try{
    $lastCron = $pdo->query("SELECT * FROM scheduler_runs ORDER BY created_at DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
}catch(Exception $e){}

try{
    $lastInsight = $pdo->query("SELECT * FROM ai_insights ORDER BY created_at DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
}catch(Exception $e){}

try{
    $lastSignal = $pdo->query("SELECT * FROM smart_signals ORDER BY created_at DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
}catch(Exception $e){}

try{
    $lastOpenAiError = $pdo->query("
        SELECT *
        FROM ai_insights
        WHERE title ILIKE '%OpenAI Error%' OR content ILIKE '%quota%' OR content ILIKE '%API%'
        ORDER BY created_at DESC
        LIMIT 1
    ")->fetch(PDO::FETCH_ASSOC);
}catch(Exception $e){}

$recentActivity = [];
try{
    $recentActivity = $pdo->query("
        SELECT *
        FROM activity_logs
        ORDER BY created_at DESC
        LIMIT 10
    ")->fetchAll(PDO::FETCH_ASSOC);
}catch(Exception $e){}

$recentCron = [];
try{
    $recentCron = $pdo->query("
        SELECT *
        FROM scheduler_runs
        ORDER BY created_at DESC
        LIMIT 10
    ")->fetchAll(PDO::FETCH_ASSOC);
}catch(Exception $e){}

$recentSignals = [];
try{
    $recentSignals = $pdo->query("
        SELECT *
        FROM smart_signals
        ORDER BY created_at DESC
        LIMIT 8
    ")->fetchAll(PDO::FETCH_ASSOC);
}catch(Exception $e){}

$services = [
    [
        "name"=>"Database",
        "status"=>"online",
        "details"=>"Supabase/Postgres conectado"
    ],
    [
        "name"=>"Auth System",
        "status"=>$totalUsers > 0 ? "online" : "warning",
        "details"=>$totalUsers." usuarios registrados"
    ],
    [
        "name"=>"OpenAI",
        "status"=>$lastOpenAiError ? "warning" : "online",
        "details"=>$lastOpenAiError ? "Revisar último error OpenAI" : "Sin errores recientes detectados"
    ],
    [
        "name"=>"Scheduler",
        "status"=>$lastCron ? ($lastCron['status']==='success' ? "online" : "warning") : "warning",
        "details"=>$lastCron ? $lastCron['run_type']." · ".$lastCron['status'] : "Sin ejecuciones"
    ],
    [
        "name"=>"Signals Engine",
        "status"=>$totalSignals > 0 ? "online" : "warning",
        "details"=>$totalSignals." señales generadas"
    ],
    [
        "name"=>"Notifications",
        "status"=>$totalNotifications > 0 ? "online" : "warning",
        "details"=>$totalNotifications." notificaciones"
    ]
];

function statusClass($s){
    if($s === 'online' || $s === 'success') return 'ok';
    if($s === 'warning') return 'warn';
    if($s === 'failed' || $s === 'error') return 'bad';
    return 'neutral';
}

function niceDate($d){
    if(!$d) return 'N/A';
    return date("M d, h:i A", strtotime($d));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>AI Admin Center</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="../assets/style_v5.css">
<link rel="stylesheet" href="../assets/unified_pages.css">
<link rel="stylesheet" href="../assets/menu_dropdown.css">
<link rel="stylesheet" href="../assets/ai_admin_center.css">
</head>
<body>

<div class="layout">

<aside class="sidebar">
<div>
<div class="brand">
<div class="logo">🧠</div>
<div><h1>AI Admin</h1><p>Control Center</p></div>
</div>

<nav class="premium-menu">
<a href="../index_v5.php">🏠 Dashboard</a>
<a class="active" href="ai_admin_center.php">🧠 AI Admin Center</a>
<a href="users.php">👥 Usuarios</a>
<a href="activity_logs.php">🛡️ Activity Logs</a>
<a href="automation_center.php">⚙️ Automation</a>
<a href="scheduler_center.php">⏰ Scheduler</a>
<a href="ai_insights.php">✨ AI Insights</a>
<a href="smart_signals.php">🤖 Smart Signals</a>
</nav>
</div>

<div class="sidebar-footer">
<a href="../api/run_admin_health_check.php">🩺 Health Check</a>
<a href="../api/logout.php">Cerrar sesión</a>
</div>
</aside>

<main class="content">

<section class="admin-hero">
<div>
<p>AI Admin Center</p>
<h1>Centro administrativo inteligente</h1>
<span>Monitorea usuarios, IA, cron jobs, señales, logs y salud del sistema.</span>
</div>

<div class="admin-hero-actions">
<a href="../api/run_admin_health_check.php">🩺 Health Check</a>
<a href="activity_logs.php">🛡️ Ver auditoría</a>
</div>
</section>

<section class="admin-kpis">
<div class="admin-kpi">
<span>Usuarios</span>
<h2><?=$totalUsers?></h2>
<small><?=$activeUsers?> activos</small>
</div>

<div class="admin-kpi">
<span>Activity Logs</span>
<h2><?=$totalLogs?></h2>
<small><?=$todayLogs?> hoy</small>
</div>

<div class="admin-kpi">
<span>AI Insights</span>
<h2><?=$totalInsights?></h2>
<small><?= $lastInsight ? niceDate($lastInsight['created_at']) : 'Sin insights' ?></small>
</div>

<div class="admin-kpi">
<span>Smart Signals</span>
<h2><?=$totalSignals?></h2>
<small><?= $lastSignal ? niceDate($lastSignal['created_at']) : 'Sin señales' ?></small>
</div>
</section>

<section class="admin-grid">

<div class="admin-panel system-panel">
<div class="panel-title">
<h2>Estado del sistema</h2>
<span>Servicios principales</span>
</div>

<div class="service-list">
<?php foreach($services as $s): ?>
<div class="service-row">
<div>
<strong><?=$s['name']?></strong>
<small><?=$s['details']?></small>
</div>
<span class="status-pill <?=statusClass($s['status'])?>"><?=$s['status']?></span>
</div>
<?php endforeach; ?>
</div>
</div>

<div class="admin-panel ai-panel">
<div class="panel-title">
<h2>Estado IA / OpenAI</h2>
<span>Último diagnóstico</span>
</div>

<div class="ai-status-card <?= $lastOpenAiError ? 'warn-card' : 'ok-card' ?>">
<h3><?= $lastOpenAiError ? 'Revisar OpenAI' : 'IA operativa' ?></h3>
<p>
<?php if($lastOpenAiError): ?>
<?=htmlspecialchars(substr($lastOpenAiError['content'],0,260))?>
<?php else: ?>
No se detectan errores recientes de OpenAI. Los insights y AI Chat están listos.
<?php endif; ?>
</p>
<small><?= $lastOpenAiError ? niceDate($lastOpenAiError['created_at']) : 'Estado actual' ?></small>
</div>

<div class="mini-actions">
<a href="ai_insights.php">✨ Generar insights</a>
<a href="ai_finance_chat.php">💬 AI Chat</a>
</div>
</div>

</section>

<section class="admin-grid">

<div class="admin-panel">
<div class="panel-title">
<h2>Actividad reciente</h2>
<span>Últimos 10 eventos</span>
</div>

<div class="compact-list">
<?php foreach($recentActivity as $a): ?>
<div class="compact-row">
<div>
<strong><?=htmlspecialchars($a['action'])?></strong>
<small><?=htmlspecialchars($a['username'] ?? 'system')?> · <?=htmlspecialchars($a['details'] ?? '')?></small>
</div>
<span><?=niceDate($a['created_at'])?></span>
</div>
<?php endforeach; ?>

<?php if(count($recentActivity)===0): ?>
<div class="empty-admin">No hay actividad todavía.</div>
<?php endif; ?>
</div>
</div>

<div class="admin-panel">
<div class="panel-title">
<h2>Cron / Scheduler</h2>
<span>Últimas ejecuciones</span>
</div>

<div class="compact-list">
<?php foreach($recentCron as $c): ?>
<div class="compact-row">
<div>
<strong><?=htmlspecialchars($c['run_type'])?></strong>
<small><?=htmlspecialchars($c['details'])?></small>
</div>
<span class="status-pill <?=statusClass($c['status'])?>"><?=htmlspecialchars($c['status'])?></span>
</div>
<?php endforeach; ?>

<?php if(count($recentCron)===0): ?>
<div class="empty-admin">No hay cron runs todavía.</div>
<?php endif; ?>
</div>
</div>

</section>

<section class="admin-panel">
<div class="panel-title">
<h2>Smart Signals recientes</h2>
<span>Últimas señales del motor</span>
</div>

<div class="signals-admin-grid">
<?php foreach($recentSignals as $s): ?>
<div class="signal-admin-card">
<div>
<strong><?=$s['ticker']?></strong>
<span><?=$s['signal']?></span>
</div>
<p><?=htmlspecialchars($s['note'] ?? '')?></p>
<small>Confianza <?=$s['confidence']?>% · Riesgo <?=$s['risk_score']?>%</small>
</div>
<?php endforeach; ?>

<?php if(count($recentSignals)===0): ?>
<div class="empty-admin">No hay señales todavía.</div>
<?php endif; ?>
</div>
</section>

</main>
</div>

<script src="../assets/menu_dropdown.js"></script>
</body>
</html>
