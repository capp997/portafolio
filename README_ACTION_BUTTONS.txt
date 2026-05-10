ACTION BUTTONS UPDATE

Esta actualización agrega estilos y snippets para botones funcionales.

INSTALACIÓN:

1. Copia:
assets/action_buttons.css

dentro de:
C:\xampp\htdocs\portafolio-dashboard-v4\assets\

2. En index_v5.php, antes de </head>, agrega:

<link rel="stylesheet" href="assets/action_buttons.css">

3. En pages/dividend_tracker.php y pages/centro_alertas.php, antes de </head>, agrega:

<link rel="stylesheet" href="../assets/action_buttons.css">

4. Abre los archivos .html dentro de la carpeta snippets y copia los bloques:

- snippets_dashboard.html → pegar después del topbar en index_v5.php
- snippets_dividend_tracker.html → pegar después del hero en dividend_tracker.php
- snippets_centro_alertas.html → pegar después del topbar en centro_alertas.php

BOTONES INCLUIDOS:
- Actualizar precios
- Guardar snapshot
- Escanear alertas
- Smart Dividend Engine
- Dividend Tracker
- Centro Alertas
