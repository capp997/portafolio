<?php
session_start();
require_once __DIR__ . "/config/db.php";

$mode = $_GET['mode'] ?? 'login';
if(!in_array($mode, ['login','register','forgot'])){
    $mode = 'login';
}

$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
$registered = isset($_GET['registered']);

function errorText($e){
    return match($e){
        'missing' => 'Completa todos los campos requeridos.',
        'invalid' => 'Datos inválidos.',
        'exists' => 'Ese usuario o email ya existe.',
        'password' => 'La contraseña debe tener mínimo 6 caracteres.',
        'match' => 'Las contraseñas no coinciden.',
        'email' => 'Email no válido.',
        'login' => 'Usuario o contraseña incorrectos.',
        'inactive' => 'Este usuario está desactivado.',
        'db' => 'Error conectando con la base de datos.',
        'notfound' => 'No encontramos ese usuario o email.',
        default => ''
    };
}

function successText($s){
    return match($s){
        'registered' => 'Cuenta creada correctamente. Ya puedes iniciar sesión.',
        'reset' => 'Contraseña actualizada correctamente. Ya puedes iniciar sesión.',
        default => ''
    };
}

if($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST['action'] ?? '') === 'login'){
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if(!$username || !$password){
        header("Location: login.php?error=missing");
        exit;
    }

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
            header("Location: login.php?error=login");
            exit;
        }

        if((int)($user["is_active"] ?? 1) !== 1){
            header("Location: login.php?error=inactive");
            exit;
        }

        if(password_verify($password, $user["password_hash"])){
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = $user["role"] ?? "user";

            header("Location: index_v5.php");
            exit;
        }

        session_write_close();
        header("Location: index_v5.php");
        exit;

    }catch(Exception $e){
        header("Location: login.php?error=db");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Acceso - Portafolio V5</title>
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
                <a class="<?=$mode==='login'?'active':''?>" href="login.php?mode=login">Iniciar sesión</a>
                <a class="<?=$mode==='register'?'active':''?>" href="login.php?mode=register">Registrarse</a>
                <a class="<?=$mode==='forgot'?'active':''?>" href="login.php?mode=forgot">Olvidé contraseña</a>
            </div>

            <?php if($mode === 'login'): ?>
                <h2>Iniciar <span>sesión</span></h2>
                <p class="auth-subtitle">Bienvenido de nuevo. Accede a tu panel financiero.</p>

                <?php if($registered || $success): ?>
                <div class="success-box"><?=htmlspecialchars($registered ? 'Cuenta creada correctamente. Ya puedes iniciar sesión.' : successText($success))?></div>
                <?php endif; ?>

                <?php if($error): ?>
                <div class="error-box"><?=htmlspecialchars(errorText($error))?></div>
                <?php endif; ?>

                <form method="POST" class="auth-form">
                    <input type="hidden" name="action" value="login">

                    <label>Usuario o correo electrónico</label>
                    <div class="input-wrap">
                        <span>✉️</span>
                        <input type="text" name="username" placeholder="Ingresa tu usuario o email" autocomplete="username" required>
                    </div>

                    <label>Contraseña</label>
                    <div class="input-wrap">
                        <span>🔒</span>
                        <input id="passwordInput" type="password" name="password" placeholder="••••••••" autocomplete="current-password" required>
                        <button type="button" class="eye-btn" onclick="togglePassword('passwordInput')">👁️</button>
                    </div>

                    <div class="auth-options">
                        <label class="check-row">
                            <input type="checkbox" name="remember">
                            <span>Recordarme por 30 días</span>
                        </label>

                        <a href="login.php?mode=forgot">¿Olvidaste tu contraseña?</a>
                    </div>

                    <button class="auth-btn" type="submit">↪ Iniciar sesión</button>
                </form>

                <div class="divider"><span></span><p>o continúa con</p><span></span></div>

                <div class="social-row">
                    <button type="button">🌐 Google</button>
                    <button type="button"> Apple</button>
                </div>

                <div class="switch-link">
                    ¿No tienes una cuenta?
                    <a href="login.php?mode=register">Regístrate ahora</a>
                </div>
            <?php endif; ?>


            <?php if($mode === 'register'): ?>
                <h2>Crear <span>cuenta</span></h2>
                <p class="auth-subtitle">Regístrate para guardar tu portafolio y usar todas las funciones.</p>

                <?php if($error): ?>
                <div class="error-box"><?=htmlspecialchars(errorText($error))?></div>
                <?php endif; ?>

                <form action="api/auth_register.php" method="POST" class="auth-form">

                    <label>Usuario</label>
                    <div class="input-wrap">
                        <span>👤</span>
                        <input type="text" name="username" placeholder="Ej: carlos" autocomplete="username" required>
                    </div>

                    <label>Email</label>
                    <div class="input-wrap">
                        <span>✉️</span>
                        <input type="email" name="email" placeholder="tu@email.com" autocomplete="email">
                    </div>

                    <label>Contraseña</label>
                    <div class="input-wrap">
                        <span>🔒</span>
                        <input id="newPassword" type="password" name="password" placeholder="Mínimo 6 caracteres" autocomplete="new-password" required>
                        <button type="button" class="eye-btn" onclick="togglePassword('newPassword')">👁️</button>
                    </div>

                    <label>Confirmar contraseña</label>
                    <div class="input-wrap">
                        <span>🔐</span>
                        <input id="confirmPassword" type="password" name="confirm_password" placeholder="Repite la contraseña" autocomplete="new-password" required>
                        <button type="button" class="eye-btn" onclick="togglePassword('confirmPassword')">👁️</button>
                    </div>

                    <button class="auth-btn" type="submit">➕ Crear cuenta</button>
                </form>

                <div class="switch-link">
                    ¿Ya tienes cuenta?
                    <a href="login.php?mode=login">Inicia sesión</a>
                </div>
            <?php endif; ?>


            <?php if($mode === 'forgot'): ?>
                <h2>Recuperar <span>contraseña</span></h2>
                <p class="auth-subtitle">Por ahora puedes restablecer tu contraseña con tu usuario o email registrado.</p>

                <?php if($error): ?>
                <div class="error-box"><?=htmlspecialchars(errorText($error))?></div>
                <?php endif; ?>

                <form action="api/auth_reset_password.php" method="POST" class="auth-form">

                    <label>Usuario o email</label>
                    <div class="input-wrap">
                        <span>✉️</span>
                        <input type="text" name="identifier" placeholder="Usuario o email" autocomplete="username" required>
                    </div>

                    <label>Nueva contraseña</label>
                    <div class="input-wrap">
                        <span>🔒</span>
                        <input id="resetPassword" type="password" name="password" placeholder="Nueva contraseña" autocomplete="new-password" required>
                        <button type="button" class="eye-btn" onclick="togglePassword('resetPassword')">👁️</button>
                    </div>

                    <label>Confirmar nueva contraseña</label>
                    <div class="input-wrap">
                        <span>🔐</span>
                        <input id="resetConfirm" type="password" name="confirm_password" placeholder="Repite la nueva contraseña" autocomplete="new-password" required>
                        <button type="button" class="eye-btn" onclick="togglePassword('resetConfirm')">👁️</button>
                    </div>

                    <button class="auth-btn" type="submit">🔁 Cambiar contraseña</button>
                </form>

                <div class="switch-link">
                    ¿Recordaste tu contraseña?
                    <a href="login.php?mode=login">Iniciar sesión</a>
                </div>
            <?php endif; ?>

        </div>

    </section>

    <section class="preview-card">

        <div class="hero-content">
            <h2>Tu portafolio.<br>Tu futuro. <span>Tu control.</span></h2>
            <p>Administra tus inversiones, analiza el mercado y toma decisiones inteligentes con Portafolio V5.</p>

            <div class="feature-row">
                <div><i>📊</i><span>Análisis en<br>tiempo real</span></div>
                <div><i>🧾</i><span>Reportes<br>avanzados</span></div>
                <div><i>🔔</i><span>Alertas<br>inteligentes</span></div>
                <div><i>🤖</i><span>Insights<br>con IA</span></div>
            </div>
        </div>

        <div class="preview-dashboard">

            <div class="preview-header">
                <h3>Resumen de portafolio</h3>
                <span>Últimos 6 meses⌄</span>
            </div>

            <div class="preview-kpis">
                <div><span>Valor total</span><strong>$124,580.75</strong><b>+12.45%</b></div>
                <div><span>Ganancia / Pérdida</span><strong>$13,742.50</strong><b>+12.45%</b></div>
                <div><span>Rentabilidad</span><strong>+12.45%</strong><b>+2.34%</b></div>
                <div><span>Efectivo disponible</span><strong>$6,250.00</strong><b>USD</b></div>
            </div>

            <div class="preview-bottom">
                <div class="performance-card">
                    <h4>Rendimiento del portafolio</h4>

                    <svg viewBox="0 0 520 210" class="line-chart">
                        <defs>
                            <linearGradient id="chartFill" x1="0" x2="0" y1="0" y2="1">
                                <stop offset="0%" stop-color="#22c55e" stop-opacity=".45"/>
                                <stop offset="100%" stop-color="#22c55e" stop-opacity="0"/>
                            </linearGradient>
                        </defs>
                        <path d="M10,170 C60,150 70,120 115,125 C160,130 170,80 225,95 C270,110 285,70 340,72 C390,74 410,55 455,60 C490,64 500,30 515,22 L515,205 L10,205 Z" fill="url(#chartFill)"></path>
                        <path d="M10,170 C60,150 70,120 115,125 C160,130 170,80 225,95 C270,110 285,70 340,72 C390,74 410,55 455,60 C490,64 500,30 515,22" fill="none" stroke="#22c55e" stroke-width="4"></path>
                        <circle cx="515" cy="22" r="6" fill="#22c55e"></circle>
                    </svg>

                    <div class="months"><span>Ene</span><span>Feb</span><span>Mar</span><span>Abr</span><span>May</span><span>Jun</span></div>
                </div>

                <div class="allocation-card">
                    <h4>Distribución de activos</h4>
                    <div class="donut"><div><strong>124.6K</strong><span>Total</span></div></div>
                    <ul>
                        <li><span class="dot green"></span>Acciones <b>60.2%</b></li>
                        <li><span class="dot blue"></span>ETFs <b>20.1%</b></li>
                        <li><span class="dot yellow"></span>Criptomonedas <b>10.3%</b></li>
                        <li><span class="dot purple"></span>Efectivo <b>9.4%</b></li>
                    </ul>
                </div>
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
