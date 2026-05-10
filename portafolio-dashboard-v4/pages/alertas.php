<?php require_once __DIR__.'/../config/db.php'; require_once __DIR__.'/../config/layout.php'; page_start('Alertas','alertas'); $assets=$pdo->query("SELECT * FROM assets ORDER BY id")->fetchAll(PDO::FETCH_ASSOC); function fmt($n){return '$'.rtrim(rtrim(number_format((float)$n,8), '0'), '.');} ?>
<section class="hero"><div><h1>Alertas</h1><p>Compra en caídas y toma ganancias en subidas.</p></div><div class="pill">BUY / SELL / HOLD</div></section>
<section class="panel"><div class="table-wrap"><table><thead><tr><th>Activo</th><th>Base</th><th>Actual</th><th>-5%</th><th>-10%</th><th>+10%</th><th>+20%</th><th>Estado</th></tr></thead><tbody>
<?php foreach($assets as $a):$b=$a['base_price'];$c=$a['current_price'];$buy5=$b*.95;$buy10=$b*.90;$sell10=$b*1.10;$sell20=$b*1.20;if($c<=$buy10){$s='BUY FUERTE';$cl='buy';}elseif($c<=$buy5){$s='BUY';$cl='buy';}elseif($c>=$sell20){$s='SELL FUERTE';$cl='sell';}elseif($c>=$sell10){$s='SELL';$cl='sell';}else{$s='HOLD';$cl='hold';}?>
<tr><td><b><?=$a['ticker']?></b><br><small><?=$a['name']?></small></td><td><?=fmt($b)?></td><td><?=fmt($c)?></td><td><?=fmt($buy5)?></td><td><?=fmt($buy10)?></td><td><?=fmt($sell10)?></td><td><?=fmt($sell20)?></td><td><span class="badge <?=$cl?>"><?=$s?></span></td></tr>
<?php endforeach;?></tbody></table></div></section>
<?php page_end(); ?>
