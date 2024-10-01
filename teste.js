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
        
        if (i === currentPage) {
            pageNumber.classList.add('active-page');
        }

        pageNumber.onclick = () => {
            currentPage = i;
            clearInterval(autoPageInterval); // Limpa o intervalo ao clicar
            clearInterval(progressInterval); // Limpa a barra de progresso ao clicar
            updateTableAndPagination(filteredRows);
            updateProgressBar(); // Reinicia a barra de progresso
        };

        pageNumbersContainer.appendChild(pageNumber);
    }

    prevSetBtn.disabled = currentSet === 1;
    nextSetBtn.disabled = currentSet === totalSets;

    showPage(currentPage, filteredRows);
}

// Função para avançar para a próxima página
function nextPage() {
    currentPage++;
    const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
    if (currentPage > totalPages) {
        currentPage = 1; // Volta para a primeira página
    }
    updateTableAndPagination(filteredRows);
    updateProgressBar(); // Atualiza a barra de progresso
}

// Inicia a paginação automática
function startAutoPagination() {
    updateProgressBar(); // Inicia a barra de progresso
    autoPageInterval = setInterval(nextPage, intervalTime);
}

// Atualiza a barra de progresso
function updateProgressBar() {
    const progressBar = document.getElementById('progress-bar');
    progressBar.style.width = '0%'; // Reseta a largura da barra
    let width = 0; // Inicializa a largura da barra
    const increment = 100 / (intervalTime / 100); // Incremento da barra

    clearInterval(progressInterval); // Limpa qualquer intervalo anterior
    progressInterval = setInterval(() => {
        if (width >= 100) {
            clearInterval(progressInterval); // Para quando a barra está cheia
        } else {
            width++;
            progressBar.style.width = width + '%'; // Atualiza a largura da barra
        }
    }, 100); // Atualiza a barra a cada 100ms
}

// Parar a paginação automática (opcional, caso queira adicionar um botão)
function stopAutoPagination() {
    clearInterval(autoPageInterval);
    clearInterval(progressInterval);
}

// Mostrar a primeira página inicialmente
showPage(currentPage, filteredRows);
startAutoPagination(); // Inicia a paginação automática














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

function updateTableAndPagination(rows) {
    showPage(currentPage, rows);
    updatePagination(rows);
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
    showPage(currentPage, filteredRows);
    updatePagination(filteredRows);
});


function convertToDate(dateStr) {
    const parts = dateStr.split('/'); 
    return new Date(parts[2], parts[1] - 1, parts[0]); 
}

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

function convertToDateTime(dateStr) {
    const [datePart, timePart] = dateStr.split(' ');
    const parts = datePart.split('/'); 
    const timeParts = timePart.split(':'); 

    return new Date(parts[2], parts[1] - 1, parts[0], timeParts[0], timeParts[1]);
}

const admissaoHeader = document.getElementById('admissao-header');
const sortAdmissaoIcon = document.getElementById('sort-admissao-icon'); 
function convertToDateTime(dateStr) {
    const [datePart, timePart] = dateStr.split(' ');
    const parts = datePart.split('/'); 
    const timeParts = timePart.split(':'); 
    return new Date(parts[2], parts[1] - 1, parts[0], timeParts[0], timeParts[1]);
}

let isAdmissaoAsc = false; 

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
let isConvenioAsc = false; // Para controlar a ordenação da coluna de Convênio

// Evento de clique no cabeçalho de Convênio para ordenar
document.getElementById('convenio-header').onclick = function() {
    filteredRows.sort((a, b) => {
        const convenioA = a.cells[2].textContent.trim().toLowerCase(); // Índice 2, ajuste se necessário
        const convenioB = b.cells[2].textContent.trim().toLowerCase(); // Índice 2, ajuste se necessário

        if (convenioA < convenioB) return isConvenioAsc ? -1 : 1;
        if (convenioA > convenioB) return isConvenioAsc ? 1 : -1;
        return 0;
    });

    isConvenioAsc = !isConvenioAsc; 

    // Atualização do ícone
    const sortConvenioIcon = document.getElementById('sort-convenio-icon');
    sortConvenioIcon.className = isConvenioAsc ? 'fa-solid fa-caret-up sort-icon rotate-up' : 'fa-solid fa-caret-down sort-icon rotate-down';
    sortConvenioIcon.style.display = 'inline';

    currentPage = 1; // Reseta para a primeira página
    updatePagination(filteredRows); 
};
let isIdadeAsc = false; // Para controlar a ordenação da coluna de Idade

// Evento de clique no cabeçalho de Idade para ordenar
document.getElementById('idade-header').onclick = function() {
    filteredRows.sort((a, b) => {
        const idadeA = parseInt(a.cells[7].textContent.trim(), 10); // Índice 7, ajuste se necessário
        const idadeB = parseInt(b.cells[7].textContent.trim(), 10); // Índice 7, ajuste se necessário

        return isIdadeAsc ? idadeA - idadeB : idadeB - idadeA; 
    });

    isIdadeAsc = !isIdadeAsc; 

    const sortIdadeIcon = document.getElementById('sort-idade-icon');
    sortIdadeIcon.className = isIdadeAsc ? 'fa-solid fa-caret-up sort-icon rotate-up' : 'fa-solid fa-caret-down sort-icon rotate-down';
    sortIdadeIcon.style.display = 'inline';

    currentPage = 1; 
    updatePagination(filteredRows); 
};
let isPacienteAsc = false; 
document.getElementById('paciente-header').onclick = function() {
    filteredRows.sort((a, b) => {
        const pacienteA = a.cells[1].textContent.trim().toLowerCase(); 
        const pacienteB = b.cells[1].textContent.trim().toLowerCase(); 

        if (pacienteA < pacienteB) return isPacienteAsc ? -1 : 1;
        if (pacienteA > pacienteB) return isPacienteAsc ? 1 : -1;
        return 0;
    });

    isPacienteAsc = !isPacienteAsc; 

    const sortPacienteIcon = document.getElementById('sort-paciente-icon');
    sortPacienteIcon.className = isPacienteAsc ? 'fa-solid fa-caret-up sort-icon rotate-up' : 'fa-solid fa-caret-down sort-icon rotate-down';
    sortPacienteIcon.style.display = 'inline';

    currentPage = 1; 
    updatePagination(filteredRows); 
};
let isHorasAsc = false; 

document.getElementById('horas-header').onclick = function() {
    filteredRows.sort((a, b) => {
        const horasA = a.cells[7].textContent.trim(); 
        const horasB = b.cells[7].textContent.trim(); 

        // Conversão de HH:mm para minutos para comparação
        const [hoursA, minutesA] = horasA.split(':').map(Number);
        const [hoursB, minutesB] = horasB.split(':').map(Number);
        
        const totalMinutesA = hoursA * 60 + minutesA;
        const totalMinutesB = hoursB * 60 + minutesB;

        return isHorasAsc ? totalMinutesA - totalMinutesB : totalMinutesB - totalMinutesA; 
    });

    isHorasAsc = !isHorasAsc; 

    // Atualização do ícone
    const sortHorasIcon = document.getElementById('sort-horas-icon');
    sortHorasIcon.className = isHorasAsc ? 'fa-solid fa-caret-up sort-icon rotate-up' : 'fa-solid fa-caret-down sort-icon rotate-down';
    sortHorasIcon.style.display = 'inline';

    currentPage = 1; 
    updatePagination(filteredRows); 
};