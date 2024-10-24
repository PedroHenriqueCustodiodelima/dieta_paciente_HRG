<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DIETA PACIENTES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/teste1.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<?php 
include 'conexao.php'; 
include 'header.php';



?>

<a href="index.php" class="custom-link">
    <i class="fa-solid fa-circle-left" style="font-size: 20px; margin-right: 8px;"></i>
    <span>Voltar</span>
</a>


<div class="container">
    <h1 class="text-center my-4">Pacientes</h1>
    <div class="row">

        <!-- Primeira linha de cards -->
        <div class="col-md-3"> <!-- Card 1: Triagem -->
            <div class="card mb-4 shadow card-triagem">
                <div class="card-body text-center">
                    <h5 class="card-title">Triagem</h5>
                    <div class="card-numbers">
                        <div class="number-display">
                            <span class="number">50</span>
                            <span class="label">paciente(s)</span>
                        </div>
                        <div class="number-display">
                            <span class="number">30</span>
                            <span class="label">minuto(s)</span>
                        </div>
                    </div>
                    <i class="fa-solid fa-stethoscope icon-background"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3"> <!-- Card 2: Recepção -->
            <div class="card mb-4 shadow card-recepcao">
                <div class="card-body text-center">
                    <h5 class="card-title">Recepção</h5>
                    <div class="card-numbers">
                        <div class="number-display">
                            <span class="number">30</span>
                            <span class="label">paciente(s)</span>
                        </div>
                        <div class="number-display">
                            <span class="number">20</span>
                            <span class="label">minuto(s)</span>
                        </div>
                    </div>
                    <i class="fa-solid fa-user-nurse icon-background"></i>
                </div>
            </div>
        </div>

        <!-- Segunda linha de cards -->
        <div class="col-md-3"> <!-- Card 3: Clínica Médica -->
            <div class="card mb-4 shadow card-clinica">
                <div class="card-body text-center">
                    <h5 class="card-title">Clínica médica</h5>
                    <div class="card-numbers">
                        <div class="number-display">
                            <span class="number">30</span>
                            <span class="label">paciente(s)</span>
                        </div>
                        <div class="number-display">
                            <span class="number">20</span>
                            <span class="label">minuto(s)</span>
                        </div>
                    </div>
                    <i class="fa-solid fa-house-chimney-medical icon-background"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3"> <!-- Card 4: Ortopedia -->
            <div class="card mb-4 shadow card-ortopedia">
                <div class="card-body text-center">
                    <h5 class="card-title">Ortopedia</h5>
                    <div class="card-numbers">
                        <div class="number-display">
                            <span class="number">30</span>
                            <span class="label">paciente(s)</span>
                        </div>
                        <div class="number-display">
                            <span class="number">20</span>
                            <span class="label">minuto(s)</span>
                        </div>
                    </div>
                    <i class="fa-solid fa-user-doctor icon-background"></i>
                </div>
            </div>
        </div>

    </div>
</div>








<?php
// Valores estáticos para os gráficos
$leitoCounts = [
    'Leito 1' => 10,
    'Leito 2' => 15,
    'Leito 3' => 8,
    'Leito 4' => 20,
];

$convênioCounts = [
    'Convenio A' => 12,
    'Convenio B' => 22,
    'Convenio C' => 18,
];

$unidadeCounts = [
    'Unidade X' => 5,
    'Unidade Y' => 10,
    'Unidade Z' => 15,
];

$prescricaoCounts = [
    'Prescricao 1' => 14,
    'Prescricao 2' => 6,
    'Prescricao 3' => 11,
];

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
        // Dados estáticos para os gráficos
        const leitos = ['Leito 1', 'Leito 2', 'Leito 3', 'Leito 4'];
        const countsByLeito = [10, 15, 8, 20];

        const convenios = ['Convenio A', 'Convenio B', 'Convenio C'];
        const countsByConvenio = [12, 22, 18];

        const unidades = ['Unidade X', 'Unidade Y', 'Unidade Z'];
        const countsByUnidade = [5, 10, 15];

        const prescricoes = ['Prescricao 1', 'Prescricao 2', 'Prescricao 3'];
        const countsByPrescricao = [14, 6, 11];

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
