<?php
// db_connection.php

$host = '10.1.3.195'; // Endereço do servidor
$port = '50000'; // Porta do servidor
$db = 'smart'; // Nome do banco de dados
$user = 'bi_user'; // Nome de usuário
$pass = 'Nad*HggLka'; // Senha

// Tenta criar a conexão
try {
    $connection = new PDO("sqlsrv:server=$host,$port;Database=$db", $user, $pass);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexão bem-sucedida!";
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
}
?>
