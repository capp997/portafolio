<?php
session_start();
if(isset($_SESSION["user_id"]) && !isset($_GET["debug"])){
    header("Location: /index_v5.php");
    exit;
}
require_once __DIR__ . "/config/db.php";

$error = "";
$debug = isset($_GET["debug"]);

if($_SERVER["REQUEST_METHOD"] === "POST"){

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if(!$username || !$password){
        $error = "Completa usuario/email y contraseña.";
    }else{

        try{
            $stmt = $pdo->prepare("
                SELECT *
                FROM users
                WHERE username = ? OR email = ?
                LIMIT 1
            ");

            $stmt->execute([$username, $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!$user){
                $error = "Usuario no encontrado.";
            }elseif((int)($user["is_active"] ?? 1) !== 1){
                $error = "Usuario desactivado.";
            }else{

                $validPassword = false;

                if(isset($user["password_hash"]) && password_verify($password, $user["password_hash"])){
                    $validPassword = true;
                }

                if(isset($user["password"]) && password_verify($password, $user["password"])){
                    $validPassword = true;
                }

                if(isset($user["password"]) && $password === $user["password"]){
                    $validPassword = true;
                }

                if(!$validPassword){
                    $error = "Contraseña incorrecta.";
                }else{

                    $_SESSION["user_id"] = $user["id"];
                    $_SESSION["username"] = $user["username"] ?? $user["email"];
                    $_SESSION["role"] = $user["role"] ?? "user";

                    session_write_close();

                    header("Location: /index_v5.php");
                    exit;
                }
            }

        }catch(Exception $e){
            $error = "Error DB: " . $e->getMessage();
        }
    }
}

$mode = $_GET["mode"] ?? "login";
if(!in_array($mode, ["login","register","forgot"])){
    $mode = "login";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Login - Portafolio V5</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="assets/auth_center.css">
</head>

<body>

<div class="auth-page">

    <section class="auth-card">

        <div class="brand-row">
            <div class="brand-logo">📈</div>
            <div>
                <h1>Portafolio <span>V5</span></h1>
                <p>Premium Dashboard</p>
            </div>
        </div>

        <div class="auth-box">

            <div class="auth-tabs">
                <a class="active" href="login.php">Iniciar sesión</a>
                <a href="login.php?mode=register">Registrarse</a>
                <a href="login.php?mode=forgot">Olvidé contraseña</a>
            </div>

            <h2>Iniciar <span>sesión</span></h2>
            <p class="auth-subtitle">Bienvenido de nuevo. Accede a tu panel financiero.</p>

            <?php if(isset($_GET["success"])): ?>
            <div class="success-box">Operación completada correctamente ✅</div>
            <?php endif; ?>

            <?php if($error): ?>
            <div class="error-box"><?=htmlspecialchars($error)?></div>
            <?php endif; ?>

            <?php if($debug): ?>
            <div class="error-box">
                DEBUG activo<br>
                Request: <?=htmlspecialchars($_SERVER["REQUEST_METHOD"])?><br>
                Session user_id: <?=htmlspecialchars($_SESSION["user_id"] ?? "none")?><br>
                Error: <?=htmlspecialchars($error ?: "none")?>
            </div>
            <?php endif; ?>

            <form method="POST" action="login.php<?= $debug ? '?debug=1' : '' ?>" class="auth-form">

                <label>Usuario o correo electrónico</label>
                <div class="input-wrap">
                    <span>✉️</span>
                    <input 
                        type="text" 
                        name="username" 
                        placeholder="Ingresa tu usuario o email"
                        autocomplete="username"
                        required>
                </div>

                <label>Contraseña</label>
                <div class="input-wrap">
                    <span>🔒</span>
                    <input 
                        id="passwordInput"
                        type="password" 
                        name="password" 
                        placeholder="••••••••"
                        autocomplete="current-password"
                        required>
                    <button type="button" class="eye-btn" onclick="togglePassword('passwordInput')">👁️</button>
                </div>

                <div class="auth-options">
                    <label class="check-row">
                        <input type="checkbox" name="remember">
                        <span>Recordarme</span>
                    </label>

                    <a href="login.php?mode=forgot">¿Olvidaste tu contraseña?</a>
                </div>

                <button class="auth-btn" type="submit">↪ Iniciar sesión</button>
            </form>

            <div class="switch-link">
                ¿No tienes una cuenta?
                <a href="login.php?mode=register">Regístrate ahora</a>
            </div>

        </div>

    </section>

    <section class="preview-card">
        <div class="hero-content">
            <h2>Tu portafolio.<br>Tu futuro. <span>Tu control.</span></h2>
            <p>Administra tus inversiones, analiza el mercado y toma decisiones inteligentes con Portafolio V5.</p>
        </div>

        <div class="preview-dashboard">
            <div class="preview-header">
                <h3>Resumen de portafolio</h3>
                <span>Premium</span>
            </div>

            <div class="preview-kpis">
                <div><span>Valor total</span><strong>$124,580.75</strong><b>+12.45%</b></div>
                <div><span>Ganancia / Pérdida</span><strong>$13,742.50</strong><b>+12.45%</b></div>
                <div><span>Rentabilidad</span><strong>+12.45%</strong><b>+2.34%</b></div>
                <div><span>AI Score</span><strong>88/100</strong><b>Activo</b></div>
            </div>
        </div>
    </section>

</div>

<script>
function togglePassword(id){
    const input = document.getElementById(id);
    input.type = input.type === "password" ? "text" : "password";
}
</script>

</body>
</html>
