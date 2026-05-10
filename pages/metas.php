<?php
require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/../config/layout.php';

page_start('Metas','metas');

$goals = $pdo->query("SELECT * FROM goals ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
$totalPortfolio = $pdo->query("SELECT COALESCE(SUM(shares * current_price),0) FROM assets")->fetchColumn();
$totalDividends = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM dividends")->fetchColumn();

function money($n){
    return '$'.number_format((float)$n,2);
}

function progressColorClass($pct){
    if ($pct >= 75) return "goal-progress-high";
    if ($pct >= 40) return "goal-progress-mid";
    return "goal-progress-low";
}
?>

<section class="hero">
    <div>
        <h1>Metas Financieras</h1>
        <p>Controla tu progreso para vivir cómodamente del portafolio.</p>
    </div>
    <div class="pill">Progreso visual</div>
</section>

<section class="cards">
    <div class="card">
        <span>Valor actual del portafolio</span>
        <strong><?= money($totalPortfolio) ?></strong>
    </div>

    <div class="card">
        <span>Dividendos acumulados</span>
        <strong class="green"><?= money($totalDividends) ?></strong>
    </div>

    <div class="card">
        <span>Metas activas</span>
        <strong><?= count($goals) ?></strong>
    </div>

    <div class="card">
        <span>Estado</span>
        <strong class="green">Activo</strong>
    </div>
</section>

<section class="panel">
    <h2>Agregar nueva meta</h2>

    <form action="../api/add_goal.php" method="POST" class="goal-add-form">
        <input name="title" placeholder="Nombre de la meta" required>
        <input type="number" step="0.01" name="target_amount" placeholder="Objetivo $" required>
        <input type="number" step="0.01" name="current_amount" placeholder="Actual $" value="0">
        <input type="date" name="deadline">
        <button>Agregar meta</button>
    </form>
</section>

<section class="goals-wrapper">
<?php foreach($goals as $g):
    $target = (float)$g['target_amount'];
    $current = (float)$g['current_amount'];
    $pct = $target > 0 ? min(100, ($current / $target) * 100) : 0;
    $remaining = max(0, $target - $current);
    $progressClass = progressColorClass($pct);
?>
    <article class="goal-card-clean">
        <form action="../api/update_goal.php" method="POST">
            <input type="hidden" name="id" value="<?= $g['id'] ?>">

            <div class="goal-header-clean">
                <div class="goal-title-box">
                    <label>Meta</label>
                    <input name="title" value="<?= htmlspecialchars($g['title']) ?>">
                </div>

                <div class="goal-percent-box">
                    <?= number_format($pct,1) ?>%
                </div>
            </div>

            <div class="goal-progress-track">
                <div class="goal-progress-bar <?= $progressClass ?>" style="width: <?= $pct ?>%;"></div>
            </div>

            <div class="goal-money-grid">
                <div>
                    <span>Actual</span>
                    <strong><?= money($current) ?></strong>
                </div>

                <div>
                    <span>Objetivo</span>
                    <strong><?= money($target) ?></strong>
                </div>

                <div>
                    <span>Falta</span>
                    <strong class="<?= $remaining > 0 ? 'orange' : 'green' ?>"><?= money($remaining) ?></strong>
                </div>
            </div>

            <div class="goal-edit-grid">
                <div>
                    <label>Actual $</label>
                    <input type="number" step="0.01" name="current_amount" value="<?= $g['current_amount'] ?>">
                </div>

                <div>
                    <label>Objetivo $</label>
                    <input type="number" step="0.01" name="target_amount" value="<?= $g['target_amount'] ?>">
                </div>

                <div>
                    <label>Fecha</label>
                    <input type="date" name="deadline" value="<?= $g['deadline'] ?>">
                </div>
            </div>

            <div class="goal-buttons">
                <button>Guardar</button>
        </form>

                <form action="../api/delete_goal.php" method="POST" onsubmit="return confirm('¿Eliminar esta meta?')">
                    <input type="hidden" name="id" value="<?= $g['id'] ?>">
                    <button class="btn-gray">Eliminar</button>
                </form>
            </div>
    </article>
<?php endforeach; ?>
</section>

<section class="grid2">
    <div class="panel">
        <h2>Cómo usarlo</h2>
        <ul>
            <li>Para metas de capital, copia el valor del portafolio en “Actual”.</li>
            <li>Para metas de dividendos, copia dividendos acumulados en “Actual”.</li>
            <li>Presiona Guardar y la barra se actualiza.</li>
        </ul>
    </div>

    <div class="panel">
        <h2>Lectura rápida</h2>
        <ul>
            <li>0% - 39%: empezando.</li>
            <li>40% - 74%: buen avance.</li>
            <li>75% - 100%: cerca de completar.</li>
        </ul>
    </div>
</section>

<?php page_end(); ?>
