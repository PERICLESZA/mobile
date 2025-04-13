src="https://cdn.jsdelivr.net/npm/tesseract.js@5.0.0/dist/tesseract.min.js"
src="https://cdn.jsdelivr.net/npm/pdf-lib/dist/pdf-lib.min.js"

async function processar() {
    const input = document.getElementById('scanner');
    const file = input.files[0];
    const imgData = await file.arrayBuffer();

    const { data: { text } } = await Tesseract.recognize(file, 'por');

    const pdfDoc = await PDFLib.PDFDocument.create();
    const page = pdfDoc.addPage([600, 800]);

    const img = await pdfDoc.embedJpg(imgData); // ou embedPng

    page.drawImage(img, {
        x: 50,
        y: 300,
        width: 500,
        height: 400,
    });

    page.drawText(text, {
        x: 50,
        y: 200,
        size: 10
    });

    const pdfBytes = await pdfDoc.save();

    const blob = new Blob([pdfBytes], { type: "application/pdf" });
    const formData = new FormData();
    formData.append("pdf", blob, "documento.pdf");

    await fetch("../controller/uploadpdfcontroller.php", {
        method: "POST",
        body: formData
    });

    alert("PDF enviado com sucesso!");
}
