// Mobile Mode Premium
(function(){
    function createMobileUI(){
        const sidebar = document.querySelector(".sidebar");
        if(!sidebar) return;

        const btn = document.createElement("button");
        btn.className = "mobile-menu-btn";
        btn.innerHTML = "☰";
        document.body.appendChild(btn);

        const overlay = document.createElement("div");
        overlay.className = "mobile-overlay";
        document.body.appendChild(overlay);

        btn.addEventListener("click", () => {
            sidebar.classList.add("mobile-open");
            overlay.classList.add("show");
        });

        overlay.addEventListener("click", () => {
            sidebar.classList.remove("mobile-open");
            overlay.classList.remove("show");
        });

        sidebar.querySelectorAll("a").forEach(link => {
            link.addEventListener("click", () => {
                sidebar.classList.remove("mobile-open");
                overlay.classList.remove("show");
            });
        });

        if(!document.querySelector(".mobile-bottom-nav")){
            const nav = document.createElement("div");
            nav.className = "mobile-bottom-nav";

            nav.innerHTML = `
                <a href="/portafolio-dashboard-v4/index_v5.php" class="active-mobile">
                    <span>🏠</span>Home
                </a>
                <a href="/portafolio-dashboard-v4/pages/alertas.php">
                    <span>🔔</span>Alertas
                </a>
                <a href="/portafolio-dashboard-v4/pages/historial.php">
                    <span>📈</span>Historial
                </a>
                <a href="/portafolio-dashboard-v4/pages/metas.php">
                    <span>🎯</span>Metas
                </a>
            `;

            document.body.appendChild(nav);
        }
    }

    document.addEventListener("DOMContentLoaded", createMobileUI);
})();
