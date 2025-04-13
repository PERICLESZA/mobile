<?php
if (isset($_FILES['pdf'])) {
    $arquivo = $_FILES['pdf'];
    $nome = basename($arquivo['name']);
    $pastaDestino = "../documentos/";

    if (!is_dir($pastaDestino)) {
        mkdir($pastaDestino, 0755, true);
    }

    $caminho = $pastaDestino . $nome;

    if (move_uploaded_file($arquivo['tmp_name'], $caminho)) {
        echo "PDF salvo com sucesso!";
    } else {
        echo "Erro ao salvar o PDF.";
    }
} else {
    echo "Nenhum arquivo recebido.";
}
