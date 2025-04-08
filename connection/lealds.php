<?php
# lealds.php

// Configuração dos bancos de dados
$databases = [
    ['name' => 'cedroibr7', 'user' => 'cedroibr_7', 'password' => 'tdnvgbN2sH0%B95ts']
];

$host = 'mysql.cedroinfo.com.br';
$port = 3306;

$connections = [];

foreach ($databases as $db) {
    try {
        $connections[$db['name']] = new PDO(
            "mysql:host=$host;port=$port;dbname={$db['name']};charset=utf8",
            $db['user'],
            $db['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    } catch (PDOException $e) {
        die("Erro ao conectar ao banco {$db['name']}: " . $e->getMessage());
    }
}

return $connections;
