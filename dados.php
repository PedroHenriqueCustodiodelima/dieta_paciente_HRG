<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DIETA PACIENTES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dados.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<?php 
include 'conexao.php'; 
include 'header.php';


function capitalizeFirstLetters($string) {
    return ucwords(strtolower($string));
}

$totalPacientes = 0;
$pacientesAlta = 0;
$pacientesAdmissao = 0;

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
        PSC.PSC_OBS AS 'OBS',
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
            RTRIM(STR.STR_NOME) AS 'UNIDADE',
            LOC.LOC_NOME AS 'LEITO',
            ISNULL(PSC.PSC_DHINI, '') AS 'PRESCRICAO',
            ISNULL(ADP.ADP_NOME, '') AS 'DIETA',
            PSC.PSC_OBS AS 'OBS',
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
            $unidade = capitalizeFirstLetters($row['UNIDADE']);
            $prescricao = !empty($row['PRESCRICAO']) ? date('d/m/Y', strtotime($row['PRESCRICAO'])) : '';
            $admissao = date('d/m/Y H:i', strtotime($row['DATA_EVENTO']));
            $idade = $row['IDADE'];
            $tipo = $row['TIPO'];
            $registro = $row['REGISTRO']; 
            if (!isset($groupedPatients[$registro])) {
                $totalPacientes++;
                
                if ($tipo === 'ALTA') {
                    $pacientesAlta++;
                } else if ($tipo === 'ADMISSAO') {
                    $pacientesAdmissao++;
                }
                
                $groupedPatients[$registro] = [
                    'REGISTRO' => $registro,
                    'PACIENTE' => $patientName,
                    'CONVENIO' => $convenio,
                    'UNIDADE' => $unidade,
                    'LEITO' => $leito,
                    'PRESCRICAO' => $prescricao,
                    'DIETAS' => [],
                    'OBS' => [],
                    'ADMISSÃO' => $admissao,
                    'IDADE' => $idade,
                    'TIPO' => $tipo
                ];
            } else {
                if ($previousStates[$registro] === 'ADMISSAO' && $tipo === 'ALTA') {
                    echo "<script>showNotification('$patientName');</script>";
                }
            }
            $previousStates[$registro] = $tipo;

            if (!empty($row['DIETA'])) {
                $dietName = capitalizeFirstLetters($row['DIETA']);
                if (!in_array($dietName, $groupedPatients[$registro]['DIETAS'])) {
                    $groupedPatients[$registro]['DIETAS'][] = $dietName;
                }
            }

            if (!empty($row['OBS'])) {
                $obsText = capitalizeFirstLetters($row['OBS']);
                if (!in_array($obsText, $groupedPatients[$registro]['OBS'])) {
                    $groupedPatients[$registro]['OBS'][] = $obsText;
                }
            }
        }
        $groupedPatients = array_values($groupedPatients);
    }
        $queryLeitos = "
        SELECT 
            LOC.LOC_NOME AS 'LEITO',
            COUNT(HSP.HSP_NUM) AS 'QUANTIDADE_PACIENTES'
        FROM
            HSP
        INNER JOIN LOC ON HSP_LOC = LOC_COD
        WHERE
            HSP_TRAT_INT = 'I'
            AND HSP_STAT = 'A'
        GROUP BY
            LOC.LOC_NOME
        ORDER BY
            LOC.LOC_NOME;
        ";

        $resultLeitos = $connection->query($queryLeitos)->fetchAll(PDO::FETCH_ASSOC);
        $leitos = [];
        $quantidadePacientes = [];

        if (count($resultLeitos) > 0) {
        foreach ($resultLeitos as $row) {
            $leitos[] = $row['LEITO'];
            $quantidadePacientes[] = $row['QUANTIDADE_PACIENTES'];
        }
        }
        
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>

<a href="index.php" class="custom-link">
    <i class="fa-solid fa-circle-left" style="font-size: 20px; margin-right: 8px;"></i>
    <span>Voltar</span>
</a>


<div class="container">
    <h1 class="text-center my-4">Pacientes</h1>

    <form method="POST" action="" class="mb-4">
        <div class="input-group">
            <label class="input-group-text" for="filter">Filtrar por horas:</label>
            <select name="filter" id="filter" class="form-select">
                <option value="">Selecione um horário</option>
                <option value="last24hours" <?php echo (isset($_POST['filter']) && $_POST['filter'] == 'last24hours') ? 'selected' : ''; ?>>Últimas 24 horas</option>
                <option value="last12hours" <?php echo (isset($_POST['filter']) && $_POST['filter'] == 'last12hours') ? 'selected' : ''; ?>>Últimas 12 horas</option>
                <option value="last6hours" <?php echo (isset($_POST['filter']) && $_POST['filter'] == 'last6hours') ? 'selected' : ''; ?>>Últimas 6 horas</option>
            </select>
            <button class="btn btn-primary" type="submit">Filtrar</button>
        </div>
    </form>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4 shadow card-total">
                <div class="card-body">
                    <h5 class="card-title">Total de Pacientes</h5>
                    <i class="fa-solid fa-person icon-background"></i>
                    <p class="card-text"><?php echo $totalPacientes; ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4 shadow card-alta">
                <div class="card-body">
                    <h5 class="card-title">Pacientes em Alta</h5>
                    <i class="fa-solid fa-house-medical icon-background"></i>
                    <p class="card-text"><?php echo $pacientesAlta; ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4 shadow card-admissao">
                <div class="card-body">
                    <h5 class="card-title">Pacientes em Admissão</h5>
                    <i class="fa-solid fa-bed-pulse icon-background"></i>
                    <p class="card-text"><?php echo $pacientesAdmissao; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>



<?php

$leitoCounts = [];
foreach ($groupedPatients as $patient) {
    $leito = $patient['LEITO'];

    if (!isset($leitoCounts[$leito])) {
        $leitoCounts[$leito] = 0;
    }
    $leitoCounts[$leito]++;
}

$convênioCounts = [];
foreach ($groupedPatients as $patient) {
    $convenio = $patient['CONVENIO'];

    if (!isset($convênioCounts[$convenio])) {
        $convênioCounts[$convenio] = 0;
    }
    $convênioCounts[$convenio]++;
}
$unidadeCounts = [];
foreach ($groupedPatients as $patient) {
    $unidade = $patient['UNIDADE'];

    if (!isset($unidadeCounts[$unidade])) {
        $unidadeCounts[$unidade] = 0;
    }
    $unidadeCounts[$unidade]++;
}
$prescricaoCounts = [];
foreach ($groupedPatients as $patient) {
    $prescricao = $patient['PRESCRICAO'];

    if (!isset($prescricaoCounts[$prescricao])) {
        $prescricaoCounts[$prescricao] = 0;
    }
    $prescricaoCounts[$prescricao]++;
}

ksort($leitoCounts);
ksort($convênioCounts);
ksort($unidadeCounts);
ksort($prescricaoCounts);
?>

<?php if (!empty($leitoCounts) || !empty($convênioCounts) || !empty($unidadeCounts) || !empty($prescricaoCounts)): ?>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h4>Gráficos de Pacientes</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-4"> 
                        <canvas id="barChart" style="width: 100%; height: 300px;"></canvas>
                    </div>
                    <div class="col-md-6 mb-4">
                        <canvas id="lineChart" style="width: 100%; height: 300px;"></canvas>
                    </div>
                    <div class="col-md-6 mb-4">
                        <canvas id="unitBarChart" style="width: 100%; height: 300px;"></canvas>
                    </div>
                    <div class="col-md-6 mb-4">
                        <canvas id="prescriptionChart" style="width: 100%; height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const leitos = <?php echo json_encode(array_keys($leitoCounts)); ?>;
        const countsByLeito = <?php echo json_encode(array_values($leitoCounts)); ?>;

        const convenios = <?php echo json_encode(array_keys($convênioCounts)); ?>;
        const countsByConvenio = <?php echo json_encode(array_values($convênioCounts)); ?>;

        const unidades = <?php echo json_encode(array_keys($unidadeCounts)); ?>;
        const countsByUnidade = <?php echo json_encode(array_values($unidadeCounts)); ?>;

        const prescricoes = <?php echo json_encode(array_keys($prescricaoCounts)); ?>;
        const countsByPrescricao = <?php echo json_encode(array_values($prescricaoCounts)); ?>;

        const barCtx = document.getElementById('barChart').getContext('2d');
        const lineCtx = document.getElementById('lineChart').getContext('2d');
        const unitBarCtx = document.getElementById('unitBarChart').getContext('2d');
        const prescriptionCtx = document.getElementById('prescriptionChart').getContext('2d');
        const colors = [
            'rgba(54, 162, 235, 0.6)',
            'rgba(255, 99, 132, 0.6)',
            'rgba(75, 192, 192, 0.6)',
            'rgba(255, 206, 86, 0.6)',
            'rgba(153, 102, 255, 0.6)',
            'rgba(255, 159, 64, 0.6)',
            'rgba(201, 203, 207, 0.6)'
        ];
        const barChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: leitos,
                datasets: [{
                    label: 'Quantidade de Pacientes por Leito',
                    data: countsByLeito,
                    backgroundColor: countsByLeito.map((_, index) => colors[index % colors.length]),
                    borderColor: countsByLeito.map((_, index) => colors[index % colors.length].replace('0.6', '1')),
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        const lineChart = new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: convenios,
                datasets: [{
                    label: 'Quantidade de Pacientes por Convênio',
                    data: countsByConvenio,
                    fill: false,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        const unitBarChart = new Chart(unitBarCtx, {
            type: 'bar',
            data: {
                labels: unidades,
                datasets: [{
                    label: 'Quantidade de Pacientes por Unidade',
                    data: countsByUnidade,
                    backgroundColor: countsByUnidade.map((_, index) => colors[index % colors.length]),
                    borderColor: countsByUnidade.map((_, index) => colors[index % colors.length].replace('0.6', '1')),
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        const prescriptionChart = new Chart(prescriptionCtx, {
            type: 'line', 
            data: {
                labels: prescricoes,
                datasets: [{
                    label: 'Quantidade de Pacientes por Prescrição',
                    data: countsByPrescricao,
                    fill: false, 
                    borderColor: 'rgba(75, 192, 192, 1)', 
                    tension: 0.1 
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, 
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
    </script>
<?php else: ?>
    <div class="container mt-4">
        <h2>Não há dados suficientes para exibir gráficos.</h2>
    </div>
<?php endif; ?>



<script>
    setInterval(updateCurrentTime, 1000);
    updateCurrentTime();
    setInterval(() => {
        location.reload();
    }, 300000); 
</script>






<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="js/dados.js"></script>
</body>
</html>
