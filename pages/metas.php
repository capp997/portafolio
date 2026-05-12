<?php
require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/../config/layout.php';

page_start('Metas','metas');

$rows = $pdo->query("SELECT * FROM goals ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

function m($n){
    return '$'.number_format((float)$n,2);
}

function pct($current,$target){
    return $target > 0 ? min(100,($current/$target)*100) : 0;
}

$totalCurrent = 0;
$totalTarget = 0;

foreach($rows as $r){
    $totalCurrent += (float)$r['current_amount'];
    $totalTarget += (float)$r['target_amount'];
}

$globalPct = pct($totalCurrent,$totalTarget);
?>

<style>
/* METAS PAGE FIX */

.metas-hero{
    background:radial-gradient(circle at top right,rgba(34,197,94,.24),transparent 38%),#0b1220;
    border:1px solid #26344f;
    border-radius:30px;
    padding:30px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:20px;
    margin-bottom:20px;
    box-shadow:0 22px 70px rgba(0,0,0,.24);
}

.metas-hero p{
    color:#86efac;
    font-weight:bold;
    margin:0 0 8px;
}

.metas-hero h1{
    font-size:42px;
    margin:0 0 8px;
}

.metas-hero span{
    color:#cbd5e1;
}

.metas-score{
    background:#020617;
    border:1px solid #334155;
    border-radius:22px;
    padding:20px;
    min-width:220px;
    text-align:center;
}

.metas-score span{
    display:block;
    color:#94a3b8;
    margin-bottom:8px;
}

.metas-score strong{
    font-size:34px;
    color:#22c55e;
}

.metas-form-panel{
    background:linear-gradient(180deg,#0f172a,#0b1220);
    border:1px solid #26344f;
    border-radius:26px;
    padding:22px;
    margin-bottom:20px;
    box-shadow:0 18px 55px rgba(0,0,0,.22);
}

.metas-form-panel h2{
    margin:0 0 16px;
    font-size:26px;
}

.metas-add-form{
    display:grid;
    grid-template-columns:1.3fr 1fr 1fr .9fr auto;
    gap:12px;
    align-items:center;
}

.metas-add-form input,
.meta-edit-form input{
    background:#020617;
    border:1px solid #334155;
    color:white;
    border-radius:14px;
    padding:13px 14px;
    min-height:46px;
    outline:none;
}

.metas-add-form input:focus,
.meta-edit-form input:focus{
    border-color:#22c55e;
    box-shadow:0 0 0 4px rgba(34,197,94,.10);
}

.metas-add-form button,
.meta-edit-form button{
    background:#16a34a;
    color:white;
    border:0;
    border-radius:14px;
    padding:13px 18px;
    min-height:46px;
    font-weight:bold;
    cursor:pointer;
}

.metas-grid{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:18px;
    margin-bottom:30px;
}

.meta-card{
    background:linear-gradient(180deg,#0f172a,#0b1220);
    border:1px solid #26344f;
    border-radius:26px;
    padding:22px;
    box-shadow:0 18px 55px rgba(0,0,0,.22);
}

.meta-top{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:16px;
    margin-bottom:14px;
}

.meta-top span{
    color:#94a3b8;
    font-weight:bold;
    font-size:13px;
}

.meta-top h2{
    margin:6px 0 0;
    font-size:26px;
}

.meta-top b{
    background:#052e16;
    border:1px solid #22c55e;
    color:#86efac;
    padding:8px 12px;
    border-radius:999px;
    font-size:14px;
}

.progress-line{
    width:100%;
    height:12px;
    background:#020617;
    border:1px solid #334155;
    border-radius:999px;
    overflow:hidden;
    margin-bottom:18px;
}

.progress-line span{
    display:block;
    height:100%;
    background:linear-gradient(90deg,#22c55e,#86efac);
    border-radius:999px;
}

.meta-numbers{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:12px;
    margin-bottom:18px;
}

.meta-numbers div{
    background:#020617;
    border:1px solid #334155;
    border-radius:16px;
    padding:14px;
}

.meta-numbers span{
    display:block;
    color:#94a3b8;
    font-size:13px;
    margin-bottom:7px;
}

.meta-numbers strong{
    font-size:17px;
}

.green{
    color:#22c55e !important;
}

.orange{
    color:#f97316 !important;
}

.meta-edit-form{
    display:grid;
    grid-template-columns:auto 1fr auto 1fr auto 1fr auto;
    gap:10px;
    align-items:center;
    margin-bottom:12px;
}

.meta-edit-form label{
    color:#cbd5e1;
    font-weight:bold;
    white-space:nowrap;
}

.delete-form{
    margin:0;
}

.btn-gray,
.danger-btn{
    background:#334155;
    color:white;
    border:0;
    border-radius:14px;
    padding:12px 16px;
    font-weight:bold;
    cursor:pointer;
}

.danger-btn:hover{
    background:#991b1b;
}

.empty-metas{
    grid-column:1/-1;
    background:#0b1220;
    border:1px dashed #334155;
    border-radius:24px;
    padding:34px;
    text-align:center;
    color:#94a3b8;
}

@media(max-width:1200px){
    .metas-add-form{
        grid-template-columns:1fr 1fr;
    }

    .metas-add-form button{
        grid-column:1/-1;
    }

    .meta-edit-form{
        grid-template-columns:1fr;
    }

    .meta-edit-form label{
        margin-top:4px;
    }
}

@media(max-width:900px){
    .metas-hero{
        flex-direction:column;
        align-items:flex-start;
    }

    .metas-score{
        width:100%;
    }

    .metas-grid{
        grid-template-columns:1fr;
    }

    .meta-numbers{
        grid-template-columns:1fr 1fr;
    }
}

@media(max-width:600px){
    .metas-add-form,
    .meta-numbers{
        grid-template-columns:1fr;
    }

    .metas-hero h1{
        font-size:32px;
    }

    .meta-top{
        flex-direction:column;
    }
}
</style>

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
            <div>
                <span>Actual</span>
                <strong class="green"><?=m($r['current_amount'])?></strong>
            </div>

            <div>
                <span>Objetivo</span>
                <strong><?=m($r['target_amount'])?></strong>
            </div>

            <div>
                <span>Falta</span>
                <strong class="orange"><?=m($missing)?></strong>
            </div>

            <div>
                <span>Fecha</span>
                <strong><?= $r['deadline'] ? date('m/d/Y', strtotime($r['deadline'])) : 'Sin fecha' ?></strong>
            </div>
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

        <form class="delete-form" action="../api/delete_goal.php" method="POST" onsubmit="return confirm('¿Eliminar esta meta?')">
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
