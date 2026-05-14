// MOBILE RESPONSIVE FULL FIX

(function(){
    function ready(fn){
        if(document.readyState !== "loading") fn();
        else document.addEventListener("DOMContentLoaded", fn);
    }

    ready(function(){
        const sidebar = document.querySelector(".sidebar");
        if(!sidebar) return;

        let btn = document.querySelector(".mobile-menu-btn");
        if(!btn){
            btn = document.createElement("button");
            btn.className = "mobile-menu-btn";
            btn.type = "button";
            btn.innerHTML = "☰";
            document.body.appendChild(btn);
        }

        let overlay = document.querySelector(".mobile-overlay");
        if(!overlay){
            overlay = document.createElement("div");
            overlay.className = "mobile-overlay";
            document.body.appendChild(overlay);
        }

        function openMenu(){
            sidebar.classList.add("mobile-open");
            overlay.classList.add("show");
            document.body.style.overflow = "hidden";
        }

        function closeMenu(){
            sidebar.classList.remove("mobile-open");
            overlay.classList.remove("show");
            document.body.style.overflow = "";
        }

        btn.addEventListener("click", function(e){
            e.preventDefault();
            if(sidebar.classList.contains("mobile-open")){
                closeMenu();
            }else{
                openMenu();
            }
        });

        overlay.addEventListener("click", closeMenu);

        document.addEventListener("keydown", function(e){
            if(e.key === "Escape") closeMenu();
        });

        sidebar.querySelectorAll("a").forEach(link => {
            link.addEventListener("click", closeMenu);
        });

        if(!document.querySelector(".mobile-bottom-nav")){
            const nav = document.createElement("div");
            nav.className = "mobile-bottom-nav";

            nav.innerHTML = `
                <a href="/index_v5.php" class="active-mobile">
                    <span>🏠</span>Home
                </a>
                <a href="/pages/centro_alertas.php">
                    <span>🔔</span>Alertas
                </a>
                <a href="/pages/live_charts.php">
                    <span>📈</span>Charts
                </a>
                <a href="/pages/metas.php">
                    <span>🎯</span>Metas
                </a>
            `;

            document.body.appendChild(nav);
        }
    });
})();
