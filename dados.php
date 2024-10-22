<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabela de Pacientes com Paginação Dinâmica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/teste1.css">
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
    $hoursFilter = 24; 

    if (isset($_POST['filter'])) {
        switch ($_POST['filter']) {
            case 'last6hours':
                $hoursFilter = 6;
                break;
            case 'last12hours':
                $hoursFilter = 12;
                break;
            case 'last24hours':
                $hoursFilter = 24;
                break;
            default:
                $hoursFilter = 24; 
                break;
        }
    }

    $query = "
      SELECT 
        'ADMISSAO' AS TIPO,
        HSP.HSP_NUM AS 'IH',
        HSP.HSP_DTHRE AS 'DATA_EVENTO',
        HSP.HSP_PAC AS 'REGISTRO',
        PAC.PAC_NOME AS 'PACIENTE',
        CASE
            WHEN DATEDIFF(YEAR, PAC.PAC_NASC, GETDATE()) < 1 
                THEN CAST(DATEDIFF(DAY, PAC.PAC_NASC, GETDATE()) AS VARCHAR(50)) + ' Dia(s).'
            WHEN DATEDIFF(YEAR, PAC.PAC_NASC, GETDATE()) >= 1 
                THEN CAST(DATEDIFF(YEAR, PAC.PAC_NASC, GETDATE()) AS VARCHAR(50)) + ' Ano(s).'
        END AS 'IDADE',
        CNV.CNV_NOME AS 'CONVENIO',
        RTRIM(STR.STR_NOME) AS 'UNIDADE',
        LOC.LOC_NOME AS 'LEITO',
        ISNULL(PSC.PSC_DHINI, '') AS 'PRESCRICAO',
        ISNULL(ADP.ADP_NOME, '') AS 'DIETA',
        PSC.PSC_OBS AS 'OBS' ,
        DATEDIFF(HOUR, HSP.HSP_DTHRE, GETDATE()) AS 'HORAS'
    FROM
        HSP
    INNER JOIN LOC ON HSP_LOC = LOC_COD
    INNER JOIN STR ON STR_COD = LOC_STR
    INNER JOIN PAC ON PAC.PAC_REG = HSP.HSP_PAC
    INNER JOIN CNV ON CNV_COD = HSP.HSP_CNV
    LEFT JOIN PSC ON PSC.PSC_HSP = HSP.HSP_NUM AND PSC.PSC_PAC = HSP.HSP_PAC AND PSC.PSC_TIP = 'D'
    LEFT JOIN ADP ON ADP.ADP_COD = PSC.PSC_ADP AND ADP_TIPO = 'D'
    WHERE
        HSP_TRAT_INT = 'I'
        AND HSP_STAT = 'A'
        AND PSC.PSC_STAT <> 'S'
    ";

    if ($hoursFilter > 0) {
        $query .= " AND HSP.HSP_DTHRE >= DATEADD(HOUR, -$hoursFilter, GETDATE())"; 
    }

    $query .= "
        UNION ALL

        SELECT 
            'ALTA' AS TIPO,
            HSP.HSP_NUM AS 'IH',
            HSP.HSP_DTHRA AS 'DATA_EVENTO',
            HSP.HSP_PAC AS 'REGISTRO',
            PAC.PAC_NOME AS 'PACIENTE',
            CASE
                WHEN DATEDIFF(YEAR, PAC.PAC_NASC, GETDATE()) < 1 
                    THEN CAST(DATEDIFF(DAY, PAC.PAC_NASC, GETDATE()) AS VARCHAR(50)) + ' Dia(s).'
                WHEN DATEDIFF(YEAR, PAC.PAC_NASC, GETDATE()) >= 1 
                    THEN CAST(DATEDIFF(YEAR, PAC.PAC_NASC, GETDATE()) AS VARCHAR(50)) + ' Ano(s).'
            END AS 'IDADE',
            CNV.CNV_NOME AS 'CONVENIO',
            RTRIM(STR.STR_NOME) AS 'UNIDADE',
            LOC.LOC_NOME AS 'LEITO',
            ISNULL(PSC.PSC_DHINI, '') AS 'PRESCRICAO',
            ISNULL(ADP.ADP_NOME, '') AS 'DIETA',
            PSC.PSC_OBS AS 'OBS' ,
            DATEDIFF(HOUR, HSP.HSP_DTHRE, GETDATE()) AS 'HORAS'
        FROM
            HSP
        INNER JOIN LOC ON HSP_LOC = LOC_COD
        INNER JOIN STR ON STR_COD = LOC_STR
        INNER JOIN PAC ON PAC.PAC_REG = HSP.HSP_PAC
        INNER JOIN CNV ON CNV_COD = HSP.HSP_CNV
        LEFT JOIN PSC ON PSC.PSC_HSP = HSP.HSP_NUM AND PSC.PSC_PAC = HSP.HSP_PAC AND PSC.PSC_TIP = 'D'
        LEFT JOIN ADP ON ADP.ADP_COD = PSC.PSC_ADP AND ADP_TIPO = 'D'
        WHERE
            HSP_TRAT_INT = 'I'
            AND HSP_STAT = 'E'
    ";

    if ($hoursFilter > 0) {
        $query .= " AND HSP.HSP_DTHRA >= DATEADD(HOUR, -$hoursFilter, GETDATE())"; 
    }

    $query .= " ORDER BY DATA_EVENTO DESC;"; 

    $result = $connection->query($query)->fetchAll(PDO::FETCH_ASSOC);
    
    // Inicializa a variável como um array vazio
    $groupedPatients = []; 

    if (count($result) > 0) {
        $previousStates = []; 
        
        foreach ($result as $row) {
            $patientName = capitalizeFirstLetters($row['PACIENTE']);
            $convenio = capitalizeFirstLetters($row['CONVENIO']);
            $leito = capitalizeFirstLetters($row['LEITO']);
            $unidade = capitalizeFirstLetters($row['UNIDADE']);
            $prescricao = !empty($row['PRESCRICAO']) ? date('d/m/Y', strtotime($row['PRESCRICAO'])) : '';
            $admissao = date('d/m/Y H:i', strtotime($row['DATA_EVENTO']));
            $idade = $row['IDADE'];
            $tipo = $row['TIPO'];
            $registro = $row['REGISTRO']; 
        
            if (!isset($groupedPatients[$registro])) {
                $groupedPatients[$registro] = [
                    'REGISTRO' => $registro,
                    'PACIENTE' => $patientName,
                    'CONVENIO' => $convenio,
                    'UNIDADE' => $unidade, 
                    'LEITO' => $leito,     
                    'PRESCRICAO' => $prescricao,
                    'DIETAS' => [],
                    'OBS' => [],
                    'ADMISSÃO' => $admissao,
                    'IDADE' => $idade,
                    'TIPO' => $tipo
                ];
                
            } else {
                if ($previousStates[$registro] === 'ADMISSAO' && $tipo === 'ALTA') {
                    echo "<script>showNotification('$patientName');</script>";
                }
            }
            $previousStates[$registro] = $tipo;
        
            if (!empty($row['DIETA'])) {
                $dietName = capitalizeFirstLetters($row['DIETA']);
                if (!in_array($dietName, $groupedPatients[$registro]['DIETAS'])) {
                    $groupedPatients[$registro]['DIETAS'][] = $dietName;
                }
            }
            
            if (!empty($row['OBS'])) {
                $obsText = capitalizeFirstLetters($row['OBS']);
                if (!in_array($obsText, $groupedPatients[$registro]['OBS'])) {
                    $groupedPatients[$registro]['OBS'][] = $obsText;
                }
            }
        }
        
        $groupedPatients = array_values($groupedPatients);
        
    }

} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}


$totalPacientes = count($result); 
$pacientesAlta = 0; 
$pacientesAdmissao = 0; 


?>

<div class="container">
    <h1 class="text-center my-4">Dashboard de Pacientes</h1>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <h5 class="card-title">Total de Pacientes</h5>
                    <p class="card-text"><?php echo $totalPacientes; ?></p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <h5 class="card-title">Pacientes em Alta</h5>
                    <p class="card-text"><?php echo $pacientesAlta; ?></p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <h5 class="card-title">Pacientes em Admissão</h5>
                    <p class="card-text"><?php echo $pacientesAdmissao; ?></p>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function showNotification(patientName) {
    alert("Paciente em alta: " + patientName);
}
</script>

</body>
</html>
