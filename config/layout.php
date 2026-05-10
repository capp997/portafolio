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
