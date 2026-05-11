<?php
require_once __DIR__ . "/../config/db.php";

/*
AUTO SCHEDULER CRON ENDPOINT

Render Cron llamará este archivo con:
https://TU_RENDER_URL/api/cron_runner.php?token=TU_CRON_SECRET

Configura CRON_SECRET en Render Environment Variables.
*/

$token = $_GET['token'] ?? '';
$secret = getenv("CRON_SECRET") ?: '';

if(!$secret || $token !== $secret){
    http_response_code(403);
    die("Forbidden");
}

$steps = [];

function saveSchedulerRun($pdo, $type, $status, $details){
    try{
        $stmt = $pdo->prepare("
            INSERT INTO scheduler_runs(run_type,status,details)
            VALUES(?,?,?)
        ");
        $stmt->execute([$type,$status,$details]);
    }catch(Exception $e){}
}

function runLocalApi($file){
    $path = __DIR__ . "/" . $file;

    if(!file_exists($path)){
        return ["status"=>"missing","details"=>"File not found: $file"];
    }

    try{
        ob_start();
        include $path;
        ob_end_clean();

        return ["status"=>"success","details"=>"Executed $file"];
    }catch(Throwable $e){
        return ["status"=>"failed","details"=>$e->getMessage()];
    }
}

/*
IMPORTANTE:
Algunos archivos API hacen header redirect.
Por eso este cron usa include con buffer.
Si algún archivo hace exit, puede detener el proceso.
Por eso aquí ejecutamos lógica segura propia cuando sea posible.
*/

$jobs = [
    "Market Data" => "market_data_engine.php",
    "Smart Signals" => "generate_smart_signals.php",
    "Notifications" => "generate_notifications.php",
    "AI Advisor" => "generate_ai_report.php"
];

foreach($jobs as $name=>$file){
    $result = runLocalApi($file);

    $steps[] = [
        "name"=>$name,
        "status"=>$result['status'],
        "details"=>$result['details']
    ];

    saveSchedulerRun($pdo, $name, $result['status'], $result['details']);
}

header("Content-Type: application/json");

echo json_encode([
    "ok"=>true,
    "time"=>date("c"),
    "steps"=>$steps
], JSON_PRETTY_PRINT);
?>
