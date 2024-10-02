<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabela de Pacientes com Paginação Dinâmica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="teste.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400&display=swap" rel="stylesheet">
</head>
<body>


<?php 
include 'conexao.php'; 
include 'header.php';

function capitalizeFirstLetters($string) {
    return ucwords(strtolower($string));
}

try {

    $query = "
        SELECT 
            HSP.HSP_NUM AS 'IH', 
            HSP.HSP_PAC AS 'REGISTRO', 
            PAC.PAC_NOME AS 'PACIENTE', 
            CNV.CNV_NOME AS 'CONVENIO', 
            LOC.LOC_NOME AS 'LEITO', 
            PSC.PSC_DHINI AS 'PRESCRICAO', 
            ISNULL(ADP.ADP_NOME, '') AS 'DIETA', 
            HSP.HSP_DTHRE AS 'ADMISSÃO', 
            DATEDIFF(year, PAC.PAC_NASC, GETDATE()) AS 'IDADE', 
            HSP.HSP_DTHRA AS 'HSP_DTHRA' 
        FROM 
            HSP 
        INNER JOIN LOC ON HSP_LOC = LOC_COD 
        INNER JOIN PAC ON PAC.PAC_REG = HSP.HSP_PAC 
        INNER JOIN CNV ON CNV_COD = HSP.HSP_CNV 
        LEFT JOIN PSC ON PSC.PSC_HSP = HSP.HSP_NUM AND PSC.PSC_PAC = HSP.HSP_PAC AND PSC.PSC_TIP = 'D' 
        LEFT JOIN ADP ON ADP.ADP_COD = PSC.PSC_ADP AND ADP_TIPO = 'D' 
        WHERE 
            HSP_TRAT_INT = 'I' 
            AND HSP_STAT = 'A' 
            AND PSC.PSC_STAT <> 'S' 
    ";

    if (isset($_POST['filterLast6Hours'])) {
        $query .= " AND HSP.HSP_DTHRE >= DATEADD(HOUR, -6, GETDATE())";
    }


    $query .= " AND PSC.PSC_DHINI = (
        SELECT MAX(PSCMAX.PSC_DHINI) 
        FROM PSC PSCMAX 
        WHERE PSCMAX.PSC_PAC = PSC.PSC_PAC 
        AND PSCMAX.PSC_HSP = PSC.PSC_HSP 
        AND PSCMAX.PSC_TIP = 'D' 
        AND PSCMAX.PSC_STAT = 'A'
    ) ORDER BY PAC.PAC_NOME, LOC.LOC_NOME;";

    
    $result = $connection->query($query)->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) > 0) {
        $groupedPatients = [];
        foreach ($result as $row) {
            $registro = $row['REGISTRO'];
            $patientName = capitalizeFirstLetters($row['PACIENTE']);
            
            if (!isset($groupedPatients[$registro])) {
                $groupedPatients[$registro] = [
                    'REGISTRO' => $registro,
                    'PACIENTE' => $patientName,
                    'CONVENIO' => capitalizeFirstLetters($row['CONVENIO']),
                    'LEITO' => capitalizeFirstLetters($row['LEITO']),
                    'PRESCRICAO' => date('d/m/Y', strtotime($row['PRESCRICAO'])),
                    'DIETAS' => [capitalizeFirstLetters($row['DIETA'])],
                    'ADMISSÃO' => date('d/m/Y H:i', strtotime($row['ADMISSÃO'])),
                    'IDADE' => $row['IDADE'],
                    'ALTA' => !empty($row['HSP_DTHRA']) ? 'SIM' : 'NÃO'
                ];
            } else {
                if (!empty($row['DIETA'])) {
                    $groupedPatients[$registro]['DIETAS'][] = capitalizeFirstLetters($row['DIETA']);
                }
            }
        }

        foreach ($groupedPatients as $registro => &$patient) {
            $patient['DIETAS'] = array_unique($patient['DIETAS']);
        }

    }

} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>


<div class="container-fluid mt-5">
    <div class="row justify-content-center">
        <div class="col-12">

        <form method="POST" action="">
                <button type="submit" class="btn btn-primary mb-3" id="filterLast6Hours" name="filterLast6Hours">Filtrar Últimas 6 Horas</button>
                <button type="submit" class="btn btn-secondary mb-3" id="showAllData" name="showAllData">Mostrar Todos os Dados</button>
            </form>

        
                <div class="mb-3">
                    <input type="text" id="filterInput" class="form-control" placeholder="Filtrar por paciente..." onkeyup="filterTable()">
                </div>
                <div id="progress-container" style="width: 100%; background-color: #f3f3f3; border-radius: 5px; overflow: hidden;">
                    <div id="progress-bar" style="width: 0%; height: 5px; background-color: #001f3f"></div>
                </div>


                <table class="table table-striped table-bordered table-hover">
    <thead style="background-color: green; color:white;">
        <tr>
            <th>Registro</th>
            <th id="paciente-header" style="cursor: pointer;">
                Paciente
                <i id="sort-paciente-icon" class="fa-solid fa-caret-up"></i>
            </th>
            <th id="convenio-header" style="cursor: pointer;">
                Convênio
                <i id="sort-convenio-icon" class="fa-solid fa-caret-up"></i>
            </th>
            <th>Leito</th>
            <th id="prescricao-header" style="min-width: 150px;">
                Prescrição 
                <i id="sort-icon" class="fa-solid fa-caret-up"></i> 
            </th>
            <th>Dieta</th>
            <th id="admissao-header" style="min-width: 150px;">
                Data
                <i id="sort-admissao-icon" class="fa-solid fa-caret-up"></i>
            </th>
            <th id="idade-header" style="cursor: pointer; min-width: 150px;">
                Idade
                <i id="sort-idade-icon" class="fa-solid fa-caret-up"></i>
            </th>
            <th id="alta-header" style="min-width: 150px;">ALTA</th> 
            <th>Acompanhante</th>
        </tr>
    </thead>
            <tbody id="table-body">
                <?php 
                foreach ($groupedPatients as $patient) { 
                ?>
                <tr class="trdados">
                    <td class="text-start align-middle col-1"><?= htmlspecialchars($patient['REGISTRO']); ?></td>
                    <td class="text-start align-middle col-2"><?= htmlspecialchars($patient['PACIENTE']); ?></td>
                    <td class="text-start align-middle col-1"><?= htmlspecialchars($patient['CONVENIO']); ?></td>
                    <td class="text-start align-middle col-2"><?= htmlspecialchars($patient['LEITO']); ?></td>
                    <td class="text-center align-middle col-1"><?= htmlspecialchars($patient['PRESCRICAO']); ?></td>
                    <td class="text-start align-middle col-2"><?= htmlspecialchars(implode(', ', $patient['DIETAS'])); ?></td>


                    <td class="text-start align-middle col-1"><?= htmlspecialchars($patient['ADMISSÃO']); ?></td>
                    <td class="text-center align-middle "><?= htmlspecialchars($patient['IDADE']); ?></td>
                    <td class="text-centro align-middle col-1"><?= htmlspecialchars($patient['ALTA']); ?></td> <!-- Coluna de alta -->
                    <td class="text-start align-middle col-1"><?= htmlspecialchars($patient['ACOMPANHANTE'] ?? ''); ?></td> <!-- Se você tiver a coluna de acompanhante, adicione aqui -->
                </tr>
                <?php 
                } 
                ?>
            </tbody>
        </table>
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
                <div class="pagination-container" id="pagination-container">
                    <button class="btn btn-success" id="prev-set" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div id="page-numbers" class="mx-2"></div>
                    <button class="btn btn-success" id="next-set">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="teste.js"></script>
</body>
</html>