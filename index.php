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
    $pageTitle = "Tabela de Pacientes";         
    include 'header.php'; 
    ?>
    <div class="container mt-5">
    
        <table class="table table-striped table-bordered table-hover ">
            <thead style="background-color: green; color:white;">
                <tr>
                    <th >Data</th>
                    <th>Leito</th>
                    <th>Dieta</th>
                    <th>Unidade</th>
                    <th>Nome</th>
                    <th>Registro</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <tr class="trdados">
                    <td>24/09/2024</td>
                    <td>102</td>
                    <td>Normal</td>
                    <td>13</td>

                    <td>João da Silva</td>
                    <td>12345</td>
                </tr>
                <tr class="trdados">
                    <td>24/09/2024</td>
                    <td>103</td>
                    <td>Sem lactose</td>
                    <td>1</td>
                    <td>Maria Oliveira</td>
                    <td>67890</td>
                </tr>
                <tr class="trdados">
                    <td>25/09/2024</td>
                    <td>104</td>
                    <td>Vegetariana</td>
                    <td>15</td>
                    <td>Pedro Souza</td>
                    <td>54321</td>
                </tr>
                <tr class="trdados">
                    <td>25/09/2024</td>
                    <td>105</td>
                    <td>Hipossódica</td>
                    <td>13</td>
                    <td>Ana Pereira</td>
                    <td>98765</td>
                </tr>
                <tr class="trdados">
                    <td>26/09/2024</td>
                    <td>106</td>
                    <td>Sem glúten</td>
                    <td>3</td>
                    <td>Carlos Costa</td>
                    <td>11223</td>
                </tr>
                <tr class="trdados">
                    <td>26/09/2024</td>
                    <td>107</td>
                    <td>Normal</td>
                    <td>17</td>
                    <td>Beatriz Lima</td>
                    <td>33445</td>
                </tr>
                <tr class="trdados" >
                    <td>27/09/2024</td>
                    <td>108</td>
                    <td>Vegetariana</td>
                    <td>8</td>
                    <td>Felipe Santos</td>
                    <td>55667</td>
                </tr>
                <tr class="trdados">
                    <td>27/09/2024</td>
                    <td>109</td>
                    <td>Sem lactose</td>
                    <td>11</td>
                    <td>Luana Matos</td>
                    <td>77889</td>
                </tr>
                <tr class="trdados">
                    <td>28/09/2024</td>
                    <td>110</td>
                    <td>Normal</td>
                    <td>9</td>
                    <td>Gabriel Souza</td>
                    <td>99001</td>
                </tr>
                <tr class="trdados">
                    <td>28/09/2024</td>
                    <td>111</td>
                    <td>Hipossódica</td>
                    <td>13</td>
                    <td>Aline Rocha</td>
                    <td>11324</td>
                </tr>
                <tr class="trdados">
                    <td>29/09/2024</td>
                    <td>112</td>
                    <td>Sem glúten</td>
                    <td>1</td>
                    <td>Renato Barbosa</td>
                    <td>22345</td>
                </tr>
                <tr class="trdados">
                    <td>29/09/2024</td>
                    <td>113</td>
                    <td>Normal</td>
                    <td>13</td>
                    <td>Mariana Ferraz</td>
                    <td>33456</td>
                </tr>
                <tr class="trdados">
                    <td>30/09/2024</td>
                    <td>114</td>
                    <td>Sem lactose</td>
                    <td>12</td>
                    <td>Ricardo Silva</td>
                    <td>44567</td>
                </tr>
                <tr class="trdados">
                    <td>30/09/2024</td>
                    <td>115</td>
                    <td>Vegetariana</td>
                    <td>10</td>
                    <td>Sara Martins</td>
                    <td>55678</td>
                </tr>
                <tr class="trdados">
                    <td>01/10/2024</td>
                    <td>116</td>
                    <td>Normal</td>
                    <td>13</td>
                    <td>Paulo Dias</td>
                    <td>66789</td>
                </tr>
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
