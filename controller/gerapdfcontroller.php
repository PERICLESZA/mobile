<?php
ob_start(); // Evita erro de "Some data has already been output"

require_once(__DIR__ . '/../libs/tfpdf/tfpdf.php');
require_once(__DIR__ . '/../libs/fpdi/src/autoload.php');

use setasign\Fpdi\Tfpdf\Fpdi;

$modelos = [
    'procuracao' => [
        'arquivo' => __DIR__ . '/../libs/pdf_modelos/procuracao_m.pdf',
        'posicoes' => [
            "NOME" => [24, 22.5, 10, 5],
            "NACIONALIDADE" => [36, 28.7, 10, 5],
            "PROFISSAO" => [110, 28.7, 10, 5],
            "ESTADO_CIVIL" => [171, 28.7, 10, 5],
            "RG" => [23, 34.5, 10, 5],
            "CPF" => [101, 34.5, 10, 5],
            "ENDERECO" => [8.5, 41, 10, 5],
            "BAIRRO" => [160, 41, 10, 5],
            "MUNICIPIO" => [33, 47, 10, 5],
            "CEP" => [105, 47, 10, 5],
            "TELEFONE" => [166, 47, 10, 5]
        ]
    ],
    'contrato' => [
        'arquivo' => __DIR__ . '/../libs/pdf_modelos/contrato_m.pdf',
        'posicoes' => [
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
        ]
    ],
    'declaracao' => [
        'arquivo' => __DIR__ . '/../libs/pdf_modelos/declaracao_m.pdf',
        'posicoes' => [
            "NOME" => [49, 23, 10, 5],
            "NACIONALIDADE" => [36, 29, 10, 5],
            "PROFISSAO" => [110, 29, 10, 5],
            "ESTADO_CIVIL" => [171, 29, 10, 5],
            "RG" => [23, 35, 10, 5],
            "CPF" => [101, 35, 10, 5],
            "ENDERECO" => [8.5, 41.5, 10, 5],
            "BAIRRO" => [160, 41.5, 10, 5],
            "MUNICIPIO" => [33, 47.5, 10, 5],
            "CEP" => [105, 47.5, 10, 5],
            "TELEFONE" => [166, 47.5, 10, 5]
        ]
    ],
    'revogacao' => [
        'arquivo' => __DIR__ . '/../libs/pdf_modelos/revogacao_m.pdf',
        'posicoes' => [
            "NOME" => [24, 26, 10, 5],
            "NACIONALIDADE" => [36, 32, 10, 5],
            "PROFISSAO" => [110, 32, 10, 5],
            "ESTADO_CIVIL" => [171, 32, 10, 5],
            "RG" => [23, 38, 10, 5],
            "CPF" => [101, 38, 10, 5],
            "ENDERECO" => [8.5, 45, 10, 5],
            "BAIRRO" => [160, 45, 10, 5],
            "MUNICIPIO" => [33, 50, 10, 5],
            "CEP" => [105, 50, 10, 5],
            "TELEFONE" => [166, 50, 10, 5]
        ]
    ]
];


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
if (!isset($modelos[$documento]) && $documento !== 'todos') {
    echo "Documento inválido.";
    exit;
}

$pdf = new Fpdi();

if ($documento === 'todos') {
    foreach ($modelos as $doc => $config) {
        $pdf->AddPage();
        $pdf->setSourceFile($config['arquivo']);
        $template = $pdf->importPage(1);
        $pdf->useTemplate($template);

        $pdf->AddFont('DejaVu', '', 'DejaVuSans.ttf', true);
        $pdf->SetFont('DejaVu', '', 10);
        $pdf->SetTextColor(0, 0, 0);

        foreach ($config['posicoes'] as $campo => [$x, $y, $w, $h]) {
            $valor = $dados[$campo] ?? '';
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Rect($x, $y, $w, $h, 'F');
            $pdf->SetXY($x, $y);
            $pdf->Cell($w, $h, $valor, 0, 0);
        }
    }
} else {
    $config = $modelos[$documento];
    $pdf->AddPage();
    $pdf->setSourceFile($config['arquivo']);
    $template = $pdf->importPage(1);
    $pdf->useTemplate($template);

    $pdf->AddFont('DejaVu', '', 'DejaVuSans.ttf', true);
    $pdf->SetFont('DejaVu', '', 10);
    $pdf->SetTextColor(0, 0, 0);

    foreach ($config['posicoes'] as $campo => [$x, $y, $w, $h]) {
        $valor = $dados[$campo] ?? '';
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Rect($x, $y, $w, $h, 'F');
        $pdf->SetXY($x, $y);
        $pdf->Cell($w, $h, $valor, 0, 0);
    }
}

ob_end_clean();
$pdf->Output("I", $documento === 'todos' ? "documentos_completos.pdf" : "{$documento}.pdf");
exit;
