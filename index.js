const rowsPerPage = 15; 
const pagesPerSet = 10; 
let currentPage = 1;
let currentSet = 1; 
const tableRows = document.querySelectorAll('#table-body tr');
const totalPages = Math.ceil(tableRows.length / rowsPerPage);
const paginationContainer = document.getElementById('pagination-container');
const prevSetBtn = document.getElementById('prev-set');
const nextSetBtn = document.getElementById('next-set');
const pageNumbersContainer = document.getElementById('page-numbers');
      
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

function updateNavigationButtons() {
    prevSetBtn.disabled = currentSet === 1;
    nextSetBtn.disabled = currentSet * pagesPerSet >= totalPages;
}

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
    const tableBody = document.getElementById('table-body');
    tableBody.innerHTML = '';
    const filteredRows = [];
    tableRows.forEach(row => {
        let matchFound = false; 
        for (let cell of row.cells) {
            if (cell.textContent.toLowerCase().includes(filterValue)) {
                matchFound = true;
                break; 
            }
        }
        if (matchFound) {
            const newRow = row.cloneNode(true);
            filteredRows.push(newRow);
            filteredCount++;
        }
    });
    if (filteredCount > 0) {
        filteredRows.forEach(filteredRow => tableBody.appendChild(filteredRow));
    } else {
        const noResultsRow = document.createElement('tr');
        noResultsRow.innerHTML = '<td colspan="9" style="text-align:center;">Nenhum resultado encontrado.</td>';
        tableBody.appendChild(noResultsRow);
    }
    currentPage = 1; 
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
    
document.getElementById('scrollToTop').onclick = function() {
    window.scrollTo({top: 0, behavior: 'smooth'});
};

function showKeyboardNavigationMessage() {
    const messageElement = document.getElementById('keyboard-navigation-message');
    messageElement.style.display = 'block'; 
}

function isDesktop() {
    return navigator.userAgent.indexOf('Mobi') === -1; 
}

window.onload = function() {
    if (isDesktop()) {
        showKeyboardNavigationMessage(); 
    }
};

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

function handleKeydown(event) {
    if (event.key === "ArrowLeft") {
        if (currentPage > 1) {
            currentPage--;
            showPage(currentPage);
        }
    } else if (event.key === "ArrowRight") {
        if (currentPage < totalPages) {
            currentPage++;
            showPage(currentPage);
        }
    }
}
window.addEventListener('keydown', handleKeydown);
