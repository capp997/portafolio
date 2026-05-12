<?php
$topRisk = null;
$topOpportunity = null;
$marketInsight = null;
$portfolioState = [
    "title" => "Portafolio estable",
    "content" => "No hay señales críticas por ahora.",
    "tag" => "HOLD"
];

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
    $marketInsight = $pdo->query("
        SELECT *
        FROM ai_insights
        WHERE insight_type='market'
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
?>

<section class="ai-dashboard-section">
    <div class="ai-section-header">
        <div>
            <p>✣ AI Insights Automáticos</p>
            <h2>Panel inteligente del portafolio</h2>
        </div>

        <a href="pages/ai_insights.php">✣ Ver todos los insights</a>
    </div>

    <div class="ai-card-grid">

        <div class="ai-dash-card ai-risk">
            <div class="ai-card-top">
                <span>Mayor riesgo</span>
                <b><?= $topRisk ? intval($topRisk['confidence']).'%' : 'N/A' ?></b>
            </div>
            <h3><?= $topRisk ? htmlspecialchars($topRisk['title']) : 'Riesgo no detectado' ?></h3>
            <p><?= $topRisk ? htmlspecialchars($topRisk['content']) : 'No hay riesgos destacados por IA en este momento.' ?></p>
        </div>

        <div class="ai-dash-card ai-opportunity">
            <div class="ai-card-top">
                <span>Oportunidad</span>
                <b><?= $topOpportunity ? intval($topOpportunity['confidence']).'%' : 'N/A' ?></b>
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

        <div class="ai-dash-card ai-market">
            <div class="ai-card-top">
                <span>Tendencia del mercado</span>
                <b><?= $marketInsight ? intval($marketInsight['confidence']).'%' : '70%' ?></b>
            </div>
            <h3><?= $marketInsight ? htmlspecialchars($marketInsight['title']) : 'Mercado en observación' ?></h3>
            <p><?= $marketInsight ? htmlspecialchars($marketInsight['content']) : 'Genera AI Insights para ver análisis automático del mercado aquí.' ?></p>
        </div>

    </div>
</section>
