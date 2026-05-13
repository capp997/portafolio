<?php
/*
DRAGGABLE DASHBOARD WIDGETS
Insertar en index_v5.php donde quieras los widgets:

<?php include __DIR__ . "/components/draggable_widgets.php"; ?>
*/

$totalValue = $totalValue ?? 0;
$totalPL = $totalPL ?? 0;
$cryptoValue = $cryptoValue ?? 0;
$etfValue = $etfValue ?? 0;
?>

<section class="widget-actions">
    <button type="button" onclick="resetWidgetOrder()">Reset widgets</button>
</section>

<section class="drag-widget-zone">

    <article class="drag-widget good" data-widget-id="portfolio-value">
        <div class="drag-widget-header">
            <div>
                <h3>Valor del portafolio</h3>
                <span>Widget movible</span>
            </div>
            <div class="drag-handle">⋮⋮</div>
        </div>
        <div class="drag-widget-value">$<?=number_format((float)$totalValue,2)?></div>
        <p>Valor total calculado desde tus activos actuales.</p>
    </article>

    <article class="drag-widget <?=((float)$totalPL >= 0 ? 'good' : 'bad')?>" data-widget-id="portfolio-pl">
        <div class="drag-widget-header">
            <div>
                <h3>Ganancia / Pérdida</h3>
                <span>Performance</span>
            </div>
            <div class="drag-handle">⋮⋮</div>
        </div>
        <div class="drag-widget-value">$<?=number_format((float)$totalPL,2)?></div>
        <p>Resultado actual frente a tu costo promedio.</p>
    </article>

    <article class="drag-widget warn" data-widget-id="crypto-exposure">
        <div class="drag-widget-header">
            <div>
                <h3>Crypto Exposure</h3>
                <span>Riesgo</span>
            </div>
            <div class="drag-handle">⋮⋮</div>
        </div>
        <div class="drag-widget-value">$<?=number_format((float)$cryptoValue,2)?></div>
        <p>Valor aproximado expuesto a criptomonedas.</p>
    </article>

    <article class="drag-widget good" data-widget-id="etf-base">
        <div class="drag-widget-header">
            <div>
                <h3>ETF / Base</h3>
                <span>Estabilidad</span>
            </div>
            <div class="drag-handle">⋮⋮</div>
        </div>
        <div class="drag-widget-value">$<?=number_format((float)$etfValue,2)?></div>
        <p>Parte estable o base del portafolio.</p>
    </article>

</section>
