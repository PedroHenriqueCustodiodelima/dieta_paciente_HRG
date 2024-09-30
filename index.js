
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

        showPage(currentPage);
        updatePageNumbers();
        updateNavigationButtons();
        window.onscroll = function() {
            const button = document.getElementById('scrollToTop');
            if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
                button.style.display = "block";
            } else {
                button.style.display = "none";
            }
        };
    
        // Define a ação do botão para rolar para o topo
        document.getElementById('scrollToTop').onclick = function() {
            window.scrollTo({top: 0, behavior: 'smooth'});
        };