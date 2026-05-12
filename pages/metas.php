<?php
require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/../config/layout.php';
page_start('Metas','metas');
$rows=$pdo->query("SELECT * FROM goals ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
function m($n){return '$'.number_format((float)$n,2);} 
function pct($current,$target){return $target>0?min(100,($current/$target)*100):0;}
$totalCurrent = 0; $totalTarget = 0;
foreach($rows as $r){ $totalCurrent += (float)$r['current_amount']; $totalTarget += (float)$r['target_amount']; }
$globalPct = pct($totalCurrent,$totalTarget);
?>

<section class="metas-hero">
    <div>
        <p>Goal Center</p>
        <h1>Metas Financieras</h1>
        <span>Controla objetivos, progreso, fechas y faltante de cada meta.</span>
    </div>

    <div class="metas-score">
        <span>Progreso total</span>
        <strong><?=number_format($globalPct,1)?>%</strong>
    </div>
</section>

<section class="metas-form-panel">
    <h2>Agregar nueva meta</h2>
    <form action="../api/add_goal.php" method="POST" class="metas-add-form">
        <input type="text" name="title" placeholder="Nombre de la meta" required>
        <input type="number" step="0.01" name="target_amount" placeholder="Objetivo $" required>
        <input type="number" step="0.01" name="current_amount" placeholder="Actual $" value="0">
        <input type="date" name="deadline">
        <button>Agregar meta</button>
    </form>
</section>

<section class="metas-grid">
<?php foreach($rows as $r):
    $p = pct((float)$r['current_amount'], (float)$r['target_amount']);
    $missing = max(0, (float)$r['target_amount'] - (float)$r['current_amount']);
?>
    <article class="meta-card">
        <div class="meta-top">
            <div>
                <span>Meta</span>
                <h2><?=htmlspecialchars($r['title'])?></h2>
            </div>
            <b><?=number_format($p,1)?>%</b>
        </div>

        <div class="progress-line">
            <span style="width:<?=$p?>%"></span>
        </div>

        <div class="meta-numbers">
            <div><span>Actual</span><strong class="green"><?=m($r['current_amount'])?></strong></div>
            <div><span>Objetivo</span><strong><?=m($r['target_amount'])?></strong></div>
            <div><span>Falta</span><strong class="orange"><?=m($missing)?></strong></div>
            <div><span>Fecha</span><strong><?= $r['deadline'] ? date('m/d/Y', strtotime($r['deadline'])) : 'Sin fecha' ?></strong></div>
        </div>

        <form action="../api/update_goal.php" method="POST" class="meta-edit-form">
            <input type="hidden" name="id" value="<?=$r['id']?>">
            <label>Actual $</label>
            <input type="number" step="0.01" name="current_amount" value="<?=$r['current_amount']?>">
            <label>Objetivo $</label>
            <input type="number" step="0.01" name="target_amount" value="<?=$r['target_amount']?>">
            <label>Fecha</label>
            <input type="date" name="deadline" value="<?=$r['deadline']?>">
            <button>Guardar</button>
        </form>

        <form action="../api/delete_goal.php" method="POST" onsubmit="return confirm('¿Eliminar esta meta?')">
            <input type="hidden" name="id" value="<?=$r['id']?>">
            <button class="btn-gray danger-btn">Eliminar</button>
        </form>
    </article>
<?php endforeach; ?>

<?php if(count($rows)===0): ?>
    <div class="empty-metas">No hay metas todavía. Agrega la primera arriba.</div>
<?php endif; ?>
</section>

<?php page_end(); ?>
