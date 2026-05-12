<?php
function menu_file_exists($file){
    return file_exists(__DIR__ . '/../pages/' . $file);
}

function menu_link($active, $key, $href, $label, $icon=''){
    $class = $active === $key ? 'active' : '';
    echo '<a class="'.$class.'" href="'.$href.'">'.($icon ? $icon.' ' : '').$label.'</a>';
}

function menu_group($active, $keys, $label, $icon, $items){
    $open = in_array($active, $keys) ? 'open' : '';
    echo '<div class="menu-group '.$open.'">';
    echo '<button type="button" class="menu-parent"><span>'.$icon.' '.$label.'</span><span class="chevron">⌄</span></button>';
    echo '<div class="submenu">';
    foreach($items as $it){
        if(isset($it['file']) && !menu_file_exists($it['file'])) continue;
        menu_link($active, $it['key'], $it['href'], $it['label'], $it['icon'] ?? '');
    }
    echo '</div></div>';
}

function page_start($title, $active){
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($title) ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../assets/style.css">
<link rel="stylesheet" href="../assets/menu_metas_fix.css">
</head>
<body>
<div class="app">
<aside class="sidebar">
    <div class="brand">
        <div class="logo">📈</div>
        <div>
            <h2>Portafolio<br>V5</h2>
            <p>Premium Dashboard</p>
        </div>
    </div>

    <nav class="premium-menu">
        <?php menu_link($active,'dashboard','../index.php','Dashboard','🏠'); ?>
        <?php menu_link($active,'activos','activos.php','Activos','📊'); ?>

        <?php menu_group($active, ['compras','sell'], 'Operaciones', '💼', [
            ['key'=>'compras','href'=>'compras.php','label'=>'Compras','icon'=>'🛒','file'=>'compras.php'],
            ['key'=>'sell','href'=>'sell.php','label'=>'Ventas / Sell','icon'=>'💸','file'=>'sell.php'],
        ]); ?>

        <?php menu_group($active, ['alertas','centro_alertas','notifications','push_notifications','ai_insights'], 'Alertas', '🔔', [
            ['key'=>'alertas','href'=>'alertas.php','label'=>'Alertas','icon'=>'🔔','file'=>'alertas.php'],
            ['key'=>'centro_alertas','href'=>'centro_alertas.php','label'=>'Centro Alertas','icon'=>'🚨','file'=>'centro_alertas.php'],
            ['key'=>'notifications','href'=>'notifications.php','label'=>'Notificaciones','icon'=>'📬','file'=>'notifications.php'],
            ['key'=>'push_notifications','href'=>'push_notifications.php','label'=>'Push Notifications','icon'=>'📲','file'=>'push_notifications.php'],
            ['key'=>'ai_insights','href'=>'ai_insights.php','label'=>'AI Insights','icon'=>'✨','file'=>'ai_insights.php'],
        ]); ?>

        <?php menu_group($active, ['dividendos','dividend_tracker'], 'Dividendos', '💰', [
            ['key'=>'dividendos','href'=>'dividendos.php','label'=>'Dividendos','icon'=>'💵','file'=>'dividendos.php'],
            ['key'=>'dividend_tracker','href'=>'dividend_tracker.php','label'=>'Dividend Tracker','icon'=>'📅','file'=>'dividend_tracker.php'],
        ]); ?>

        <?php menu_group($active, ['advanced_analytics','market_data','live_charts','ai_portfolio_advisor','ai_finance_chat','smart_signals'], 'Analytics', '📊', [
            ['key'=>'advanced_analytics','href'=>'advanced_analytics.php','label'=>'Advanced Analytics','icon'=>'📈','file'=>'advanced_analytics.php'],
            ['key'=>'market_data','href'=>'market_data.php','label'=>'Market Data','icon'=>'📡','file'=>'market_data.php'],
            ['key'=>'live_charts','href'=>'live_charts.php','label'=>'Live Charts','icon'=>'📉','file'=>'live_charts.php'],
            ['key'=>'smart_signals','href'=>'smart_signals.php','label'=>'Smart Signals','icon'=>'🤖','file'=>'smart_signals.php'],
            ['key'=>'ai_portfolio_advisor','href'=>'ai_portfolio_advisor.php','label'=>'AI Advisor','icon'=>'🧠','file'=>'ai_portfolio_advisor.php'],
            ['key'=>'ai_finance_chat','href'=>'ai_finance_chat.php','label'=>'AI Chat','icon'=>'💬','file'=>'ai_finance_chat.php'],
        ]); ?>

        <?php menu_group($active, ['automation_center','scheduler_center'], 'Automation', '⚙️', [
            ['key'=>'automation_center','href'=>'automation_center.php','label'=>'Automation Center','icon'=>'⚙️','file'=>'automation_center.php'],
            ['key'=>'scheduler_center','href'=>'scheduler_center.php','label'=>'Scheduler','icon'=>'⏰','file'=>'scheduler_center.php'],
        ]); ?>

        <?php menu_group($active, ['historial','historial_avanzado'], 'Historial', '📚', [
            ['key'=>'historial','href'=>'historial.php','label'=>'Historial','icon'=>'📜','file'=>'historial.php'],
            ['key'=>'historial_avanzado','href'=>'historial_avanzado.php','label'=>'Historial Pro','icon'=>'📈','file'=>'historial_avanzado.php'],
        ]); ?>

        <?php menu_link($active,'rebalanceo','rebalanceo.php','Rebalanceo','⚖️'); ?>
        <?php menu_link($active,'rutinas','rutinas.php','Rutinas','🗓️'); ?>
        <?php menu_link($active,'metas','metas.php','Metas','🎯'); ?>

        <?php if(menu_file_exists('report_center.php')) menu_link($active,'report_center','report_center.php','Report Center','📄'); ?>
        <?php if(menu_file_exists('users.php')) menu_link($active,'users','users.php','Usuarios','👤'); ?>
    </nav>

    <div class="sidebar-footer">
        <?php if(file_exists(__DIR__.'/../api/market_data_engine.php')): ?>
            <a href="../api/market_data_engine.php?redirect=../index.php">📡 Actualizar precios</a>
        <?php endif; ?>
        <?php if(file_exists(__DIR__.'/../api/save_snapshot.php')): ?>
            <a href="../api/save_snapshot.php">💾 Guardar snapshot</a>
        <?php endif; ?>
        <?php if(file_exists(__DIR__.'/../api/logout.php')): ?>
            <a class="logout-link" href="../api/logout.php">Cerrar sesión</a>
        <?php endif; ?>
    </div>
</aside>
<main class="main">
<?php
}
function page_end(){
?>
</main>
</div>
<script src="../assets/app.js"></script>
<script src="../assets/menu_metas_fix.js"></script>
</body>
</html>
<?php } ?>
