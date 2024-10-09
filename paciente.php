<?php 
include 'conexao.php'; 
include 'header.php';

function capitalizeFirstLetters($string) {
    return ucwords(strtolower($string));
}

try {
    $hoursFilter = 12; 

    if (isset($_POST['filter'])) {
        switch ($_POST['filter']) {
            case 'last6hours':
                $hoursFilter = 6;
                break;
            case 'last12hours':
                $hoursFilter = 12;
                break;
            case 'last24hours':
                $hoursFilter = 24;
                break;
            default:
                $hoursFilter = 24; 
                break;
        }
    }

    $query = "
        SELECT 
        'ADMISSAO' AS TIPO,
        HSP.HSP_NUM AS 'IH',
        HSP.HSP_DTHRE AS 'DATA_EVENTO',
        HSP.HSP_PAC AS 'REGISTRO',
        PAC.PAC_NOME AS 'PACIENTE',
        CASE
            WHEN DATEDIFF(YEAR, PAC.PAC_NASC, GETDATE()) < 1 
                THEN CAST(DATEDIFF(DAY, PAC.PAC_NASC, GETDATE()) AS VARCHAR(50)) + ' Dia(s).'
            WHEN DATEDIFF(YEAR, PAC.PAC_NASC, GETDATE()) >= 1 
                THEN CAST(DATEDIFF(YEAR, PAC.PAC_NASC, GETDATE()) AS VARCHAR(50)) + ' Ano(s).'
        END AS 'IDADE',
        CNV.CNV_NOME AS 'CONVENIO',
        RTRIM(STR.STR_NOME) AS 'UNIDADE',
        LOC.LOC_NOME AS 'LEITO',
        ISNULL(PSC.PSC_DHINI, '') AS 'PRESCRICAO',
        ISNULL(ADP.ADP_NOME, '') AS 'DIETA',
        DATEDIFF(HOUR, HSP.HSP_DTHRE, GETDATE()) AS 'HORAS',
        NULL AS 'DATA_ALTA' -- Coluna para data de alta
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
    ";

    if ($hoursFilter > 0) {
        $query .= " AND HSP.HSP_DTHRE >= DATEADD(HOUR, -$hoursFilter, GETDATE())"; 
    }
    $query .= " 
        UNION ALL

        SELECT 
            'ALTA' AS TIPO,
            HSP.HSP_NUM AS 'IH',
            HSP.HSP_DTHRA AS 'DATA_EVENTO',
            HSP.HSP_PAC AS 'REGISTRO',
            PAC.PAC_NOME AS 'PACIENTE',
            CASE
                WHEN DATEDIFF(YEAR, PAC.PAC_NASC, GETDATE()) < 1 
                    THEN CAST(DATEDIFF(DAY, PAC.PAC_NASC, GETDATE()) AS VARCHAR(50)) + ' Dia(s).'
                WHEN DATEDIFF(YEAR, PAC.PAC_NASC, GETDATE()) >= 1 
                    THEN CAST(DATEDIFF(YEAR, PAC.PAC_NASC, GETDATE()) AS VARCHAR(50)) + ' Ano(s).'
            END AS 'IDADE',
            CNV.CNV_NOME AS 'CONVENIO',
            RTRIM(STR.STR_NOME) AS 'UNIDADE',
            LOC.LOC_NOME AS 'LEITO',
            ISNULL(PSC.PSC_DHINI, '') AS 'PRESCRICAO',
            ISNULL(ADP.ADP_NOME, '') AS 'DIETA',
            DATEDIFF(HOUR, HSP.HSP_DTHRE, GETDATE()) AS 'HORAS',
            HSP.HSP_DTHRA AS 'DATA_ALTA' -- Data de alta aqui
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
            AND HSP_STAT = 'E'
    ";

    if ($hoursFilter > 0) {
        $query .= " AND HSP.HSP_DTHRA >= DATEADD(HOUR, -$hoursFilter, GETDATE());"; 
    } else {
        $query .= ";"; 
    }

    $result = $connection->query($query)->fetchAll(PDO::FETCH_ASSOC);

    // Variáveis para contagem
    $countAdmissao = 0;
    $countAlta = 0;

    if (count($result) > 0) {
        $groupedPatients = [];
        $previousStates = []; 
        
        foreach ($result as $row) {
            $patientName = capitalizeFirstLetters($row['PACIENTE']);
            $convenio = capitalizeFirstLetters($row['CONVENIO']);
            $leito = capitalizeFirstLetters($row['LEITO']);
            $prescricao = !empty($row['PRESCRICAO']) ? date('d/m/Y', strtotime($row['PRESCRICAO'])) : '';
            $admissao = date('d/m/Y H:i', strtotime($row['DATA_EVENTO'])); 
            $idade = $row['IDADE'];
            $tipo = $row['TIPO']; 
            $dataAlta = $tipo === 'ALTA' ? 'ALTA' : 'INTERNADO'; // Mostrar "ALTA" em vez da data de alta

            // Contagem
            if ($tipo === 'ADMISSAO') {
                $countAdmissao++;
            } elseif ($tipo === 'ALTA') {
                $countAlta++;
            }

            if (!isset($groupedPatients[$patientName])) {
                $groupedPatients[$patientName] = [
                    'REGISTRO' => $row['REGISTRO'],
                    'PACIENTE' => $patientName,
                    'CONVENIO' => $convenio,
                    'LEITO' => $leito,
                    'PRESCRICAO' => $prescricao,
                    'DIETAS' => [], 
                    'ADMISSÃO' => $admissao, 
                    'IDADE' => $idade,
                    'TIPO' => $tipo,
                    'DATA_ALTA' => $dataAlta 
                ];
            } else {
                if ($previousStates[$row['REGISTRO']] === 'ADMISSAO' && $tipo === 'ALTA') {
                    echo "<script>showNotification('$patientName');</script>"; 
                }
            }
            $previousStates[$row['REGISTRO']] = $tipo;

            if (!empty($row['DIETA'])) {
                $dietName = capitalizeFirstLetters($row['DIETA']);
                if (!in_array($dietName, $groupedPatients[$patientName]['DIETAS'])) {
                    $groupedPatients[$patientName]['DIETAS'][] = $dietName;
                }
            }
        }
        $groupedPatients = array_values($groupedPatients);
    }

    // Dados para o gráfico
    $dataForChart = [
        'labels' => ['Admissão', 'Alta'],
        'data' => [$countAdmissao, $countAlta]
    ];

} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>



<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pacientes Atendidos</title>
    <link rel="stylesheet" href="paciente.css"> 
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <?php if (isset($errorMessage)): ?>
        <p><?php echo $errorMessage; ?></p>
    <?php else: ?>
        <h1>Pacientes Atendidos</h1>
        <?php if (count($result) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Registro</th>
                        <th>Paciente</th>
                        <th>Idade</th>
                        <th>Data de Admissão</th>
                        <th>Situação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($groupedPatients as $patient): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($patient['REGISTRO']); ?></td>
                            <td><?php echo htmlspecialchars($patient['PACIENTE']); ?></td>
                            <td><?php echo htmlspecialchars($patient['IDADE']); ?></td>
                            <td><?php echo htmlspecialchars($patient['ADMISSÃO']); ?></td>
                            <td><?php echo htmlspecialchars($patient['DATA_ALTA']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        
        <?php else: ?>
            <p>Nenhum paciente encontrado.</p>
        <?php endif; ?>
    <?php endif; ?>

</body>
</html>
