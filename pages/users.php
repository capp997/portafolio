<?php
require_once __DIR__ . "/../config/menu.php";
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";

require_role(['admin']);

$users = $pdo->query("SELECT id,username,role,is_active,created_at FROM users ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Usuarios</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../assets/style_v5.css">
<link rel="stylesheet" href="../assets/unified_pages.css">
<link rel="stylesheet" href="../assets/security.css">
<link rel="stylesheet" href="../assets/menu_dropdown.css">
<link rel="stylesheet" href="../assets/sidebar_buttons_fix.css">
<link rel="stylesheet" href="../assets/mobile_premium.css">
</head>
<body>

<div class="layout">
<?php render_sidebar('users', '../'); ?>

<main class="content">

<section class="security-hero">
<div>
<h1>Gestión de usuarios</h1>
<p>Crea usuarios, define roles y protege el dashboard.</p>
</div>
</section>

<section class="panel">
<h2>Crear usuario</h2>
<form action="../api/create_user.php" method="POST" class="secure-form">
<input name="username" placeholder="Usuario" required>
<input type="password" name="password" placeholder="Contraseña" required>
<select name="role">
<option value="viewer">viewer</option>
<option value="admin">admin</option>
</select>
<button>Crear usuario</button>
</form>
</section>

<section class="panel">
<h2>Usuarios existentes</h2>
<div class="table-wrap">
<table>
<thead>
<tr>
<th>ID</th>
<th>Usuario</th>
<th>Rol</th>
<th>Activo</th>
<th>Creado</th>
<th>Acción</th>
</tr>
</thead>
<tbody>
<?php foreach($users as $u): ?>
<tr>
<td><?=$u['id']?></td>
<td><b><?=htmlspecialchars($u['username'])?></b></td>
<td><?=$u['role']?></td>
<td><?=$u['is_active'] ? 'Sí' : 'No'?></td>
<td><?=$u['created_at']?></td>
<td>
<?php if($u['username'] !== 'admin'): ?>
<form action="../api/toggle_user.php" method="POST">
<input type="hidden" name="id" value="<?=$u['id']?>">
<button><?=$u['is_active'] ? 'Desactivar' : 'Activar'?></button>
</form>
<?php else: ?>
<small>Protegido</small>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</section>

<section class="panel">
<h2>Cambiar contraseña admin</h2>
<form action="../api/change_password.php" method="POST" class="secure-form">
<input type="password" name="new_password" placeholder="Nueva contraseña" required>
<button>Cambiar contraseña</button>
</form>
</section>

</main>
</div>

<script src="../assets/menu_dropdown.js"></script>
<script src="../assets/mobile_premium.js"></script>
</body>
</html>
