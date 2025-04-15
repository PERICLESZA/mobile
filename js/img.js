let stream;
const video = document.getElementById("camera");
const canvas = document.getElementById("snapshot");

// Função para detectar se é um dispositivo móvel
function isMobileDevice() {
  return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
}

// Função reutilizável para acessar a câmera com configuração adaptada
async function getCameraStream() {
  const videoConfig = isMobileDevice()
    ? {
        facingMode: { ideal: "environment" }, // traseira em celular
        width: { ideal: 1110 },
        height: { ideal: 860 }
      }
    : {
        width: { ideal: 1110 },
        height: { ideal: 860 } // evita erro em desktop
      };

  try {
    const stream = await navigator.mediaDevices.getUserMedia({ video: videoConfig });
    return stream;
  } catch (e) {
    console.error("Erro ao acessar a câmera:", e);
    alert("Erro ao acessar a câmera: " + e.message);
    throw e;
  }
}

// Inicializa a câmera ao carregar a página
getCameraStream().then((s) => {
  stream = s;
  video.srcObject = stream;
  carregarStatusDasImagens(); // Atualiza os status das imagens
  atualizarStatusDosBotoes(); // Atualiza os botões logo após a câmera ser iniciada
});

// Função de captura da foto com melhoria de nitidez
async function tirarFotoPara(tipo) {
  const cpf = document.getElementById("cpf").value.trim();
  if (!cpf) {
    alert("Nenhuma pessoa selecionada com CPF válido.");
    return;
  }

  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;

  const ctx = canvas.getContext("2d");
  ctx.imageSmoothingEnabled = true;
  ctx.imageSmoothingQuality = "high";
  ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

  canvas.toBlob(async (blob) => {
    const formData = new FormData();
    const nomeArquivo = `${tipo}_${cpf}.pdf`;

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
    formData.append("tipo", tipo); // <- isso é essencial

    const res = await fetch("../controller/imgcontroller.php", {
      method: "POST",
      body: formData,
    });

    const resultado = await res.text();
    alert(resultado);
    atualizarStatusDosBotoes();  // Atualiza os botões com os novos status
  }, "image/jpeg", 1); // qualidade máxima
     
}

// Função para carregar e pintar os botões com base nos status das imagens
async function carregarStatusDasImagens() {
  try {
    const response = await fetch("../controller/imgcontroller.php");
    const data = await response.json();

    console.log("JSON recebido:", data); // Verifique o que está chegando

    if (data.error) {
      console.warn("Erro ao carregar status:", data.error);
      return;
    }

    Object.entries(data).forEach(([campo, valor]) => {
      console.log(`Campo: ${campo}, Valor: ${valor}`); // Debug: mostra cada par recebido
      
      if (valor === "1" || valor === 1) { // Se o valor for 1 (como string ou número)
        const botao = document.getElementById(campo);
        if (botao) {
          botao.style.backgroundColor = "green";
          botao.style.color = "white";
          console.log(`Pintando o botão ${campo} de verde`);
        } else {
          console.warn(`Botão com id "${campo}" não encontrado!`);
        }
      }
    });
  } catch (error) {
    console.error("Erro ao carregar status das imagens:", error);
  }
}

// Chama assim que a câmera estiver pronta
getCameraStream().then((s) => {
  stream = s;
  video.srcObject = stream;
  carregarStatusDasImagens(); // <- Aqui!
  atualizarStatusDosBotoes();  // Atualiza os botões com os novos status
});

function atualizarStatusDosBotoes() {
    fetch('img.php')
        .then(response => response.json())
        .then(status => {
            // Aqui você atualiza o estilo de cada botão com base no status retornado
            for (let chave in status) {
                const botao = document.getElementById(chave);
                if (status[chave] == 1) {
                    botao.classList.remove('bg-gray-300');
                    botao.classList.add('bg-green-500', 'text-white');
                } else {
                    botao.classList.remove('bg-green-500', 'text-white');
                    botao.classList.add('bg-gray-300');
                }
            }
        });
}
