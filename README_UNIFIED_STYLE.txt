ACTUALIZACIÓN ESTILO UNIFICADO

Objetivo:
- Dashboard premium muestra Historial en el menú.
- Todas las páginas usan style_v5.css + unified_pages.css.
- El diseño ya no cambia tanto entre Dashboard, Alertas, Activos, Metas, etc.

INSTALACIÓN:

1. Copia estas carpetas dentro de:
C:\xampp\htdocs\portafolio-dashboard-v4\

- config
- assets

2. Reemplaza:
config\layout.php

3. Copia:
assets\unified_pages.css

4. Ahora abre tu archivo:
C:\xampp\htdocs\portafolio-dashboard-v4\index_v5.php

Busca en el menú donde dice:
<a href="pages/metas.php">Metas</a>

Debajo agrega:
<a href="pages/historial.php">Historial</a>

Debe quedar:
<a href="pages/metas.php">Metas</a>
<a href="pages/historial.php">Historial</a>

5. Abre:
http://localhost/portafolio-dashboard-v4/index_v5.php

NOTA:
Las páginas antiguas que usan page_start() ahora tomarán el estilo premium.
