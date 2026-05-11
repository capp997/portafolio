<?php
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/menu.php";

function page_start($title, $active){
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($title) ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="../assets/style_v5.css">
<link rel="stylesheet" href="../assets/unified_pages.css">
<link rel="stylesheet" href="../assets/sidebar_buttons_fix.css">
<link rel="stylesheet" href="../assets/menu_dropdown.css">
<link rel="stylesheet" href="../assets/mobile_premium.css">
<link rel="stylesheet" href="../assets/action_buttons.css">
<link rel="stylesheet" href="../assets/advanced_analytics.css">
<link rel="stylesheet" href="../assets/advanced_history.css">
<link rel="stylesheet" href="../assets/ai_insights.css">
<link rel="stylesheet" href="../assets/ai_portfolio.css">
<link rel="stylesheet" href="../assets/automation_layer.css">
<link rel="stylesheet" href="../assets/dividend_tracker.css">
<link rel="stylesheet" href="../assets/history.css">
<link rel="stylesheet" href="../assets/market_data_engine.css">
<link rel="stylesheet" href="../assets/notifications.css">
<link rel="stylesheet" href="../assets/openai_chat.css">
<link rel="stylesheet" href="../assets/push_notifications.css">
<link rel="stylesheet" href="../assets/scheduler.css">
<link rel="stylesheet" href="../assets/security.css">
<link rel="stylesheet" href="../assets/sell.css">
<link rel="stylesheet" href="../assets/smart_alerts.css">
<link rel="stylesheet" href="../assets/smart_signals.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
<div class="layout">
<?php render_sidebar($active, '../'); ?>
<main class="content">
<?php
}

function page_end(){
?>
</main>
</div>
<script src="../assets/menu_dropdown.js"></script>
<script src="../assets/mobile_premium.js"></script>
<script src="../assets/app.js"></script>
</body>
</html>
<?php
}
?>
