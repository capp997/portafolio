<?php
require_once __DIR__.'/../config/db.php';
$title = trim($_POST['title'] ?? '');
$target = $_POST['target_amount'] ?? 0;
$current = $_POST['current_amount'] ?? 0;
$deadline = $_POST['deadline'] ?: null;
if($title && $target > 0){
    $stmt=$pdo->prepare('INSERT INTO goals(title,target_amount,current_amount,deadline) VALUES(?,?,?,?)');
    $stmt->execute([$title,$target,$current,$deadline]);
}
header('Location: ../pages/metas.php');
exit;
?>
