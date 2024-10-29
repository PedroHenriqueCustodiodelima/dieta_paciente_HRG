<?php
// Simulando dados para a tabela
$data = [
    ['08:00', '12345', 30, '233', 15, 'Ativo', 'João da Silva', 'Convênio A', '123456', 'Não', 1, 'Sem observações', '1234', 'Consulta', 'Dr. José', 'Sem anotações'],
    ['08:00', '12345', 30, '233', 15, 'Ativo', 'João da Silva', 'Convênio A', '123456', 'Não', 1, 'Sem observações', '1234', 'Consulta', 'Dr. José', 'Sem anotações'],
    ['08:00', '12345', 30, '233', 15, 'Ativo', 'João da Silva', 'Convênio A', '123456', 'Não', 1, 'Sem observações', '1234', 'Consulta', 'Dr. José', 'Sem anotações'],
    ['08:00', '12345', 30, '233', 15, 'Ativo', 'João da Silva', 'Convênio A', '123456', 'Não', 1, 'Sem observações', '1234', 'Consulta', 'Dr. José', 'Sem anotações'],
    ['08:15', '12349', 20, '233', 10, 'Ativo', 'Ana Souza', 'Convênio E', '123450', 'Sim', 2, 'Primeira consulta', '5678', 'Exame', 'Dr. Maria', 'Notas adicionais'],
    ['08:30', '12350', 40, '233', 25, 'Ativo', 'Pedro Almeida', 'Convênio F', '123451', 'Não', 3, 'Consulta de rotina', '9101', 'Check-up', 'Dr. Ana', 'Sem observações'],
    // Adicionando mais 10 linhas
    ['08:45', '12351', 25, '233', 20, 'Ativo', 'Lucas Mendes', 'Convênio G', '123452', 'Não', 4, 'Sem observações', '1122', 'Consulta', 'Dr. Carla', 'Sem anotações'],
    ['09:00', '12352', 15, '233', 5, 'Ativo', 'Mariana Lima', 'Convênio H', '123453', 'Sim', 5, 'Consulta de retorno', '3344', 'Exame', 'Dr. Bruno', 'Notas adicionais'],
    ['09:15', '12353', 30, '233', 10, 'Ativo', 'Gabriel Costa', 'Convênio I', '123454', 'Não', 6, 'Primeira consulta', '5566', 'Consulta', 'Dr. Rafael', 'Sem observações'],
    ['09:30', '12354', 50, '233', 25, 'Inativo', 'Fernanda Rocha', 'Convênio J', '123455', 'Sim', 7, 'Sem observações', '7788', 'Exame', 'Dr. Lara', 'Notas adicionais'],
    ['09:45', '12355', 20, '233', 15, 'Ativo', 'Ricardo Dias', 'Convênio K', '123456', 'Não', 8, 'Consulta de rotina', '9900', 'Check-up', 'Dr. Alice', 'Sem anotações'],
    ['10:00', '12356', 35, '233', 30, 'Ativo', 'Juliana Ferreira', 'Convênio L', '123457', 'Não', 9, 'Consulta de seguimento', '2233', 'Consulta', 'Dr. Pedro', 'Sem observações'],
    ['10:15', '12357', 45, '233', 40, 'Inativo', 'Carlos Alberto', 'Convênio M', '123458', 'Sim', 10, 'Sem observações', '4455', 'Exame', 'Dr. Tiago', 'Notas adicionais'],
    ['10:30', '12358', 20, '233', 15, 'Ativo', 'Vanessa Martins', 'Convênio N', '123459', 'Não', 11, 'Consulta de rotina', '6677', 'Check-up', 'Dr. Ana', 'Sem anotações'],
    ['10:45', '12359', 10, '233', 5, 'Ativo', 'Eduardo Santos', 'Convênio O', '123460', 'Não', 12, 'Sem observações', '8899', 'Consulta', 'Dr. Cláudio', 'Sem anotações'],
];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabela de Atendimento</title>
    <link rel="stylesheet" href="css/teste1.css">
</head>
<body>

<div class="table-container">
    <table class="table">
        <thead>
            <tr class="cabe">
                <th>Chegada</th>
                <th>Marc</th>
                <th>Tmp</th>
                <th>Atd.</th>
                <th>Tmp</th>
                <th>Sit.</th>
                <th>Nome</th>
                <th>Conv.</th>
                <th>Pront.</th>
                <th>SX</th>
                <th>ID</th>
                <th>Observação</th>
                <th>BIP/Senha</th>
                <th>Procedimento</th>
                <th>Responsável</th>
                <th>Anotações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <?php foreach ($row as $index => $cell): ?>
                        <td>
                            <?php
                            // Adiciona a bolinha de status com base no valor da coluna "Sit"
                            if ($index === 5) { // Supondo que o status esteja na 6ª coluna (índice 5)
                                if ($cell === 'Ativo') {
                                    echo '<span class="status-bola ativo"></span>';
                                } else {
                                    echo '<span class="status-bola inativo"></span>';
                                }
                            } else {
                                echo htmlspecialchars($cell);
                            }
                            ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
