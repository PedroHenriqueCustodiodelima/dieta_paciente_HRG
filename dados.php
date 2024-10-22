<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabela de Pacientes com Paginação Dinâmica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/teste1.css">
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
        
            // Contagem de pacientes
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

        // Preparar os dados para o gráfico
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

<a href="inicio.php" class="custom-link">
    <i class="fa-solid fa-circle-left" style="font-size: 20px; margin-right: 8px;"></i>
    <span>Voltar</span>
</a>
<div class="container">
    <h1 class="text-center my-4">Dados de Pacientes</h1>

    <form method="POST" action="" class="mb-4">
        <div class="input-group">
            <label class="input-group-text" for="filter">Filtrar por horas:</label>
            <select name="filter" id="filter" class="form-select">
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











<div class="container">
    <div class="canvas-container">
        <canvas id="barChart"></canvas>
        <canvas id="lineChart"></canvas>
    </div>
</div>

<style>
    .canvas-container {
        width: 100%;
        height: 300px; 
        display: flex; 
        justify-content: space-around; 
    }
    
    canvas {
        width: 100% !important;
        height: auto !important; 
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Dados dos leitos
        const labels = <?php echo json_encode($leitos); ?>; // Nomes dos leitos
        const data = {
            labels: labels,
            datasets: [
                {
                    label: 'Quantidade de Pacientes por Leito',
                    data: <?php echo json_encode($quantidadePacientes); ?>, // Quantidade de pacientes por leito
                    backgroundColor: 'rgba(54, 162, 235, 0.2)', // Azul claro
                    borderColor: 'rgba(54, 162, 235, 1)', // Azul escuro
                    borderWidth: 2,
                }
            ]
        };

        // Configuração do gráfico de barras
        const configBar = {
            type: 'bar',
            data: data,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        };

        // Criar o gráfico de barras
        const barChart = new Chart(
            document.getElementById('barChart'),
            configBar
        );

        // Configuração do gráfico de linha (opcional, mas pode ser mantido)
        const configLine = {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Distribuição de Pacientes por Leito'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        };

        // Criar o gráfico de linha
        const lineChart = new Chart(
            document.getElementById('lineChart'),
            configLine
        );
    });
</script>




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="js/dados.js"></script>
</body>
</html>
