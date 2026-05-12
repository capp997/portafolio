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
<link rel="stylesheet" href="../assets/menu_dropdown.css">
<link rel="stylesheet" href="../assets/dashboard_premium_ui.css">
<link rel="stylesheet" href="../assets/premium_ui_pack.css">
<link rel="stylesheet" href="../assets/global_page_fix.css">
<link rel="stylesheet" href="../assets/menu_unified_full.css">
</head>
<body>
<div class="layout app">
<?php render_sidebar($active, '../'); ?>
<main class="content main">
<?php
}

function page_end(){
?>
</main>
</div>
<script src="../assets/menu_dropdown.js"></script>
</body>
</html>
<?php } ?>
