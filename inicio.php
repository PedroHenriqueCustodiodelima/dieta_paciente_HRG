<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DIETA PACIENTES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/inicio.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</head>
<body>

<?php 
include 'conexao.php'; 
include 'header.php';
?>

<main class="container my-5">
    <div class="row">
        <div class="col-md-4">
            <a href="dados.php" class="card-link">
                <div class="card grafico-card">
                    <div class="icon-part">
                        <i class="fa-solid fa-chart-pie" style="font-size: 2rem;"></i>
                    </div>
                    <div class="text-part">
                        <h5 class="card-title">Gráfico</h5>
                        <p class="card-text">Visualize os dados em formato de gráfico.</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="index.php" class="card-link">
                <div class="card paineis-card">
                    <div class="icon-part">
                        <i class="fa-solid fa-table-columns" style="font-size: 2rem;"></i>
                    </div>
                    <div class="text-part">
                        <h5 class="card-title">Painéis</h5>
                        <p class="card-text">Painel para copeiros.</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="teste1.php" class="card-link">
                <div class="card nutricionista-card">
                    <div class="icon-part">
                        <i class="fa-solid fa-user-doctor" style="font-size: 2rem;"></i>
                    </div>
                    <div class="text-part">
                        <h5 class="card-title">Nutricionista</h5>
                        <p class="card-text">Painel para nutricionista.</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="edicao.php" class="card-link">
                <div class="card edicao-card">
                    <div class="icon-part">
                        <i class="fa-solid fa-user-pen" style="font-size: 2rem;"></i>
                    </div>
                    <div class="text-part">
                        <h5 class="card-title">Edição</h5>
                        <p class="card-text">Edite os dados dos pacientes.</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</main>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/teste.js"></script>
</body>
</html>
