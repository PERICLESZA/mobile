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
    <link rel="stylesheet" href="../css/img.css">
    <script src="https://cdn.jsdelivr.net/npm/pdf-lib/dist/pdf-lib.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>

</head>

<body>
    <div class="dashboard-container">
        <h2>Registrar Imagens</h2>
        <div class="form-container">

            <div class="button-group">
                <button type="button" id="backButton" onclick="window.close()">Voltar ao Cadastro da Pessoa</button>
                <br>
            </div>

            <div class="cad-group">
                <div class="input-container">
                    <label for="cpf">CPF</label>
                    <input type="text" id="cpf" name="cpf" value="<?= htmlspecialchars($cpf) ?>" readonly>
                </div>
            </div>

            <video id="camera" autoplay playsinline width="400" style="border: 1px solid #ccc;"></video>
            <canvas id="snapshot" style="display: none;"></canvas>

            <div class="button-pair">
                <button type="button" id='procuracao' onclick="tirarFotoPara('procuracao')">Procuração</button>
                <button type="button" onclick="mostrarFotoPara('procuracao')">Ver</button>
            </div>

            <div class="button-pair">
                <button type="button" id='contrato' onclick="tirarFotoPara('contrato')">Contrato</button>
                <button type="button" onclick="mostrarFotoPara('contrato')">Ver</button>
            </div>

            <div class="button-pair">
                <button type="button" id='declaracao' onclick="tirarFotoPara('declaracao')">Declaração</button>
                <button type="button" onclick="mostrarFotoPara('declaracao')">Ver</button>
            </div>

            <div class="button-pair">
                <button type="button" id='revogacao' onclick="tirarFotoPara('revogacao')">Revogação</button>
                <button type="button" onclick="mostrarFotoPara('revogacao')">Ver</button>
            </div>

            <div class="button-pair">
                <button type="button" id='rg' onclick="tirarFotoPara('rg')">RG</button>
                <button type="button" onclick="mostrarFotoPara('rg')">Ver</button>
            </div>

            <div class="button-pair">
                <button type="button" id='cnh' onclick="tirarFotoPara('cnh')">CNH</button>
                <button type="button" onclick="mostrarFotoPara('cnh')">Ver</button>
            </div>

            <div class="button-pair">
                <button type="button" id='endereco' onclick="tirarFotoPara('endereco')">Endereço</button>
                <button type="button" onclick="mostrarFotoPara('endereco')">Ver</button>
            </div>

            <div class="button-pair">
                <button type="button" id='docextra1' onclick="tirarFotoPara('docextra1')">Doc extra1</button>
                <button type="button" onclick="mostrarFotoPara('docextra1')">Ver</button>
            </div>

            <div class="button-pair">
                <button type="button" id='docextra2' onclick="tirarFotoPara('docextra2')">Doc extra2</button>
                <button type="button" onclick="mostrarFotoPara('docextra2')">Ver</button>
            </div>

            <div class="button-pair">
                <button type="button" id='docextra3' onclick="tirarFotoPara('docextra3')">Doc extra3</button>
                <button type="button" onclick="mostrarFotoPara('docextra3')">Ver</button>
            </div>

            <div class="button-pair">
                <button type="button" id='docextra4' onclick="tirarFotoPara('docextra4')">Doc extra4</button>
                <button type="button" onclick="mostrarFotoPara('docextra4')">Ver</button>
            </div>
        </div>
    </div>

    <script src="../js/img.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            carregarStatusDasImagens(); // Executa imediatamente ao carregar a página
            atualizarStatusDosBotoes(); // Atualiza os botões já no início
        });
    </script>
</body>

</html>