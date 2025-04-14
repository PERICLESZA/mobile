<?php include '../controller/auth.php'; ?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Pessoa</title>
    <link rel="stylesheet" href="../css/cadastro.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="dashboard-container">
        <h1>Pessoa</h1>

        <div class="form-container">
            <div class="cad-group">
                <div class="input-container">
                    <label for="nome">Nome</label>
                    <input type="text" id="nome" placeholder="Digite o nome completo">
                </div>
                <div class="input-container">
                    <label for="cpf">CPF</label>
                    <input type="text" id="cpf" placeholder="Digite o CPF">
                </div>
                <div class="input-container">
                    <label for="nacionalidade">Nacionalidade</label>
                    <input type="text" id="nacionalidade" placeholder="Digite a nacionalidade">
                </div>
                <div class="input-container">
                    <label for="profissao">Profissão</label>
                    <input type="text" id="profissao" placeholder="Digite a profissão">
                </div>
                <div class="input-container">
                    <label for="estado_civil">Estado Civil</label>
                    <input type="text" id="estado_civil" placeholder="Digite o estado civil">
                </div>
                <div class="input-container">
                    <label for="rg">RG</label>
                    <input type="text" id="rg" placeholder="Digite o RG">
                </div>
            </div>

            <div class="cad-group">
                <div class="input-container">
                    <label for="endereco">Endereço</label>
                    <input type="text" id="endereco" placeholder="Digite o endereço">
                </div>
                <div class="input-container">
                    <label for="bairro">Bairro</label>
                    <input type="text" id="bairro" placeholder="Digite o bairro">
                </div>
                <div class="input-container">
                    <label for="municipio">Município</label>
                    <input type="text" id="municipio" placeholder="Digite o município">
                </div>
                <div class="input-container">
                    <label for="uf">UF</label>
                    <input type="text" id="uf" placeholder="UF" maxlength="2">
                </div>
                <div class="input-container">
                    <label for="cep">CEP</label>
                    <input type="text" id="cep" placeholder="Digite o CEP">
                </div>
                <div class="input-container">
                    <label for="telefone">Telefone</label>
                    <input type="text" id="telefone" placeholder="Digite o telefone">
                </div>
            </div>
            <div class="cad-group">
                <div class="input-container">
                    <label for="docespecial">Documento</label>
                    <input type="text" id="docespecial" placeholder="Digite um documento extra">
                </div>
            </div>
            <input type="hidden" id="cdpessoa">
            <input type="hidden" id="excluido">

            <div class="button-group">
                <button type="button" id="saveBtn" onclick="savePessoa()">Salvar Pessoa</button>
                <button type="button" id="backButton">Voltar ao Menu Principal</button>
            </div>
            <div class="button-group">
                <button type="button" class="btn-vermelho" onclick="gerarPDF('todos')">Gerar docs</button>
                <button type="button" id="abrirImgBtn">Capturar Imagem</button>

                <!-- <button type="button" onclick="gerarPDF('procuracao')">Procuração</button>
                <button type="button" onclick="gerarPDF('contrato')">Contrato</button>
                <button type="button" onclick="gerarPDF('declaracao')">Declaração</button>
                <button type="button" onclick="gerarPDF('revogacao')">Revogação</button> -->

                <!-- <video id="camera" autoplay playsinline width="400" style="border: 1px solid #ccc;"></video>
                <canvas id="snapshot" style="display: none;"></canvas>

                <button onclick="tirarFoto()">Foto</button> -->


            </div>
            <div>
                <h2>Buscar Pessoa</h2>
                <input
                    type="text"
                    id="searchInput"
                    placeholder="Digite o nome da pessoa..."
                    style="width: 300px; padding: 8px; margin-bottom: 20px;">
            </div>

            <div class="table-container">
                <table border="0" class="table">
                    <thead>
                        <tr>
                            <th>Edit</th>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Telefone</th>
                            <th>Município</th>
                            <th>UF</th>
                            <th>Del</th>
                        </tr>
                    </thead>
                    <tbody id="pessoa_data"></tbody>
                </table>
            </div>
        </div>

        <script src="../js/pessoa.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/tesseract.js@5.0.0/dist/tesseract.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/pdf-lib/dist/pdf-lib.min.js"></script>

        <script>
            const video = document.getElementById('camera');
            const canvas = document.getElementById('snapshot');
            let stream;

            // Inicia a câmera traseira
            // navigator.mediaDevices.getUserMedia({
            //         video: {
            //             facingMode: 'environment'
            //         } // Acessa a câmera traseira
            //     })
            //     .then(s => {
            //         stream = s;
            //         video.srcObject = stream;
            //     })
            //     .catch(e => alert('Erro ao acessar a câmera: ' + e));

            // function tirarFoto() {
            //     canvas.width = video.videoWidth;
            //     canvas.height = video.videoHeight;
            //     canvas.getContext('2d').drawImage(video, 0, 0);
            //     gerarPDFPesquisavel(); // Chama a geração de PDF logo após tirar a foto
            // }

            async function gerarPDFPesquisavel() {
                const imageBlob = await new Promise(resolve =>
                    canvas.toBlob(resolve, 'image/jpeg', 1)
                );

                // OCR com Tesseract.js
                const {
                    data: {
                        text
                    }
                } = await Tesseract.recognize(imageBlob, 'por', {
                    logger: m => console.log(m) // pode remover se quiser
                });

                // Geração de PDF com texto
                const pdfDoc = await PDFLib.PDFDocument.create();
                const page = pdfDoc.addPage([595.28, 841.89]); // A4
                const font = await pdfDoc.embedFont(PDFLib.StandardFonts.Helvetica);

                const lines = text.split('\n');
                let y = 800;
                for (let line of lines) {
                    const clean = line.trim();
                    if (!clean) continue;
                    if (y < 30) {
                        page = pdfDoc.addPage([595.28, 841.89]);
                        y = 800;
                    }
                    page.drawText(clean, {
                        x: 50,
                        y: y,
                        size: 12,
                        font,
                        color: PDFLib.rgb(0, 0, 0),
                    });
                    y -= 16;
                }

                const pdfBytes = await pdfDoc.save();
                const blob = new Blob([pdfBytes], {
                    type: "application/pdf"
                });

                const formData = new FormData();
                formData.append("pdf", blob, "documento_camera.pdf");

                const res = await fetch("../controller/uploadcontroller.php", {
                    method: "POST",
                    body: formData
                });

                const resultado = await res.text();
                console.log(resultado);
            }
        </script>

</body>

</html>