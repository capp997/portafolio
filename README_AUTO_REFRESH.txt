ACTUALIZACIÓN AUTO REFRESH

NO borra tus datos.

INSTALACIÓN:

1. Copia estas carpetas dentro de:
C:\xampp\htdocs\portafolio-dashboard-v4\

- assets
- api

2. Reemplaza api/update_prices.php cuando Windows pregunte.

3. Abre tu archivo:
C:\xampp\htdocs\portafolio-dashboard-v4\index_v5.php

4. Antes de </body> pega estas dos líneas:

<link rel="stylesheet" href="assets/auto_refresh.css">
<script src="assets/auto_refresh.js"></script>

Debe quedar cerca del final, antes de:
</body>

5. Abre:
http://localhost/portafolio-dashboard-v4/index_v5.php

USO:

- Aparecerá un panel abajo a la derecha.
- ON activa actualización automática cada 5 minutos.
- OFF la apaga.
- Actualizar = actualiza precios manualmente.
- Snapshot = guarda valor actual en historial.

IMPORTANTE:
Para que auto refresh funcione, tu archivo config/api.php debe tener tu API key de Finnhub.
