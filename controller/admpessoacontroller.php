<?php

require '../connection/connect.php'; // Conexão com o banco

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
        searchAdmPessoas($conn);
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

    $stmtCheck = $conn->prepare("SELECT COUNT(*) as total FROM pessoa WHERE cpf = :cpf");
    $stmtCheck->execute([':cpf' => $cpf]);
    $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if ($result['total'] > 0) {
        echo json_encode(["error" => "CPF JÁ CADASTRADO!!!"]);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO pessoa 
        (nome, nacionalidade, profissao, estado_civil, rg, cpf, endereco, bairro, municipio, uf, cep, telefone, docespecial, excluido, idlogin, dtcad)
        VALUES 
        (:nome, :nacionalidade, :profissao, :estado_civil, :rg, :cpf, :endereco, :bairro, :municipio, :uf, :cep, :telefone, :docespecial, :excluido, :idlogin, :dtcad)");

    $stmt->execute([
        ':nome'          => strtoupper($_POST['nome'] ?? ''),
        ':nacionalidade' => strtoupper($_POST['nacionalidade'] ?? ''),
        ':profissao'     => strtoupper($_POST['profissao'] ?? ''),
        ':estado_civil'  => strtoupper($_POST['estado_civil'] ?? ''),
        ':rg'            => strtoupper($_POST['rg'] ?? ''),
        ':cpf'           => $cpf,
        ':endereco'      => strtoupper($_POST['endereco'] ?? ''),
        ':bairro'        => strtoupper($_POST['bairro'] ?? ''),
        ':municipio'     => strtoupper($_POST['municipio'] ?? ''),
        ':uf'            => strtoupper($_POST['uf'] ?? ''),
        ':cep'           => strtoupper($_POST['cep'] ?? ''),
        ':telefone'      => strtoupper($_POST['telefone'] ?? ''),
        ':docespecial'   => strtoupper($_POST['docespecial'] ?? ''),
        ':excluido'      => $_POST['excluido'] ?? 0,
        ':idlogin'       => $_SESSION['idlogin'] ?? '',
        ':dtcad'         => date('Y-m-d H:i:s'),
    ]);

    $cdpessoa = $conn->lastInsertId();

    $stmt2 = $conn->prepare("INSERT INTO imagem (cdpessoa) VALUES (:cdpessoa)");
    $stmt2->execute([':cdpessoa' => $cdpessoa]);

    echo json_encode(["success" => "Pessoa cadastrada com sucesso", "cdpessoa" => $cdpessoa]);
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
        excluido = :excluido,
        idlogin = :idlogin,
        dtalt = :dtalt,
        ok = :ok
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
        ':excluido' => $_POST['excluido'] ?? 0,
        ':idlogin' => $_SESSION['idlogin'] ?? '',
        ':dtalt' => date('Y-m-d'),
        ':ok' => $_POST['ok'] ?? ''
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
    $dtdel = date('Y-m-d');
    $idlogin = $_SESSION['idlogin'];

    $stmt = $conn->prepare("UPDATE pessoa SET cpf = :cpf, excluido = 1, dtdel = :dtdel, idlogin = :idlogin WHERE cdpessoa = :cdpessoa");
    $stmt->execute([
        ':cpf' => $cpfModificado,
        ':dtdel' => $dtdel,
        ':idlogin' => $idlogin,
        ':cdpessoa' => $cdpessoa
    ]);

    echo json_encode(["success" => "Pessoa excluída com sucesso"]);
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

function searchAdmPessoas($conn)
{
    $term = $_GET['term'] ?? '';
    $ok = $_GET['ok'] ?? '';
    $idlogin = $_SESSION['idlogin'] ?? '';

    // Se o usuário quiser todos os registros diretamente com "*"
    if ($term === '*') {
        $sql = "SELECT cdpessoa, nome, cpf, excluido, dtcad, dtalt, dtdel, idlogin, ok
                FROM pessoa
                ORDER BY nome ASC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    } else {
        $termParam = '%' . $term . '%';

        // Monta o SQL dinamicamente com ou sem o filtro "ok"
        $sql = "SELECT cdpessoa, nome, cpf, excluido, dtcad, dtalt, dtdel, idlogin, ok
                FROM pessoa
                 WHERE (nome LIKE :term OR cpf LIKE :term)";

        $params = [':term' => $termParam];

        // Só aplica o filtro "ok" se não for 2 (ou seja, se for 0 ou 1)
        if ($ok !== '' && $ok !== '2') {
            $sql .= " AND ok = :ok";
            $params[':ok'] = $ok;
        }

        $sql .= " ORDER BY nome ASC";

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
    }

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
