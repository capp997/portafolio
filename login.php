<?php
session_start();

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: index_v5.php");
    exit;
}

$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Login Seguro</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="assets/security.css">
</head>
<body>

<div class="login-page">
    <div class="login-card">
        <div class="login-logo">🔐</div>
        <h1>Acceso seguro</h1>
        <p>Entra a tu plataforma financiera personal.</p>

        <?php if($error): ?>
            <div class="error-box">Usuario o contraseña incorrectos.</div>
        <?php endif; ?>

        <form action="api/login_secure.php" method="POST">
            <label>Usuario</label>
            <input type="text" name="username" required placeholder="admin">

            <label>Contraseña</label>
            <input type="password" name="password" required placeholder="Tu contraseña">

            <button type="submit">Entrar</button>
        </form>

        <small>
            Usuario inicial: <b>admin</b><br>
            Contraseña inicial: <b>CambiaEstaClave123!</b>
        </small>
    </div>
</div>

</body>
</html>
