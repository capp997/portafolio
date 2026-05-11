<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/openai.php";

header("Content-Type: application/json");

if(!$OPENAI_API_KEY){
    echo json_encode([
        "ok"=>false,
        "error"=>"OPENAI_API_KEY missing"
    ]);
    exit;
}

$prompt = trim($_POST['prompt'] ?? '');

if(!$prompt){
    echo json_encode([
        "ok"=>false,
        "error"=>"Empty prompt"
    ]);
    exit;
}

$assets = $pdo->query("
SELECT ticker,current_price,shares,category
FROM assets
ORDER BY ticker ASC
LIMIT 20
")->fetchAll(PDO::FETCH_ASSOC);

$portfolioContext = json_encode($assets);

$system = "
You are an advanced AI financial assistant for a portfolio dashboard.
Be concise, practical and explain things clearly.
Use the portfolio context when useful.
";

$user = "
Portfolio:
$portfolioContext

User question:
$prompt
";

$payload = [
    "model" => "gpt-4.1-mini",
    "messages" => [
        ["role"=>"system","content"=>$system],
        ["role"=>"user","content"=>$user]
    ],
    "temperature" => 0.7
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
    echo json_encode([
        "ok"=>false,
        "error"=>curl_error($ch)
    ]);
    exit;
}

$data = json_decode($response, true);

$message = $data['choices'][0]['message']['content'] ?? 'No response';

echo json_encode([
    "ok"=>true,
    "response"=>$message
]);
?>
