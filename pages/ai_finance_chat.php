<?php
require_once __DIR__ . "/../config/menu.php";
require_once __DIR__ . "/../config/auth.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>AI Finance Chat</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="../assets/style_v5.css">
<link rel="stylesheet" href="../assets/unified_pages.css">
<link rel="stylesheet" href="../assets/menu_dropdown.css">
<link rel="stylesheet" href="../assets/openai_chat.css">
<link rel="stylesheet" href="../assets/global_page_fix.css">
</head>
<body>

<div class="layout">

<?php render_sidebar('ai_finance_chat', '../'); ?>
<main class="content">

<section class="ai-hero">
<div>
<p>OpenAI Integration</p>
<h1>AI Finance Assistant</h1>
<span>Consulta el mercado, tu portafolio y recibe insights inteligentes.</span>
</div>
</section>

<section class="chat-panel">

<div id="chatMessages" class="chat-messages">
<div class="msg ai">
Hola 👋 Soy tu AI Finance Assistant. Pregúntame sobre tu portafolio, mercado o señales.
</div>
</div>

<div class="chat-input-wrap">
<textarea id="prompt" placeholder="Ejemplo: ¿Qué activo de mi portafolio tiene más riesgo?"></textarea>

<button id="sendBtn">
Enviar
</button>
</div>

</section>

</main>
</div>

<script>
const btn = document.getElementById("sendBtn");
const promptEl = document.getElementById("prompt");
const chat = document.getElementById("chatMessages");

function addMessage(text, type="ai"){
    const div = document.createElement("div");
    div.className = "msg " + type;
    div.textContent = text;
    chat.appendChild(div);
    chat.scrollTop = chat.scrollHeight;
}

btn.addEventListener("click", async () => {

    const prompt = promptEl.value.trim();

    if(!prompt) return;

    addMessage(prompt, "user");

    promptEl.value = "";

    addMessage("Pensando...", "ai");

    const form = new FormData();
    form.append("prompt", prompt);

    const res = await fetch("../api/openai_chat.php", {
        method: "POST",
        body: form
    });

    const data = await res.json();

    document.querySelector(".msg.ai:last-child").remove();

    if(data.ok){
        addMessage(data.response, "ai");
    } else {
        addMessage("Error: " + data.error, "ai");
    }
});
</script>

<script src="../assets/menu_dropdown.js"></script>
</body>
</html>
