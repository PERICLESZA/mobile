<?php
    include '../controller/auth.php';
    $cpf = $_SESSION['cpf'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['pdf']) || !isset($_POST['nome'])) {
        echo "Arquivo ou nome não recebido.";
        exit;
    }

    if (!isset($_SESSION['cpf'])) {
        echo "CPF não encontrado na sessão.";
        exit;
    }

    $cpf = preg_replace('/[^0-9]/', '', $_SESSION['cpf']); // remove possíveis caracteres especiais
    $arquivo = $_FILES['pdf'];
    $nome = basename($_POST['nome']);

    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        echo "Erro ao enviar o arquivo.";
        exit;
    }

    $diretorioBase = __DIR__ . '/../uploads';
    $diretorioCPF = $diretorioBase . '/' . $cpf;

    // Cria o diretório base, se necessário
    if (!is_dir($diretorioBase)) {
        mkdir($diretorioBase, 0777, true);
    }

    // Cria o diretório do CPF, se necessário
    if (!is_dir($diretorioCPF)) {
        mkdir($diretorioCPF, 0777, true);
    }

    $destino = $diretorioCPF . '/' . $nome;
    if (move_uploaded_file($arquivo['tmp_name'], $destino)) {
        echo "Documento salvo com sucesso como $nome na pasta $cpf.";
    } else {
        echo "Erro ao salvar o documento.";
    }
} else {
    echo "Requisição inválida.";
}
