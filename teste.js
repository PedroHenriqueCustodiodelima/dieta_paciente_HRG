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

const controlButton = document.getElementById('control-button');
let isPlaying = false;

function showPage(page, rows) {
    const start = (page - 1) * rowsPerPage;
    const end = start + rowsPerPage; 

    tableBody.innerHTML = ''; 
    const rowsToDisplay = rows.slice(start, end); 
    if (rowsToDisplay.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="X">Sem pacientes internados</td></tr>'; 
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
            resetProgressBar(); // Resetar a barra de progresso ao mudar de página
        };

        pageNumbersContainer.appendChild(pageNumber);
    }

    prevSetBtn.disabled = currentSet === 1;
    nextSetBtn.disabled = currentSet === totalSets;

    showPage(currentPage, filteredRows);
}

// Atualize a função updateTableAndPagination se necessário
function updateTableAndPagination(rows) {
    showPage(currentPage, rows);
    updatePagination(rows);
}

function resetProgressBar() {
    const progressBar = document.getElementById('progress-bar');
    progressBar.style.width = '0%'; // Redefinir a largura da barra para 0%
    updateProgressBar(); // Iniciar a barra de progresso novamente
}

function nextPage() {
    const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
    console.log(`Total de páginas: ${totalPages}, Página atual antes: ${currentPage}`);

    if (currentPage < totalPages) {
        currentPage++;
    } else {
        currentPage = 1; // Se estiver na última, vai para a primeira
    }

    console.log(`Página atual após: ${currentPage}`);
    updateTableAndPagination(filteredRows);
}

function startAutoPagination() {
    if (!isPlaying) {
        updateTableAndPagination(filteredRows); // Atualiza a tabela para a página atual
        updateProgressBar(); 
        autoPageInterval = setInterval(nextPage, intervalTime); 
        isPlaying = true; // Defina isPlaying como true aqui
    }
}

function updateProgressBar() {
    const progressBar = document.getElementById('progress-bar');
    let currentWidth = parseFloat(progressBar.style.width) || 0; // Captura a largura atual
    let startTime = null; 
    const duration = intervalTime; // Duração total do progresso

    function animateProgress(timestamp) {
        if (!startTime) startTime = timestamp; 
        const elapsed = timestamp - startTime; 
        const progress = Math.min((elapsed / duration) * 100 + currentWidth, 100); // Adiciona a largura atual

        progressBar.style.width = progress + '%'; 

        if (progress < 100) {
            progressInterval = requestAnimationFrame(animateProgress); 
        } else {
            resetProgressBar(); // Reinicia a barra quando atingir 100%
            startAutoPagination(); // Reinicia a paginação automática
        }
    }

    progressInterval = requestAnimationFrame(animateProgress); 
}

function stopAutoPagination() {
    clearInterval(autoPageInterval);
    if (progressInterval) {
        cancelAnimationFrame(progressInterval); // Para a animação da barra de progresso
    }
    const progressBar = document.getElementById('progress-bar');
    const currentWidth = parseFloat(progressBar.style.width); // Obtém a largura atual da barra
    progressBar.style.width = currentWidth + '%'; // Mantém a largura atual
    isPlaying = false; // Defina isPlaying como false aqui
}

// Controlar a funcionalidade de play e pause
controlButton.onclick = () => {
    if (isPlaying) {
        stopAutoPagination(); // Para a paginação automática
        controlButton.innerHTML = '<i class="fa-solid fa-play"></i>'; // Muda para ícone de play
    } else {
        startAutoPagination(); 
        controlButton.innerHTML = '<i class="fa-solid fa-pause"></i>'; 
    }
    // Remover a alternância de isPlaying aqui
};

updateTableAndPagination(filteredRows);
