<?php

class Conexao {

    function query($q) { 
        $host = "10.1.3.195,50000";
        $user = array(
            "UID" => "bi_user", 
            "PWD" => "Nad*HggLka", 
            "Database" => "smart",
            "CharacterSet" => "UTF-8"
        );

        $conexao = sqlsrv_connect($host, $user);

        if ($conexao === false) {
            echo "Could not connect.\n";
            die(print_r(sqlsrv_errors(), true));
        }

        $result = array();
        $query = sqlsrv_query($conexao, $q);

        if ($query === false) {
            echo "Error in SQL query.\n";
            die(print_r(sqlsrv_errors(), true)); // Exibe os erros na consulta
        }

        while ($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
            array_push($result, $row);
        }

        sqlsrv_free_stmt($query); // Libere a declaração após o uso
        sqlsrv_close($conexao); // Feche a conexão
        return $result;
    }
}

?>
