<?php
require_once __DIR__ . "/../config/menu.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$countAssets = $pdo->query("SELECT COUNT(*) FROM assets")->fetchColumn();

$countSales = 0;
try{ $countSales = $pdo->query("SELECT COUNT(*) FROM sales")->fetchColumn(); }catch(Exception $e){}

$countSignals = 0;
try{ $countSignals = $pdo->query("SELECT COUNT(*) FROM smart_signals")->fetchColumn(); }catch(Exception $e){}

$countHistory = 0;
try{ $countHistory = $pdo->query("SELECT COUNT(*) FROM portfolio_history")->fetchColumn(); }catch(Exception $e){}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Report Center</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="../assets/style_v5.css">
<link rel="stylesheet" href="../assets/unified_pages.css">
<link rel="stylesheet" href="../assets/menu_dropdown.css">
<link rel="stylesheet" href="../assets/report_center.css">
<link rel="stylesheet" href="../assets/menu_unified_full.css">
</head>

<body>

<div class="layout">

<?php render_sidebar('report_center', '../'); ?>

<main class="content">

<section class="reports-hero">
<div>
<p>Export / Report Center</p>
<h1>Centro de reportes</h1>
<span>Exporta CSV y genera reportes imprimibles tipo PDF.</span>
</div>

<a class="reports-main-btn" href="portfolio_report.php">Abrir reporte PDF</a>
</section>

<section class="reports-stats">
<div>
<span>Activos</span>
<h2><?=$countAssets?></h2>
</div>

<div>
<span>Ventas</span>
<h2><?=$countSales?></h2>
</div>

<div>
<span>Señales</span>
<h2><?=$countSignals?></h2>
</div>

<div>
<span>Historial</span>
<h2><?=$countHistory?></h2>
</div>
</section>

<section class="reports-grid">

<div class="report-card">
<h2>Portfolio Report</h2>
<p>Resumen general del portafolio, exposición, dividendos y señales.</p>
<a href="portfolio_report.php">Abrir / Guardar PDF</a>
</div>

<div class="report-card">
<h2>Activos CSV</h2>
<p>Descarga todos tus activos actuales.</p>
<a href="../api/export_csv.php?type=assets">Descargar CSV</a>
</div>

<div class="report-card">
<h2>Compras CSV</h2>
<p>Historial de compras para control personal.</p>
<a href="../api/export_csv.php?type=purchases">Descargar CSV</a>
</div>

<div class="report-card">
<h2>Ventas CSV</h2>
<p>Ventas, proceeds y P/L realizado.</p>
<a href="../api/export_csv.php?type=sales">Descargar CSV</a>
</div>

<div class="report-card">
<h2>Dividendos CSV</h2>
<p>Registro de dividendos guardados.</p>
<a href="../api/export_csv.php?type=dividends">Descargar CSV</a>
</div>

<div class="report-card">
<h2>Smart Signals CSV</h2>
<p>Historial de señales BUY/HOLD/SELL.</p>
<a href="../api/export_csv.php?type=smart_signals">Descargar CSV</a>
</div>

<div class="report-card">
<h2>Notificaciones CSV</h2>
<p>Historial de notificaciones internas.</p>
<a href="../api/export_csv.php?type=app_notifications">Descargar CSV</a>
</div>

<div class="report-card">
<h2>Historial Portfolio CSV</h2>
<p>Snapshots históricos del portafolio.</p>
<a href="../api/export_csv.php?type=portfolio_history">Descargar CSV</a>
</div>

</section>

<section class="panel">
<div class="table-header">
<h2>Cómo usar reporte PDF</h2>
<span>Browser PDF</span>
</div>

<p class="report-help">
Abre “Portfolio Report” y presiona <b>Imprimir / Guardar PDF</b>. En el navegador selecciona destino <b>Save as PDF</b>.
</p>
</section>

</main>
</div>

<script src="../assets/menu_dropdown.js"></script>
</body>
</html>
