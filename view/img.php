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
        <h1>Capturas</h1>

        <div class="form-container">
            <div class="cad-group">
                <div class="input-container">
                    <label for="cpf">CPF</label>
                    <input type="text" id="cpf" name="cpf" value="<?= htmlspecialchars($cpf) ?>" readonly>
                </div>
            </div>

            <video id="camera" autoplay playsinline width="400" style="border: 1px solid #ccc;"></video>
            <canvas id="snapshot" style="display: none;"></canvas>

            <div class="button-group">
                <button type="button" onclick="tirarFotoPara('procuracao')">Foto Procuração</button>
                <button type="button" onclick="tirarFotoPara('contrato')">Foto Contrato</button>
                <button type="button" onclick="tirarFotoPara('declaracao')">Foto Declaração</button>
                <button type="button" onclick="tirarFotoPara('revogacao')">Foto Revogação</button>
                <button type="button" onclick="window.close()">Voltar</button>
            </div>
        </div>
    </div>

    <script src="../js/img.js"></script>
</body>

</html>