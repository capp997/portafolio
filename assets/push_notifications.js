// PUSH NOTIFICATIONS PHASE 1

async function registerPushSystem(){
    if(!("Notification" in window)){
        alert("Este navegador no soporta notificaciones.");
        return;
    }

    if(!("serviceWorker" in navigator)){
        alert("Service Worker no está disponible.");
        return;
    }

    const permission = await Notification.requestPermission();

    await fetch("/api/save_push_permission.php", {
        method: "POST",
        headers: {"Content-Type":"application/json"},
        body: JSON.stringify({
            permission: permission,
            user_agent: navigator.userAgent
        })
    });

    if(permission === "granted"){
        const reg = await navigator.serviceWorker.ready;

        reg.showNotification("Portafolio V5 activo 🔔", {
            body: "Las notificaciones están activadas correctamente.",
            icon: "/assets/icons/icon-192.png",
            badge: "/assets/icons/icon-192.png"
        });
    } else {
        alert("Permiso de notificaciones no activado.");
    }
}

async function sendTestNotification(){
    if(Notification.permission !== "granted"){
        await registerPushSystem();
        return;
    }

    const reg = await navigator.serviceWorker.ready;

    reg.showNotification("Prueba de notificación ✅", {
        body: "Tu PWA ya puede mostrar notificaciones del sistema.",
        icon: "/assets/icons/icon-192.png",
        badge: "/assets/icons/icon-192.png"
    });
}

document.addEventListener("DOMContentLoaded", function(){
    const enableBtn = document.getElementById("enablePushBtn");
    const testBtn = document.getElementById("testPushBtn");

    if(enableBtn){
        enableBtn.addEventListener("click", registerPushSystem);
    }

    if(testBtn){
        testBtn.addEventListener("click", sendTestNotification);
    }
});
