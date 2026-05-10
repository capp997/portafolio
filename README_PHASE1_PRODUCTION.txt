FASE 1 - PREPARAR PRODUCCIÓN LOCAL

INSTALACIÓN:

1. Copia dentro de:
C:\xampp\htdocs\portafolio-dashboard-v4\

- config/security.php
- api/_guard.php
- .htaccess
- production_cleanup_check.php

2. Abre:
http://localhost/portafolio-dashboard-v4/production_cleanup_check.php

3. Si te dice eliminar archivos, elimínalos o muévelos fuera del proyecto.

4. Después de revisar, borra:
production_cleanup_check.php

5. Revisa:
http://localhost/portafolio-dashboard-v4/index_v5.php

BORRAR SI EXISTEN:
- reset_admin.php
- test.php
- phpinfo.php
- debug.php
- archivos .zip dentro del proyecto
- archivos .sql dentro del proyecto después de importarlos

NO BORRES:
- config/db.php
- config/auth.php
- config/api.php
- assets
- api
- pages
