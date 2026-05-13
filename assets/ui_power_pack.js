// UI POWER PACK

(function(){
    const body = document.body;

    const savedTheme = localStorage.getItem("pv5_theme");
    const savedSidebar = localStorage.getItem("pv5_sidebar");

    if(savedTheme === "light"){
        body.classList.add("light-mode");
    }

    if(savedSidebar === "collapsed"){
        body.classList.add("sidebar-collapsed");
    }

    const controls = document.createElement("div");
    controls.className = "ui-power-controls";
    controls.innerHTML = `
        <button class="ui-control-btn" id="themeToggle" title="Dark/Light">🌓</button>
        <button class="ui-control-btn" id="sidebarToggle" title="Colapsar menú">☰</button>
    `;
    document.body.appendChild(controls);

    document.getElementById("themeToggle").addEventListener("click", () => {
        body.classList.toggle("light-mode");
        localStorage.setItem("pv5_theme", body.classList.contains("light-mode") ? "light" : "dark");
    });

    document.getElementById("sidebarToggle").addEventListener("click", () => {
        body.classList.toggle("sidebar-collapsed");
        localStorage.setItem("pv5_sidebar", body.classList.contains("sidebar-collapsed") ? "collapsed" : "expanded");
    });

    initDragWidgets();
})();

function initDragWidgets(){
    const zone = document.querySelector(".drag-widget-zone");
    if(!zone) return;

    const savedOrder = localStorage.getItem("pv5_widget_order");
    if(savedOrder){
        try{
            const ids = JSON.parse(savedOrder);
            ids.forEach(id => {
                const el = zone.querySelector(`[data-widget-id="${id}"]`);
                if(el) zone.appendChild(el);
            });
        }catch(e){}
    }

    let dragged = null;

    zone.querySelectorAll(".drag-widget").forEach(widget => {
        widget.setAttribute("draggable", "true");

        widget.addEventListener("dragstart", () => {
            dragged = widget;
            widget.classList.add("dragging");
        });

        widget.addEventListener("dragend", () => {
            widget.classList.remove("dragging");
            dragged = null;
            saveWidgetOrder(zone);
        });
    });

    zone.addEventListener("dragover", e => {
        e.preventDefault();
        const afterElement = getDragAfterElement(zone, e.clientY);
        if(!dragged) return;

        if(afterElement == null){
            zone.appendChild(dragged);
        }else{
            zone.insertBefore(dragged, afterElement);
        }
    });
}

function getDragAfterElement(container, y){
    const draggableElements = [...container.querySelectorAll(".drag-widget:not(.dragging)")];

    return draggableElements.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;

        if(offset < 0 && offset > closest.offset){
            return {offset: offset, element: child};
        }else{
            return closest;
        }
    }, {offset: Number.NEGATIVE_INFINITY}).element;
}

function saveWidgetOrder(zone){
    const ids = [...zone.querySelectorAll(".drag-widget")].map(el => el.dataset.widgetId);
    localStorage.setItem("pv5_widget_order", JSON.stringify(ids));
}

function resetWidgetOrder(){
    localStorage.removeItem("pv5_widget_order");
    location.reload();
}
