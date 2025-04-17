<?php

require '../connection/connect.php'; // $conn já estará disponível aqui

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        listPessoas($conn);
        break;
    case 'create':
        createPessoa($conn);
        break;
    case 'update':
        updatePessoa($conn);
        break;
    case 'delete':
        deletePessoa($conn);
        break;
    case 'search':
        searchPessoas($conn);
        break;
    case 'getPessoa':
        getPessoaById($conn);
        break;
    default:
        echo json_encode(["error" => "Ação inválida"]);
}

function listPessoas($conn)
{
    $sql = "SELECT cdpessoa, nome FROM pessoa ORDER BY nome ASC";
    $stmt = $conn->query($sql);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function createPessoa($conn)
{
    $stmt = $conn->prepare("INSERT INTO pessoa 
        (nome, nacionalidade, profissao, estado_civil, rg, cpf, endereco, bairro, municipio, uf, cep, telefone, docespecial, excluido)
        VALUES 
        (:nome, :nacionalidade, :profissao, :estado_civil, :rg, :cpf, :endereco, :bairro, :municipio, :uf, :cep, :telefone, :docespecial, :excluido)");

    $stmt->execute([
        ':nome' => $_POST['nome'] ?? '',
        ':nacionalidade' => $_POST['nacionalidade'] ?? '',
        ':profissao' => $_POST['profissao'] ?? '',
        ':estado_civil' => $_POST['estado_civil'] ?? '',
        ':rg' => $_POST['rg'] ?? '',
        ':cpf' => $_POST['cpf'] ?? '',
        ':endereco' => $_POST['endereco'] ?? '',
        ':bairro' => $_POST['bairro'] ?? '',
        ':municipio' => $_POST['municipio'] ?? '',
        ':uf' => $_POST['uf'] ?? '',
        ':cep' => $_POST['cep'] ?? '',
        ':telefone' => $_POST['telefone'] ?? '',
        ':docespecial' => $_POST['docespecial'] ?? '',
        ':excluido' => $_POST['excluido'] ?? '',

        
    ]);

    echo json_encode(["success" => "Pessoa cadastrada com sucesso"]);
}

function updatePessoa($conn)
{
    $cdpessoa = $_POST['cdpessoa'] ?? null;

    if (!$cdpessoa) {
        echo json_encode(["error" => "ID da pessoa não informado"]);
        return;
    }

    $stmt = $conn->prepare("UPDATE pessoa SET 
        nome = :nome,
        nacionalidade = :nacionalidade,
        profissao = :profissao,
        estado_civil = :estado_civil,
        rg = :rg,
        cpf = :cpf,
        endereco = :endereco,
        bairro = :bairro,
        municipio = :municipio,
        uf = :uf,
        cep = :cep,
        telefone = :telefone,
        docespecial = :docespecial,
        excluido = :excluido
        WHERE cdpessoa = :cdpessoa");

    $stmt->execute([
        ':cdpessoa' => $cdpessoa,
        ':nome' => $_POST['nome'] ?? '',
        ':nacionalidade' => $_POST['nacionalidade'] ?? '',
        ':profissao' => $_POST['profissao'] ?? '',
        ':estado_civil' => $_POST['estado_civil'] ?? '',
        ':rg' => $_POST['rg'] ?? '',
        ':cpf' => $_POST['cpf'] ?? '',
        ':endereco' => $_POST['endereco'] ?? '',
        ':bairro' => $_POST['bairro'] ?? '',
        ':municipio' => $_POST['municipio'] ?? '',
        ':uf' => $_POST['uf'] ?? '',
        ':cep' => $_POST['cep'] ?? '',
        ':telefone' => $_POST['telefone'] ?? '',
        ':docespecial' => $_POST['docespecial'] ?? '',
        ':excluido' => $_POST['excluido'] ?? ''
    ]);

    echo json_encode(["success" => "Pessoa atualizada com sucesso"]);
}

function deletePessoa($conn)
{
    $cdpessoa = $_POST['cdpessoa'] ?? null;
    $cpf = $_POST['cpf'] ?? null;

    if (!$cdpessoa) {
        echo json_encode(["error" => "ID inválido"]);
        return;
    }
    $cpfModificado = $cpf . '-' . $cdpessoa;

    // Exibindo os valores para verificação
    // error_log("Atualizando CPF: " . $cpfModificado);
    // error_log("CDPESSOA: " . $cdpessoa);

    $stmt = $conn->prepare("UPDATE pessoa SET cpf = :cpf, excluido = 1 WHERE cdpessoa = :cdpessoa");
    $stmt->bindParam(":cdpessoa", $cdpessoa);
    $stmt->bindParam(":cpf", $cpfModificado);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Pessoa excluída com sucesso"]);
    } else {
        echo json_encode(["error" => "Erro ao excluir pessoa"]);
    }
}

function getPessoaById($conn)
{
    $cdpessoa = $_POST['cdpessoa'] ?? null;

    if (!$cdpessoa) {
        echo json_encode(["error" => "ID da pessoa não informado"]);
        return;
    }

    $stmt = $conn->prepare("SELECT * FROM pessoa WHERE cdpessoa = :cdpessoa");
    $stmt->execute([':cdpessoa' => $cdpessoa]);
    $pessoa = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($pessoa);
}

function searchPessoas($conn)
{
    $term = $_GET['term'] ?? '';

    if ($term === '****') {
        // Buscar todos os registros, excluindo os marcados como excluido = 1
        $sql = "SELECT 
                    cdpessoa, 
                    nome, 
                    cpf,
                    telefone, 
                    municipio, 
                    uf
                FROM 
                    pessoa
                WHERE excluido != 1
                ORDER BY nome ASC";

        $stmt = $conn->query($sql); // sem bind, pois não tem parâmetros
    } else {
        // Buscar com filtro por nome, excluindo os marcados como excluido = 1
        $sql = "SELECT 
                    cdpessoa, 
                    nome, 
                    cpf,
                    telefone, 
                    municipio, 
                    uf
                FROM 
                    pessoa
                WHERE nome LIKE :term AND excluido != 1
                ORDER BY nome ASC";

        $stmt = $conn->prepare($sql);
        $stmt->execute([':term' => "%$term%"]);
    }

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
