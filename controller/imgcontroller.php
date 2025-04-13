<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['pdf']) || !isset($_POST['nome'])) {
        echo "Arquivo ou nome não recebido.";
        exit;
    }

    $arquivo = $_FILES['pdf'];
    $nome = basename($_POST['nome']);

    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        echo "Erro ao enviar o arquivo.";
        exit;
    }

    $diretorio = __DIR__ . '/../uploads/docs';
    if (!is_dir($diretorio)) {
        mkdir($diretorio, 0777, true);
    }

    $destino = $diretorio . '/' . $nome;
    if (move_uploaded_file($arquivo['tmp_name'], $destino)) {
        echo "Documento salvo com sucesso como $nome.";
    } else {
        echo "Erro ao salvar o documento.";
    }
} else {
    echo "Requisição inválida.";
}
