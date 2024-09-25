<?php
include 'conexao.php'; // Inclui o arquivo de conexão

// Verifique se a variável $connection foi definida
if (isset($connection)) {
    echo "Conexão estabelecida com sucesso!";
} else {
    echo "Erro na conexão.";
}
?>
