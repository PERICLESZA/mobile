<?php
include '../controller/auth.php';

// Verifica se a chave 'key' está definida na sessão e se tem o valor esperado
$show_buttons = isset($_SESSION['key']) && $_SESSION['key'] === "@MasterPaulo";

// Obtém o valor da variável de sessão 'store'
$selected_store = isset($_SESSION['store']) ? $_SESSION['store'] : 'Nenhuma loja selecionada';
$nmstore = isset($_SESSION['nmstore']) ? $_SESSION['nmstore'] : 'Nenhuma loja selecionada';
$usuario = isset($_SESSION['nome']) ? $_SESSION['nome'] : 'Nenhum usuário selecionada';
$perfil = isset($_SESSION['perfil']) ? $_SESSION['perfil'] : '';

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/menuprincipal.css">
    <title>Menu</title>
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
</head>

<body>
    <div class="menu-container">
        <div class="sidebar">
            <h2>Menu</h2>
            <ul>
                <?php if ($perfil === 'A'): ?>
                    <li><a href="user.php">Cadastro do Usuário</a></li>
                    <li><a href="admpessoa.php">Administrar Cadastro Pessoa</a></li>
                    <li><a href="pessoa.php">Cadastro da Pessoa</a></li>
                <?php elseif ($perfil === 'C'): ?>
                    <li><a href="admpessoa.php">Administrar Cadastro Pessoa</a></li>
                    <li><a href="pessoa.php">Cadastro da Pessoa</a></li>
                <?php else: ?>
                    <li><a href="pessoa.php">Cadastro da Pessoa</a></li>
                <?php endif; ?>
                <br><br>
                <a href="../controller/logout.php">Logout</a>
            </ul>
            <!-- Exibir a variável de sessão 'store' no final da sidebar -->
        </div>
        <div class="content">
            <h1>Bem vindo ao sistema Mobile!</h1>
            <p>Usuário logado: <strong><?php echo $usuario; ?></strong></p>
            <p>Use a barra lateral para navegar nas opções.</p>
        </div>
    </div>
</body>

<script>
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

    // Chamada inicial ao carregar a página
    resetTimeout(); // Inicia o timer assim que a página é carregada
</script>

</html>