<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabela de Pacientes com Paginação Dinâmica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="teste.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400&display=swap" rel="stylesheet">
</head>
<body>


<?php 
include 'conexao.php'; 
include 'header.php';

function capitalizeFirstLetters($string) {
    return ucwords(strtolower($string));
}

try {
    $hoursFilter = 24; 
    if (isset($_POST['filterLast6Hours'])) {
        $hoursFilter = 6;
    } elseif (isset($_POST['filterLast12Hours'])) {
        $hoursFilter = 12;
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
        DATEDIFF(HOUR, HSP.HSP_DTHRE, GETDATE()) AS 'HORAS'
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
    ";

    if ($hoursFilter > 0) {
        $query .= " AND HSP.HSP_DTHRE >= DATEADD(HOUR, -$hoursFilter, GETDATE())"; // Filtro com base nas horas selecionadas
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
            DATEDIFF(HOUR, HSP.HSP_DTHRE, GETDATE()) AS 'HORAS'
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

    if (count($result) > 0) {
        $groupedPatients = [];
        
        foreach ($result as $row) {
            $patientName = capitalizeFirstLetters($row['PACIENTE']);
            $convenio = capitalizeFirstLetters($row['CONVENIO']);
            $leito = capitalizeFirstLetters($row['LEITO']);
            $prescricao = !empty($row['PRESCRICAO']) ? date('d/m/Y', strtotime($row['PRESCRICAO'])) : '';
            $admissao = date('d/m/Y H:i', strtotime($row['DATA_EVENTO'])); 
            $idade = $row['IDADE'];
            $tipo = $row['TIPO']; 

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
                    'TIPO' => $tipo 
                ];
            }
            if (!empty($row['DIETA'])) {
                $dietName = capitalizeFirstLetters($row['DIETA']);
                if (!in_array($dietName, $groupedPatients[$patientName]['DIETAS'])) {
                    $groupedPatients[$patientName]['DIETAS'][] = $dietName;
                }
            }
        }
        $groupedPatients = array_values($groupedPatients);
    }

} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>
<div class="container-fluid mt-5">
    <div class="row justify-content-center">
        <div class="col-12">

        <form method="POST" action="" class="text-end">
            <button type="submit" class="btn btn-primary mb-3 btn-sm" id="filterLast6Hours" name="filterLast6Hours">Filtrar Últimas 6 Horas</button>
            <button type="submit" class="btn btn-secondary mb-3 btn-sm" id="filterLast12Hours" name="filterLast12Hours">Filtrar Últimas 12 Horas</button>
            <button type="submit" class="btn btn-success mb-3 btn-sm" id="filterLast24Hours" name="filterLast24Hours">Filtrar Últimas 24 Horas</button> <!-- Botão para filtrar as últimas 24 horas -->
        </form>

        <div class="d-flex justify-content-around mb-3">
            <div>
                <strong>Total de Pacientes:</strong> <?= count($groupedPatients); ?>
            </div>
            <div style="color: #001f3f;">
                <strong>Total de Admissões:</strong> <?= count(array_filter($groupedPatients, fn($p) => $p['TIPO'] === 'ADMISSAO')); ?>
            </div>
            <div style="color: green;">
                <strong>Total de Altas:</strong> <?= count(array_filter($groupedPatients, fn($p) => $p['TIPO'] === 'ALTA')); ?>
            </div>
        </div>


        <div class="mb-3">
            <input type="text" id="filterInput" class="form-control" placeholder="Filtrar por paciente..." onkeyup="filterTable()">
        </div>
        <div id="progress-container" style="width: 100%; background-color: #f3f3f3; border-radius: 5px; overflow: hidden;">
            <div id="progress-bar" style="width: 0%; height: 5px; background-color: #001f3f"></div>
        </div>
        <table class="table table-striped table-bordered table-hover">
            <thead style="background-color: green; color:white;">
                <tr>
                    <th>Registro</th>
                    <th id="paciente-header" style="cursor: pointer;">Paciente <i id="sort-paciente-icon" class="fa-solid fa-caret-up"></i></th>
                    <th id="convenio-header" style="cursor: pointer;">Convênio <i id="sort-convenio-icon" class="fa-solid fa-caret-up"></i></th>
                    <th>Leito</th>
                    <th id="prescricao-header" style="min-width: 150px;">Prescrição <i id="sort-icon" class="fa-solid fa-caret-up"></i></th>
                    <th>Dieta</th>
                    <th id="admissao-header" style="min-width: 150px;">Data <i id="sort-admissao-icon" class="fa-solid fa-caret-up"></i></th>
                    <th id="idade-header" style="cursor: pointer; min-width: 150px;">Idade <i id="sort-idade-icon" class="fa-solid fa-caret-up"></i></th>
                    <th id="tipo-header" style="min-width: 100px;">Tipo</th>
                    <th>Acompanhante</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <?php foreach ($groupedPatients as $patient) { ?>
                <tr class="trdados">
                    <td class="text-start align-middle col-1"><?= htmlspecialchars($patient['REGISTRO']); ?></td>
                    <td class="text-start align-middle col-2"><?= htmlspecialchars($patient['PACIENTE']); ?></td>
                    <td class="text-start align-middle col-1"><?= htmlspecialchars($patient['CONVENIO']); ?></td>
                    <td class="text-start align-middle col-2"><?= htmlspecialchars($patient['LEITO']); ?></td>
                    <td class="text-center align-middle col-1"><?= htmlspecialchars($patient['PRESCRICAO']); ?></td>
                    <td class="text-start align-middle col-2"><?= htmlspecialchars(implode(', ', $patient['DIETAS'])); ?></td>
                    <td class="text-start align-middle col-1"><?= htmlspecialchars($patient['ADMISSÃO'] ?? ''); ?></td>
                    <td class="text-center align-middle "><?= htmlspecialchars($patient['IDADE']); ?></td>
                    <td class="text-start align-middle col-1" style="<?= ($patient['TIPO'] === 'ADMISSAO') ? 'background-color: #001f3f; color: white;' : (($patient['TIPO'] === 'ALTA') ? 'background-color: green; color: white;' : ''); ?>">
                        <?= htmlspecialchars($patient['TIPO']); ?>
                    </td>
                    <td class="text-start align-middle col-1"><?= htmlspecialchars($patient['ACOMPANHANTE'] ?? ''); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>




            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
            
            <div class="pagination-container" id="pagination-container">
                <button class="btn btn-success" id="prev-set" disabled><i class="fas fa-chevron-left"></i></button>
                <div id="page-numbers" class="mx-2"></div>
                <button class="btn btn-success" id="next-set"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </div>
</div>
<script>
    setInterval(updateCurrentTime, 1000);
    updateCurrentTime();
    setInterval(() => {
        location.reload();
    }, 300000); 
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="teste.js"></script>
</body>
</html>