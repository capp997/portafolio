<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$editUser = null;

if(isset($_GET['edit'])){
    $stmt = $pdo->prepare("
        SELECT id, username, email, role, is_active, created_at
        FROM users
        WHERE id=?
        LIMIT 1
    ");
    $stmt->execute([$_GET['edit']]);
    $editUser = $stmt->fetch(PDO::FETCH_ASSOC);
}

$users = $pdo->query("
    SELECT id, username, email, role, is_active, created_at
    FROM users
    ORDER BY created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

function msgText($msg){
    return match($msg){
        'updated' => 'Usuario actualizado correctamente ✅',
        'password' => 'Contraseña actualizada correctamente ✅',
        'deleted' => 'Usuario eliminado correctamente ✅',
        'status' => 'Estado del usuario actualizado ✅',
        default => ''
    };
}

function errorText($error){
    return match($error){
        'missing' => 'Faltan datos requeridos.',
        'exists' => 'Ese usuario o email ya existe.',
        'self_delete' => 'No puedes eliminar tu propio usuario.',
        'notfound' => 'Usuario no encontrado.',
        'password' => 'La contraseña debe tener mínimo 6 caracteres.',
        'db' => 'Error en base de datos.',
        default => 'Error procesando la acción.'
    };
}
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
<link rel="stylesheet" href="../assets/users_crud.css">
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
<a href="../login.php?mode=register">➕ Crear usuario</a>
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
<span>Edita, activa/desactiva, cambia contraseña o elimina usuarios.</span>
</div>

<a href="../login.php?mode=register">Crear usuario</a>
</section>

<?php if(isset($_GET['msg']) && msgText($_GET['msg'])): ?>
<div class="users-ok"><?=msgText($_GET['msg'])?></div>
<?php endif; ?>

<?php if(isset($_GET['error'])): ?>
<div class="users-error"><?=errorText($_GET['error'])?></div>
<?php endif; ?>

<?php if($editUser): ?>
<section class="users-edit-panel">
<div class="edit-head">
<div>
<h2>Editar usuario</h2>
<p>ID #<?=$editUser['id']?> · <?=htmlspecialchars($editUser['username'])?></p>
</div>
<a href="users.php">Cancelar</a>
</div>

<div class="edit-grid">

<form action="../api/update_user.php" method="POST" class="user-form">
<input type="hidden" name="id" value="<?=$editUser['id']?>">

<label>Usuario</label>
<input type="text" name="username" value="<?=htmlspecialchars($editUser['username'])?>" required>

<label>Email</label>
<input type="email" name="email" value="<?=htmlspecialchars($editUser['email'] ?? '')?>">

<div class="form-row">
<div>
<label>Rol</label>
<select name="role">
<option value="user" <?=$editUser['role']==='user'?'selected':''?>>user</option>
<option value="admin" <?=$editUser['role']==='admin'?'selected':''?>>admin</option>
</select>
</div>

<div>
<label>Activo</label>
<select name="is_active">
<option value="1" <?=$editUser['is_active']?'selected':''?>>Sí</option>
<option value="0" <?=!$editUser['is_active']?'selected':''?>>No</option>
</select>
</div>
</div>

<button class="save-btn" type="submit">💾 Guardar cambios</button>
</form>

<form action="../api/change_user_password.php" method="POST" class="user-form">
<input type="hidden" name="id" value="<?=$editUser['id']?>">

<label>Nueva contraseña</label>
<input type="password" name="password" placeholder="Mínimo 6 caracteres" required autocomplete="new-password">

<label>Confirmar contraseña</label>
<input type="password" name="confirm_password" placeholder="Repite contraseña" required autocomplete="new-password">

<button class="password-btn" type="submit">🔐 Cambiar contraseña</button>
</form>

</div>
</section>
<?php endif; ?>

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
<th>Acciones</th>
</tr>
</thead>
<tbody>
<?php foreach($users as $u): ?>
<tr>
<td><?=$u['id']?></td>
<td><b><?=htmlspecialchars($u['username'])?></b></td>
<td><?=htmlspecialchars($u['email'] ?? '')?></td>
<td><span class="role-pill role-<?=htmlspecialchars($u['role'])?>"><?=htmlspecialchars($u['role'])?></span></td>
<td>
<span class="<?=$u['is_active'] ? 'status-active' : 'status-inactive'?>">
<?=$u['is_active'] ? 'Activo' : 'Inactivo'?>
</span>
</td>
<td><?=date("M d, Y", strtotime($u['created_at']))?></td>
<td>
<div class="user-actions">

<a class="icon-btn edit" href="users.php?edit=<?=$u['id']?>" title="Editar">✏️</a>

<form action="../api/toggle_user_status.php" method="POST">
<input type="hidden" name="id" value="<?=$u['id']?>">
<input type="hidden" name="is_active" value="<?=$u['is_active'] ? 0 : 1?>">
<button class="icon-btn <?=$u['is_active'] ? 'disable' : 'enable'?>" title="<?=$u['is_active'] ? 'Desactivar' : 'Activar'?>">
<?=$u['is_active'] ? '🚫' : '✅'?>
</button>
</form>

<form action="../api/delete_user.php" method="POST" onsubmit="return confirm('¿Seguro que quieres eliminar este usuario?');">
<input type="hidden" name="id" value="<?=$u['id']?>">
<button class="icon-btn delete" title="Eliminar">🗑️</button>
</form>

</div>
</td>
</tr>
<?php endforeach; ?>

<?php if(count($users)===0): ?>
<tr>
<td colspan="7">No hay usuarios registrados.</td>
</tr>
<?php endif; ?>
</tbody>
</table>
</div>
</section>

</main>
</div>

<script src="../assets/menu_dropdown.js"></script>
</body>
</html>
