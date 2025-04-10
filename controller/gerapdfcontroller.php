<?php
ob_start();

$dados = [
    "nome" => $_POST["nome"] ?? '',
    "nacionalidade" => $_POST["nacionalidade"] ?? '',
    "profissao" => $_POST["profissao"] ?? '',
    "estado_civil" => $_POST["estado_civil"] ?? '',
    "rg" => $_POST["rg"] ?? '',
    "cpf" => $_POST["cpf"] ?? '',
    "endereco" => $_POST["endereco"] ?? '',
    "bairro" => $_POST["bairro"] ?? '',
    "municipio" => $_POST["municipio"] ?? '',
    "uf" => $_POST["uf"] ?? '',
    "cep" => $_POST["cep"] ?? '',
    "telefone" => $_POST["telefone"] ?? ''
];

$documento = $_POST["documento"] ?? 'procuracao';

switch ($documento) {
    case 'procuracao':
    case 'contrato':
    case 'declaracao':
    case 'revogacao':
        $modeloDOCX = __DIR__ . "/../docs/{$documento}_m.docx";
        break;
    case 'todos':
        echo "Função para todos ainda não implementada.";
        exit;
    default:
        echo "Documento inválido.";
        exit;
}

$saida = tempnam(sys_get_temp_dir(), 'docx_') . ".docx";
copy($modeloDOCX, $saida);

$zip = new ZipArchive;
if ($zip->open($saida) === TRUE) {
    $conteudo = $zip->getFromName('word/document.xml');

    foreach ($dados as $chave => $valor) {
        $conteudo = preg_replace('/{{\s*' . preg_quote($chave, '/') . '\s*}}/i', htmlspecialchars($valor), $conteudo);
    }

    $zip->addFromString('word/document.xml', $conteudo);
    $zip->close();

    header("Content-Description: File Transfer");
    header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
    header("Content-Disposition: attachment; filename=\"{$documento}_preenchido.docx\"");
    header("Content-Length: " . filesize($saida));
    readfile($saida);
    unlink($saida);
    exit;
} else {
    echo 'Erro ao abrir o modelo DOCX.';
}
