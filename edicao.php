<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DIETA PACIENTES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/edicao.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</head>
<body>

<?php 
include 'conexao.php'; 
include 'header.php';
?>

<a href="inicio.php" class="custom-link">
    <i class="fa-solid fa-circle-left" style="font-size: 20px; margin-right: 8px;"></i>
    <span>Voltar</span>
</a>

<main class="container my-5">
    <h2 class="mb-4">Edição de Pacientes</h2>

    <form id="editarPacienteForm" action="atualizar_paciente.php" method="POST">
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" required>
            </div>
            <div class="col-md-6">
                <label for="idade" class="form-label">Idade</label>
                <input type="number" class="form-control" id="idade" name="idade" min="0" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="sexo" class="form-label">Sexo</label>
            <select class="form-select" id="sexo" name="sexo" required>
                <option value="" disabled selected>Selecione o sexo</option>
                <option value="masculino">Masculino</option>
                <option value="feminino">Feminino</option>
                <option value="outro">Outro</option>
            </select>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label for="convenio" class="form-label">Convênio</label>
                <input type="text" class="form-control" id="convenio" name="convenio">
            </div>
            <div class="col-md-4">
                <label for="unidade" class="form-label">Unidade</label>
                <input type="text" class="form-control" id="unidade" name="unidade">
            </div>
            <div class="col-md-4">
                <label for="leito" class="form-label">Leito</label>
                <input type="text" class="form-control" id="leito" name="leito">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="prescricao" class="form-label">Prescrição</label>
                <input type="text" class="form-control" id="prescricao" name="prescricao">
            </div>
            <div class="col-md-6">
                <label for="dieta" class="form-label">Dieta</label>
                <input type="text" class="form-control" id="dieta" name="dieta">
            </div>
        </div>

        <div class="mb-3">
            <label for="observacoes" class="form-label">Observações</label>
            <textarea class="form-control" id="observacoes" name="observacoes" rows="4"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Atualizar Paciente</button>
    </form>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/teste.js"></script>
</body>
</html>
