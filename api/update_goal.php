<?php
require_once __DIR__.'/../config/db.php';
$id=$_POST['id'] ?? null;
$current=$_POST['current_amount'] ?? 0;
$target=$_POST['target_amount'] ?? 0;
$deadline=$_POST['deadline'] ?: null;
if($id){
    $stmt=$pdo->prepare('UPDATE goals SET current_amount=?, target_amount=?, deadline=? WHERE id=?');
    $stmt->execute([$current,$target,$deadline,$id]);
}
header('Location: ../pages/metas.php');
exit;
?>
