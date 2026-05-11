<?php
require_once __DIR__ . "/../config/menu.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$assets = $pdo->query("
    SELECT *
    FROM assets
    WHERE shares > 0
    ORDER BY ticker ASC
")->fetchAll(PDO::FETCH_ASSOC);

$sales = $pdo->query("
    SELECT *
    FROM sales
    ORDER BY created_at DESC
    LIMIT 50
")->fetchAll(PDO::FETCH_ASSOC);

$totalRealized = $pdo->query("
    SELECT COALESCE(SUM(realized_pl),0)
    FROM sales
")->fetchColumn();

function money($n){
    return '$'.number_format((float)$n,2);
}

function numberFlex($n){
    return rtrim(rtrim(number_format((float)$n,8), '0'), '.');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Sell / Vender</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="../assets/style_v5.css">
<link rel="stylesheet" href="../assets/unified_pages.css">
<link rel="stylesheet" href="../assets/menu_dropdown.css">
<link rel="stylesheet" href="../assets/sell.css">
<link rel="stylesheet" href="../assets/global_page_fix.css">
</head>

<body>

<div class="layout">

<?php render_sidebar('sell', '../'); ?>
<main class="content">

<section class="sell-hero">
<div>
<p>Sell Center</p>
<h1>Registrar venta de activos</h1>
<span>Descuenta shares, calcula proceeds y ganancia/pérdida realizada.</span>
</div>

<div class="sell-total">
<span>P/L realizado total</span>
<strong class="<?=$totalRealized>=0?'green':'red'?>"><?=money($totalRealized)?></strong>
</div>
</section>

<?php if(isset($_GET['success'])): ?>
<div class="sell-success">Venta registrada correctamente ✅</div>
<?php endif; ?>

<?php if(isset($_GET['error'])): ?>
<div class="sell-error">
<?php
$error = $_GET['error'];
if($error === 'shares') echo "No puedes vender más shares de los que tienes.";
elseif($error === 'invalid') echo "Datos inválidos. Revisa cantidad y precio.";
elseif($error === 'asset') echo "Activo no encontrado.";
else echo "Error guardando la venta.";
?>
</div>
<?php endif; ?>

<section class="sell-grid">

<div class="sell-panel">
<h2>Nueva venta</h2>

<form action="../api/process_sell.php" method="POST" class="sell-form">

<label>Activo</label>
<select name="asset_id" required>
<option value="">Selecciona activo</option>
<?php foreach($assets as $a): ?>
<option 
value="<?=$a['id']?>"
data-price="<?=$a['current_price']?>"
data-shares="<?=$a['shares']?>"
data-avg="<?=$a['avg_cost']?>"
>
<?=$a['ticker']?> — <?=numberFlex($a['shares'])?> shares — Precio: <?=money($a['current_price'])?>
</option>
<?php endforeach; ?>
</select>

<div class="form-row">
<div>
<label>Fecha de venta</label>
<input type="date" name="sell_date" value="<?=date('Y-m-d')?>" required>
</div>

<div>
<label>Shares a vender</label>
<input type="number" step="0.00000001" name="shares_sold" id="sharesSold" required>
</div>
</div>

<div class="form-row">
<div>
<label>Precio de venta</label>
<input type="number" step="0.00000001" name="sell_price" id="sellPrice" required>
</div>

<div>
<label>Nota</label>
<input type="text" name="note" placeholder="Ej: toma parcial, rebalanceo...">
</div>
</div>

<button>Registrar venta</button>

</form>
</div>

<div class="sell-panel estimate-panel">
<h2>Estimado</h2>

<div class="estimate-card">
<span>Shares disponibles</span>
<strong id="availableShares">0</strong>
</div>

<div class="estimate-card">
<span>Avg cost</span>
<strong id="avgCost">$0.00</strong>
</div>

<div class="estimate-card">
<span>Proceeds</span>
<strong id="proceeds">$0.00</strong>
</div>

<div class="estimate-card">
<span>P/L estimado</span>
<strong id="estimatedPL">$0.00</strong>
</div>
</div>

</section>

<section class="panel">
<div class="table-header">
<h2>Historial de ventas</h2>
<span>Últimas 50 ventas</span>
</div>

<div class="table-wrap">
<table>
<thead>
<tr>
<th>Fecha</th>
<th>Ticker</th>
<th>Shares</th>
<th>Precio venta</th>
<th>Avg Cost</th>
<th>Proceeds</th>
<th>P/L Realizado</th>
<th>Nota</th>
</tr>
</thead>
<tbody>
<?php foreach($sales as $s): ?>
<tr>
<td><?=date("M d, Y", strtotime($s['sell_date']))?></td>
<td><b><?=$s['ticker']?></b></td>
<td><?=numberFlex($s['shares_sold'])?></td>
<td><?=money($s['sell_price'])?></td>
<td><?=money($s['avg_cost'])?></td>
<td><?=money($s['proceeds'])?></td>
<td class="<?=$s['realized_pl']>=0?'green':'red'?>"><?=money($s['realized_pl'])?></td>
<td><?=htmlspecialchars($s['note'])?></td>
</tr>
<?php endforeach; ?>

<?php if(count($sales)===0): ?>
<tr>
<td colspan="8">No hay ventas registradas todavía.</td>
</tr>
<?php endif; ?>
</tbody>
</table>
</div>
</section>

</main>
</div>

<script>
const select = document.querySelector("select[name='asset_id']");
const sharesInput = document.getElementById("sharesSold");
const priceInput = document.getElementById("sellPrice");

function money(n){
    return "$" + Number(n || 0).toFixed(2);
}

function updateEstimate(){
    const opt = select.options[select.selectedIndex];

    if(!opt || !opt.dataset.price){
        return;
    }

    const available = Number(opt.dataset.shares || 0);
    const avg = Number(opt.dataset.avg || 0);
    const price = Number(priceInput.value || opt.dataset.price || 0);
    const sold = Number(sharesInput.value || 0);

    const proceeds = sold * price;
    const cost = sold * avg;
    const pl = proceeds - cost;

    document.getElementById("availableShares").textContent = available.toFixed(8).replace(/0+$/,'').replace(/\.$/,'');
    document.getElementById("avgCost").textContent = money(avg);
    document.getElementById("proceeds").textContent = money(proceeds);

    const plEl = document.getElementById("estimatedPL");
    plEl.textContent = money(pl);
    plEl.className = pl >= 0 ? "green" : "red";
}

select.addEventListener("change", () => {
    const opt = select.options[select.selectedIndex];

    if(opt && opt.dataset.price){
        priceInput.value = opt.dataset.price;
    }

    updateEstimate();
});

sharesInput.addEventListener("input", updateEstimate);
priceInput.addEventListener("input", updateEstimate);
</script>

<script src="../assets/menu_dropdown.js"></script>
</body>
</html>
