

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/teste1.css"> 
</head>
<body>
<?php
include 'conexao1.php'; // Certifique-se de que o caminho está correto
include 'header.php';
$con = new Conexao();

// Sua consulta SQL
$query = "
    SELECT 
        FLE.FLE_DTHR_CHEGADA AS 'FILA - CHEGADA',
        DATEDIFF(MINUTE, FLE.FLE_DTHR_CHEGADA, GETDATE()) AS TEMPO_ESPERA,
        FLE.FLE_DTHR_ATENDIMENTO AS 'FILA - ATD',
        FLE.FLE_COR AS 'FILA - CR',
        FLE.FLE_STATUS AS 'FILA - STATUS DE ATENDIMENTO CÓDIGO',
        CASE 
            WHEN FLE.FLE_STATUS = 'P' THEN 'Em Procedimento'
            WHEN FLE.FLE_STATUS = 'A' THEN 'Aguardando'
            WHEN FLE.FLE_STATUS = 'X' THEN 'Concluído'
            WHEN FLE.FLE_STATUS = 'E' THEN 'Em Atendimento'
        END AS 'FILA - STATUS DE ATENDIMENTO NOME',
        PSV.PSV_COD AS 'FILA CÓDIGO',
        PSV.PSV_NOME AS 'FILA NOME',
        PAC.PAC_REG AS 'PACIENTE REGISTRO',
        PAC.PAC_NOME AS 'PACIENTE NOME',
        RCL.RCL_COD AS 'CONSULTA CÓDIGO',
        RCL.RCL_DTHR AS 'CONSULTA DATA/HORA LANÇAMENTO',
        RCL.RCL_MED AS 'CONSULTA MÉDICO',
        CNV.CNV_COD AS 'CÓDIGO CONVENIO', 
        CNV.CNV_NOME AS 'NOME CONVÊNIO'
    FROM 
        FLE 
    INNER JOIN PSV ON FLE.FLE_PSV_COD = PSV.PSV_COD 
    INNER JOIN PAC ON PAC.PAC_REG = FLE.FLE_PAC_REG
    LEFT JOIN HSP ON FLE.FLE_DTHR_CHEGADA BETWEEN HSP.HSP_DTHRE AND DATEADD(HOUR,4,HSP.HSP_DTHRE) 
        AND FLE.FLE_PAC_REG = HSP.HSP_PAC
    LEFT JOIN RCL ON RCL.RCL_HSP = HSP.HSP_NUM 
        AND RCL.RCL_PAC = PAC.PAC_REG 
        AND RCL.RCL_COD = '00010022' 
        AND RCL.RCL_STAT <> 'C' 
        AND RCL.RCL_TXT LIKE '@%'
    LEFT JOIN CNV ON CNV.CNV_COD = HSP.HSP_CNV 
    WHERE 
        FLE.FLE_DTHR_CHEGADA BETWEEN GETDATE() - 1 AND GETDATE() 
        AND FLE.FLE_PAC_REG <> 0 
        AND FLE.FLE_PSV_COD IN (900250,900197,900290,900289)
        AND FLE.FLE_STATUS = 'A' -- Adicionado para filtrar apenas os aguardando
    ORDER BY 
        FLE.FLE_PAC_REG,
        FLE.FLE_DTHR_CHEGADA
";

$resultado = $con->query($query);
?>



<div class="container">
    <table>
        <tr>
            <th>FILA - CHEGADA</th>
            <th>TEMPO ESPERA</th>
            <th>FILA - ATD</th>
            <th>FILA - CR</th>
            <th>FILA - STATUS DE ATENDIMENTO CÓDIGO</th>
            <th>FILA - STATUS DE ATENDIMENTO NOME</th>
            <th>FILA CÓDIGO</th>
            <th>FILA NOME</th>
            <th>PACIENTE REGISTRO</th>
            <th>PACIENTE NOME</th>
            <th>CONSULTA CÓDIGO</th>
            <th>CONSULTA DATA/HORA LANÇAMENTO</th>
            <th>CONSULTA MÉDICO</th>
            <th>CÓDIGO CONVENIO</th>
            <th>NOME CONVÊNIO</th>
        </tr>
        <?php
        if (count($resultado) > 0) {
            foreach ($resultado as $row) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['FILA - CHEGADA'] instanceof DateTime ? $row['FILA - CHEGADA']->format('Y-m-d H:i:s') : $row['FILA - CHEGADA']) . "</td>
                        <td>" . htmlspecialchars($row['TEMPO_ESPERA']) . "</td>
                        <td>" . htmlspecialchars($row['FILA - ATD'] instanceof DateTime ? $row['FILA - ATD']->format('Y-m-d H:i:s') : $row['FILA - ATD']) . "</td>
                        <td>" . htmlspecialchars($row['FILA - CR']) . "</td>
                        <td class='sit-column'>" . htmlspecialchars($row['FILA - STATUS DE ATENDIMENTO CÓDIGO']) . "</td>
                        <td>" . htmlspecialchars($row['FILA - STATUS DE ATENDIMENTO NOME']) . "</td>
                        <td>" . htmlspecialchars($row['FILA CÓDIGO']) . "</td>
                        <td>" . htmlspecialchars($row['FILA NOME']) . "</td>
                        <td>" . htmlspecialchars($row['PACIENTE REGISTRO']) . "</td>
                        <td>" . htmlspecialchars($row['PACIENTE NOME']) . "</td>
                        <td>" . htmlspecialchars($row['CONSULTA CÓDIGO']) . "</td>
                        <td>" . htmlspecialchars($row['CONSULTA DATA/HORA LANÇAMENTO'] instanceof DateTime ? $row['CONSULTA DATA/HORA LANÇAMENTO']->format('Y-m-d H:i:s') : $row['CONSULTA DATA/HORA LANÇAMENTO']) . "</td>
                        <td>" . htmlspecialchars($row['CONSULTA MÉDICO']) . "</td>
                        <td>" . htmlspecialchars($row['CÓDIGO CONVENIO']) . "</td>
                        <td>" . htmlspecialchars($row['NOME CONVÊNIO']) . "</td>
                    </tr>";
            }
        }
        ?>
    </table>
</div>

    
</body>
</html>
