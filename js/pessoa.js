document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("backButton").addEventListener("click", function () {
        window.location.href = "../view/menuprincipal.php";
    });
    autocompletePessoa();
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
                   <td>${pessoa.cdpessoa}</td>
                   <td>${pessoa.nome}</td>
                   <td>${pessoa.profissao}</td>
                   <td>${pessoa.telefone}</td>
                   <td>${pessoa.municipio}</td>
                   <td>${pessoa.uf}</td>
                   <td class="action-icons">
                       <a href="#" onclick="editPessoa(${pessoa.cdpessoa})"><i class="fas fa-edit edit-icon" title="Editar"></i></a>
                       <a href="#" onclick="deletePessoa(${pessoa.cdpessoa})"><i class="fas fa-trash-alt delete-icon" title="Excluir"></i></a>
                   </td>
             `;
                tbody.appendChild(tr);
            });
        });
}

function savePessoa() {
    const fields = [
        "nome", "nacionalidade", "profissao", "estado_civil", "rg", "cpf",
        "endereco", "bairro", "municipio", "uf", "cep", "telefone"
    ];

     // Verificação simples: nome não pode estar vazio
    const nome = document.getElementById("nome").value.trim();
    if (nome === '') {
        alert("O nome não pode estar vazio.");
        return;
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

    fetch(`../controller/pessoacontroller.php?action=${action}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: formData
    })
    .then(res => res.text())
    .then(response => {
        // alert(response);
        fields.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = '';
        });

        editingPessoaId = null;
        document.getElementById("saveBtn").textContent = "Adicionar Pessoa";
        autocompletePessoa();
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

            document.getElementById("saveBtn").textContent = "Salvar Alteração";
            editingPessoaId = id;
        } else {
            alert("Pessoa não encontrada.");
        }
    })
    .catch(error => {
        console.error("Erro ao buscar dados da pessoa:", error);
    });
}

function deletePessoa(id) {
    if (!confirm("Tem certeza que deseja excluir esta pessoa?")) return;

    fetch('../controller/pessoacontroller.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'delete',
            cdpessoa: id
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
