<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabela de Pacientes com Paginação Dinâmica</title>
    <!-- Link do Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php 
    // Define o título da página
    $pageTitle = "Tabela de Pacientes";         
    include 'header.php'; 

    // Executa a consulta SQL
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

    // Verifica se houve resultados
    if ($query->num_rows > 0) {
?>

    <div class="container mt-5">
        <table class="table table-striped table-bordered table-hover">
            <thead style="background-color: green; color:white;">
                <tr>
                    <th>Data</th>
                    <th>Leito</th>
                    <th>Dieta</th>
                    <th>Unidade</th>
                    <th>Nome</th>
                    <th>Registro</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <?php 
                // Loop através dos resultados da consulta
                while ($row = $query->fetch_assoc()) { 
                ?>
                <tr class="trdados">
                    <td><?= date('d/m/Y', strtotime($row['PRESCRICAO'])); ?></td> <!-- Data da prescrição -->
                    <td><?= htmlspecialchars($row['LEITO']); ?></td> <!-- Leito -->
                    <td><?= htmlspecialchars($row['DIETA']); ?></td> <!-- Dieta -->
                    <td><?= htmlspecialchars($row['UNIDADE']); ?></td> <!-- Unidade -->
                    <td><?= htmlspecialchars($row['PACIENTE']); ?></td> <!-- Nome do paciente -->
                    <td><?= htmlspecialchars($row['REGISTRO']); ?></td> <!-- Registro do paciente -->
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Paginação -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center" id="pagination">
                <li class="page-item" id="prev-page">
                    <a class="page-link" href="#" tabindex="-1">Anterior</a>
                </li>
                <!-- Os números das páginas serão gerados dinamicamente -->
                <li class="page-item" id="next-page">
                    <a class="page-link" href="#">Próximo</a>
                </li>
            </ul>
        </nav>
    </div>

<?php 
    } else {
        echo "<p>Nenhum paciente encontrado.</p>";
    }

    include 'footer.php'; 
?>

    <!-- Link do Bootstrap JS e Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const rowsPerPage = 5;
        let currentPage = 1;
        const tableRows = document.querySelectorAll('#table-body tr');
        const totalPages = Math.ceil(tableRows.length / rowsPerPage);
        const paginationContainer = document.getElementById('pagination');
        const prevPageBtn = document.getElementById('prev-page');
        const nextPageBtn = document.getElementById('next-page');

        // Função para gerar os números de página na paginação
        function generatePaginationNumbers() {
            for (let i = 1; i <= totalPages; i++) {
                const pageItem = document.createElement('li');
                pageItem.classList.add('page-item');
                if (i === currentPage) pageItem.classList.add('active');
                
                const pageLink = document.createElement('a');
                pageLink.classList.add('page-link');
                pageLink.href = "#";
                pageLink.textContent = i;

                pageLink.addEventListener('click', function(event) {
                    event.preventDefault();
                    currentPage = i;
                    showPage(currentPage);
                    updatePaginationButtons();
                });

                pageItem.appendChild(pageLink);
                nextPageBtn.before(pageItem);
            }
        }

        function showPage(page) {
            tableRows.forEach((row, index) => {
                row.style.display = 'none'; // Esconde todas as linhas
                if (index >= (page - 1) * rowsPerPage && index < page * rowsPerPage) {
                    row.style.display = ''; // Mostra as linhas da página atual
                }
            });

            // Atualiza a classe 'active' nos números da paginação
            document.querySelectorAll('.pagination .page-item').forEach((item, index) => {
                if (index === page) {
                    item.classList.add('active');
                } else {
                    item.classList.remove('active');
                }
            });
        }

        function updatePaginationButtons() {
            prevPageBtn.classList.toggle('disabled', currentPage === 1);
            nextPageBtn.classList.toggle('disabled', currentPage === totalPages);

            // Atualiza a classe 'active' nos números de página
            document.querySelectorAll('.pagination .page-item').forEach((item, index) => {
                if (index === currentPage) {
                    item.classList.add('active');
                } else {
                    item.classList.remove('active');
                }
            });
        }

        prevPageBtn.addEventListener('click', function(event) {
            event.preventDefault();
            if (currentPage > 1) {
                currentPage--;
                showPage(currentPage);
                updatePaginationButtons();
            }
        });

        nextPageBtn.addEventListener('click', function(event) {
            event.preventDefault();
            if (currentPage < totalPages) {
                currentPage++;
                showPage(currentPage);
                updatePaginationButtons();
            }
        });

        // Inicialização
        generatePaginationNumbers();
        showPage(currentPage);
        updatePaginationButtons();
    </script>
</body>
</html>
