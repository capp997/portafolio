// MOBILE MENU BUTTON FORCE FIX

(function(){
    function initMobileMenu(){
        const sidebar =
            document.querySelector("aside.sidebar") ||
            document.querySelector(".sidebar") ||
            document.querySelector(".unified-sidebar");

        if(!sidebar){
            console.warn("Mobile menu: sidebar not found");
            return;
        }

        let btn = document.querySelector(".mobile-menu-btn");
        if(!btn){
            btn = document.createElement("button");
            btn.type = "button";
            btn.className = "mobile-menu-btn";
            btn.setAttribute("aria-label", "Abrir menú");
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
            document.body.classList.add("mobile-menu-open");
            sidebar.classList.add("mobile-open");
            overlay.classList.add("show");
            btn.innerHTML = "×";
        }

        function closeMenu(){
            document.body.classList.remove("mobile-menu-open");
            sidebar.classList.remove("mobile-open");
            overlay.classList.remove("show");
            btn.innerHTML = "☰";
        }

        function toggleMenu(e){
            if(e){
                e.preventDefault();
                e.stopPropagation();
            }

            if(document.body.classList.contains("mobile-menu-open")){
                closeMenu();
            }else{
                openMenu();
            }
        }

        btn.onclick = toggleMenu;
        overlay.onclick = closeMenu;

        document.addEventListener("keydown", function(e){
            if(e.key === "Escape") closeMenu();
        });

        sidebar.querySelectorAll("a").forEach(function(a){
            a.addEventListener("click", closeMenu);
        });
    }

    if(document.readyState === "loading"){
        document.addEventListener("DOMContentLoaded", initMobileMenu);
    }else{
        initMobileMenu();
    }

    // Por si algún script re-renderiza el menú
    setTimeout(initMobileMenu, 500);
})();
