<?php
// EXPORT MYSQL DATA TO JSON
// Copia este archivo a la raíz de tu proyecto local y abre:
// http://localhost/portafolio-dashboard-v4/export_for_supabase.php

require_once __DIR__ . "/config/db.php";

$tables = [
    "users",
    "assets",
    "purchases",
    "dividends",
    "goals",
    "routines",
    "portfolio_history",
    "smart_alerts",
    "dividend_settings"
];

$out = [];

foreach($tables as $table){
    try{
        $stmt = $pdo->query("SELECT * FROM `$table`");
        $out[$table] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(Exception $e){
        $out[$table] = [];
    }
}

header("Content-Type: application/json");
header("Content-Disposition: attachment; filename=portfolio_export.json");

echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
