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
<<<<<<< HEAD
let isConvenioAsc = false; 


document.getElementById('convenio-header').onclick = function() {
    filteredRows.sort((a, b) => {
        const convenioA = a.cells[2].textContent.trim().toLowerCase(); 
        const convenioB = b.cells[2].textContent.trim().toLowerCase(); 

        if (convenioA < convenioB) return isConvenioAsc ? -1 : 1;
        if (convenioA > convenioB) return isConvenioAsc ? 1 : -1;
        return 0;
    });

    isConvenioAsc = !isConvenioAsc; 

 
    const sortConvenioIcon = document.getElementById('sort-convenio-icon');
    sortConvenioIcon.className = isConvenioAsc ? 'fa-solid fa-caret-up sort-icon rotate-up' : 'fa-solid fa-caret-down sort-icon rotate-down';
    sortConvenioIcon.style.display = 'inline';

    currentPage = 1; 
    updatePagination(filteredRows); 
};
let isIdadeAsc = false; 

document.getElementById('idade-header').onclick = function() {
    filteredRows.sort((a, b) => {
        const idadeA = parseInt(a.cells[7].textContent.trim(), 10); 
        const idadeB = parseInt(b.cells[7].textContent.trim(), 10); 

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

        
        const [hoursA, minutesA] = horasA.split(':').map(Number);
        const [hoursB, minutesB] = horasB.split(':').map(Number);
        
        const totalMinutesA = hoursA * 60 + minutesA;
        const totalMinutesB = hoursB * 60 + minutesB;

        return isHorasAsc ? totalMinutesA - totalMinutesB : totalMinutesB - totalMinutesA; 
    });

    isHorasAsc = !isHorasAsc; 

   
    const sortHorasIcon = document.getElementById('sort-horas-icon');
    sortHorasIcon.className = isHorasAsc ? 'fa-solid fa-caret-up sort-icon rotate-up' : 'fa-solid fa-caret-down sort-icon rotate-down';
    sortHorasIcon.style.display = 'inline';

    currentPage = 1; 
    updatePagination(filteredRows); 
};


=======

// Outras funções de ordenação para "Convenio", "Idade", "Paciente" e "Horas" devem seguir o mesmo padrão

// Inicia a exibição inicial
updateTableAndPagination(filteredRows);
startAutoPagination();
>>>>>>> 20f7ecab1f16eab702ca4ebb797471d1d8ae6e08
