<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$insights = $pdo->query("
    SELECT *
    FROM ai_insights
    ORDER BY created_at DESC
    LIMIT 40
")->fetchAll(PDO::FETCH_ASSOC);

function typeClass($type){
    if($type === 'risk') return 'risk';
    if($type === 'opportunity') return 'opportunity';
    if($type === 'dividend') return 'dividend';
    if($type === 'rebalance') return 'rebalance';
    return 'market';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>AI Insights</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="../assets/style_v5.css">
<link rel="stylesheet" href="../assets/unified_pages.css">
<link rel="stylesheet" href="../assets/menu_dropdown.css">
<link rel="stylesheet" href="../assets/ai_insights_engine.css">
</head>

<body>

<div class="layout">

<aside class="sidebar">
<div>
<div class="brand">
<div class="logo">🧠</div>
<div>
<h1>AI Insights</h1>
<p>Intelligence Engine</p>
</div>
</div>

<nav class="premium-menu">
<a href="../index_v5.php">🏠 Dashboard</a>
<a class="active" href="ai_insights.php">🧠 AI Insights</a>
<a href="ai_finance_chat.php">💬 AI Chat</a>
<a href="ai_portfolio_advisor.php">🧠 AI Advisor</a>
<a href="smart_signals.php">🤖 Smart Signals</a>
<a href="advanced_analytics.php">📊 Analytics</a>
<a href="report_center.php">📄 Reportes</a>
</nav>
</div>

<div class="sidebar-footer">
<a href="../api/generate_ai_insights.php">✨ Generar insights</a>
<a href="../api/logout.php">Cerrar sesión</a>
</div>
</aside>

<main class="content">

<section class="insights-hero">
<div>
<p>AI Insights Engine</p>
<h1>Insights inteligentes del portafolio</h1>
<span>OpenAI analiza tus activos, señales y riesgo para generar ideas prácticas.</span>
</div>

<a class="hero-btn" href="../api/generate_ai_insights.php">Generar insights</a>
</section>

<?php if(isset($_GET['generated'])): ?>
<div class="ok-box">Se generaron <?=intval($_GET['generated'])?> insights correctamente ✅</div>
<?php endif; ?>

<?php if(isset($_GET['error'])): ?>
<div class="error-box">
Error generando insights. Si ves “quota”, activa billing de OpenAI. Si no, revisa tu API key.
</div>
<?php endif; ?>

<section class="insights-grid">

<?php foreach($insights as $i): ?>
<div class="insight-card <?=typeClass($i['insight_type'])?>">
<div class="insight-top">
<span class="badge"><?=strtoupper(htmlspecialchars($i['insight_type']))?></span>
<small><?=intval($i['confidence'])?>% confidence</small>
</div>

<h2><?=htmlspecialchars($i['title'])?></h2>
<p><?=htmlspecialchars($i['content'])?></p>

<div class="insight-footer">
<?=date("M d, Y h:i A", strtotime($i['created_at']))?>
</div>
</div>
<?php endforeach; ?>

<?php if(count($insights) === 0): ?>
<div class="empty-box">
No hay insights todavía. Presiona <b>Generar insights</b>.
</div>
<?php endif; ?>

</section>

</main>
</div>

<script src="../assets/menu_dropdown.js"></script>
</body>
</html>
