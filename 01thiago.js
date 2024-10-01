// Função para aplicar as cores baseadas nas horas
function applyColorBasedOnHours() {
    const rows = document.querySelectorAll('#table-body tr');
    rows.forEach(row => {
        const hoursCell = row.querySelector('td:nth-child(9)'); // Coluna de horas
        const prescriptionCell = row.querySelector('td:nth-child(7)'); // Coluna de prescrição
        const hours = parseInt(hoursCell.textContent, 10);

        if (hours <= 24) {
            prescriptionCell.style.backgroundColor = 'lightgreen'; // Até 24 horas, verde claro
        } else if (hours <= 72) {
            prescriptionCell.style.backgroundColor = 'yellow'; // 25 a 72 horas, amarelo
        } else {
            prescriptionCell.style.backgroundColor = 'red'; // Mais de 72 horas, vermelho
        }
    });
}