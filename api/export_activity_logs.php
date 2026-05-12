<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$rows = $pdo->query("
    SELECT *
    FROM activity_logs
    ORDER BY created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

$filename = "activity_logs_" . date("Y-m-d") . ".csv";

header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename={$filename}");

$out = fopen("php://output", "w");

if(count($rows)){
    fputcsv($out, array_keys($rows[0]));
    foreach($rows as $r){
        fputcsv($out, $r);
    }
}else{
    fputcsv($out, ["No data"]);
}

fclose($out);
exit;
?>
