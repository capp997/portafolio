<?php
require_once __DIR__ . "/../config/menu.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$report = $pdo->query("
SELECT * FROM portfolio_ai_reports
ORDER BY created_at DESC
LIMIT 1
")->fetch(PDO::FETCH_ASSOC);

if(!$report){
    $report = [
        'portfolio_score'=>0,
        'risk_level'=>'Sin análisis',
        'diversification_score'=>0,
        'recommendation'=>'Genera el primer análisis.'
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>AI Portfolio Advisor</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="../assets/style_v5.css">
<link rel="stylesheet" href="../assets/ai_portfolio.css">
<link rel="stylesheet" href="../assets/menu_dropdown.css">
<link rel="stylesheet" href="../assets/sidebar_buttons_fix.css">
<link rel="stylesheet" href="../assets/mobile_premium.css">
</head>
<body>

<div class="advisor-wrap">

<section class="advisor-hero">
<div>
<p>AI Advisor</p>
<h1>Portfolio Intelligence</h1>
<span>Análisis automático de riesgo y diversificación.</span>
</div>

<a href="../api/generate_ai_report.php" class="advisor-btn">
Generar análisis
</a>
</section>

<?php if(isset($_GET['generated'])): ?>
<div class="advisor-success">
Análisis generado correctamente ✅
</div>
<?php endif; ?>

<section class="advisor-grid">

<div class="advisor-card glow">
<span>Portfolio Score</span>
<h2><?= $report['portfolio_score'] ?>/100</h2>
</div>

<div class="advisor-card">
<span>Riesgo</span>
<h2><?= $report['risk_level'] ?></h2>
</div>

<div class="advisor-card">
<span>Diversificación</span>
<h2><?= $report['diversification_score'] ?>%</h2>
</div>

</section>

<section class="advisor-panel">
<h3>Recomendación IA</h3>
<p><?= htmlspecialchars($report['recommendation']) ?></p>
</section>

<section class="advisor-panel">
<h3>Qué evalúa la IA</h3>

<ul>
<li>Distribución crypto/stocks</li>
<li>Diversificación general</li>
<li>Cantidad de activos</li>
<li>Exposición de riesgo</li>
<li>Balance del portafolio</li>
</ul>
</section>

<a href="../index_v5.php" class="advisor-back">
← Volver al Dashboard
</a>

</div>

<script src="../assets/menu_dropdown.js"></script>
<script src="../assets/mobile_premium.js"></script>
</body>
</html>
