<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$type = $_GET['type'] ?? 'assets';

$allowed = [
    'assets' => 'assets',
    'purchases' => 'purchases',
    'sales' => 'sales',
    'dividends' => 'dividends',
    'smart_signals' => 'smart_signals',
    'app_notifications' => 'app_notifications',
    'portfolio_history' => 'portfolio_history'
];

if(!isset($allowed[$type])){
    die("Invalid export type.");
}

$table = $allowed[$type];

try{
    $rows = $pdo->query("SELECT * FROM {$table} ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
}catch(Exception $e){
    die("Error exporting data.");
}

$filename = $type . "_" . date("Y-m-d") . ".csv";

header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename={$filename}");

$out = fopen("php://output", "w");

if(count($rows) > 0){
    fputcsv($out, array_keys($rows[0]));

    foreach($rows as $row){
        fputcsv($out, $row);
    }
}else{
    fputcsv($out, ["No data"]);
}

fclose($out);
exit;
?>
