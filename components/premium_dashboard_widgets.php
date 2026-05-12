<?php
/*
PREMIUM DASHBOARD WIDGETS
Insertar en index_v5.php debajo de las AI cards:

<?php include __DIR__ . "/components/premium_dashboard_widgets.php"; ?>
*/

$latestSignals = [];
try{
    $latestSignals = $pdo->query("
        SELECT DISTINCT ON (ticker)
        ticker, signal, confidence
        FROM smart_signals
        ORDER BY ticker, created_at DESC
        LIMIT 6
    ")->fetchAll(PDO::FETCH_ASSOC);
}catch(Exception $e){}

$historyValues = [];
try{
    $history = $pdo->query("
        SELECT total_value
        FROM portfolio_history
        ORDER BY created_at DESC
        LIMIT 10
    ")->fetchAll(PDO::FETCH_ASSOC);

    $historyValues = array_reverse(array_map(fn($r)=>(float)$r['total_value'], $history));
}catch(Exception $e){}

if(count($historyValues) === 0){
    $historyValues = [40,55,48,62,70,64,76,84,79,92];
}

$maxValue = max($historyValues);
if($maxValue <= 0) $maxValue = 1;

function signalClassMini($signal){
    if(str_contains($signal, 'BUY')) return 'pulse-up';
    if(str_contains($signal, 'SELL')) return 'pulse-down';
    return 'pulse-neutral';
}
?>

<section class="premium-widgets">

    <div class="premium-widget premium-border-glow">
        <h2>📈 Pulso del portafolio</h2>
        <p>Movimiento visual basado en los últimos snapshots guardados.</p>

        <div class="mini-chart">
            <?php foreach($historyValues as $v): 
                $h = max(18, min(135, ($v / $maxValue) * 135));
            ?>
            <span style="height:<?=$h?>px"></span>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="premium-widget">
        <h2>🤖 Señales recientes</h2>
        <p>Resumen rápido del Smart Signals Engine.</p>

        <div class="market-pulse-list">
            <?php foreach($latestSignals as $s): ?>
            <div class="market-pulse-row">
                <strong><?=$s['ticker']?></strong>
                <span class="<?=signalClassMini($s['signal'])?>">
                    <?=$s['signal']?> · <?=$s['confidence']?>%
                </span>
            </div>
            <?php endforeach; ?>

            <?php if(count($latestSignals) === 0): ?>
            <div class="market-pulse-row">
                <strong>Sin señales</strong>
                <span class="pulse-neutral">Generar</span>
            </div>
            <?php endif; ?>
        </div>
    </div>

</section>
