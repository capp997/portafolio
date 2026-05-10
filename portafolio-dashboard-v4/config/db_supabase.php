<?php
$host = "db.cipordvaolnnfougylrl.supabase.co";
$port = "5432";
$dbname = "postgres";
$user = "postgres";
$password = "alcarajo*1208";

try {
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require",
        $user,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
    die("Error conectando a Supabase/Postgres: " . $e->getMessage());
}
?>
