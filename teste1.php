<?php
// Simulando dados para a tabela com todos os procedimentos em cada linha
$data = [
    ['08:00', '12345', 30, '233', 15, 'Ativo', 'João da Silva', 'Convênio A', '123456', 'Não', 1, 'Sem observações', '1234', 'Dr. José', 'Sem anotações'],
    ['08:15', '12349', 20, '233', 10, 'Ativo', 'Ana Souza', 'Convênio E', '123450', 'Sim', 2, 'Primeira consulta', '5678', 'Dr. Maria', 'Notas adicionais'],
    ['08:30', '12350', 40, '233', 25, 'Ativo', 'Pedro Almeida', 'Convênio F', '123451', 'Não', 3, 'Consulta de rotina', '9101', 'Dr. Ana', 'Sem observações'],
    // Adicionando mais 10 linhas com todos os procedimentos
];

$procedimentos = ['TRIA', 'LAB', 'RX', 'US', 'TC'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabela de Atendimento</title>
    <link rel="stylesheet" href="css/teste1.css">
    <style>
        .background-tria {
            background-color: green;
            color: white; /* Para melhor legibilidade */
            padding: 2px 5px; /* Para espaçamento */
        }
        .background-lab,
        .background-rx,
        .background-us,
        .background-tc {
            background-color: gray;
            color: white; /* Para melhor legibilidade */
            padding: 2px 5px; /* Para espaçamento */
        }
    </style>
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
                <th>Responsável</th> <!-- Responsável aqui -->
                <th>Anotações</th> <!-- Anotações aqui -->
                <th>Procedimentos</th> <!-- Procedimentos aqui -->
            </tr>
        </thead>
        <tbody>
    <?php foreach ($data as $row): ?>
        <tr>
            <?php foreach ($row as $index => $cell): ?>
                <td>
                    <?php
                    if ($index === 5) { // Status
                        if ($cell === 'Ativo') {
                            echo '<span class="status-bola ativo"></span>';
                        } else {
                            echo '<span class="status-bola inativo"></span>';
                        }
                    } elseif ($index === 12) { // BIP/Senha
                        // Exibe a bolinha de status e o número
                        echo '<span class="status-bola ' . strtolower($row[5]) . '"></span>'; // Ajusta para pegar a classe da situação
                        echo htmlspecialchars($cell); // Exibe o número do BIP/Senha
                    } else {
                        echo htmlspecialchars($cell);
                    }
                    ?>
                </td>
            <?php endforeach; ?>
            <td>Dr. José</td> <!-- Exemplo de responsável -->
            <td>Sem anotações</td> <!-- Exemplo de anotações -->
            <td>
                <?php foreach ($procedimentos as $procedimento): ?>
                    <span class="<?php echo $procedimento === 'TRIA' ? 'background-tria' : 'background-' . strtolower($procedimento); ?>">
                        <?php echo htmlspecialchars($procedimento); ?>
                    </span>
                <?php endforeach; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>

    </table>
</div>

</body>
</html>
