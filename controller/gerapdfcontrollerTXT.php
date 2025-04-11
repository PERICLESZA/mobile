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
$modeloTXT = __DIR__ . "/../docs/{$documento}_m.txt";

if (!file_exists($modeloTXT)) {
    echo "Modelo de documento não encontrado.";
    exit;
}

// Lê o conteúdo do modelo
$conteudo = file_get_contents($modeloTXT);

if (empty(trim($conteudo))) {
    echo "Arquivo de modelo está vazio.";
    exit;
}


// Substitui os placeholders pelos dados
foreach ($dados as $chave => $valor) {
    $conteudo = preg_replace('/{{\s*' . preg_quote($chave, '/') . '\s*}}/i', htmlspecialchars($valor), $conteudo);
}

echo "<!DOCTYPE html>
<html lang='pt-br'>
<head>
    <meta charset='UTF-8'>
    <title>Documento: {$documento}</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 11pt;
            width: 210mm;
            height: 297mm; /* <- REMOVA ESTA LINHA */
            margin: auto;
            padding: 0.5cm;
            line-height: 1.3;
            text-align: justify;
            background: #fff;
            color: #000;
        }
    

        p {
            margin-bottom: 0.6em;
            page-break-inside: avoid;
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
            html, body {
                margin: 0;
                padding: 0;
                height: auto !important;
                overflow: visible;
            }
        
            p {
                page-break-inside: avoid;
            }
        
            * {
                box-sizing: border-box;
            }
        }
    </style>
    <style>
        .pagina-unica {
            width: 210mm;
            height: 297mm;
            overflow: hidden;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .print-btn {
                display: none;
            }
        }
    </style>
    <script>
        window.onload = function () {
            const body = document.body;
            [...body.children].forEach(el => {
                if (el.textContent.trim() === '' && el.tagName !== 'BUTTON') {
                    el.remove();
                }
            });

            // Debug opcional
            console.log('Altura do conteúdo:', body.scrollHeight + 'px');
        };
    </script>
</head>
<body>
<button class='print-btn' onclick='window.print()'>🖨️ Imprimir</button>
<div class='pagina-unica'>
    " . implode('', array_map(fn($par) => '<p>' . htmlspecialchars(trim($par)) . '</p>', preg_split('/\r\n|\r|\n/', $conteudo))) . "
</body>
</html>";
