<?php
function menu_page_exists($base, $file){
    return file_exists(__DIR__ . '/../pages/' . $file);
}

function menu_item($active, $key, $href, $icon, $label){
    $class = $active === $key ? 'active' : '';
    echo '<a class="'.$class.'" href="'.$href.'"><span class="menu-ico">'.$icon.'</span><span>'.$label.'</span></a>';
}

function menu_group($active, $keys, $icon, $label, $items, $base){
    $open = in_array($active, $keys) ? 'open' : '';
    echo '<div class="menu-group '.$open.'">';
    echo '<button type="button" class="menu-parent"><span><span class="menu-ico">'.$icon.'</span>'.$label.'</span><span class="chevron">⌄</span></button>';
    echo '<div class="submenu">';
    foreach($items as $it){
        if(isset($it['file']) && !menu_page_exists($base, $it['file'])) continue;
        menu_item($active, $it['key'], $base.'pages/'.$it['file'], $it['icon'], $it['label']);
    }
    echo '</div></div>';
}

function render_sidebar($active='dashboard', $base=''){
    if(session_status() === PHP_SESSION_NONE){ session_start(); }

    $isAdmin = (($_SESSION['role'] ?? '') === 'admin');

    $operations = ['compras','sell'];
    $alerts = ['alertas','centro_alertas','notifications','push_notifications'];
    $ai = ['ai_insights','ai_finance_chat','ai_portfolio_advisor','ai_admin_center','smart_signals'];
    $analytics = ['advanced_analytics','market_data','live_charts','report_center','portfolio_report'];
    $dividends = ['dividendos','dividend_tracker'];
    $automation = ['automation_center','scheduler_center'];
    $history = ['historial','historial_avanzado','activity_logs'];
    $planning = ['metas','rutinas','rebalanceo'];
    $admin = ['users','activity_logs','ai_admin_center'];
?>
<aside class="sidebar unified-sidebar">
    <div>
        <div class="brand">
            <div class="logo">📈</div>
            <div>
                <h1>Portafolio V5</h1>
                <p>Premium Dashboard</p>
            </div>
        </div>

        <nav class="premium-menu unified-menu">
            <?php menu_item($active,'dashboard',$base.'index_v5.php','🏠','Dashboard'); ?>
            <?php if(menu_page_exists($base,'activos.php')) menu_item($active,'activos',$base.'pages/activos.php','📊','Activos'); ?>

            <?php menu_group($active,$operations,'💼','Operaciones',[
                ['key'=>'compras','file'=>'compras.php','icon'=>'🛒','label'=>'Compras'],
                ['key'=>'sell','file'=>'sell.php','icon'=>'💸','label'=>'Ventas'],
            ],$base); ?>

            <?php menu_group($active,$alerts,'🔔','Alertas',[
                ['key'=>'alertas','file'=>'alertas.php','icon'=>'🔔','label'=>'Alertas'],
                ['key'=>'centro_alertas','file'=>'centro_alertas.php','icon'=>'🚨','label'=>'Centro Alertas'],
                ['key'=>'notifications','file'=>'notifications.php','icon'=>'📬','label'=>'Notificaciones'],
                ['key'=>'push_notifications','file'=>'push_notifications.php','icon'=>'📲','label'=>'Push Notifications'],
            ],$base); ?>

            <?php menu_group($active,$ai,'🧠','AI Center',[
                ['key'=>'ai_insights','file'=>'ai_insights.php','icon'=>'✨','label'=>'AI Insights'],
                ['key'=>'ai_finance_chat','file'=>'ai_finance_chat.php','icon'=>'💬','label'=>'AI Finance Chat'],
                ['key'=>'ai_portfolio_advisor','file'=>'ai_portfolio_advisor.php','icon'=>'🧠','label'=>'AI Advisor'],
                ['key'=>'smart_signals','file'=>'smart_signals.php','icon'=>'🤖','label'=>'Smart Signals'],
                ['key'=>'ai_admin_center','file'=>'ai_admin_center.php','icon'=>'🛡️','label'=>'AI Admin Center'],
            ],$base); ?>

            <?php menu_group($active,$analytics,'📊','Analytics & Reports',[
                ['key'=>'advanced_analytics','file'=>'advanced_analytics.php','icon'=>'📈','label'=>'Advanced Analytics'],
                ['key'=>'market_data','file'=>'market_data.php','icon'=>'📡','label'=>'Market Data'],
                ['key'=>'live_charts','file'=>'live_charts.php','icon'=>'📉','label'=>'Live Charts'],
                ['key'=>'report_center','file'=>'report_center.php','icon'=>'📄','label'=>'Report Center'],
                ['key'=>'portfolio_report','file'=>'portfolio_report.php','icon'=>'🖨️','label'=>'Portfolio Report'],
            ],$base); ?>

            <?php menu_group($active,$dividends,'💰','Dividendos',[
                ['key'=>'dividendos','file'=>'dividendos.php','icon'=>'💵','label'=>'Dividendos'],
                ['key'=>'dividend_tracker','file'=>'dividend_tracker.php','icon'=>'📅','label'=>'Dividend Tracker'],
            ],$base); ?>

            <?php menu_group($active,$planning,'🎯','Planificación',[
                ['key'=>'metas','file'=>'metas.php','icon'=>'🎯','label'=>'Metas'],
                ['key'=>'rutinas','file'=>'rutinas.php','icon'=>'🗓️','label'=>'Rutinas'],
                ['key'=>'rebalanceo','file'=>'rebalanceo.php','icon'=>'⚖️','label'=>'Rebalanceo'],
            ],$base); ?>

            <?php menu_group($active,$automation,'⚙️','Automation',[
                ['key'=>'automation_center','file'=>'automation_center.php','icon'=>'⚙️','label'=>'Automation Center'],
                ['key'=>'scheduler_center','file'=>'scheduler_center.php','icon'=>'⏰','label'=>'Scheduler / Cron'],
            ],$base); ?>

            <?php menu_group($active,$history,'📚','Historial & Auditoría',[
                ['key'=>'historial','file'=>'historial.php','icon'=>'📜','label'=>'Historial'],
                ['key'=>'historial_avanzado','file'=>'historial_avanzado.php','icon'=>'📈','label'=>'Historial Pro'],
                ['key'=>'activity_logs','file'=>'activity_logs.php','icon'=>'🛡️','label'=>'Activity Logs'],
            ],$base); ?>

            <?php if($isAdmin): ?>
            <?php menu_group($active,$admin,'👥','Admin',[
                ['key'=>'users','file'=>'users.php','icon'=>'👥','label'=>'Usuarios'],
                ['key'=>'activity_logs','file'=>'activity_logs.php','icon'=>'🛡️','label'=>'Activity Logs'],
                ['key'=>'ai_admin_center','file'=>'ai_admin_center.php','icon'=>'🧠','label'=>'AI Admin Center'],
            ],$base); ?>
            <?php endif; ?>
        </nav>
    </div>

    <div class="sidebar-footer">
        <?php if(file_exists(__DIR__.'/../api/market_data_engine.php')): ?>
        <a href="<?= $base ?>api/market_data_engine.php?redirect=<?= $base ?>index_v5.php">📡 Actualizar precios</a>
        <?php endif; ?>
        <?php if(file_exists(__DIR__.'/../api/save_snapshot.php')): ?>
        <a href="<?= $base ?>api/save_snapshot.php">💾 Guardar snapshot</a>
        <?php endif; ?>
        <?php if(file_exists(__DIR__.'/../api/generate_notifications.php')): ?>
        <a href="<?= $base ?>api/generate_notifications.php">🔔 Generar notificaciones</a>
        <?php endif; ?>
        <?php if(file_exists(__DIR__.'/../api/logout.php')): ?>
        <a class="logout-link" href="<?= $base ?>api/logout.php">Cerrar sesión</a>
        <?php endif; ?>
    </div>
</aside>
<?php } ?>
