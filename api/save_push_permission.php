<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

$permission = $data['permission'] ?? 'default';
$userAgent = $data['user_agent'] ?? '';
$userId = $_SESSION['user_id'] ?? null;

$stmt = $pdo->prepare("
    INSERT INTO push_subscriptions(user_id, permission, user_agent, is_active)
    VALUES(?,?,?,1)
");

$stmt->execute([$userId, $permission, $userAgent]);

header("Content-Type: application/json");
echo json_encode(["ok"=>true]);
?>
