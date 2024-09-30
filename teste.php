<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabela de Pacientes com Paginação Dinâmica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400&display=swap" rel="stylesheet">

    <style>
        table {
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            font-weight: 300;
            line-height: 1.5;
            letter-spacing: 0.5px;
            text-transform: none;
        }
        table {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        table th, table td {
            padding: 10px;
            text-align: center;
            vertical-align: middle;
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

    // Função para capitalizar a primeira letra de cada palavra
    function capitalizeFirstLetters($string) {
        return ucwords(strtolower($string));
    }

    try {
        // Consulta SQL para buscar os dados dos pacientes
        $query = $connection->query("
            SELECT 
                HSP.HSP_NUM AS 'IH',
                HSP.HSP_PAC AS 'REGISTRO',
                PAC.PAC_NOME AS 'PACIENTE',
                CNV.CNV_NOME AS 'CONVENIO',
                RTRIM(STR.STR_NOME) AS 'UNIDADE',
                LOC.LOC_NOME AS 'LEITO',
                ISNULL(PSC.PSC_DHINI, '') AS 'PRESCRICAO',
                ISNULL(ADP.ADP_NOME, '') AS 'DIETA',
                DATEDIFF(hour, HSP.HSP_DTHRE, GETDATE()) AS 'horas'
            FROM 
                HSP 
            INNER JOIN LOC ON HSP_LOC = LOC_COD 
            INNER JOIN STR ON STR_COD = LOC_STR
            INNER JOIN PAC ON PAC.PAC_REG = HSP.HSP_PAC
            INNER JOIN CNV ON CNV_COD = HSP.HSP_CNV
            LEFT JOIN PSC ON PSC.PSC_HSP = HSP.HSP_NUM 
                AND PSC.PSC_PAC = HSP.HSP_PAC 
                AND PSC.PSC_TIP = 'D'
            LEFT JOIN ADP ON ADP.ADP_COD = PSC.PSC_ADP 
                AND ADP_TIPO = 'D'
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
            ORDER BY PAC.PAC_NOME, STR.STR_NOME, LOC.LOC_NOME;
        ");

        $result = $query->fetchAll(PDO::FETCH_ASSOC); 

        if (count($result) > 0) {
            // Agrupando os dados por nome do paciente
            $groupedPatients = [];
            foreach ($result as $row) {
                $patientName = capitalizeFirstLetters($row['PACIENTE']);
                if (!isset($groupedPatients[$patientName])) {
                    $groupedPatients[$patientName] = [
                        'IH' => $row['IH'],
                        'REGISTRO' => $row['REGISTRO'],
                        'PACIENTE' => $patientName,
                        'CONVENIO' => capitalizeFirstLetters($row['CONVENIO']),
                        'UNIDADE' => capitalizeFirstLetters($row['UNIDADE']),
                        'LEITO' => capitalizeFirstLetters($row['LEITO']),
                        'PRESCRICAO' => date('d/m/Y', strtotime($row['PRESCRICAO'])),
                        'DIETAS' => [capitalizeFirstLetters($row['DIETA'])],
                        'horas' => $row['horas']
                    ];
                } else {
                    // Adiciona a dieta à lista de dietas
                    $groupedPatients[$patientName]['DIETAS'][] = capitalizeFirstLetters($row['DIETA']);
                }
            }
?>

<div class="container-fluid mt-5">
    <div class="row justify-content-center">
        <div class="col-12">
                <!-- Campo de filtro -->
                <div class="mb-3">
                    <input type="text" id="filterInput" class="form-control" placeholder="Filtrar por paciente..." onkeyup="filterTable()">
                </div>
                
                <table class="table table-striped table-bordered table-hover ">
                    <thead style="background-color: green; color:white;">
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
                        foreach ($groupedPatients as $patient) { 
                        ?>
                        <tr class="trdados">
                            <td class="text-center align-middle"><?= htmlspecialchars($patient['IH']); ?></td>
                            <td class="text-center align-middle"><?= htmlspecialchars($patient['REGISTRO']); ?></td>
                            <td class="text-center align-middle"><?= htmlspecialchars($patient['PACIENTE']); ?></td>
                            <td class="text-center align-middle"><?= htmlspecialchars($patient['CONVENIO']); ?></td>
                            <td class="text-center align-middle"><?= htmlspecialchars($patient['UNIDADE']); ?></td>
                            <td class="text-center align-middle"><?= htmlspecialchars($patient['LEITO']); ?></td>
                            <td class="text-center align-middle"><?= htmlspecialchars($patient['PRESCRICAO']); ?></td>
                            <td class="text-center align-middle col-3"><?= htmlspecialchars(implode(', ', $patient['DIETAS'])); ?></td>
                            <td class="text-center align-middle"><?= htmlspecialchars($patient['horas']); ?></td>
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
        </div>
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
    const rowsPerPage = 20; // Número de linhas por página
    const pagesPerSet = 10; // Número de páginas por conjunto
    let currentPage = 1;
    let currentSet = 1; // Conjunto atual de páginas
    const tableBody = document.getElementById('table-body');
    let tableRows = Array.from(tableBody.querySelectorAll('tr')); // Converte NodeList em array
    let filteredRows = tableRows; // Inicializa com todas as linhas
    const paginationContainer = document.getElementById('pagination-container');
    const prevSetBtn = document.getElementById('prev-set');
    const nextSetBtn = document.getElementById('next-set');
    const pageNumbersContainer = document.getElementById('page-numbers');

    // Mostra as linhas da tabela para a página atual
    function showPage(page, rows) {
        const start = (page - 1) * rowsPerPage;
        const end = page * rowsPerPage;
        tableBody.innerHTML = '';
        const rowsToDisplay = rows.slice(start, end);
        rowsToDisplay.forEach(row => tableBody.appendChild(row));
    }

    // Atualiza a paginação
    function updatePagination(rows) {
        const totalPages = Math.ceil(rows.length / rowsPerPage);
        const totalSets = Math.ceil(totalPages / pagesPerSet);
        const startSet = (currentSet - 1) * pagesPerSet + 1;
        const endSet = Math.min(startSet + pagesPerSet - 1, totalPages);

        pageNumbersContainer.innerHTML = '';

        for (let i = startSet; i <= endSet; i++) {
            const pageNumber = document.createElement('button');
            pageNumber.className = 'btn btn-success mx-1';
            pageNumber.textContent = i;
            pageNumber.onclick = () => {
                currentPage = i;
                showPage(currentPage, filteredRows);
                updatePagination(filteredRows);
            };
            pageNumbersContainer.appendChild(pageNumber);
        }

        prevSetBtn.disabled = currentSet === 1;
        nextSetBtn.disabled = currentSet === totalSets;

        showPage(currentPage, filteredRows);
    }

    // Filtro de pacientes
    function filterTable() {
        const filterValue = document.getElementById('filterInput').value.toLowerCase();
        filteredRows = tableRows.filter(row => {
            const patientName = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            return patientName.includes(filterValue);
        });
        currentPage = 1; // Resetar a página atual
        updatePagination(filteredRows);
    }

    prevSetBtn.onclick = () => {
        currentSet--;
        updatePagination(filteredRows);
    };

    nextSetBtn.onclick = () => {
        currentSet++;
        updatePagination(filteredRows);
    };

    // Inicializa a tabela
    updatePagination(tableRows);
    </script>
</body>
</html>
