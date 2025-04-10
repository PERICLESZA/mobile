<?php
ob_start(); // Evita erro de "Some data has already been output"

require_once(__DIR__ . '/../libs/tfpdf/tfpdf.php');
require_once(__DIR__ . '/../libs/fpdi/src/autoload.php');

use setasign\Fpdi\Tfpdf\Fpdi;

// Recebe os dados
$dados = [
    "NOME" => $_POST["nome"] ?? '',
    "NACIONALIDADE" => $_POST["nacionalidade"] ?? '',
    "PROFISSAO" => $_POST["profissao"] ?? '',
    "ESTADO_CIVIL" => $_POST["estado_civil"] ?? '',
    "RG" => $_POST["rg"] ?? '',
    "CPF" => $_POST["cpf"] ?? '',
    "ENDERECO" => $_POST["endereco"] ?? '',
    "BAIRRO" => $_POST["bairro"] ?? '',
    "MUNICIPIO" => $_POST["municipio"] ?? '',
    "UF" => $_POST["uf"] ?? '',
    "CEP" => $_POST["cep"] ?? '',
    "TELEFONE" => $_POST["telefone"] ?? ''
];

$documento = $_POST["documento"] ?? 'procuracao';

// Caminho do modelo PDF
switch ($documento) {
    case 'procuracao':
        $modeloPDF = __DIR__ . '/../libs/pdf_modelos/procuracao_m.pdf';
        $posicoes = [
            "NOME" => [24, 22.5, 10, 5],
            "NACIONALIDADE" => [36, 28.7, 10, 5],
            "PROFISSAO" => [110, 28.7, 10, 5],
            "ESTADO_CIVIL" => [171, 28.7, 10, 5],
            "RG" => [23, 34.5, 10, 5],
            "CPF" => [101, 34.5, 10, 5],
            "ENDERECO" => [8.5, 41, 10, 5],
            "BAIRRO" => [155, 41, 10, 5],
            "MUNICIPIO" => [33, 47, 10, 5],
            "CEP" => [105, 47, 10, 5],
            "TELEFONE" => [166, 47, 10, 5]
        ];
        break;
    case 'contrato':
        $modeloPDF = __DIR__ . '/../libs/pdf_modelos/contrato_m.pdf';
        $posicoes = [
            "NOME" => [67, 24.6, 10, 5],
            "NACIONALIDADE" => [170, 24.6, 10, 5],
            "PROFISSAO" => [25, 28.8, 10, 5],
            "ESTADO_CIVIL" => [82, 28.8, 10, 5],
            "RG" => [120, 28.8, 10, 5],
            "CPF" => [166, 28.8, 10, 5],
            "ENDERECO" => [50, 33, 10, 5],
            "BAIRRO" => [18, 37, 10, 5],
            "MUNICIPIO" => [68, 37, 10, 5],
            "CEP" => [112, 37, 10, 5],
            "TELEFONE" => [157, 37, 10, 5]
        ];
        break;
    case 'declaracao':
        $modeloPDF = __DIR__ . '/../libs/pdf_modelos/declaracao_m.pdf';
        $posicoes = [
            "NOME" => [23, 32.9, 10, 5],
            "NACIONALIDADE" => [38, 38.9, 10, 5],
            "PROFISSAO" => [87, 38.9, 10, 5],
            "ESTADO_CIVIL" => [161, 38.9, 10, 5],
            "RG" => [26, 45, 10, 5],
            "CPF" => [92, 45, 10, 5],
            "ENDERECO" => [12, 51, 10, 5],
            "BAIRRO" => [149, 51, 10, 5],
            "MUNICIPIO" => [35, 57, 10, 5],
            "CEP" => [105, 57, 10, 5],
            "TELEFONE" => [155, 57, 10, 5]
        ];
        break;
    case 'revogacao':
        $modeloPDF = __DIR__ . '/../libs/pdf_modelos/revogacao_m.pdf';
        $posicoes = [
            "NOME" => [23, 32.9, 10, 5],
            "NACIONALIDADE" => [38, 38.9, 10, 5],
            "PROFISSAO" => [87, 38.9, 10, 5],
            "ESTADO_CIVIL" => [161, 38.9, 10, 5],
            "RG" => [26, 45, 10, 5],
            "CPF" => [92, 45, 10, 5],
            "ENDERECO" => [30, 66, 10, 5],
            "BAIRRO" => [149, 51, 10, 5],
            "MUNICIPIO" => [35, 72.2, 10, 5],
            "CEP" => [105, 72.2, 10, 5],
            "TELEFONE" => [155, 72.2, 10, 5]
        ];
        break;
    case 'todos':
        // lógica para imprimir todos os documentos
        // pode ser um loop chamando os modelos anteriores
        echo "Função para todos ainda não implementada.";
        exit;
    default:
        echo "Documento inválido.";
        exit;
}

$pdf = new Fpdi();
$pdf->AddPage();
$pdf->setSourceFile($modeloPDF);
$template = $pdf->importPage(1);
$pdf->useTemplate($template);

// Fonte
$pdf->AddFont('DejaVu', '', 'DejaVuSans.ttf', true);
$pdf->SetFont('DejaVu', '', 10);
$pdf->SetTextColor(0, 0, 0);

// Insere os dados nas posições
foreach ($posicoes as $campo => [$x, $y, $w, $h]) {
    $valor = $dados[$campo] ?? '';
    $pdf->SetFillColor(255, 255, 255);
    $pdf->Rect($x, $y, $w, $h, 'F');
    $pdf->SetXY($x, $y);
    $pdf->Cell($w, $h, $valor, 0, 0);
}

ob_end_clean(); // Limpa qualquer saída anterior
$pdf->Output();
