<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$users = $pdo->query("
    SELECT id, username, email, role, is_active, created_at
    FROM users
    ORDER BY created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Usuarios</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../assets/style_v5.css">
<link rel="stylesheet" href="../assets/unified_pages.css">
<link rel="stylesheet" href="../assets/menu_dropdown.css">
<link rel="stylesheet" href="../assets/users_multiuser.css">
</head>
<body>

<div class="layout">

<aside class="sidebar">
<div>
<div class="brand">
<div class="logo">👥</div>
<div><h1>Usuarios</h1><p>Multiusuario</p></div>
</div>

<nav class="premium-menu">
<a href="../index_v5.php">🏠 Dashboard</a>
<a class="active" href="users.php">👥 Usuarios</a>
<a href="../register.php">➕ Crear usuario</a>
</nav>
</div>

<div class="sidebar-footer">
<a href="../api/logout.php">Cerrar sesión</a>
</div>
</aside>

<main class="content">

<section class="users-hero">
<div>
<p>Multiusuario</p>
<h1>Gestión de usuarios</h1>
<span>Usuarios registrados desde login y panel interno.</span>
</div>

<a href="../register.php">Crear usuario</a>
</section>

<section class="panel">
<div class="table-header">
<h2>Usuarios registrados</h2>
<span><?=count($users)?> usuarios</span>
</div>

<div class="table-wrap">
<table>
<thead>
<tr>
<th>ID</th>
<th>Usuario</th>
<th>Email</th>
<th>Rol</th>
<th>Activo</th>
<th>Creado</th>
</tr>
</thead>
<tbody>
<?php foreach($users as $u): ?>
<tr>
<td><?=$u['id']?></td>
<td><b><?=htmlspecialchars($u['username'])?></b></td>
<td><?=htmlspecialchars($u['email'] ?? '')?></td>
<td><?=htmlspecialchars($u['role'])?></td>
<td><?=$u['is_active'] ? 'Sí' : 'No'?></td>
<td><?=date("M d, Y", strtotime($u['created_at']))?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</section>

</main>
</div>

<script src="../assets/menu_dropdown.js"></script>
</body>
</html>
