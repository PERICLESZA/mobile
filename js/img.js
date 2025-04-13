let stream;
const video = document.getElementById("camera");
const canvas = document.getElementById("snapshot");

navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
    .then((s) => {
        stream = s;
        video.srcObject = stream;
    })
    .catch((e) => alert("Erro ao acessar a câmera: " + e));

function tirarFotoPara(tipo) {
    const cpf = document.getElementById("cpf").value.trim();
    if (!cpf) {
        alert("Nenhuma pessoa selecionada com CPF válido.");
        return;
    }

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext("2d").drawImage(video, 0, 0);

    canvas.toBlob(async (blob) => {
        const formData = new FormData();
        const nomeArquivo = `${tipo}_${cpf}.pdf`;

        // Cria PDF com imagem capturada
        const pdfDoc = await PDFLib.PDFDocument.create();
        const img = await pdfDoc.embedJpg(await blob.arrayBuffer());
        const page = pdfDoc.addPage([img.width, img.height]);
        page.drawImage(img, {
            x: 0,
            y: 0,
            width: img.width,
            height: img.height,
        });

        const pdfBytes = await pdfDoc.save();
        const pdfBlob = new Blob([pdfBytes], { type: "application/pdf" });
        formData.append("pdf", pdfBlob, nomeArquivo);
        formData.append("nome", nomeArquivo);

        const res = await fetch("../controller/imgcontroller.php", {
            method: "POST",
            body: formData,
        });

        const resultado = await res.text();
        alert(resultado);
    }, "image/jpeg", 1);
}
