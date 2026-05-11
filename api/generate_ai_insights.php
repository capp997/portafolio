<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/openai.php";

function saveInsight($pdo, $type, $title, $content, $confidence){
    $stmt = $pdo->prepare("
        INSERT INTO ai_insights(insight_type,title,content,confidence)
        VALUES(?,?,?,?)
    ");
    $stmt->execute([$type,$title,$content,$confidence]);
}

if(!$OPENAI_API_KEY){
    header("Location: ../pages/ai_insights.php?error=no_key");
    exit;
}

$assets = $pdo->query("
    SELECT ticker,name,category,shares,current_price,avg_cost
    FROM assets
    ORDER BY ticker ASC
")->fetchAll(PDO::FETCH_ASSOC);

$signals = [];
try{
    $signals = $pdo->query("
        SELECT DISTINCT ON (ticker)
        ticker,signal,confidence,note
        FROM smart_signals
        ORDER BY ticker, created_at DESC
        LIMIT 20
    ")->fetchAll(PDO::FETCH_ASSOC);
}catch(Exception $e){}

$total = 0;
$crypto = 0;
$highestRisk = "N/A";

foreach($assets as $a){
    $value = (float)$a['shares'] * (float)$a['current_price'];
    $total += $value;

    if(in_array(strtoupper($a['ticker']), ['BTC','ETH','DOGE'])){
        $crypto += $value;
        $highestRisk = strtoupper($a['ticker']);
    }
}

$portfolioJson = json_encode($assets, JSON_UNESCAPED_UNICODE);
$signalsJson = json_encode($signals, JSON_UNESCAPED_UNICODE);

$prompt = "
Analiza este portafolio y genera exactamente 4 insights financieros prácticos en español.

Datos del portafolio:
$portfolioJson

Últimas señales:
$signalsJson

Devuelve SOLO JSON válido, sin markdown, sin explicación extra.

Formato:
[
  {
    \"type\":\"risk\",
    \"title\":\"Título corto\",
    \"content\":\"Explicación práctica de 2 a 3 frases.\",
    \"confidence\":80
  }
]

Tipos permitidos:
risk, opportunity, dividend, rebalance, market
";

$payload = [
    "model" => "gpt-4.1-mini",
    "messages" => [
        ["role"=>"system","content"=>"Eres un asistente financiero para un dashboard personal. No das asesoría financiera definitiva; das análisis educativo y práctico."],
        ["role"=>"user","content"=>$prompt]
    ],
    "temperature" => 0.5
];

$ch = curl_init("https://api.openai.com/v1/chat/completions");

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer ".$OPENAI_API_KEY
    ],
    CURLOPT_POSTFIELDS => json_encode($payload)
]);

$response = curl_exec($ch);

if(curl_errno($ch)){
    header("Location: ../pages/ai_insights.php?error=curl");
    exit;
}

$data = json_decode($response, true);

if(isset($data['error']['message'])){
    $msg = substr($data['error']['message'],0,230);
    saveInsight($pdo, "market", "OpenAI Error", $msg, 0);
    header("Location: ../pages/ai_insights.php?error=openai");
    exit;
}

$content = trim($data['choices'][0]['message']['content'] ?? '');

if(!$content){
    saveInsight($pdo, "market", "Sin respuesta IA", "OpenAI no devolvió contenido. Revisa billing, modelo o API key.", 0);
    header("Location: ../pages/ai_insights.php?error=empty");
    exit;
}

$content = str_replace(["```json","```"], "", $content);
$json = json_decode(trim($content), true);

if(!is_array($json)){
    saveInsight($pdo, "market", "Respuesta IA no válida", $content, 40);
    header("Location: ../pages/ai_insights.php?error=json");
    exit;
}

$count = 0;

foreach($json as $i){
    $type = $i['type'] ?? 'market';
    $title = trim($i['title'] ?? 'AI Insight');
    $body = trim($i['content'] ?? '');
    $confidence = (int)($i['confidence'] ?? 70);

    if(!$body) continue;

    saveInsight(
        $pdo,
        substr($type,0,80),
        substr($title,0,255),
        $body,
        max(0,min(100,$confidence))
    );

    $count++;
}

header("Location: ../pages/ai_insights.php?generated=".$count);
exit;
?>
