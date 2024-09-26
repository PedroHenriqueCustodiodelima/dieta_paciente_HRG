<style>
    table {
    width: 80%; /* Reduz a largura da tabela */
    border-collapse: collapse;
    margin: 20px 0;
    font-size: 14px; /* Diminui o tamanho da fonte */
    text-align: left;
    }

    th, td {
        padding: 8px 10px; /* Reduz o espaçamento interno das células */
    }

    thead {
        background-color: #4CAF50; /* Cor verde para o cabeçalho */
        color: white; /* Texto branco */
    }

    tbody tr {
        border-bottom: 1px solid #dddddd;
    }

    tbody tr:nth-of-type(even) {
        background-color: #f3f3f3; /* Cor de fundo diferente para linhas pares */
    }

    tbody tr:last-of-type {
        border-bottom: 2px solid #4CAF50; /* Adiciona uma borda mais grossa no final */
    }

    tbody tr:hover {
        background-color: #f1f1f1; /* Cor de fundo ao passar o mouse */
    }

    td {
        text-align: center; /* Centraliza o conteúdo das células */
        font-size: 12px; /* Diminui ainda mais o tamanho da fonte nas células */
    }

    table th:first-child, 
    table td:first-child {
        text-align: left; /* Primeira coluna alinhada à esquerda */
    }

    .table-container {
        max-width: 80%; /* Ajusta o tamanho máximo para responsividade */
        overflow-x: auto; /* Permite rolagem horizontal em telas pequenas */
        margin: 0 auto; /* Centraliza a tabela horizontalmente */
    }

</style>

<?php
// index.php

include 'conexao.php'; // Inclui o arquivo de conexão



try {
    // Consulta SQL
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
        echo '<div class="table-container">';
        echo '<table border="1">'; // Começa a tabela
        echo '<thead>';
        echo '<tr>
                <th>IH</th>
                <th>REGISTRO</th>
                <th>PACIENTE</th>
                <th>CONVENIO</th>
                <th>UNIDADE</th>
                <th>LEITO</th>
                <th>PRESCRICAO</th>
                <th>DIETA</th>
                <th>HORAS</th>
              </tr>';
        echo '</thead>';
        echo '<tbody>';
        // Exibe os dados em linhas da tabela
        foreach ($result as $row) {
            echo '<tr>
                    <td>' . htmlspecialchars($row['IH']) . '</td>
                    <td>' . htmlspecialchars($row['REGISTRO']) . '</td>
                    <td>' . htmlspecialchars($row['PACIENTE']) . '</td>
                    <td>' . htmlspecialchars($row['CONVENIO']) . '</td>
                    <td>' . htmlspecialchars($row['UNIDADE']) . '</td>
                    <td>' . htmlspecialchars($row['LEITO']) . '</td>
                    <td>' . htmlspecialchars($row['PRESCRICAO']) . '</td>
                    <td>' . htmlspecialchars($row['DIETA']) . '</td>
                    <td>' . htmlspecialchars($row['horas']) . '</td>
                  </tr>';
        }
        echo '</tbody>';
        echo '</table>'; // Finaliza a tabela
        echo '</div>';
    } else {
        echo "Nenhum resultado encontrado."; // Mensagem se não houver resultados
    }
} catch (PDOException $e) {
    echo "Erro na consulta: " . $e->getMessage(); // Exibe erro na consulta
}
?>
