ACTUALIZACIÓN - BOTÓN CERRAR SESIÓN VISIBLE

Esta actualización NO borra tus datos.

INSTALACIÓN:

1. Copia estos archivos dentro de tu proyecto:
   C:\xampp\htdocs\portafolio-dashboard-v4\

   - api\logout.php
   - config\auth.php
   - config\topbar.php

2. Abre tu archivo:
   C:\xampp\htdocs\portafolio-dashboard-v4\assets\style.css

3. Copia y pega al FINAL el contenido de:
   assets\logout-visible.css

4. Ahora abre cada página principal donde quieras ver el botón y agrega esta línea DESPUÉS de los require iniciales:

   require_once __DIR__ . "/config/topbar.php";

Para index.php usa:
   require_once __DIR__ . "/config/topbar.php";

Para páginas dentro de /pages usa:
   require_once __DIR__ . "/../config/topbar.php";

EJEMPLO EN index.php:

<?php
require_once __DIR__ . "/config/auth.php";
require_once __DIR__ . "/config/db.php";
require_once __DIR__ . "/config/topbar.php";

EJEMPLO EN pages/activos.php:

<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/layout.php";
require_once __DIR__ . "/../config/topbar.php";

Si tus páginas usan layout.php actualizado, también debe salir en el menú lateral.
