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
            
            // Adicione a classe 'active-page' à página atual
            if (i === currentPage) {
                pageNumber.classList.add('active-page');
            }
    
            pageNumber.onclick = () => {
                currentPage = i;
                showPage(currentPage, filteredRows);
                updatePagination(filteredRows); // Reatualizar a paginação após a mudança de página
            };
    
            pageNumbersContainer.appendChild(pageNumber);
        }
    
        prevSetBtn.disabled = currentSet === 1;
        nextSetBtn.disabled = currentSet === totalSets;
    
        showPage(currentPage, filteredRows);
    }
    
function filterTable() {
    const filterValue = document.getElementById('filterInput').value.toLowerCase();
    filteredRows = tableRows.filter(row => {
        return Array.from(row.cells).some(cell => {
            return cell.textContent.toLowerCase().includes(filterValue); 
        });
    });
    currentPage = 1; 
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
document.addEventListener('keydown', (event) => {
    if (event.key === 'ArrowRight') {
   
        if (currentPage < Math.ceil(filteredRows.length / rowsPerPage)) {
            currentPage++;
            showPage(currentPage, filteredRows);
        }
    } else if (event.key === 'ArrowLeft') {
      
        if (currentPage > 1) {
            currentPage--;
            showPage(currentPage, filteredRows);
        }
    } else if (event.key === 'PageDown') {

        if (currentPage < Math.ceil(filteredRows.length / rowsPerPage)) {
            currentPage++;
            showPage(currentPage, filteredRows);
        }
    } else if (event.key === 'PageUp') {

        if (currentPage > 1) {
            currentPage--;
            showPage(currentPage, filteredRows);
        }
    }

    updatePagination(filteredRows);
});


    updatePagination(tableRows);



     
      function convertToDate(dateStr) {
        const parts = dateStr.split('/'); 
        const day = parts[0];
        const month = parts[1] - 1; 
        const year = parts[2];
        return new Date(year, month, day); 
    }

    let isAsc = false; 
    const prescricaoHeader = document.getElementById('prescricao-header');
    const sortIcon = document.getElementById('sort-icon');

    prescricaoHeader.onclick = function() {
     
        filteredRows.sort((a, b) => {
            const dateA = convertToDate(a.cells[6].textContent.trim()); 
            const dateB = convertToDate(b.cells[6].textContent.trim());
            
            if (isAsc) {
                return dateA - dateB; 
            } else {
                return dateB - dateA; 
            }
        });

        isAsc = !isAsc; 

        if (isAsc) {
            sortIcon.className = 'fa-solid fa-caret-up'; 
        } else {
            sortIcon.className = 'fa-solid fa-caret-down'; 
        }

 
        sortIcon.style.display = 'inline';

      
        currentPage = 1;
        updatePagination(filteredRows); 
    };


    document.getElementById('horas-header').addEventListener('click', function() {
        let table = document.getElementById('table-body');
        let rows = Array.from(table.getElementsByTagName('tr'));
        let isAscending = this.getAttribute('data-order') === 'asc';
    
        // Ordena as linhas com base no valor de "Horas"
        rows.sort(function(rowA, rowB) {
            // Acesse a célula de horas, que deve estar no índice correto
            let horasA = parseFloat(rowA.cells[7].textContent.trim()) || 0; // Ajuste o índice se necessário
            let horasB = parseFloat(rowB.cells[7].textContent.trim()) || 0; // Ajuste o índice se necessário
    
            // Log para verificar os valores
            console.log('Horas A:', horasA, 'Horas B:', horasB);
    
            // Retorna a diferença para ordenação
            return isAscending ? horasA - horasB : horasB - horasA;
        });
    
        // Reordena as linhas na tabela
        rows.forEach(function(row) {
            table.appendChild(row);
        });
    

        this.setAttribute('data-order', isAscending ? 'desc' : 'asc');
    
      
        let sortIcon = document.getElementById('sort-horas-icon');
        if (isAscending) {
            sortIcon.classList.remove('fa-caret-up');
            sortIcon.classList.add('fa-caret-down');
        } else {
            sortIcon.classList.remove('fa-caret-down');
            sortIcon.classList.add('fa-caret-up');
        }
    });
    