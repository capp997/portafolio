<?php
$error = $_GET['error'] ?? '';
function errorMessage($error){
    return match($error){
        'missing' => 'Completa todos los campos requeridos.',
        'username' => 'El usuario debe tener mínimo 3 caracteres.',
        'password' => 'La contraseña debe tener mínimo 6 caracteres.',
        'match' => 'Las contraseñas no coinciden.',
        'email' => 'El email no es válido.',
        'exists' => 'Ese usuario o email ya existe.',
        'db' => 'Error creando usuario. Revisa la base de datos.',
        default => ''
    };
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Crear cuenta - Portafolio V5</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="assets/auth_multiuser.css">
</head>
<body>

<div class="auth-bg">

    <div class="auth-card">

        <div class="auth-logo">📈</div>

        <h1>Crear cuenta</h1>
        <p>Regístrate para usar Portafolio V5.</p>

        <?php if($error): ?>
        <div class="auth-error"><?=errorMessage($error)?></div>
        <?php endif; ?>

        <form action="api/register_user.php" method="POST" class="auth-form">

            <label>Usuario</label>
            <input type="text" name="username" required placeholder="Ej: carlos" autocomplete="username">

            <label>Email</label>
            <input type="email" name="email" placeholder="tu@email.com" autocomplete="email">

            <label>Contraseña</label>
            <input type="password" name="password" required placeholder="Mínimo 6 caracteres" autocomplete="new-password">

            <label>Confirmar contraseña</label>
            <input type="password" name="confirm_password" required placeholder="Repite la contraseña" autocomplete="new-password">

            <button type="submit">Crear cuenta</button>

        </form>

        <div class="auth-links">
            <a href="login.php">Ya tengo cuenta</a>
        </div>

    </div>

</div>

</body>
</html>
