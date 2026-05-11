<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$steps = [];

function logStep($pdo,$name,$status,$details){
    $stmt=$pdo->prepare("INSERT INTO automation_logs(action_name,status,details) VALUES(?,?,?)");
    $stmt->execute([$name,$status,$details]);
}

$jobs = [
    [
        "name"=>"Market Data Engine",
        "file"=>"market_data_engine.php"
    ],
    [
        "name"=>"Smart Signals",
        "file"=>"generate_smart_signals.php"
    ],
    [
        "name"=>"Notifications",
        "file"=>"generate_notifications.php"
    ],
    [
        "name"=>"AI Advisor",
        "file"=>"generate_ai_report.php"
    ]
];

foreach($jobs as $job){

    $path = __DIR__ . "/" . $job['file'];

    if(file_exists($path)){

        try{
            ob_start();
            include $path;
            ob_end_clean();

            logStep($pdo,$job['name'],"success","Executed successfully");

            $steps[] = [
                "name"=>$job['name'],
                "status"=>"success"
            ];

        }catch(Exception $e){

            logStep($pdo,$job['name'],"failed",$e->getMessage());

            $steps[] = [
                "name"=>$job['name'],
                "status"=>"failed"
            ];
        }

    } else {

        logStep($pdo,$job['name'],"missing","File missing");

        $steps[] = [
            "name"=>$job['name'],
            "status"=>"missing"
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Automation Runner</title>
<link rel="stylesheet" href="../assets/automation_layer.css">
</head>
<body>

<div class="auto-wrap">
<div class="auto-card">

<h1>AI Automation Layer 🤖</h1>
<p>Todos los motores ejecutados automáticamente.</p>

<div class="auto-grid">
<?php foreach($steps as $s): ?>
<div class="auto-item <?=$s['status']?>">
<strong><?=$s['name']?></strong>
<span><?=$s['status']?></span>
</div>
<?php endforeach; ?>
</div>

<a href="../pages/automation_center.php">Ir al Automation Center</a>

</div>
</div>

</body>
</html>
