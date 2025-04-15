<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Retorna o array de conexões
$connections = require '../connection/lealds.php';

// Escolha a conexão desejada
$conn = $connections['cedroibr7'];
