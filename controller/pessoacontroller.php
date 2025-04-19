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
    $cpf = $_POST['cpf'] ?? '';

    // Verifica se o CPF já existe
    $stmtCheck = $conn->prepare("SELECT COUNT(*) as total FROM pessoa WHERE cpf = :cpf");
    $stmtCheck->execute([':cpf' => $cpf]);
    $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if ($result['total'] > 0) {
        echo json_encode(["error" => "CPF JÁ CADASTRADO!!!"]);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO pessoa 
        (nome, nacionalidade, profissao, estado_civil, rg, cpf, endereco, bairro, municipio, uf, cep, telefone, docespecial, excluido, apelido, idlogin, dtcad)
        VALUES 
        (:nome, :nacionalidade, :profissao, :estado_civil, :rg, :cpf, :endereco, :bairro, :municipio, :uf, :cep, :telefone, :docespecial, :excluido, :apelido,  :idlogin, :dtcad)");

    $stmt->execute([
        ':nome'          => strtoupper($_POST['nome'] ?? ''),
        ':nacionalidade' => strtoupper($_POST['nacionalidade'] ?? ''),
        ':profissao'     => strtoupper($_POST['profissao'] ?? ''),
        ':estado_civil'  => strtoupper($_POST['estado_civil'] ?? ''),
        ':rg'            => strtoupper($_POST['rg'] ?? ''),
        ':cpf'           => $cpf, // se necessário converter, também use strtoupper($cpf)
        ':endereco'      => strtoupper($_POST['endereco'] ?? ''),
        ':bairro'        => strtoupper($_POST['bairro'] ?? ''),
        ':municipio'     => strtoupper($_POST['municipio'] ?? ''),
        ':uf'            => strtoupper($_POST['uf'] ?? ''),
        ':cep'           => strtoupper($_POST['cep'] ?? ''),
        ':telefone'      => strtoupper($_POST['telefone'] ?? ''),
        ':docespecial'   => strtoupper($_POST['docespecial'] ?? ''),
        ':apelido'       => strtoupper($_POST['apelido'] ?? ''),
        ':excluido'      => $_POST['excluido'] ?? '',
        ':idlogin'       => $_SESSION['idlogin'] ?? '',
        ':dtcad'         => date('Y-m-d H:i:s'),
    ]);

    // pega o último ID inserido na tabela pessoa
    $cdpessoa = $conn->lastInsertId();

    // insere na tabela imagem usando o mesmo cdpessoa
    $stmt2 = $conn->prepare("INSERT INTO imagem (cdpessoa) VALUES (:cdpessoa)");
    $stmt2->execute([':cdpessoa' => $cdpessoa]);

    echo json_encode(["success" => "Pessoa e imagem cadastradas com sucesso"]);
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
        apelido = :apelido,
        excluido = :excluido,
        idlogin = :idlogin,
        dtalt = :dtalt
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
        ':apelido' => $_POST['apelido'] ?? '',
        ':excluido' => $_POST['excluido'] ?? '',
        ':idlogin' => $_SESSION['idlogin'] ?? '',
        ':dtalt' => date('Y-m-d')

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

    $dtdel = date('Y-m-d');
    $idlogin = $_SESSION['idlogin'];

    $stmt = $conn->prepare("UPDATE pessoa SET cpf = :cpf, excluido = 1, dtdel = :dtdel, idlogin = :idlogin WHERE cdpessoa = :cdpessoa");
    $stmt->bindParam(":cdpessoa", $cdpessoa);
    $stmt->bindParam(":cpf", $cpfModificado);
    $stmt->bindParam(":dtdel", $dtdel);
    $stmt->bindParam(":idlogin", $idlogin);

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
    $idlogin = $_SESSION['idlogin'];

    if ($term === '****') {
        // Buscar todos os registros, excluindo os marcados como excluido = 1
        $sql = "SELECT 
                    cdpessoa, nome, cpf, telefone, municipio, uf,idlogin,ok
                FROM 
                    pessoa
                WHERE excluido != 1 
                  AND idlogin = :idlogin 
                  AND ok = 0
                ORDER BY nome ASC";

        $stmt = $conn->prepare($sql);
        $stmt->execute([':idlogin' => $idlogin]);
    } else {
        // Buscar com filtro por nome, cpf ou apelido
        $sql = "SELECT 
                    cdpessoa, nome, cpf, telefone, municipio, uf, idlogin, apelido, ok
                FROM 
                    pessoa
                WHERE 
                    (nome LIKE :term OR cpf LIKE :term OR apelido LIKE :term)
                    AND excluido != 1
                    AND ok = 0
                    AND idlogin = :idlogin
                ORDER BY nome ASC";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':term' => "%$term%",
            ':idlogin' => $idlogin
        ]);
    }

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
