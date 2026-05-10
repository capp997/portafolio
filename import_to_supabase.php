<?php
require_once __DIR__ . "/config/db_supabase.php";

$jsonFile = __DIR__ . "/portfolio_export.json";

if(!file_exists($jsonFile)){
    die("No se encontró portfolio_export.json.");
}

$data = json_decode(file_get_contents($jsonFile), true);

if(!$data){
    die("JSON inválido.");
}

function insertRows($pdo, $table, $rows){
    if(!isset($rows) || count($rows) === 0){
        return 0;
    }

    $count = 0;

    foreach($rows as $row){
        if(!$row || !is_array($row)) continue;

        $columns = array_keys($row);
        $placeholders = array_map(fn($c) => ":" . $c, $columns);

        $sql = "INSERT INTO {$table} (" . implode(",", $columns) . ")
                VALUES (" . implode(",", $placeholders) . ")
                ON CONFLICT (id) DO NOTHING";

        $stmt = $pdo->prepare($sql);

        foreach($row as $key => $value){
            if($value === ""){
                $value = null;
            }
            $stmt->bindValue(":" . $key, $value);
        }

        $stmt->execute();
        $count++;
    }

    return $count;
}

$order = [
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

$results = [];

foreach($order as $table){
    $rows = $data[$table] ?? [];
    $results[$table] = insertRows($pdo, $table, $rows);
}

foreach($order as $table){
    try{
        $pdo->exec("
            SELECT setval(
                pg_get_serial_sequence('{$table}', 'id'),
                COALESCE((SELECT MAX(id) FROM {$table}), 1),
                true
            )
        ");
    } catch(Exception $e){}
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Import Supabase</title>
<style>
body{background:#020617;color:white;font-family:Arial;padding:40px;}
.box{max-width:900px;margin:auto;background:#0b1220;border:1px solid #1e293b;border-radius:24px;padding:28px;}
.row{display:flex;justify-content:space-between;background:#020617;border:1px solid #334155;border-radius:14px;padding:14px;margin-bottom:10px;}
.good{color:#86efac;font-weight:bold;}
.warn{color:#fca5a5;}
</style>
</head>
<body>
<div class="box">
<h1>Importación a Supabase completada ✅</h1>

<?php foreach($results as $table=>$count): ?>
<div class="row">
    <span><?=$table?></span>
    <span class="good"><?=$count?> registros procesados</span>
</div>
<?php endforeach; ?>

<p class="warn">
Por seguridad, borra import_to_supabase.php y portfolio_export.json después.
</p>
</div>
</body>
</html>
