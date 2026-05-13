<?php
/*
DASHBOARD WORKSPACE TOOLBAR
Pegar en index_v5.php arriba de las cards/widgets:
<?php include __DIR__ . "/components/dashboard_workspace_toolbar.php"; ?>
*/
?>
<section class="dashboard-workspace-toolbar">
    <div class="workspace-title">
        <strong>Workspace personalizado</strong>
        <span>Mueve, cambia tamaño, oculta o restaura widgets.</span>
    </div>
    <div class="workspace-actions">
        <button type="button" class="primary" onclick="pv5SaveDashboardLayout()">Guardar layout</button>
        <button type="button" onclick="pv5ToggleWidgetPicker()">Mostrar/Ocultar widgets</button>
        <button type="button" onclick="pv5ResetDashboardLayout()">Reset</button>
    </div>
</section>
<section class="widget-picker">
    <h3>Administrar widgets</h3>
    <div class="widget-picker-list"></div>
</section>
