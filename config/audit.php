<?php
function audit_log($pdo, $action, $entity = null, $entity_id = null, $details = null){
    try{
        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }

        $userId = $_SESSION["user_id"] ?? null;
        $username = $_SESSION["username"] ?? null;
        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"] ?? $_SERVER["REMOTE_ADDR"] ?? "";
        $agent = $_SERVER["HTTP_USER_AGENT"] ?? "";

        if(str_contains($ip, ",")){
            $ip = trim(explode(",", $ip)[0]);
        }

        $stmt = $pdo->prepare("
            INSERT INTO activity_logs(
                user_id, username, action, entity, entity_id, details, ip_address, user_agent
            )
            VALUES(?,?,?,?,?,?,?,?)
        ");

        $stmt->execute([
            $userId,
            $username,
            $action,
            $entity,
            $entity_id,
            $details,
            $ip,
            $agent
        ]);

    }catch(Throwable $e){}
}
?>
