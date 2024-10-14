<?php 
include 'conexao.php'; 
include 'header.php';

function capitalizeFirstLetters($string) {
    return ucwords(strtolower($string));
}

try {
    // Inicializa contadores para admissões e altas
    $counts = [
        '6_hours' => ['admitidos' => 0, 'alta' => 0],
        '12_hours' => ['admitidos' => 0, 'alta' => 0],
        '24_hours' => ['admitidos' => 0, 'alta' => 0],
    ];

    // Contagem total de admitidos e em alta
    $totalQuery = "
        SELECT 
            COUNT(CASE WHEN HSP_STAT = 'A' THEN 1 END) AS admitidos,
            COUNT(CASE WHEN HSP_STAT = 'E' THEN 1 END) AS alta
        FROM HSP
    ";
    $totalResult = $connection->query($totalQuery)->fetch(PDO::FETCH_ASSOC);
    
    // Filtra as últimas 6, 12 e 24 horas
    foreach ([6, 12, 24] as $hoursFilter) {
        // Contagem de pacientes
        $query = "
            SELECT 
                COUNT(*) AS total,
                HSP_STAT AS status
            FROM HSP
            WHERE HSP_DTHRE >= DATEADD(HOUR, -$hoursFilter, GETDATE())
            GROUP BY HSP_STAT
        ";
        $result = $connection->query($query)->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($result as $row) {
            if ($row['status'] === 'A') {
                $counts["{$hoursFilter}_hours"]['admitidos'] = $row['total'];
            } elseif ($row['status'] === 'E') {
                $counts["{$hoursFilter}_hours"]['alta'] = $row['total'];
            }
        }
    }

    // Dados para o gráfico
    $labels = ['6 Horas', '12 Horas', '24 Horas'];
    $dataAdmitidos = [$counts['6_hours']['admitidos'], $counts['12_hours']['admitidos'], $counts['24_hours']['admitidos']];
    $dataAlta = [$counts['6_hours']['alta'], $counts['12_hours']['alta'], $counts['24_hours']['alta']];

    // Dados para o gráfico de pizza
    $totalAdmitidos = $totalResult['admitidos'] ?? 0;
    $totalAlta = $totalResult['alta'] ?? 0;
    $totalLabels = ['Admitidos', 'Altas'];
    $totalData = [$totalAdmitidos, $totalAlta];

} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráficos de Pacientes Atendidos</title>
    <link rel="stylesheet" href="paciente.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            text-align: center; /* Centraliza o texto no corpo */
        }

        .chart-container {
            display: flex; /* Ativa o flexbox */
            justify-content: space-around; /* Espalha os gráficos uniformemente */
            margin: 20px auto; /* Centraliza a div */
            max-width: 900px; /* Define uma largura máxima para a div */
        }

        .chart-wrapper {
            flex: 1; /* Faz com que os gráficos se ajustem proporcionalmente */
            margin: 0 10px; /* Espaçamento horizontal entre os gráficos */
        }

        canvas {
            max-width: 400px; 
            max-height: 400px; 
        }

        h1 {
            margin-bottom: 20px; /* Espaçamento abaixo do título */
        }
    </style>
</head>
<body>

    <h1>Gráficos de Pacientes Atendidos</h1>
    
    <!-- Contêiner para os gráficos -->
    <div class="chart-container">
        <!-- Gráfico de Pizza (Total Admitidos e Altas) -->
        <div class="chart-wrapper">
            <canvas id="myChart" width="200" height="200"></canvas>
        </div>
        
        <!-- Gráfico de Admissões e Altas nas Últimas 6, 12 e 24 Horas -->
        <div class="chart-wrapper">
            <canvas id="myChart2" width="200" height="200"></canvas>
        </div>
    </div>

    <script>
        // Código para o gráfico de pizza
        const ctx = document.getElementById('myChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'pie', 
            data: {
                labels: <?= json_encode($totalLabels) ?>,
                datasets: [{
                    label: 'Número de Pacientes',
                    data: <?= json_encode($totalData) ?>,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.5)', // Mudança de cor
                        'rgba(255, 159, 64, 0.5)' // Mudança de cor
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)', // Mudança de cor
                        'rgba(255, 159, 64, 1)' // Mudança de cor
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true, 
                plugins: {
                    legend: {
                        position: 'top', 
                    }
                }
            }
        });
    
        // Código para o gráfico de barras
        const ctx2 = document.getElementById('myChart2').getContext('2d');
        const myChart2 = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [
                    {
                        label: 'Admitidos',
                        data: <?= json_encode($dataAdmitidos) ?>,
                        backgroundColor: 'rgba(75, 192, 192, 0.5)', // Cor do gráfico de admitidos
                    },
                    {
                        label: 'Altas',
                        data: <?= json_encode($dataAlta) ?>,
                        backgroundColor: 'rgba(255, 99, 132, 0.5)', // Cor do gráfico de altas
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

</body>
</html>
