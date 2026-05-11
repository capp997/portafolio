<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/api.php";

$token = $_GET['token'] ?? '';
$secret = getenv("CRON_SECRET") ?: '';

if(!$secret || $token !== $secret){
    http_response_code(403);
    die("Forbidden");
}

$steps = [];

function logRun($pdo, $name, $status, $details){
    try{
        $stmt = $pdo->prepare("INSERT INTO scheduler_runs(run_type,status,details) VALUES(?,?,?)");
        $stmt->execute([$name,$status,$details]);
    }catch(Exception $e){}
}

/* 1. MARKET DATA */
try{
    $assets = $pdo->query("SELECT * FROM assets")->fetchAll(PDO::FETCH_ASSOC);

    foreach($assets as $a){
        $ticker = strtoupper($a['ticker']);
        $symbol = $ticker;

        if($ticker === "BTC") $symbol = "BINANCE:BTCUSDT";
        if($ticker === "ETH") $symbol = "BINANCE:ETHUSDT";
        if($ticker === "DOGE") $symbol = "BINANCE:DOGEUSDT";

        $url = "https://finnhub.io/api/v1/quote?symbol=".urlencode($symbol)."&token=".urlencode($FINNHUB_API_KEY);
        $json = @file_get_contents($url);
        $data = json_decode($json, true);

        if(isset($data['c']) && $data['c'] > 0){
            $price = (float)$data['c'];

            $stmt = $pdo->prepare("UPDATE assets SET current_price=? WHERE id=?");
            $stmt->execute([$price, $a['id']]);

            $hist = $pdo->prepare("INSERT INTO price_history(asset_id,ticker,price,source) VALUES(?,?,?,?)");
            $hist->execute([$a['id'], $ticker, $price, "cron"]);
        }
    }

    logRun($pdo,"Market Data","success","Prices updated");
    $steps[] = ["name"=>"Market Data","status"=>"success"];

}catch(Throwable $e){
    logRun($pdo,"Market Data","failed",$e->getMessage());
    $steps[] = ["name"=>"Market Data","status"=>"failed","details"=>$e->getMessage()];
}

/* 2. SMART SIGNALS */
try{
    $assets = $pdo->query("SELECT * FROM assets")->fetchAll(PDO::FETCH_ASSOC);

    foreach($assets as $a){
        $current = (float)$a['current_price'];
        $base = (float)$a['base_price'];
        $ticker = strtoupper($a['ticker']);

        $signal = "HOLD";
        $confidence = 50;
        $note = "Sin señal fuerte.";

        if($base > 0){
            if($current <= $base * 0.90){
                $signal = "BUY FUERTE";
                $confidence = 85;
                $note = "$ticker está 10% o más debajo del precio base.";
            } elseif($current <= $base * 0.95){
                $signal = "BUY";
                $confidence = 70;
                $note = "$ticker está 5% o más debajo del precio base.";
            } elseif($current >= $base * 1.20){
                $signal = "SELL FUERTE";
                $confidence = 80;
                $note = "$ticker está 20% o más arriba del precio base.";
            } elseif($current >= $base * 1.10){
                $signal = "SELL";
                $confidence = 65;
                $note = "$ticker está 10% o más arriba del precio base.";
            }
        }

        $risk = in_array($ticker, ['BTC','ETH','DOGE']) ? 75 : 40;

        $stmt = $pdo->prepare("
            INSERT INTO smart_signals(asset_id,ticker,signal,confidence,momentum,trend_score,risk_score,note)
            VALUES(?,?,?,?,?,?,?,?)
        ");

        $stmt->execute([
            $a['id'],
            $ticker,
            $signal,
            $confidence,
            0,
            50,
            $risk,
            $note
        ]);
    }

    logRun($pdo,"Smart Signals","success","Signals generated");
    $steps[] = ["name"=>"Smart Signals","status"=>"success"];

}catch(Throwable $e){
    logRun($pdo,"Smart Signals","failed",$e->getMessage());
    $steps[] = ["name"=>"Smart Signals","status"=>"failed","details"=>$e->getMessage()];
}

/* 3. NOTIFICATIONS */
try{
    $signals = $pdo->query("
        SELECT DISTINCT ON (ticker) *
        FROM smart_signals
        WHERE signal <> 'HOLD'
        ORDER BY ticker, created_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    foreach($signals as $s){
        $title = $s['ticker']." - ".$s['signal'];

        $check = $pdo->prepare("SELECT id FROM app_notifications WHERE title=? AND is_read=0 LIMIT 1");
        $check->execute([$title]);

        if(!$check->fetch()){
            $stmt = $pdo->prepare("
                INSERT INTO app_notifications(title,message,type,source)
                VALUES(?,?,?,?)
            ");

            $stmt->execute([
                $title,
                $s['note'],
                str_contains($s['signal'], 'BUY') ? 'buy' : 'sell',
                'cron_signals'
            ]);
        }
    }

    logRun($pdo,"Notifications","success","Notifications generated");
    $steps[] = ["name"=>"Notifications","status"=>"success"];

}catch(Throwable $e){
    logRun($pdo,"Notifications","failed",$e->getMessage());
    $steps[] = ["name"=>"Notifications","status"=>"failed","details"=>$e->getMessage()];
}

/* 4. AI ADVISOR */
try{
    $assets = $pdo->query("SELECT * FROM assets")->fetchAll(PDO::FETCH_ASSOC);

    $total = 0;
    $crypto = 0;

    foreach($assets as $a){
        $value = (float)$a['shares'] * (float)$a['current_price'];
        $total += $value;

        if(in_array(strtoupper($a['ticker']), ['BTC','ETH','DOGE'])){
            $crypto += $value;
        }
    }

    $cryptoPct = $total > 0 ? ($crypto / $total) * 100 : 0;

    $score = 75;
    $risk = "Moderado";
    $recommendation = "Portafolio balanceado.";

    if($cryptoPct > 25){
        $score = 60;
        $risk = "Alto";
        $recommendation = "Crypto tiene mucho peso. Revisa exposición.";
    }

    if(count($assets) >= 5){
        $score += 10;
    }

    $diversification = min(100, count($assets) * 15);

    $stmt = $pdo->prepare("
        INSERT INTO portfolio_ai_reports(portfolio_score,risk_level,diversification_score,recommendation)
        VALUES(?,?,?,?)
    ");

    $stmt->execute([$score,$risk,$diversification,$recommendation]);

    logRun($pdo,"AI Advisor","success","AI report generated");
    $steps[] = ["name"=>"AI Advisor","status"=>"success"];

}catch(Throwable $e){
    logRun($pdo,"AI Advisor","failed",$e->getMessage());
    $steps[] = ["name"=>"AI Advisor","status"=>"failed","details"=>$e->getMessage()];
}

header("Content-Type: application/json");

echo json_encode([
    "ok"=>true,
    "time"=>date("c"),
    "steps"=>$steps
], JSON_PRETTY_PRINT);
?>
