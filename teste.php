<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabela de Pacientes com Paginação Dinâmica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/teste.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

</head>
<body>


<?php 
include 'conexao.php'; 
include 'header.php';

function capitalizeFirstLetters($string) {
    return ucwords(strtolower($string));
}

try {
    $hoursFilter = 6; 

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
        RTRIM(STR.STR_NOME) AS 'UNIDADE', -- Aqui foi adicionado o campo UNIDADE
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
            RTRIM(STR.STR_NOME) AS 'UNIDADE', -- Aqui também foi adicionado o campo UNIDADE
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
        $query .= " AND HSP.HSP_DTHRA >= DATEADD(HOUR, -$hoursFilter, GETDATE())"; 
    }

    $query .= " ORDER BY DATA_EVENTO DESC;"; 

    $result = $connection->query($query)->fetchAll(PDO::FETCH_ASSOC);

    
    $groupedPatients = [];

    if (count($result) > 0) {
        $previousStates = []; 
        
        foreach ($result as $row) {
            $patientName = capitalizeFirstLetters($row['PACIENTE']);
            $convenio = capitalizeFirstLetters($row['CONVENIO']);
            $leito = capitalizeFirstLetters($row['LEITO']);
            $unidade = capitalizeFirstLetters($row['UNIDADE']); // Adicionando a unidade aqui
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
                    'UNIDADE' => $unidade, // Adicionando unidade aqui
                    'PRESCRICAO' => $prescricao,
                    'DIETAS' => [], 
                    'ADMISSÃO' => $admissao, 
                    'IDADE' => $idade,
                    'TIPO' => $tipo 
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

} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>


<div class="container-fluid mt-5">
    <div class="row justify-content-between">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center w-100" style="padding: 0;">
                <form method="POST" action="" style="margin: 0; display: inline-block;">
                    <div class="d-flex align-items-center">
                        <select name="filter" class="form-select mb-3" aria-label="Select Filter" style="width: 300px; height: 35px; margin-top: 20px;">
                            <option value="">Selecione um horário</option>
                            <option value="last6hours">Últimas 6 Horas</option>
                            <option value="last12hours">Últimas 12 Horas</option>
                            <option value="last24hours">Últimas 24 Horas</option>
                        </select>
                        <button type="submit" class="btn btn-primary mb-3 btn-sm ms-2" style="margin-top: 20px;">Aplicar Filtro</button>
                    </div>
                </form>
                
                <form method="POST" action="" class="text-start" style="margin: 0;">
                    <div class="button-style_total">
                        <strong>Total de Pacientes:</strong> <?= count($groupedPatients); ?>
                    </div>
                    <div class="button-style_admissao">
                        <strong>Total de Admissões:</strong> <?= count(array_filter($groupedPatients, fn($p) => $p['TIPO'] === 'ADMISSAO')); ?>
                    </div>
                    <div class="button-style_altas">
                        <strong>Total de Altas:</strong> <?= count(array_filter($groupedPatients, fn($p) => $p['TIPO'] === 'ALTA')); ?>
                    </div>
                </form>
            </div>
        <script>
            function showNotification(pacienteNome) {
                Toastify({
                    text: `Paciente ${pacienteNome} mudou de estado: de ADMISSÃO para ALTA.`,
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: 'right',
                    backgroundColor: "#4CAF50",
                }).showToast();
            }
        </script>
        <div class="mb-3">
            <input type="text" id="filterInput" class="form-control" placeholder="Filtrar por paciente..." onkeyup="filterTable()">
        </div>

        <div id="progress-container" style="width: 100%; background-color: #f3f3f3; border-radius: 5px; overflow: hidden;">
            <div id="progress-bar" style="width: 0%; height: 5px; background-color: #001f3f"></div>
        </div>
        <div>
            <button id="control-button" class="btn btn-success">
                <i class="fa-solid fa-play"></i>
            </button>
        </div>




        <table class="table table-striped table-bordered table-hover">
            <thead style="background-color: green; color:white;">
                <tr>
                    <th>Registro</th>
                    <th id="paciente-header" style="cursor: pointer;">Paciente <i id="sort-paciente-icon" class="fa-solid fa-caret-up"></i></th>
                    <th id="convenio-header" style="cursor: pointer;">Convênio <i id="sort-convenio-icon" class="fa-solid fa-caret-up"></i></th>
                    <th>Leito e Unidade</th> <!-- Coluna combinada -->
                    <th id="prescricao-header" style="min-width: 150px;">Prescrição <i id="sort-icon" class="fa-solid fa-caret-up"></i></th>
                    <th>Dieta</th>
                    <th id="admissao-header" style="min-width: 150px;">Data <i id="sort-admissao-icon" class="fa-solid fa-caret-up"></i></th>
                    <th id="idade-header" style="cursor: pointer; min-width: 150px;">Idade <i id="sort-idade-icon" class="fa-solid fa-caret-up"></i></th>
                    <th id="tipo-header" style="min-width: 100px;">Alta</th>
                    <th>Acompanhante</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <?php foreach ($groupedPatients as $patient) { ?>
                <tr class="trdados">
                    <td class="text-start align-middle col-1"><?= htmlspecialchars($patient['REGISTRO']); ?></td>
                    <td class="text-start align-middle col-2"><?= htmlspecialchars($patient['PACIENTE']); ?></td>
                    <td class="text-start align-middle col-1"><?= htmlspecialchars($patient['CONVENIO']); ?></td>
                    <td class="text-start align-middle col-2"><?= htmlspecialchars($patient['LEITO'] . ', ' . $patient['UNIDADE']); ?></td> <!-- Leito e Unidade juntos -->
                    <td class="text-center align-middle col-1"><?= htmlspecialchars($patient['PRESCRICAO']); ?></td>
                    <td class="text-start align-middle col-2"><?= htmlspecialchars(implode(', ', $patient['DIETAS'])); ?></td>
                    <td class="text-start align-middle col-1"><?= htmlspecialchars($patient['ADMISSÃO'] ?? ''); ?></td>
                    <td class="text-center align-middle"><?= htmlspecialchars($patient['IDADE']); ?></td>
                    <td class="text-start align-middle col-1" style="<?= ($patient['TIPO'] === 'ADMISSAO') ? 'background-color: #234F88; color: white;' : (($patient['TIPO'] === 'ALTA') ? 'background-color: #23884D; color: white;' : ''); ?>">
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
    }, 600000); 
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/teste.js"></script>
</body>
</html>