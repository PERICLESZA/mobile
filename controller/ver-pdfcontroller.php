<?php
$cpf = $_GET['cpf'] ?? '';
$tipo = $_GET['tipo'] ?? '';
$arquivo = "../uploads/$cpf/{$tipo}_{$cpf}.pdf";

if (!file_exists($arquivo)) {
    http_response_code(404);
    echo "Arquivo não encontrado.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Visualizar Documento</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>
    <style>
        #pdf-viewer {
            width: 100%;
            height: 100vh;
            overflow: auto;
            background: #f4f4f4;
        }

        canvas {
            display: block;
            margin: 0 auto;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div id="pdf-viewer"></div>

    <script>
        const url = '<?= $arquivo ?>';

        const viewer = document.getElementById('pdf-viewer');

        const renderPDF = async (url) => {
            const loadingTask = pdfjsLib.getDocument(url);
            const pdf = await loadingTask.promise;

            for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                const page = await pdf.getPage(pageNum);
                const containerWidth = viewer.clientWidth;
                const unscaledViewport = page.getViewport({
                    scale: 1
                });
                const scale = containerWidth / unscaledViewport.width;
                const viewport = page.getViewport({
                    scale
                });

                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                await page.render({
                    canvasContext: context,
                    viewport
                }).promise;
                viewer.appendChild(canvas);
            }
        };

        renderPDF(url);
    </script>
</body>

</html>