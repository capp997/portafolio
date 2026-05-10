SECURITY USERS + ROLES UPDATE

IMPORTANTE:
Esto reemplaza el login simple por login real con base de datos.

INSTALACIÓN:

1. Copia estas carpetas/archivos dentro de:
C:\xampp\htdocs\portafolio-dashboard-v4\

- api
- config
- pages
- assets

2. En phpMyAdmin selecciona:
portafolio_db

3. Ejecuta el SQL:
install_users_security.sql

4. Abre:
http://localhost/portafolio-dashboard-v4/login.php

Usuario inicial:
admin

Contraseña inicial:
CambiaEstaClave123!

5. Después entra a:
http://localhost/portafolio-dashboard-v4/pages/users.php

y cambia la contraseña.

ROLES:
admin = puede gestionar usuarios
viewer = puede ver dashboard, pero no administrar usuarios
