<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabela de Pacientes com Paginação Dinâmica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        table {
            width: 80%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 14px;
            text-align: left;
        }

        th, td {
            padding: 8px 10px;
        }

        thead {
            background-color: #4CAF50;
            color: white;
        }

        tbody tr {
            border-bottom: 1px solid #dddddd;
        }

        tbody tr:nth-of-type(even) {
            background-color: #f3f3f3;
        }

        tbody tr:last-of-type {
            border-bottom: 2px solid #4CAF50;
        }

        tbody tr:hover {
            background-color: #f1f1f1;
        }

        td {
            text-align: center;
            font-size: 12px;
        }

        table th:first-child, 
        table td:first-child {
            text-align: left;
        }

        .table-container {
            max-width: 80%;
            overflow-x: auto;
            margin: 0 auto;
        }

        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<?php 
    include 'conexao.php'; // Inclui o arquivo de conexão
    include 'header.php';
    // Executa a consulta SQL
    try {
        $query = $connection->query("
            SELECT 
                HSP.HSP_NUM AS 'IH',
                HSP.HSP_PAC AS 'REGISTRO',
                PAC.PAC_NOME AS 'PACIENTE',
                CNV.CNV_NOME AS 'CONVENIO',
                RTRIM(STR.STR_NOME) AS 'UNIDADE',
                LOC.LOC_NOME AS 'LEITO',
                ISNULL(PSC.PSC_DHINI,'') AS 'PRESCRICAO',
                ISNULL(ADP.ADP_NOME,'') AS 'DIETA',
                DATEDIFF(hour, HSP.HSP_DTHRE, GETDATE()) AS 'horas'
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
                AND PSC.PSC_DHINI = (
                    SELECT MAX(PSCMAX.PSC_DHINI) 
                    FROM PSC PSCMAX 
                    WHERE PSCMAX.PSC_PAC = PSC.PSC_PAC 
                        AND PSCMAX.PSC_HSP = PSC.PSC_HSP
                        AND PSCMAX.PSC_TIP = 'D'
                        AND PSCMAX.PSC_STAT = 'A'
                )
            GROUP BY 
                HSP.HSP_NUM,
                HSP.HSP_PAC,
                PAC.PAC_NOME,
                CNV.CNV_NOME,
                STR.STR_NOME,
                LOC.LOC_NOME,
                PSC.PSC_ADP,
                ADP.ADP_NOME,
                ISNULL(PSC.PSC_DHINI, ''), 
                DATEDIFF(hour, HSP.HSP_DTHRE, GETDATE())
            ORDER BY 
                STR.STR_NOME,
                LOC.LOC_NOME
        ");

        $result = $query->fetchAll(PDO::FETCH_ASSOC); 

        if (count($result) > 0) {
?>

            <div class="container mt-5 table-container">
                <!-- Campo de filtro -->
                <div class="mb-3">
                    <input type="text" id="filterInput" class="form-control" placeholder="Filtrar por paciente..." onkeyup="filterTable()">
                </div>
                
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>IH</th>
                            <th>Registro</th>
                            <th>Paciente</th>
                            <th>Convênio</th>
                            <th>Unidade</th>
                            <th>Leito</th>
                            <th>Prescrição</th>
                            <th>Dieta</th>
                            <th>Horas</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        <?php 
                        foreach ($result as $row) { 
                        ?>
                        <tr class="trdados">
                            <td><?= htmlspecialchars($row['IH']); ?></td>
                            <td><?= htmlspecialchars($row['REGISTRO']); ?></td>
                            <td><?= htmlspecialchars($row['PACIENTE']); ?></td>
                            <td><?= htmlspecialchars($row['CONVENIO']); ?></td>
                            <td><?= htmlspecialchars($row['UNIDADE']); ?></td>
                            <td><?= htmlspecialchars($row['LEITO']); ?></td>
                            <td><?= date('d/m/Y', strtotime($row['PRESCRICAO'])); ?></td>
                            <td><?= htmlspecialchars($row['DIETA']); ?></td>
                            <td><?= htmlspecialchars($row['horas']); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <!-- Paginação -->
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
                <div class="pagination-container" id="pagination-container">
                    <button class="btn btn-success" id="prev-set" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div id="page-numbers" class="mx-2"></div>
                    <button class="btn btn-success" id="next-set">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>

<?php 
        } else {
            echo "<p>Nenhum paciente encontrado.</p>";
        }
    } catch (Exception $e) {
        echo "Erro ao executar a consulta: " . $e->getMessage();
    }
?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const rowsPerPage = 15; // Número de linhas por página
        const pagesPerSet = 10; // Número de páginas por conjunto
        let currentPage = 1;
        let currentSet = 1; // Conjunto atual de páginas
        const tableRows = document.querySelectorAll('#table-body tr');
        const totalPages = Math.ceil(tableRows.length / rowsPerPage);
        const paginationContainer = document.getElementById('pagination-container');
        const prevSetBtn = document.getElementById('prev-set');
        const nextSetBtn = document.getElementById('next-set');
        const pageNumbersContainer = document.getElementById('page-numbers');

        // Mostra as linhas da tabela para a página atual
        function showPage(page) {
            const start = (page - 1) * rowsPerPage;
            const end = page * rowsPerPage;

            tableRows.forEach((row, index) => {
                row.style.display = 'none';
                if (index >= start && index < end) {
                    row.style.display = '';
                }
            });
        }

        // Atualiza os números das páginas exibidas
        function updatePageNumbers() {
            pageNumbersContainer.innerHTML = '';
            const totalVisiblePages = Math.min(pagesPerSet, totalPages - ((currentSet - 1) * pagesPerSet));
            const startPage = (currentSet - 1) * pagesPerSet + 1;

            for (let i = 0; i < totalVisiblePages; i++) {
                const pageNumber = startPage + i;
                if (pageNumber <= totalPages) {
                    const pageButton = document.createElement('button');
                    pageButton.innerText = pageNumber;
                    pageButton.classList.add('btn', 'btn-light', 'mx-1');
                    pageButton.onclick = () => {
                        currentPage = pageNumber;
                        showPage(currentPage);
                    };
                    pageNumbersContainer.appendChild(pageButton);
                }
            }
        }

        // Atualiza os botões de navegação
        function updateNavigationButtons() {
            prevSetBtn.disabled = currentSet === 1;
            nextSetBtn.disabled = currentSet * pagesPerSet >= totalPages;
        }

        // Navegação entre os conjuntos de páginas
        prevSetBtn.onclick = () => {
            currentSet--;
            updatePageNumbers();
            showPage(currentPage);
            updateNavigationButtons();
        };

        nextSetBtn.onclick = () => {
            currentSet++;
            updatePageNumbers();
            showPage(currentPage);
            updateNavigationButtons();
        };

        function filterTable() {
    const filterInput = document.getElementById('filterInput');
    const filterValue = filterInput.value.toLowerCase();
    let filteredCount = 0;

    // Limpa o corpo da tabela antes de filtrar
    const tableBody = document.getElementById('table-body');
    tableBody.innerHTML = '';

    // Prepara uma lista de linhas filtradas
    const filteredRows = [];

    tableRows.forEach(row => {
        let matchFound = false; // Variável para verificar se há uma correspondência
        // Verifica cada célula na linha
        for (let cell of row.cells) {
            if (cell.textContent.toLowerCase().includes(filterValue)) {
                matchFound = true;
                break; // Saia do loop se uma correspondência for encontrada
            }
        }
        // Se uma correspondência for encontrada, clone a linha e adicione-a à lista
        if (matchFound) {
            const newRow = row.cloneNode(true);
            filteredRows.push(newRow);
            filteredCount++;
        }
    });

    // Se houver resultados filtrados, adiciona-os à tabela
    if (filteredCount > 0) {
        filteredRows.forEach(filteredRow => tableBody.appendChild(filteredRow));
    } else {
        const noResultsRow = document.createElement('tr');
        noResultsRow.innerHTML = '<td colspan="9" style="text-align:center;">Nenhum resultado encontrado.</td>';
        tableBody.appendChild(noResultsRow);
    }

    // Atualizar a paginação após o filtro
    currentPage = 1; // Reseta para a primeira página
    updatePageNumbers();
    showPage(currentPage);
    updateNavigationButtons();
}

        // Inicializa a página
        showPage(currentPage);
        updatePageNumbers();
        updateNavigationButtons();
    </script>
</body>
</html>
