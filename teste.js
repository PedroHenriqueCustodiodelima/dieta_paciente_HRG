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
let progressInterval; 

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
            clearInterval(progressInterval); 
            updateTableAndPagination(filteredRows);
            updateProgressBar(); 
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
    updateProgressBar(); 
}

function startAutoPagination() {
    updateProgressBar(); 
    autoPageInterval = setInterval(nextPage, intervalTime); 
}

function updateProgressBar() {
    const progressBar = document.getElementById('progress-bar');
    progressBar.style.width = '0%'; 
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
    clearInterval(progressInterval);
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

prevSetBtn.onclick = () => {
    currentSet--;
    updateTableAndPagination(filteredRows);
};

nextSetBtn.onclick = () => {
    currentSet++;
    updateTableAndPagination(filteredRows);
};

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

let isAsc = false; 
const prescricaoHeader = document.getElementById('prescricao-header');
const sortIcon = document.getElementById('sort-icon');

prescricaoHeader.onclick = function() {
    filteredRows.sort((a, b) => {
        const dateA = convertToDate(a.cells[4].textContent.trim()); 
        const dateB = convertToDate(b.cells[4].textContent.trim());
        
        return isAsc ? dateA - dateB : dateB - dateA; 
    });

    isAsc = !isAsc; 
    sortIcon.className = isAsc ? 'fa-solid fa-caret-up' : 'fa-solid fa-caret-down';
    sortIcon.style.display = 'inline';

    currentPage = 1; 
    updatePagination(filteredRows); 
};

function convertToDate(dateStr) {
    const parts = dateStr.split('/'); 
    return new Date(parts[2], parts[1] - 1, parts[0]); 
}

let isAdmissaoAsc = false; 
const admissaoHeader = document.getElementById('admissao-header');
const sortAdmissaoIcon = document.getElementById('sort-admissao-icon'); 

admissaoHeader.onclick = function() {
    filteredRows.sort((a, b) => {
        const dateTimeA = convertToDateTime(a.cells[6].textContent.trim()); 
        const dateTimeB = convertToDateTime(b.cells[6].textContent.trim()); 
        
        return isAdmissaoAsc ? dateTimeA - dateTimeB : dateTimeB - dateTimeA; 
    });

    isAdmissaoAsc = !isAdmissaoAsc; 
    sortAdmissaoIcon.className = isAdmissaoAsc ? 'fa-solid fa-caret-up sort-icon rotate-up' : 'fa-solid fa-caret-down sort-icon rotate-down';
    sortAdmissaoIcon.style.display = 'inline';

    currentPage = 1; 
    updatePagination(filteredRows); 
};

function convertToDateTime(dateStr) {
    const [datePart, timePart] = dateStr.split(' ');
    const parts = datePart.split('/'); 
    const timeParts = timePart.split(':'); 
    return new Date(parts[2], parts[1] - 1, parts[0], timeParts[0], timeParts[1]);
}

let isConvenioAsc = false; // Para controlar a ordenação da coluna de Convênio
document.getElementById('convenio-header').onclick = function() {
    filteredRows.sort((a, b) => {
        const convenioA = a.cells[2].textContent.trim().toLowerCase(); 
        const convenioB = b.cells[2].textContent.trim().toLowerCase(); 

        return isConvenioAsc ? convenioA.localeCompare(convenioB) : convenioB.localeCompare(convenioA);
    });

    isConvenioAsc = !isConvenioAsc; 
    const sortConvenioIcon = document.getElementById('sort-convenio-icon');
    sortConvenioIcon.className = isConvenioAsc ? 'fa-solid fa-caret-up sort-icon rotate-up' : 'fa-solid fa-caret-down sort-icon rotate-down';
    sortConvenioIcon.style.display = 'inline';

    currentPage = 1; 
    updatePagination(filteredRows); 
};

let isIdadeAsc = false; // Para controlar a ordenação da coluna de Idade
document.getElementById('idade-header').onclick = function() {
    filteredRows.sort((a, b) => {
        const idadeA = parseInt(a.cells[3].textContent.trim()); 
        const idadeB = parseInt(b.cells[3].textContent.trim()); 

        return isIdadeAsc ? idadeA - idadeB : idadeB - idadeA; 
    });

    isIdadeAsc = !isIdadeAsc; 
    const sortIdadeIcon = document.getElementById('sort-idade-icon');
    sortIdadeIcon.className = isIdadeAsc ? 'fa-solid fa-caret-up sort-icon rotate-up' : 'fa-solid fa-caret-down sort-icon rotate-down';
    sortIdadeIcon.style.display = 'inline';

    currentPage = 1; 
    updatePagination(filteredRows); 
};

document.getElementById('filterInput').addEventListener('input', filterTable);
updateTableAndPagination(filteredRows);
startAutoPagination();
