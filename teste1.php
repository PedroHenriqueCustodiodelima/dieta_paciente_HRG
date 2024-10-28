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


<div class="container-fluid px-0">
    <h1 class="text-center my-4">Pacientes</h1>
    <style>
        /* Ajusta o container principal para ocupar toda a tela */
        .container-fluid {
            max-width: 100%;
            padding-left: 0;
            padding-right: 0;
        }

        /* Ajustes específicos para os cards */
        .card-custom {
            background-color: #28a745; /* Cor verde */
            color: white; /* Texto branco */
            min-height: 200px; /* Altura mínima */
            width: 100%; /* Ocupa a largura total da coluna */
        }

        /* Demais ajustes nos estilos dos cards */
        .card-custom .card-body {
            padding: 10px; /* Padding interno */
        }
        .card-custom h5, .card-custom .card-text-title {
            color: white;
            margin-bottom: 5px; /* Margem inferior */
        }
        .card-custom .number-large {
            font-size: 1.3em; /* Ajuste do tamanho do número */
            font-weight: bold;
            margin-bottom: 0; /* Remoção de margem inferior */
        }
        .icon-background {
            color: white;
            font-size: 1.8em; /* Tamanho do ícone */
        }
        .card-custom .d-flex > div {
            margin: 0; /* Remove margem entre os itens */
        }
        hr {
            margin: 5px 0; /* Margem superior e inferior */
        }
        /* Estilo específico para diminuir o tamanho do texto nos últimos dois cards */
        .text-small p,
        .text-small .card-title {
            font-size: 0.9em; /* Tamanho do texto */
        }
    </style>

    <div class="container-fluid">
        <div class="row text-center d-flex align-items-stretch mx-0">
            <!-- Coluna 1: Triagem -->
            <div class="col-2 d-flex">
                <div class="card mb-3 shadow card-custom">
                    <div class="card-body">
                        <h5 class="card-title">Triagem</h5>
                        <hr>
                        <i class="fa-solid fa-notes-medical icon-background"></i>
                        <div class="d-flex justify-content-between mt-1">
                            <div>
                                <p class="number-large">15</p>
                                <p class="card-text-title">Paciente</p>
                            </div>
                            <div>  
                                <p class="number-large">45</p>
                                <p class="card-text-title">Minuto(s)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coluna 2: Recepção -->
            <div class="col-2 d-flex">
                <div class="card mb-3 shadow card-custom">
                    <div class="card-body">
                        <h5 class="card-title">Recepção</h5>
                        <hr>
                        <i class="fa-solid fa-user-check icon-background"></i>
                        <div class="d-flex justify-content-between mt-1">
                            <div>
                                <p class="number-large">20</p>
                                <p class="card-text-title">Quantidade</p>
                            </div>
                            <div>
                                <p class="number-large">30</p>
                                <p class="card-text-title">Minuto(s)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coluna 3: Clínica Médica -->
            <div class="col-4 d-flex text-small">
                <div class="card mb-3 shadow card-custom">
                    <div class="card-body">
                        <h5 class="card-title">Clínica Médica</h5>
                        <hr>
                        <i class="fa-solid fa-stethoscope icon-background"></i>
                        <div class="d-flex justify-content-between mt-1">
                            <div>
                                <p class="card-text"><strong>Atendimento:</strong></p>
                                <div class="d-flex justify-content-between">
                                    <div class="text-center"> 
                                        <p class="number-large">10</p>
                                        <p class="card-text-title">Quantidade</p>
                                    </div>
                                    <div class="text-center"> 
                                        <p class="number-large">60</p>
                                        <p class="card-text-title">Minuto(s)</p>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <p class="card-text"><strong>Reavaliação:</strong></p>
                                <div class="d-flex justify-content-between">
                                    <div class="text-center"> 
                                        <p class="number-large">5</p>
                                        <p class="card-text-title">Quantidade</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="number-large">25</p>
                                        <p class="card-text-title">Minuto(s)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coluna 4: Ortopedia -->
            <div class="col-4 d-flex text-small">
                <div class="card mb-3 shadow card-custom">
                    <div class="card-body">
                        <h5 class="card-title">Ortopedia</h5>
                        <hr>
                        <i class="fa-solid fa-stethoscope icon-background"></i>
                        <div class="d-flex justify-content-between mt-1">
                            <div>
                                <p class="card-text"><strong>Atendimento:</strong></p>
                                <div class="d-flex justify-content-between">
                                    <div class="text-center">
                                        <p class="number-large">10</p>
                                        <p class="card-text-title">Paciente(s)</p>
                                    </div>
                                    <div class="text-center"> 
                                        <p class="number-large">18</p>
                                        <p class="card-text-title">Minutos</p>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <p class="card-text"><strong>Reavaliação:</strong></p>
                                <div class="d-flex justify-content-between">
                                    <div class="text-center">
                                        <p class="number-large">50</p>
                                        <p class="card-text-title">Paciente(s)</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="number-large">25</p>
                                        <p class="card-text-title">Minutos</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
