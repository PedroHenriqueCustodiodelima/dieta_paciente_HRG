<?php
// index.php

include 'conexao.php'; // Inclui o arquivo de conexão

try {
    // Sua consulta SQL
    $query = $connection->query("
        SELECT 
            HSP.HSP_NUM            AS 'IH',
            HSP.HSP_PAC            AS 'REGISTRO',
            PAC.PAC_NOME          AS 'PACIENTE',
            CNV.CNV_NOME          AS 'CONVENIO',
            RTRIM(STR.STR_NOME)   AS 'UNIDADE',
            LOC.LOC_NOME          AS 'LEITO',
            ISNULL(PSC.PSC_DHINI,'') AS 'PRESCRICAO',
            ISNULL(ADP.ADP_NOME,'') AS 'DIETA',
            DATEDIFF(hour, HSP.HSP_DTHRE, GETDATE()) AS 'horas'
        FROM 
            HSP 
        INNER JOIN LOC ON HSP_LOC = LOC_COD 
        INNER JOIN STR ON STR_COD = LOC_STR
        INNER JOIN PAC ON PAC.PAC_REG = HSP.HSP_PAC
        INNER JOIN CNV ON CNV_COD = HSP.HSP_CNV
        LEFT JOIN PSC ON PSC.PSC_HSP = HSP.HSP_NUM AND PSC.PSC_PAC = HSP.HSP_PAC AND PSC.PSC_TIP = 'D'
        LEFT JOIN ADP ON ADP.ADP_COD = PSC.PSC_ADP AND ADP_TIPO = 'D'
        WHERE 
            HSP_TRAT_INT = 'I'
            AND HSP_STAT = 'A'
            AND PSC.PSC_STAT <> 'S'
            AND PSC.PSC_DHINI = (
                SELECT MAX(PSCMAX.PSC_DHINI) 
                FROM PSC PSCMAX 
                WHERE PSCMAX.PSC_PAC = PSC.PSC_PAC 
                    AND PSCMAX.PSC_HSP = PSC.PSC_HSP
                    AND PSCMAX.PSC_TIP = 'D'
                    AND PSCMAX.PSC_STAT = 'A'
            )
        GROUP BY 
            HSP.HSP_NUM,
            HSP.HSP_PAC,
            PAC.PAC_NOME,
            CNV.CNV_NOME,
            STR.STR_NOME,
            LOC.LOC_NOME,
            PSC.PSC_ADP,
            ADP.ADP_NOME,
            ISNULL(PSC.PSC_DHINI, ''),
            DATEDIFF(hour, HSP.HSP_DTHRE, GETDATE())
        ORDER BY 
            STR.STR_NOME,
            LOC.LOC_NOME
    ");

    $result = $query->fetchAll(PDO::FETCH_ASSOC); // Obtém todos os resultados como um array associativo

    // Verifica se existem resultados
    if (count($result) > 0) {
        echo "<table border='1'>"; // Começa a tabela
        echo "<tr>
                <th>IH</th>
                <th>REGISTRO</th>
                <th>PACIENTE</th>
                <th>CONVENIO</th>
                <th>UNIDADE</th>
                <th>LEITO</th>
                <th>PRESCRICAO</th>
                <th>DIETA</th>
                <th>HORAS</th>
              </tr>";

        // Exibe os dados em linhas da tabela
        foreach ($result as $row) {
            echo "<tr>
                    <td>{$row['IH']}</td>
                    <td>{$row['REGISTRO']}</td>
                    <td>{$row['PACIENTE']}</td>
                    <td>{$row['CONVENIO']}</td>
                    <td>{$row['UNIDADE']}</td>
                    <td>{$row['LEITO']}</td>
                    <td>{$row['PRESCRICAO']}</td>
                    <td>{$row['DIETA']}</td>
                    <td>{$row['horas']}</td>
                  </tr>";
        }

        echo "</table>"; // Finaliza a tabela
    } else {
        echo "Nenhum resultado encontrado."; // Mensagem se não houver resultados
    }
} catch (PDOException $e) {
    echo "Erro na consulta: " . $e->getMessage(); // Exibe erro na consulta
}
?>
