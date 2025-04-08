<?php
# connect.php
session_start();

// Retorna o array de conexões
$connections = require '../connection/lealds.php';

// Aqui você pode escolher dinamicamente ou fixo:
$conn = $connections['cedroibr7'];
