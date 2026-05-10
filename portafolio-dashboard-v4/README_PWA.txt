PWA UPDATE

1. Copia:
- manifest.json
- service-worker.js
- assets/pwa.js
- assets/pwa.css
- assets/icons/

dentro de:
C:\xampp\htdocs\portafolio-dashboard-v4\

2. En index_v5.php antes de </head> pega:

<link rel="manifest" href="manifest.json">
<meta name="theme-color" content="#16a34a">
<link rel="apple-touch-icon" href="assets/icons/icon-192.png">
<link rel="stylesheet" href="assets/pwa.css">

3. Debajo del topbar pega:

<button id="installAppBtn" class="install-app-btn">
📲 Instalar App
</button>

4. Antes de </body> pega:

<script src="assets/pwa.js"></script>
