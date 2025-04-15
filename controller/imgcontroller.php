<?php
include '../controller/auth.php';
// include '../connection/connect.php';
$cpf = $_SESSION['cpf'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['pdf']) || !isset($_POST['nome'])) {
        echo "Arquivo ou nome não recebido.";
        exit;
    }

    if (!isset($_SESSION['cpf'])) {
        echo "CPF não encontrado na sessão.";
        exit;
    }

    $cpf = preg_replace('/[^0-9]/', '', $_SESSION['cpf']); // remove possíveis caracteres especiais
    $arquivo = $_FILES['pdf'];
    $nome = basename($_POST['nome']);

    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        echo "Erro ao enviar o arquivo.";
        exit;
    }

    $diretorioBase = __DIR__ . '/../uploads';
    $diretorioCPF = $diretorioBase . '/' . $cpf;

    // Cria o diretório base, se necessário
    if (!is_dir($diretorioBase)) {
        mkdir($diretorioBase, 0777, true);
    }

    // Cria o diretório do CPF, se necessário
    if (!is_dir($diretorioCPF)) {
        mkdir($diretorioCPF, 0777, true);
    }

    $destino = $diretorioCPF . '/' . $nome;
    if (move_uploaded_file($arquivo['tmp_name'], $destino)) {
        echo "Documento salvo com sucesso como $nome na pasta $cpf.";
        
        // --- NOVO BLOCO: Atualiza a tabela imagem ---
        include '../connection/connect.php';

        $tipo = $_POST['tipo'] ?? ''; // tipo = nome do campo (ex: 'procuracao', 'contrato', etc.)

        if ($tipo) {
            try {
                // Buscar cdpessoa com base no CPF
                $sqlPessoa = "SELECT cdpessoa FROM pessoa WHERE cpf = ?";
                $stmtPessoa = $conn->prepare($sqlPessoa);
                $stmtPessoa->execute([$cpf]);
                $pessoa = $stmtPessoa->fetch(PDO::FETCH_ASSOC);

                if ($pessoa) {
                    $cdpessoa = $pessoa['cdpessoa'];

                    // Verifica se já existe registro na imagem
                    $checkSql = "SELECT * FROM imagem WHERE cdpessoa = ?";
                    $checkStmt = $conn->prepare($checkSql);
                    $checkStmt->execute([$cdpessoa]);
                    $existe = $checkStmt->fetch(PDO::FETCH_ASSOC);

                    if ($existe) {
                        // Atualiza campo do tipo
                        $updateSql = "UPDATE imagem SET $tipo = 1 WHERE cdpessoa = ?";
                        $updateStmt = $conn->prepare($updateSql);
                        $updateStmt->execute([$cdpessoa]);
                    } else {
                        // Insere novo registro com o campo correto
                        $insertSql = "INSERT INTO imagem (cdpessoa, $tipo) VALUES (?, 1)";
                        $insertStmt = $conn->prepare($insertSql);
                        $insertStmt->execute([$cdpessoa]);
                    }
                }
            } catch (Exception $e) {
                error_log("Erro ao atualizar imagem: " . $e->getMessage());
            }
        }
        // --- FIM DO BLOCO NOVO ---
    } else {
        echo "Erro ao salvar o documento.";
    }
}

// Retornar status dos botões em GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json');

    include '../connection/connect.php';

    if (!isset($_SESSION['cpf'])) {
        echo json_encode(['error' => 'CPF não encontrado na sessão']);
        exit;
    }

    $cpf = preg_replace('/[^0-9]/', '', $_SESSION['cpf']);

    try {
        // Descobrir o cdpessoa com base no CPF
        $sqlPessoa = "SELECT cdpessoa FROM pessoa WHERE cpf = ?";
        $stmtPessoa = $conn->prepare($sqlPessoa);
        $stmtPessoa->execute([$cpf]);
        $pessoa = $stmtPessoa->fetch(PDO::FETCH_ASSOC);

        if (!$pessoa) {
            echo json_encode(['error' => 'Pessoa não encontrada']);
            exit;
        }

        $cdpessoa = $pessoa['cdpessoa'];

        // Buscar imagens associadas à pessoa
        $sqlImagem = "SELECT * FROM imagem WHERE cdpessoa = ?";
        $stmtImg = $conn->prepare($sqlImagem);
        $stmtImg->execute([$cdpessoa]);
        $imagens = $stmtImg->fetchAll(PDO::FETCH_ASSOC);

        $status = [];

        if ($imagens) {
            // Supondo que haja apenas um registro para essa pessoa
            $imagem = $imagens[0];
            $status['procuracao'] = $imagem['procuracao'];
            $status['contrato'] = $imagem['contrato'];
            $status['declaracao'] = $imagem['declaracao'];
            $status['revogacao'] = $imagem['revogacao'];
            $status['rg'] = $imagem['rg'];
            $status['cnh'] = $imagem['cnh'];
            $status['endereco'] = $imagem['endereco'];
            $status['docextra1'] = $imagem['docextra1'];
            $status['docextra2'] = $imagem['docextra2'];
            $status['docextra3'] = $imagem['docextra3'];
            $status['docextra4'] = $imagem['docextra4'];
        }

        // error_log(print_r($status, true));    

        echo json_encode($status);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Erro ao buscar imagem: ' . $e->getMessage()]);
    }

    exit;
}
