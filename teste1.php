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
    <h1 class="text-center my-4"></h1>
    <div class="container-fluid">
        <div class="row text-center mx-0">
            <!-- Coluna 1: Triagem -->
            <div class="col-3">
                <div class="card mb-3 shadow card-custom" onclick="toggleData('triagem')">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fa-solid fa-kit-medical icon-background"></i> Triagem
                        </h5>
                        <hr>
                        <div class="card-info">
                            <div>
                                <p class="number-large">15</p>
                                <p class="card-text-title">Paciente(s)</p>
                            </div>
                            <div>
                                <p class="number-large">30</p>
                                <p class="card-text-title">Minuto(s)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coluna 2: Recepção -->
            <div class="col-3">
                <div class="card mb-3 shadow card-custom" onclick="toggleData('recepcao')">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fa-solid fa-user-nurse icon-background"></i> Recepção
                        </h5>
                        <hr>
                        <div class="card-info">
                            <div>
                                <p class="number-large">20</p>
                                <p class="card-text-title">Paciente(s)</p>
                            </div>
                            <div>
                                <p class="number-large">40</p>
                                <p class="card-text-title">Minuto(s)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coluna 3: Clínica Médica -->
            <div class="col-3 text-small">
                <div class="card mb-3 shadow card-custom" onclick="toggleData('clinica')">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fa-solid fa-user-doctor icon-background"></i> Clínica médica
                        </h5>
                        <hr>
                        <div class="d-flex justify-content-between mt-1">
                            <div>
                                <p class="card-text">1° Atendimento</p>
                                <div class="d-flex justify-content-between">
                                    <div class="text-center">
                                        <p class="number-large1">10</p>
                                        <p class="card-text-title">Paciente(s)</p>
                                    </div>
                                    <div class="text-center" style="margin-left: 15px;">
                                        <p class="number-large1">18</p>
                                        <p class="card-text-title">Minutos</p>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <p class="card-text">Reavaliação</p>
                                <div class="d-flex justify-content-between">
                                    <div class="text-center">
                                        <p class="number-large1">50</p>
                                        <p class="card-text-title">Paciente(s)</p>
                                    </div>
                                    <div class="text-center" style="margin-left: 15px;">
                                        <p class="number-large1">25</p>
                                        <p class="card-text-title">Minutos</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coluna 4: Ortopedia -->
            <div class="col-3 text-small">
                <div class="card mb-3 shadow card-custom" onclick="toggleData('ortopedia')">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fa-solid fa-stethoscope icon-background"></i> Ortopedia
                        </h5>
                        <hr>
                        <div class="d-flex justify-content-between mt-1">
                            <div>
                                <p class="card-text">1° Atendimento</p>
                                <div class="d-flex justify-content-between">
                                    <div class="text-center">
                                        <p class="number-large1">5</p>
                                        <p class="card-text-title">Paciente(s)</p>
                                    </div>
                                    <div class="text-center" style="margin-left: 15px;">
                                        <p class="number-large1">15</p>
                                        <p class="card-text-title">Minutos</p>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <p class="card-text">Reavaliação</p>
                                <div class="d-flex justify-content-between">
                                    <div class="text-center">
                                        <p class="number-large1">8</p>
                                        <p class="card-text-title">Paciente(s)</p>
                                    </div>
                                    <div class="text-center" style="margin-left: 15px;">
                                        <p class="number-large1">20</p>
                                        <p class="card-text-title">Minutos</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabela Única -->
        <div id="dataTable" class="table-container mt-4" style="display: none;">
            <table class="table table-striped">
                <thead>
                    <tr class="cabe">
                        <th>Chegada</th>
                        <th>Marc</th>
                        <th>Tmp</th>
                        <th>Atd.</th>
                        <th>Tmp</th>
                        <th>Sit</th>
                        <th>Nome</th>
                        <th>Conv</th>
                        <th>Pront</th>
                        <th>SX</th>
                        <th>ID</th>
                        <th>Observação</th>
                        <th>BIP/Senha</th>
                        <th>Procedimento</th>
                        <th>Responsável</th>
                        <th>Anotações</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- As linhas da tabela serão preenchidas dinamicamente -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const data = {
        triagem: [
            { chegada: '08:00', marc: '12345', tmp: 30, atd: 'Sim', tmp2: 15, sit: 'Ativo', nome: 'João da Silva', conv: 'Convênio A', pront: '123456', sx: 'Não', id: 1, obs: 'Sem observações', bip: '1234', procedimento: 'Consulta', responsavel: 'Dr. José', anotacoes: 'Sem anotações' },
            { chegada: '08:00', marc: '12345', tmp: 30, atd: 'Sim', tmp2: 15, sit: 'Ativo', nome: 'João da Silva', conv: 'Convênio A', pront: '123456', sx: 'Não', id: 1, obs: 'Sem observações', bip: '1234', procedimento: 'Consulta', responsavel: 'Dr. José', anotacoes: 'Sem anotações' },
            { chegada: '08:00', marc: '12345', tmp: 30, atd: 'Sim', tmp2: 15, sit: 'Ativo', nome: 'João da Silva', conv: 'Convênio A', pront: '123456', sx: 'Não', id: 1, obs: 'Sem observações', bip: '1234', procedimento: 'Consulta', responsavel: 'Dr. José', anotacoes: 'Sem anotações' },
            { chegada: '08:00', marc: '12345', tmp: 30, atd: 'Sim', tmp2: 15, sit: 'Ativo', nome: 'João da Silva', conv: 'Convênio A', pront: '123456', sx: 'Não', id: 1, obs: 'Sem observações', bip: '1234', procedimento: 'Consulta', responsavel: 'Dr. José', anotacoes: 'Sem anotações' },
            { chegada: '08:15', marc: '12349', tmp: 20, atd: 'Não', tmp2: 10, sit: 'Ativo', nome: 'Ana Souza', conv: 'Convênio E', pront: '123450', sx: 'Sim', id: 2, obs: 'Primeira consulta', bip: '5678', procedimento: 'Exame', responsavel: 'Dr. Maria', anotacoes: 'Notas adicionais' },
            { chegada: '08:30', marc: '12350', tmp: 40, atd: 'Sim', tmp2: 25, sit: 'Ativo', nome: 'Pedro Almeida', conv: 'Convênio F', pront: '123451', sx: 'Não', id: 3, obs: 'Consulta de rotina', bip: '9101', procedimento: 'Check-up', responsavel: 'Dr. Ana', anotacoes: 'Sem observações' },
        ],
        recepcao: [
            { chegada: '08:00', marc: '12345', tmp: 30, atd: 'Sim', tmp2: 15, sit: 'Ativo', nome: 'João da Silva', conv: 'Convênio A', pront: '123456', sx: 'Não', id: 1, obs: 'Sem observações', bip: '1234', procedimento: 'Consulta', responsavel: 'Dr. José', anotacoes: 'Sem anotações' },
            { chegada: '08:00', marc: '12345', tmp: 30, atd: 'Sim', tmp2: 15, sit: 'Ativo', nome: 'João da Silva', conv: 'Convênio A', pront: '123456', sx: 'Não', id: 1, obs: 'Sem observações', bip: '1234', procedimento: 'Consulta', responsavel: 'Dr. José', anotacoes: 'Sem anotações' },
            { chegada: '08:30', marc: '12346', tmp: 20, atd: 'Sim', tmp2: 10, sit: 'Ativo', nome: 'Maria Oliveira', conv: 'Convênio B', pront: '123457', sx: 'Sim', id: 4, obs: 'Sem observações', bip: '5678', procedimento: 'Consulta', responsavel: 'Dr. Ana', anotacoes: 'Notas adicionais' },
            { chegada: '08:45', marc: '12352', tmp: 25, atd: 'Não', tmp2: 15, sit: 'Ativo', nome: 'Luiz Santos', conv: 'Convênio G', pront: '123458', sx: 'Não', id: 5, obs: 'Aguardando exames', bip: '1111', procedimento: 'Consulta', responsavel: 'Dr. Carlos', anotacoes: 'Aguardando retorno' },
            { chegada: '09:00', marc: '12353', tmp: 30, atd: 'Sim', tmp2: 5, sit: 'Ativo', nome: 'Fernanda Costa', conv: 'Convênio H', pront: '123459', sx: 'Sim', id: 6, obs: 'Consulta urgente', bip: '2222', procedimento: 'Emergência', responsavel: 'Dr. João', anotacoes: 'Prioridade' },
        ],
        clinica: [
            { chegada: '08:00', marc: '12345', tmp: 30, atd: 'Sim', tmp2: 15, sit: 'Ativo', nome: 'João da Silva', conv: 'Convênio A', pront: '123456', sx: 'Não', id: 1, obs: 'Sem observações', bip: '1234', procedimento: 'Consulta', responsavel: 'Dr. José', anotacoes: 'Sem anotações' },
            { chegada: '08:00', marc: '12345', tmp: 30, atd: 'Sim', tmp2: 15, sit: 'Ativo', nome: 'João da Silva', conv: 'Convênio A', pront: '123456', sx: 'Não', id: 1, obs: 'Sem observações', bip: '1234', procedimento: 'Consulta', responsavel: 'Dr. José', anotacoes: 'Sem anotações' },
            { chegada: '09:10', marc: '12354', tmp: 15, atd: 'Sim', tmp2: 10, sit: 'Ativo', nome: 'Carla Mendes', conv: 'Convênio C', pront: '123460', sx: 'Não', id: 7, obs: 'Reavaliação', bip: '3333', procedimento: 'Consulta', responsavel: 'Dr. Carla', anotacoes: 'Revisar exames' },
            { chegada: '09:15', marc: '12355', tmp: 18, atd: 'Não', tmp2: 20, sit: 'Ativo', nome: 'Ricardo Lima', conv: 'Convênio I', pront: '123461', sx: 'Sim', id: 8, obs: 'Consulta de rotina', bip: '4444', procedimento: 'Check-up', responsavel: 'Dr. José', anotacoes: 'Sem observações' },
            { chegada: '09:20', marc: '12356', tmp: 22, atd: 'Sim', tmp2: 8, sit: 'Ativo', nome: 'Juliana Torres', conv: 'Convênio J', pront: '123462', sx: 'Não', id: 9, obs: 'Sem observações', bip: '5555', procedimento: 'Consulta', responsavel: 'Dr. Ana', anotacoes: 'Aguardando exames' },
        ],
        ortopedia: [
            { chegada: '08:00', marc: '12345', tmp: 30, atd: 'Sim', tmp2: 15, sit: 'Ativo', nome: 'João da Silva', conv: 'Convênio A', pront: '123456', sx: 'Não', id: 1, obs: 'Sem observações', bip: '1234', procedimento: 'Consulta', responsavel: 'Dr. José', anotacoes: 'Sem anotações' },
            { chegada: '08:00', marc: '12345', tmp: 30, atd: 'Sim', tmp2: 15, sit: 'Ativo', nome: 'João da Silva', conv: 'Convênio A', pront: '123456', sx: 'Não', id: 1, obs: 'Sem observações', bip: '1234', procedimento: 'Consulta', responsavel: 'Dr. José', anotacoes: 'Sem anotações' },
            { chegada: '09:25', marc: '12357', tmp: 10, atd: 'Sim', tmp2: 15, sit: 'Ativo', nome: 'Carlos Santos', conv: 'Convênio D', pront: '123463', sx: 'Sim', id: 10, obs: 'Consulta de retorno', bip: '6666', procedimento: 'Consulta', responsavel: 'Dr. Carla', anotacoes: 'Reavaliar tratamento' },
            { chegada: '09:30', marc: '12358', tmp: 25, atd: 'Não', tmp2: 20, sit: 'Ativo', nome: 'Tatiane Lima', conv: 'Convênio K', pront: '123464', sx: 'Não', id: 11, obs: 'Reavaliação', bip: '7777', procedimento: 'Exame', responsavel: 'Dr. Carlos', anotacoes: 'Aguardando exames' },
            { chegada: '09:35', marc: '12359', tmp: 30, atd: 'Sim', tmp2: 12, sit: 'Ativo', nome: 'Fernando Alves', conv: 'Convênio L', pront: '123465', sx: 'Sim', id: 12, obs: 'Consulta', bip: '8888', procedimento: 'Consulta', responsavel: 'Dr. João', anotacoes: 'Acompanhamento' },
        ],
    };

    let currentVisibleTable = null;

    function toggleData(card) {
        const dataTable = document.getElementById('dataTable');
        const tableBody = document.getElementById('tableBody');

        // Limpa o conteúdo da tabela
        tableBody.innerHTML = '';

        // Adiciona as linhas ao corpo da tabela com base no card clicado
        data[card].forEach(item => {
            const row = `<tr>
                <td>${item.chegada}</td>
                <td>${item.marc}</td>
                <td>${item.tmp}</td>
                <td>${item.atd}</td>
                <td>${item.tmp2}</td>
                <td>${item.sit}</td>
                <td>${item.nome}</td>
                <td>${item.conv}</td>
                <td>${item.pront}</td>
                <td>${item.sx}</td>
                <td>${item.id}</td>
                <td>${item.obs}</td>
                <td>${item.bip}</td>
                <td>${item.procedimento}</td>
                <td>${item.responsavel}</td>
                <td>${item.anotacoes}</td>
            </tr>`;
            tableBody.innerHTML += row; // Adiciona a nova linha ao corpo da tabela
        });

        // Alterna a visibilidade da tabela
        if (currentVisibleTable !== card) {
            dataTable.style.display = 'block'; // Mostra a tabela
            currentVisibleTable = card; // Atualiza a tabela atualmente visível
        } else {
            dataTable.style.display = 'none'; // Oculta a tabela se já estiver visível
            currentVisibleTable = null; // Reseta a tabela atualmente visível
        }
    }
</script>









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





<!-- Bootstrap CSS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="js/dados.js"></script>
</body>
</html>
