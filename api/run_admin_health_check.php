<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

try{
    $checks = [];

    $dbOk = false;
    try{
        $pdo->query("SELECT 1");
        $dbOk = true;
    }catch(Exception $e){}

    $checks[] = ["Database", $dbOk ? "success" : "failed", $dbOk ? "Database connected" : "Database error"];

    $tables = ["users","activity_logs","smart_signals","ai_insights","scheduler_runs","app_notifications"];
    foreach($tables as $t){
        try{
            $pdo->query("SELECT COUNT(*) FROM {$t}");
            $checks[] = [$t, "success", "Table available"];
        }catch(Exception $e){
            $checks[] = [$t, "failed", "Missing or unavailable"];
        }
    }

    foreach($checks as $c){
        $stmt = $pdo->prepare("
            INSERT INTO system_health_logs(service_name,status,details)
            VALUES(?,?,?)
        ");
        $stmt->execute([$c[0],$c[1],$c[2]]);
    }

    header("Location: ../pages/ai_admin_center.php?health=1");
    exit;

}catch(Exception $e){
    header("Location: ../pages/ai_admin_center.php?error=health");
    exit;
}
?>
