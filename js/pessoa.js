document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("backButton").addEventListener("click", function () {
        window.location.href = "../view/menuprincipal.php";
    });
    autocompletePessoa();
});

document.addEventListener("DOMContentLoaded", function () {
    const cpfInput = document.getElementById("cpf");

    // Remove tudo que não for número enquanto o usuário digita
    cpfInput.addEventListener("input", function () {
        cpfInput.value = cpfInput.value.replace(/[^\d]/g, ''); // Remove caracteres não numéricos
    });

    cpfInput.addEventListener("blur", function () {
        const cpf = cpfInput.value.trim();

        if (cpf && !validarCPF(cpf)) {
            cpfInput.classList.add("input-error");

            // Exibe uma mensagem de erro visual em vez de um alert
            const errorMessage = document.createElement("span");
            errorMessage.classList.add("error-message");
            errorMessage.innerText = "CPF/RCPN inválido!";

            // Verifica se a mensagem já existe e evita duplicação
            if (!cpfInput.parentElement.querySelector(".error-message")) {
                cpfInput.parentElement.appendChild(errorMessage);
            }

            // Foca novamente no campo CPF para permitir correção
            cpfInput.focus();
        } else {
            cpfInput.classList.remove("input-error");

            // Remove a mensagem de erro
            const existingErrorMessage = cpfInput.parentElement.querySelector(".error-message");
            if (existingErrorMessage) {
                existingErrorMessage.remove();
            }
        }
    });
});


let editingPessoaId = null;

document.getElementById('searchInput').addEventListener('input', autocompletePessoa);

function autocompletePessoa() {
    const query = document.getElementById('searchInput').value.trim();

    if (query.length < 4) {
        document.getElementById('pessoa_data').innerHTML = '';
        return;
    }

    fetch(`../controller/pessoacontroller.php?action=search&term=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {

            const tbody = document.getElementById('pessoa_data');
            tbody.innerHTML = '';

            data.forEach(pessoa => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                   <td class="action-icons">
                       <a href="#" onclick="editPessoa(${pessoa.cdpessoa})"><i class="fas fa-edit edit-icon" title="Editar"></i></a>
                   </td>
                   <td>${pessoa.cdpessoa}</td>
                   <td>${pessoa.nome}</td>
                   <td>${pessoa.cpf}</td>
                   <td>${pessoa.telefone}</td>
                   <td>${pessoa.municipio}</td>
                   <td>${pessoa.uf}</td>
                   <td class="action-icons">
                      <a href="#" onclick="deletePessoa(${pessoa.cdpessoa}, ${pessoa.cpf})"><i class="fas fa-trash-alt delete-icon" title="Excluir"></i></a>
                   </td>
             `;

                tbody.appendChild(tr);
            });
        });
}

function savePessoa() {
    const cpf = document.getElementById("cpf").value.trim();
    const fields = [
        "nome", "nacionalidade", "profissao", "estado_civil", "rg", "cpf",
        "endereco", "bairro", "municipio", "uf", "cep", "telefone",
        "docespecial", "apelido", "excluido"
    ];

    // Verificação simples: nome não pode estar vazio
    const nome = document.getElementById("nome").value.trim();
    if (nome === '') {
        alert("O nome não pode estar vazio.");
        return;
    }

    const cpfInput = document.getElementById("cpf");

    if (!validarCPF(cpf)) {
        cpfInput.classList.add("input-error");
        cpfInput.focus();
        alert("CPF inválido. Por favor, digite um CPF válido.");
        return;
    } else {
        cpfInput.classList.remove("input-error");
    }

    const action = editingPessoaId ? 'update' : 'create';

    // Montar dados como pares chave=valor
    let formDataArray = fields.map(id => {
        const value = document.getElementById(id)?.value || '';
        return `${encodeURIComponent(id)}=${encodeURIComponent(value)}`;
    });

    if (editingPessoaId) {
        formDataArray.push(`cdpessoa=${encodeURIComponent(editingPessoaId)}`);
    }

    const formData = formDataArray.join('&');
    // console.log("FormData sendo enviado:", formData);

    fetch(`../controller/pessoacontroller.php?action=${action}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: formData
    })
    .then(res => res.text())
    .then(response => {
        // console.log(response)
        // Remove o prefixo "resposta do servidor " e converte para JSON
        let jsonString = response.replace("resposta do servidor ", "");
        let data;
        try {
            data = JSON.parse(jsonString);
        } catch(e) {
            console.error("Erro ao parsear JSON:", e);
            data = {error: "Resposta inesperada do servidor"};
        }
        
        // Exibir a mensagem desejada
        let message = data.error || data.success || "Resposta desconhecida";
        // Você pode exibir essa mensagem em um alert ou em um elemento na página
        alert(message);
        // Se preferir exibir em um elemento HTML, certifique-se de ter um container, como:
        // document.getElementById("msgResponse").innerText = message;
        
        // Limpa os campos do formulário
        // fields.forEach(id => {
        //     const el = document.getElementById(id);
        //     if (el) el.value = '';
        // });

        editingPessoaId = null;
        document.getElementById("saveBtn").textContent = "Adicionar Pessoa";
        autocompletePessoa();
        // Chama o botão de captura de imagem após salvar
        const abrirImgBtn = document.getElementById("abrirImgBtn");
        if (abrirImgBtn) {
            abrirImgBtn.click();
        }

    })
    .catch(error => {
        console.error("Erro na requisição:", error);
        alert("Erro na requisição.");
    });
}

function editPessoa(id) {
    fetch('../controller/pessoacontroller.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'getPessoa',
            cdpessoa: id
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data) {
            document.getElementById("nome").value = data.nome || '';
            document.getElementById("nacionalidade").value = data.nacionalidade || '';
            document.getElementById("profissao").value = data.profissao || '';
            document.getElementById("estado_civil").value = data.estado_civil || '';
            document.getElementById("rg").value = data.rg || '';
            document.getElementById("cpf").value = data.cpf || '';
            document.getElementById("endereco").value = data.endereco || '';
            document.getElementById("bairro").value = data.bairro || '';
            document.getElementById("municipio").value = data.municipio || '';
            document.getElementById("uf").value = data.uf || '';
            document.getElementById("cep").value = data.cep || '';
            document.getElementById("telefone").value = data.telefone || '';
            document.getElementById("docespecial").value = data.docespecial || '';
            document.getElementById("apelido").value = data.apelido || '';
            // document.getElementById("excluido").value = data.excluido || '';


            document.getElementById("saveBtn").textContent = "Salvar Alteração";
            editingPessoaId = id;

            // Abrir a imagem com base no CPF, se o CPF estiver disponível
            // const cpf = data.cpf || '';
            // if (cpf) {
            //     window.open(`../view/img.php?cpf=${cpf}`, '_blank');
            // }
        } else {
            alert("Pessoa não encontrada.");
        }
    })
    .catch(error => {
        console.error("Erro ao buscar dados da pessoa:", error);
    });
}

function deletePessoa(id,cpf) {
    if (!confirm("Tem certeza que deseja excluir esta pessoa?")) return;

    fetch('../controller/pessoacontroller.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'delete',
            cdpessoa: id,
            cpf: cpf
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // alert(data.success);
            autocompletePessoa();
        } else {
            alert(data.error || "Erro ao excluir pessoa.");
        }
    })
    .catch(error => {
        console.error("Erro ao excluir pessoa:", error);
        alert("Erro ao excluir pessoa.");
    });
}

function gerarPDF(documento) {
    const dados = {
        nome: document.getElementById("nome").value,
        nacionalidade: document.getElementById("nacionalidade").value,
        profissao: document.getElementById("cpf").value,
        estado_civil: document.getElementById("estado_civil").value,
        rg: document.getElementById("rg").value,
        cpf: document.getElementById("cpf").value,
        endereco: document.getElementById("endereco").value,
        bairro: document.getElementById("bairro").value,
        municipio: document.getElementById("municipio").value,
        uf: document.getElementById("uf").value,
        cep: document.getElementById("cep").value,
        telefone: document.getElementById("telefone").value,
        apelido: document.getElementById("apelido").value, // <- Envia a opção selecionada
        documento: documento // <- Envia a opção selecionada
    };

    const form = document.createElement("form");
    form.method = "POST";
    form.action = "../controller/gerapdfcontroller.php";
    form.target = "_blank";

    for (const key in dados) {
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = key;
        input.value = dados[key];
        form.appendChild(input);
    }

    document.body.appendChild(form);
    form.submit();
}

// No botão Capturar Imagem:
document.getElementById('abrirImgBtn').addEventListener('click', () => {
  const cpf = document.getElementById('cpf').value;

  fetch('../controller/set_session.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: 'cpf=' + encodeURIComponent(cpf)
  })
  .then(response => response.text())
  .then(data => {
    // console.log('Resposta do set_session:', data);
    if (data.trim() === 'OK') {
        
    // 🔁 Abre img.php em nova aba
      window.open('img.php', '_blank');

    } else {
      alert('Erro ao definir CPF na sessão');
    }
  })
  .catch(err => {
    console.error('Erro no fetch do CPF:', err);
  });
});

function validarCPF(codigo) {
    codigo = codigo.replace(/[^\d]+/g, '');

    // Validação de CPF (11 dígitos)
    if (codigo.length === 11) {
        if (/^(\d)\1+$/.test(codigo)) return false;

        let soma = 0;
        for (let i = 0; i < 9; i++) {
            soma += parseInt(codigo.charAt(i)) * (10 - i);
        }
        let resto = (soma * 10) % 11;
        if (resto === 10 || resto === 11) resto = 0;
        if (resto !== parseInt(codigo.charAt(9))) return false;

        soma = 0;
        for (let i = 0; i < 10; i++) {
            soma += parseInt(codigo.charAt(i)) * (11 - i);
        }
        resto = (soma * 10) % 11;
        if (resto === 10 || resto === 11) resto = 0;
        return resto === parseInt(codigo.charAt(10));
    }

    // Validação da Matrícula do Registro Civil (32 dígitos)
    if (codigo.length === 32) {
        return true;
        // if (!/^\d+$/.test(codigo)) return false;

        // const base = codigo.substring(0, 30);
        // const dvInformado = codigo.substring(30, 32);

        // // Cálculo do DV com módulo 11 e pesos de 2 a 9
        // let soma = 0;
        // let peso = 2;

        // for (let i = base.length - 1; i >= 0; i--) {
        //     soma += parseInt(base.charAt(i)) * peso;
        //     peso = peso < 9 ? peso + 1 : 2;
        // }

        // let mod = soma % 11;
        // let dvCalculado = mod === 0 || mod === 1 ? "00" : (11 - mod).toString().padStart(2, '0');

        // return dvCalculado === dvInformado;
    }

    // Caso não seja CPF nem matrícula
    return false;
}



// Função para tratar o tempo da sessão
let timeout;

function resetTimeout() {
    // Limpa o timeout anterior
    clearTimeout(timeout);
    // Define um novo timeout de 30 segundos
    timeout = setTimeout(function() {
        // window.location.href = "../index.php"; // Redireciona para o login.php após 30 segundos de inatividade
        window.location.href = "../controller/logout.php"; // Redireciona para logout após 10 minutos
    }, 600000);
}

// Adicionando event listeners para reiniciar o timer quando o usuário interagir
document.getElementById("nome").addEventListener("change", resetTimeout);
document.getElementById("cpf").addEventListener("change", resetTimeout);
document.querySelector("button").addEventListener("click", resetTimeout);

// Chamada inicial ao carregar a página
resetTimeout(); // Inicia o timer assim que a página é carregada