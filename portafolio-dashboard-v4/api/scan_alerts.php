<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$assets = $pdo->query("SELECT * FROM assets ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

foreach($assets as $a){
    $base = (float)$a['base_price'];
    $current = (float)$a['current_price'];
    $ticker = $a['ticker'];

    if($base <= 0 || $current <= 0) continue;

    $type = null;
    $message = null;

    if($current <= $base * 0.90){
        $type = "BUY FUERTE";
        $message = "$ticker cayó 10% o más. Zona de compra fuerte.";
    } elseif($current <= $base * 0.95){
        $type = "BUY";
        $message = "$ticker cayó 5% o más. Zona de compra ligera.";
    } elseif($current >= $base * 1.20){
        $type = "SELL FUERTE";
        $message = "$ticker subió 20% o más. Zona de tomar ganancias fuertes.";
    } elseif($current >= $base * 1.10){
        $type = "SELL";
        $message = "$ticker subió 10% o más. Posible toma parcial.";
    }

    if($type){
        $check = $pdo->prepare("SELECT id FROM smart_alerts WHERE asset_id=? AND alert_type=? AND is_reviewed=0 LIMIT 1");
        $check->execute([$a['id'], $type]);

        if(!$check->fetch()){
            $stmt = $pdo->prepare("INSERT INTO smart_alerts(asset_id,ticker,alert_type,message,price,base_price) VALUES(?,?,?,?,?,?)");
            $stmt->execute([$a['id'], $ticker, $type, $message, $current, $base]);
        }
    }
}

header("Location: ../pages/centro_alertas.php?scanned=1");
exit;
?>
