<?php
$host = 'database-adm.cgkhq7iflebh.us-east-1.rds.amazonaws.com';
$dbname = 'gestaoou_adm';
$user = 'tickets';
$password = 'l05QVSI2p8{+';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados.");
}
?>