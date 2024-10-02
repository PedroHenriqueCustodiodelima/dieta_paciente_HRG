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

const intervalTime = 10000; 
let autoPageInterval; 

function showPage(page, rows) {
    const start = (page - 1) * rowsPerPage;
    const end = start + rowsPerPage; 

    tableBody.innerHTML = ''; 
    const rowsToDisplay = rows.slice(start, end); 
    if (rowsToDisplay.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="X">Nenhum dado para exibir</td></tr>'; 
    } else {
        rowsToDisplay.forEach(row => tableBody.appendChild(row));
    }

    resetProgressBar(); // Reinicia a barra de progresso ao exibir uma nova página
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
        
        if (i === currentPage) {
            pageNumber.classList.add('active-page');
        }

        pageNumber.onclick = () => {
            currentPage = i;
            clearInterval(autoPageInterval); 
            updateTableAndPagination(filteredRows);
        };

        pageNumbersContainer.appendChild(pageNumber);
    }

    prevSetBtn.disabled = currentSet === 1;
    nextSetBtn.disabled = currentSet === totalSets;

    showPage(currentPage, filteredRows);
}

function updateTableAndPagination(rows) {
    showPage(currentPage, rows); 
    updatePagination(rows); 
}

function nextPage() {
    const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
    currentPage++;
    if (currentPage > totalPages) {
        currentPage = 1; 
    }
    
    updateTableAndPagination(filteredRows);
}

function startAutoPagination() {
    resetProgressBar(); // Reinicia a barra de progresso ao iniciar a auto-navegação
    autoPageInterval = setInterval(() => {
        nextPage();
        updateProgressBar(); // Atualiza a barra de progresso após mudar de página
    }, intervalTime); 
}

function resetProgressBar() {
    const progressBar = document.getElementById('progress-bar');
    progressBar.style.width = '0%'; 
    updateProgressBar(); // Inicia a animação da barra de progresso
}

function updateProgressBar() {
    const progressBar = document.getElementById('progress-bar');
    let startTime = null; 
    const duration = intervalTime; 

    function animateProgress(timestamp) {
        if (!startTime) startTime = timestamp; 
        const elapsed = timestamp - startTime; 
        const progress = Math.min((elapsed / duration) * 100, 100); 

        progressBar.style.width = progress + '%'; 

        if (progress < 100) {
            requestAnimationFrame(animateProgress); 
        }
    }

    requestAnimationFrame(animateProgress); 
}

function stopAutoPagination() {
    clearInterval(autoPageInterval);
}

function filterTable() {
    const filterValue = document.getElementById('filterInput').value.toLowerCase();
    filteredRows = filterValue ? tableRows.filter(row => {
        return Array.from(row.cells).some(cell => {
            return cell.textContent.toLowerCase().includes(filterValue);
        });
    }) : tableRows;
    
    currentPage = 1; 
    updateTableAndPagination(filteredRows);
}

// Eventos de navegação de conjuntos
prevSetBtn.onclick = () => {
    if (currentSet > 1) {
        currentSet--;
        updateTableAndPagination(filteredRows);
    }
};

nextSetBtn.onclick = () => {
    const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
    const totalSets = Math.ceil(totalPages / pagesPerSet);
    if (currentSet < totalSets) {
        currentSet++;
        updateTableAndPagination(filteredRows);
    }
};

// Controle de navegação por teclas
document.addEventListener('keydown', (event) => {
    if (event.key === 'ArrowRight' && currentPage < Math.ceil(filteredRows.length / rowsPerPage)) {
        currentPage++;
    } else if (event.key === 'ArrowLeft' && currentPage > 1) {
        currentPage--;
    } else if (event.key === 'PageDown' && currentPage < Math.ceil(filteredRows.length / rowsPerPage)) {
        currentPage++;
    } else if (event.key === 'PageUp' && currentPage > 1) {
        currentPage--;
    }
    updateTableAndPagination(filteredRows);
});

// Funções de ordenação
function convertToDate(dateStr) {
    const parts = dateStr.split('/'); 
    return new Date(parts[2], parts[1] - 1, parts[0]); 
}

let isAsc = false; 
document.getElementById('prescricao-header').onclick = function() {
    filteredRows.sort((a, b) => {
        const dateA = convertToDate(a.cells[4].textContent.trim()); 
        const dateB = convertToDate(b.cells[4].textContent.trim());
        return isAsc ? dateA - dateB : dateB - dateA; 
    });
    isAsc = !isAsc; 
    currentPage = 1; 
    updateTableAndPagination(filteredRows); 
};

// Função para converter string de data e hora
function convertToDateTime(dateStr) {
    const [datePart, timePart] = dateStr.split(' ');
    const parts = datePart.split('/'); 
    const timeParts = timePart.split(':'); 
    return new Date(parts[2], parts[1] - 1, parts[0], timeParts[0], timeParts[1]);
}

let isAdmissaoAsc = false; 
document.getElementById('admissao-header').onclick = function() {
    filteredRows.sort((a, b) => {
        const dateTimeA = convertToDateTime(a.cells[6].textContent.trim()); 
        const dateTimeB = convertToDateTime(b.cells[6].textContent.trim()); 
        return isAdmissaoAsc ? dateTimeA - dateTimeB : dateTimeB - dateTimeA; 
    });
    isAdmissaoAsc = !isAdmissaoAsc; 
    currentPage = 1; 
    updateTableAndPagination(filteredRows); 
};

// Outras funções de ordenação para "Convenio", "Idade", "Paciente" e "Horas" devem seguir o mesmo padrão

// Inicia a exibição inicial
updateTableAndPagination(filteredRows);
startAutoPagination();
