<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

function createNotification($pdo, $title, $message, $type, $source){
    $check = $pdo->prepare("SELECT id FROM app_notifications WHERE title=? AND source=? AND is_read=0 LIMIT 1");
    $check->execute([$title, $source]);

    if(!$check->fetch()){
        $stmt = $pdo->prepare("INSERT INTO app_notifications(title,message,type,source) VALUES(?,?,?,?)");
        $stmt->execute([$title,$message,$type,$source]);
    }
}

try{
    $alerts = $pdo->query("SELECT ticker, alert_type, message FROM smart_alerts WHERE is_reviewed=0 ORDER BY created_at DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

    foreach($alerts as $a){
        createNotification(
            $pdo,
            $a['ticker'] . " - " . $a['alert_type'],
            $a['message'],
            str_contains($a['alert_type'], 'BUY') ? 'buy' : 'sell',
            'smart_alerts'
        );
    }
}catch(Exception $e){}

try{
    $divs = $pdo->query("
        SELECT a.ticker, ds.next_pay_date
        FROM dividend_settings ds
        JOIN assets a ON a.id = ds.asset_id
        WHERE ds.next_pay_date IS NOT NULL
          AND ds.next_pay_date <= CURRENT_DATE + INTERVAL '7 days'
          AND ds.next_pay_date >= CURRENT_DATE
    ")->fetchAll(PDO::FETCH_ASSOC);

    foreach($divs as $d){
        createNotification(
            $pdo,
            "Dividendo próximo: " . $d['ticker'],
            "Pago estimado para " . $d['next_pay_date'],
            'dividend',
            'dividend_tracker'
        );
    }
}catch(Exception $e){}

if(date("N") == 1){
    createNotification($pdo, "Día de rebalanceo", "Hoy es buen día para revisar asignación, alertas y compras.", "rebalance", "routine");
}

header("Location: ../pages/notifications.php?generated=1");
exit;
?>
