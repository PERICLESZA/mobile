<?php include '../controller/auth.php'; ?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administração de Pessoa</title>
    <link rel="stylesheet" href="../css/cadastro.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="dashboard-container">
        <h1>Admin Pessoa</h1>

        <div class="form-container">
            <div class="button-group">
                <button type="button" id="backButton">Voltar ao Menu Principal</button>
                <br/>
            </div>
            <div class="cad-group">
                <div class="input-container">
                    <label for="nome">Nome</label>
                    <input type="text" id="nome" placeholder="Digite o nome completo">
                </div>
                <div class="input-container">
                    <label for="cpf">CPF</label>
                    <input type="text" id="cpf" name="cpf" placeholder="Digite o CPF">
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
                <div class="input-container">
                    <label for="ok">Ok</label>
                    <select id="ok" name="ok">
                        <option value="1">Sim</option>
                        <option value="0" selected>Não</option>
                    </select>
                </div>

            </div>
            <input type="hidden" id="cdpessoa">
            <input type="hidden" id="excluido">

            <div class="button-group">
                <button type="button" id="saveBtn" onclick="savePessoa()">Salvar Pessoa</button>
            </div>
            <div class="button-group">
                <!-- <button type="button" class="btn-vermelho" onclick="gerarPDF('todos')">Gerar docs</button> -->
                <button type="button" id="abrirImgBtn">Ver Imagem</button>
            </div>
            </br>
            <div class="cad-group">
                <div class="input-container">
                    <label for="docespecial">Pesquisa Pessoa</label>
                    <input type="text" id="searchInput" placeholder="Digite o nome da pessoa...">
                </div>

                <div class="input-container">
                    <label for="SearchOk">Ok</label>
                    <select id="SearchOk">
                        <option value="1">Sim</option>
                        <option value="0">Não</option>
                        <option value="2" selected>Todos</option>
                    </select>
                </div>
                <div>
                    <button id="searchButton">Buscar</button>
                </div>
            </div>

            <div class="table-container">
                <table border="0" class="table">
                    <thead>
                        <tr>
                            <th>Edit</th>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Cad</th>
                            <th>Alt</th>
                            <th>Rem</th>
                            <th>Ok</th>
                            <th>Del</th>
                        </tr>
                    </thead>
                    <tbody id="pessoa_data"></tbody>
                </table>
            </div>
        </div>

        <!-- JS atualizado para o novo arquivo -->
        <script src="../js/admpessoa.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/tesseract.js@5.0.0/dist/tesseract.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/pdf-lib/dist/pdf-lib.min.js"></script>

        <script>
            const video = document.getElementById('camera');
            const canvas = document.getElementById('snapshot');
            let stream;

            async function gerarPDFPesquisavel() {
                const imageBlob = await new Promise(resolve =>
                    canvas.toBlob(resolve, 'image/jpeg', 1)
                );

                const {
                    data: {
                        text
                    }
                } = await Tesseract.recognize(imageBlob, 'por', {
                    logger: m => console.log(m)
                });

                const pdfDoc = await PDFLib.PDFDocument.create();
                let page = pdfDoc.addPage([595.28, 841.89]);
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