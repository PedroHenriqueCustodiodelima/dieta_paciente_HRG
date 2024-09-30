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
        $query = $connection->query("
            SELECT 
                HSP.HSP_NUM AS 'IH',
                HSP.HSP_PAC AS 'REGISTRO',
                PAC.PAC_NOME AS 'PACIENTE',
                CNV.CNV_NOME AS 'CONVENIO',
                RTRIM(STR.STR_NOME) AS 'UNIDADE',
                LOC.LOC_NOME AS 'LEITO',
                ISNULL(PSC.PSC_DHINI, '') AS 'PRESCRICAO',
                ISNULL(ADP.ADP_NOME, '') AS 'DIETA',
                DATEDIFF(hour, HSP.HSP_DTHRE, GETDATE()) AS 'horas'
            FROM 
                HSP 
            INNER JOIN LOC ON HSP_LOC = LOC_COD 
            INNER JOIN STR ON STR_COD = LOC_STR
            INNER JOIN PAC ON PAC.PAC_REG = HSP.HSP_PAC
            INNER JOIN CNV ON CNV_COD = HSP.HSP_CNV
            LEFT JOIN PSC ON PSC.PSC_HSP = HSP.HSP_NUM 
                AND PSC.PSC_PAC = HSP.HSP_PAC 
                AND PSC.PSC_TIP = 'D'
            LEFT JOIN ADP ON ADP.ADP_COD = PSC.PSC_ADP 
                AND ADP_TIPO = 'D'
            WHERE 
                HSP_TRAT_INT = 'I'
                AND HSP_STAT = 'A'
                AND PSC.PSC_STAT <> 'S'
                AND PSC.PSC_DHINI = (
                    SELECT MAX(PSCMAX.PSC_DHINI) 
                    FROM PSC PSCMAX 
                    WHERE PSCMAX.PSC_PAC = PSC.PSC_PAC 
                    AND PSCMAX.PSC_HSP = PSC.PSC_HSP
                    AND PSCMAX.PSC_TIP = 'D'
                    AND PSCMAX.PSC_STAT = 'A'
                )
            ORDER BY PAC.PAC_NOME, STR.STR_NOME, LOC.LOC_NOME;
        ");
        $result = $query->fetchAll(PDO::FETCH_ASSOC); 
        if (count($result) > 0) {
            // Agrupando os dados por nome do paciente
            $groupedPatients = [];
            foreach ($result as $row) {
                $patientName = capitalizeFirstLetters($row['PACIENTE']);
                if (!isset($groupedPatients[$patientName])) {
                    $groupedPatients[$patientName] = [
                        'IH' => $row['IH'],
                        'REGISTRO' => $row['REGISTRO'],
                        'PACIENTE' => $patientName,
                        'CONVENIO' => capitalizeFirstLetters($row['CONVENIO']),
                        'UNIDADE' => capitalizeFirstLetters($row['UNIDADE']),
                        'LEITO' => capitalizeFirstLetters($row['LEITO']),
                        'PRESCRICAO' => date('d/m/Y', strtotime($row['PRESCRICAO'])),
                        'DIETAS' => [capitalizeFirstLetters($row['DIETA'])],
                        'horas' => $row['horas']
                    ];
                } else {
                    // Adiciona a dieta à lista de dietas
                    $groupedPatients[$patientName]['DIETAS'][] = capitalizeFirstLetters($row['DIETA']);
                }
            }
?>
<div class="container-fluid mt-5">
    <div class="row justify-content-center">
        <div class="col-12">
                <div class="mb-3">
                    <input type="text" id="filterInput" class="form-control" placeholder="Filtrar por paciente..." onkeyup="filterTable()">
                </div>
                <table class="table table-striped table-bordered table-hover ">
                    <thead style="background-color: green; color:white;">
                        <tr>
                            <th>IH</th>
                            <th>Registro</th>
                            <th>Paciente</th>
                            <th>Convênio</th>
                            <th>Unidade</th>
                            <th>Leito</th>
                            <th>Prescrição</th>
                            <th>Dieta</th>
                            <th>Horas</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        <?php 
                        foreach ($groupedPatients as $patient) { 
                        ?>
                        <tr class="trdados">
                            <td class="text-center align-middle"><?= htmlspecialchars($patient['IH']); ?></td>
                            <td class="text-center align-middle"><?= htmlspecialchars($patient['REGISTRO']); ?></td>
                            <td class="text-center align-middle"><?= htmlspecialchars($patient['PACIENTE']); ?></td>
                            <td class="text-center align-middle"><?= htmlspecialchars($patient['CONVENIO']); ?></td>
                            <td class="text-center align-middle"><?= htmlspecialchars($patient['UNIDADE']); ?></td>
                            <td class="text-center align-middle"><?= htmlspecialchars($patient['LEITO']); ?></td>
                            <td class="text-center align-middle"><?= htmlspecialchars($patient['PRESCRICAO']); ?></td>
                            <td class="text-center align-middle col-3"><?= htmlspecialchars(implode(', ', $patient['DIETAS'])); ?></td>
                            <td class="text-center align-middle"><?= htmlspecialchars($patient['horas']); ?></td>
                        </tr>
                        <?php } ?>
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
<?php 
        } else {
            echo "<p>Nenhum paciente encontrado.</p>";
        }
    } catch (Exception $e) {
        echo "Erro ao executar a consulta: " . $e->getMessage();
    }
?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="teste.js"></script>
</body>
</html>