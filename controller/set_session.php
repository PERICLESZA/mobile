<?php
require_once 'auth.php'; // já inicia a sessão

if (isset($_POST['cpf'])) {
    $_SESSION['cpf'] = $_POST['cpf'];
    session_write_close(); // força a gravação da sessão no disco
    echo 'OK';
} else {
    echo 'ERRO';
}
