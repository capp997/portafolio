<?php require_once __DIR__.'/../config/db.php'; require_once __DIR__.'/../config/layout.php'; page_start('Activos','activos'); $assets=$pdo->query("SELECT * FROM assets ORDER BY id")->fetchAll(PDO::FETCH_ASSOC); function m($n){return '$'.number_format((float)$n,2);} ?>
<section class="hero"><div><h1>Activos</h1><p>Shares, costo promedio, precio actual, valor total y ganancia/pérdida.</p></div></section>

<section class="panel">
<h2>Agregar nuevo activo</h2>
<p>Úsalo cuando compres una acción, ETF o crypto nueva.</p>
<form action="../api/add_asset.php" method="POST" class="form-row">
    <input name="ticker" placeholder="Ticker Ej: AAPL" required>
    <input name="name" placeholder="Nombre Ej: Apple Inc." required>
    <select name="category" required>
        <option value="ETF Seguro">ETF Seguro</option>
        <option value="Dividendos">Dividendos</option>
        <option value="ETF Growth">ETF Growth</option>
        <option value="Acción agresiva">Acción agresiva</option>
        <option value="Crypto">Crypto</option>
        <option value="Otra">Otra</option>
    </select>
    <input type="number" step="0.00000001" name="shares" placeholder="Shares" value="0">
    <input type="number" step="0.00000001" name="avg_cost" placeholder="Costo prom." value="0">
    <input type="number" step="0.00000001" name="current_price" placeholder="Precio actual" value="0">
    <input type="number" step="0.00000001" name="base_price" placeholder="Precio base" value="0">
    <input type="number" step="0.01" name="target_percent" placeholder="Meta %" value="0">
    <button>Agregar activo</button>
</form>
</section>

<section class="panel"><div class="table-wrap"><table><thead><tr><th>Activo</th><th>Categoría</th><th>Shares</th><th>Costo Prom.</th><th>Precio Actual</th><th>Precio Base</th><th>Valor</th><th>P/L</th><th>Meta %</th><th>Guardar</th><th>Eliminar</th></tr></thead><tbody>
<?php foreach($assets as $a):$value=$a['shares']*$a['current_price'];$pl=$value-($a['shares']*$a['avg_cost']);?>
<tr><form action="../api/update_asset.php" method="POST"><td><b><?=$a['ticker']?></b><br><small><?=$a['name']?></small><input type="hidden" name="id" value="<?=$a['id']?>"></td><td><?=$a['category']?></td><td><input name="shares" type="number" step="0.00000001" value="<?=$a['shares']?>"></td><td><input name="avg_cost" type="number" step="0.00000001" value="<?=$a['avg_cost']?>"></td><td><input name="current_price" type="number" step="0.00000001" value="<?=$a['current_price']?>"></td><td><input name="base_price" type="number" step="0.00000001" value="<?=$a['base_price']?>"></td><td><?=m($value)?></td><td class="<?=$pl>=0?'green':'red'?>"><?=m($pl)?></td><td><input name="target_percent" type="number" step="0.01" value="<?=$a['target_percent']?>"></td><td><button>Guardar</button></td></form><td><form action="../api/delete_asset.php" method="POST" onsubmit="return confirm('¿Seguro que quieres eliminar este activo?');"><input type="hidden" name="id" value="<?=$a['id']?>"><button class="btn-gray">Eliminar</button></form></td></tr>
<?php endforeach;?></tbody></table></div></section>
<?php page_end(); ?>
