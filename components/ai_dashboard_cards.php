<?php
/*
AI DASHBOARD CARDS COMPONENT
Usar en index_v5.php:
<?php include __DIR__ . "/components/ai_dashboard_cards.php"; ?>
*/

$latestInsight = null;
$topRisk = null;
$topOpportunity = null;
$portfolioState = [
    "title" => "Portafolio estable",
    "content" => "No hay señales críticas por ahora.",
    "tag" => "HOLD"
];

try{
    $latestInsight = $pdo->query("
        SELECT *
        FROM ai_insights
        WHERE title NOT ILIKE '%OpenAI Error%'
        ORDER BY created_at DESC
        LIMIT 1
    ")->fetch(PDO::FETCH_ASSOC);
}catch(Exception $e){}

try{
    $topRisk = $pdo->query("
        SELECT *
        FROM ai_insights
        WHERE insight_type='risk'
          AND title NOT ILIKE '%OpenAI Error%'
        ORDER BY confidence DESC, created_at DESC
        LIMIT 1
    ")->fetch(PDO::FETCH_ASSOC);
}catch(Exception $e){}

try{
    $topOpportunity = $pdo->query("
        SELECT *
        FROM ai_insights
        WHERE insight_type='opportunity'
          AND title NOT ILIKE '%OpenAI Error%'
        ORDER BY confidence DESC, created_at DESC
        LIMIT 1
    ")->fetch(PDO::FETCH_ASSOC);
}catch(Exception $e){}

try{
    $signal = $pdo->query("
        SELECT *
        FROM smart_signals
        ORDER BY confidence DESC, created_at DESC
        LIMIT 1
    ")->fetch(PDO::FETCH_ASSOC);

    if($signal){
        $portfolioState = [
            "title" => $signal['ticker'] . " - " . $signal['signal'],
            "content" => $signal['note'],
            "tag" => $signal['signal']
        ];
    }
}catch(Exception $e){}

function aiCardClass($type){
    if($type === 'risk') return 'ai-risk';
    if($type === 'opportunity') return 'ai-opportunity';
    if($type === 'dividend') return 'ai-dividend';
    if($type === 'rebalance') return 'ai-rebalance';
    return 'ai-market';
}
?>

<section class="ai-dashboard-section">

<div class="ai-section-header">
    <div>
        <p>AI Dashboard</p>
        <h2>Tarjetas inteligentes automáticas</h2>
    </div>

    <a href="pages/ai_insights.php">Ver AI Insights</a>
</div>

<div class="ai-card-grid">

    <div class="ai-dash-card <?= $latestInsight ? aiCardClass($latestInsight['insight_type']) : 'ai-market' ?>">
        <div class="ai-card-top">
            <span>Insight principal</span>
            <b><?= $latestInsight ? intval($latestInsight['confidence']) . '%' : 'N/A' ?></b>
        </div>

        <h3><?= $latestInsight ? htmlspecialchars($latestInsight['title']) : 'Sin insight generado' ?></h3>
        <p><?= $latestInsight ? htmlspecialchars($latestInsight['content']) : 'Genera AI Insights para ver análisis automático aquí.' ?></p>
    </div>

    <div class="ai-dash-card ai-risk">
        <div class="ai-card-top">
            <span>Mayor riesgo</span>
            <b><?= $topRisk ? intval($topRisk['confidence']) . '%' : 'N/A' ?></b>
        </div>

        <h3><?= $topRisk ? htmlspecialchars($topRisk['title']) : 'Riesgo no detectado' ?></h3>
        <p><?= $topRisk ? htmlspecialchars($topRisk['content']) : 'No hay riesgos destacados por IA en este momento.' ?></p>
    </div>

    <div class="ai-dash-card ai-opportunity">
        <div class="ai-card-top">
            <span>Oportunidad</span>
            <b><?= $topOpportunity ? intval($topOpportunity['confidence']) . '%' : 'N/A' ?></b>
        </div>

        <h3><?= $topOpportunity ? htmlspecialchars($topOpportunity['title']) : 'Sin oportunidad clara' ?></h3>
        <p><?= $topOpportunity ? htmlspecialchars($topOpportunity['content']) : 'La IA todavía no ha detectado una oportunidad fuerte.' ?></p>
    </div>

    <div class="ai-dash-card ai-signal">
        <div class="ai-card-top">
            <span>Señal dominante</span>
            <b><?= htmlspecialchars($portfolioState['tag']) ?></b>
        </div>

        <h3><?= htmlspecialchars($portfolioState['title']) ?></h3>
        <p><?= htmlspecialchars($portfolioState['content']) ?></p>
    </div>

</div>

</section>
