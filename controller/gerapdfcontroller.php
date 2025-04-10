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
    default:
        echo "Documento inválido.";
        exit;
}

$docxTemp = tempnam(sys_get_temp_dir(), 'docx_') . ".docx";
copy($modeloDOCX, $docxTemp);

$zip = new ZipArchive;
if ($zip->open($docxTemp) === TRUE) {
    $conteudo = $zip->getFromName('word/document.xml');

    foreach ($dados as $chave => $valor) {
        $conteudo = preg_replace('/{{\s*' . preg_quote($chave, '/') . '\s*}}/i', htmlspecialchars($valor), $conteudo);
    }

    // Simples substituição de tags do Word para HTML básico (bem limitado)
    $conteudo = str_replace(['<w:tbl>', '</w:tbl>', '<w:tr>', '</w:tr>', '<w:tc>', '</w:tc>'], '', $conteudo);
    $conteudo = preg_replace('/<w:p[^>]*>/', '<p>', $conteudo);
    $conteudo = str_replace('</w:p>', '</p>', $conteudo);
    $conteudo = preg_replace('/<w:t[^>]*>/', '', $conteudo);
    $conteudo = str_replace('</w:t>', '', $conteudo);

    $zip->close();
    unlink($docxTemp);

    // Exibir HTML formatado
    echo "<!DOCTYPE html><html lang='pt-br'><head>
            <meta charset='UTF-8'>
            <title>Documento: {$documento}</title>
<style>
    body {
        font-family: 'Times New Roman', serif;
        max-width: 794px; /* largura de uma folha A4 em px (21cm @ 96dpi) */
        margin: auto;
        padding: 2rem;
        line-height: 1.6;
        text-align: justify;
        background: #fff;
        color: #000;
    }

    p {
        margin-bottom: 1em;
    }

    .print-btn {
        position: fixed;
        top: 10px;
        right: 10px;
        padding: 10px 15px;
        background: #007bff;
        color: #fff;
        border: none;
        cursor: pointer;
        border-radius: 5px;
        z-index: 1000;
    }

    @media print {
        .print-btn {
            display: none;
        }

        body {
            margin: 0;
            padding: 1.5cm;
            width: 100%;
        }
    }
</style>
          </head>
          <body>
            <button class='print-btn' onclick='window.print()'>🖨️ Imprimir</button>
            $conteudo
          </body></html>";
    exit;
} else {
    echo 'Erro ao abrir o modelo DOCX.';
}
