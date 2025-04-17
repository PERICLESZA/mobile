<?php
include '../controller/auth.php';
$cpf = $_SESSION['cpf'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Captura de Documentos</title>
    <link rel="stylesheet" href="../css/cadastro.css">
    <script src="https://cdn.jsdelivr.net/npm/pdf-lib/dist/pdf-lib.min.js"></script>
</head>

<body>
    <div class="dashboard-container">
        <h2>Registrar Imagens</h2>
        <div class="form-container">
            <div class="button-group">
                <button type="button" id="backButton" onclick="window.close()">Voltar ao Cadastro da Pessoa</button>
                </br>
            </div>
            <div class="cad-group">
                <div class="input-container">
                    <label for="cpf">CPF</label>
                    <input type="text" id="cpf" name="cpf" value="<?= htmlspecialchars($cpf) ?>" readonly>
                </div>
            </div>

            <video id="camera" autoplay playsinline width="400" style="border: 1px solid #ccc;"></video>
            <canvas id="snapshot" style="display: none;"></canvas>

            <div class="button-group">
                <button type="button" id='procuracao' onclick="tirarFotoPara('procuracao')">Procuração</button>
                <button type="button" id='contrato' onclick="tirarFotoPara('contrato')">Contrato</button>
                <button type="button" id='declaracao' onclick="tirarFotoPara('declaracao')">Declaração</button>
                <button type="button" id='revogacao' onclick="tirarFotoPara('revogacao')">Revogação</button>
                <button type="button" id='rg' onclick="tirarFotoPara('rg')">RG</button>
                <button type="button" id='cnh' onclick="tirarFotoPara('cnh')">CNH</button>
                <button type="button" id='endereco' onclick="tirarFotoPara('endereco')">Endereço</button>
                <button type="button" id='docextra1' onclick="tirarFotoPara('docextra1')">Doc extra1</button>
                <button type="button" id='docextra2' onclick="tirarFotoPara('docextra2')">Doc extra2</button>
                <button type="button" id='docextra3' onclick="tirarFotoPara('docextra3')">Doc extra3</button>
                <button type="button" id='docextra4' onclick="tirarFotoPara('docextra4')">Doc extra4</button>
            </div>
        </div>
    </div>

    <script src="../js/img.js"></script>
</body>

</html>