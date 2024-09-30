<?php

$host = '10.1.3.195'; 
$port = '50000'; 
$db = 'smart'; 
$user = 'bi_user'; 
$pass = 'Nad*HggLka'; 

try {
    $connection = new PDO("sqlsrv:server=$host,$port;Database=$db", $user, $pass);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
}
?>
