ACTUALIZACIÓN LOGIN - NO BORRA TUS DATOS

INSTALACIÓN:

1. Descomprime este ZIP.

2. Copia estos archivos/carpetas dentro de tu proyecto actual:
   C:\xampp\htdocs\portafolio-dashboard-v4\

   - login.php
   - config\auth.php
   - config\layout.php
   - api\login_action.php
   - api\logout.php

3. Acepta reemplazar config\layout.php.

4. IMPORTANTE:
   Abre tu archivo:
   C:\xampp\htdocs\portafolio-dashboard-v4\index.php

   Arriba del todo, justo después de <?php, agrega esta línea:

   require_once __DIR__ . "/config/auth.php";

5. Abre:
   http://localhost/portafolio-dashboard-v4/login.php

DATOS INICIALES:
Usuario: admin
Contraseña: 1234

PARA CAMBIAR LA CONTRASEÑA:
Abre:
api\login_action.php

Cambia:
$valid_user = "admin";
$valid_pass = "1234";

SEGURIDAD:
No compartas el link de ngrok sin cambiar la contraseña.
