<?php
$cpf = $_GET['cpf'] ?? '';
$tipo = $_GET['tipo'] ?? '';
$caminho = "../uploads/{$cpf}/{$tipo}_{$cpf}.pdf";

if (file_exists($caminho)) {
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . basename($caminho) . '"');
    readfile($caminho);
    exit;
} else {
    http_response_code(404);
    echo "Arquivo não encontrado.";
}
