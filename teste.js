const rowsPerPage = 20; 
    const pagesPerSet = 10; 
    let currentPage = 1;
    let currentSet = 1;
    const tableBody = document.getElementById('table-body');
    let tableRows = Array.from(tableBody.querySelectorAll('tr')); 
    let filteredRows = tableRows; 
    const paginationContainer = document.getElementById('pagination-container');
    const prevSetBtn = document.getElementById('prev-set');
    const nextSetBtn = document.getElementById('next-set');
    const pageNumbersContainer = document.getElementById('page-numbers');
    function showPage(page, rows) {
        const start = (page - 1) * rowsPerPage;
        const end = page * rowsPerPage;
        tableBody.innerHTML = '';
        const rowsToDisplay = rows.slice(start, end);
        rowsToDisplay.forEach(row => tableBody.appendChild(row));
    }
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
    // Filtro de pacientes
function filterTable() {
    const filterValue = document.getElementById('filterInput').value.toLowerCase();
    filteredRows = tableRows.filter(row => {
        // Verifica cada célula na linha
        return Array.from(row.cells).some(cell => {
            return cell.textContent.toLowerCase().includes(filterValue); // Retorna true se o valor do filtro estiver em qualquer célula
        });
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
    // Adiciona a navegação por teclado
document.addEventListener('keydown', (event) => {
    if (event.key === 'ArrowRight') {
        // Navegar para a próxima página
        if (currentPage < Math.ceil(filteredRows.length / rowsPerPage)) {
            currentPage++;
            showPage(currentPage, filteredRows);
        }
    } else if (event.key === 'ArrowLeft') {
        // Navegar para a página anterior
        if (currentPage > 1) {
            currentPage--;
            showPage(currentPage, filteredRows);
        }
    } else if (event.key === 'PageDown') {
        // Navegar para a próxima página
        if (currentPage < Math.ceil(filteredRows.length / rowsPerPage)) {
            currentPage++;
            showPage(currentPage, filteredRows);
        }
    } else if (event.key === 'PageUp') {
        // Navegar para a página anterior
        if (currentPage > 1) {
            currentPage--;
            showPage(currentPage, filteredRows);
        }
    }

    // Atualiza a paginação após a navegação
    updatePagination(filteredRows);
});

    // Inicializa a tabela
    updatePagination(tableRows);