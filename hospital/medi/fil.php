<?php
// Conexão com o banco de dados
$host = 'localhost';
$dbname = 'agape';
$username = 'root';
$password = '1234';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}

// Início da query base
$query = "SELECT * FROM atendimentos WHERE 1=1";
$params = [];

// Filtro de Tempo
if (!empty($_GET['tempo'])) {
    if ($_GET['tempo'] === '7_dias') {
        $query .= " AND data >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    } elseif ($_GET['tempo'] === '30_dias') {
        $query .= " AND data >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
    } elseif ($_GET['tempo'] === 'personalizado' && !empty($_GET['data_inicio']) && !empty($_GET['data_fim'])) {
        $query .= " AND data BETWEEN :data_inicio AND :data_fim";
        $params[':data_inicio'] = $_GET['data_inicio'];
        $params[':data_fim'] = $_GET['data_fim'];
    }
}

// Filtro de Ordem
if (!empty($_GET['ordem'])) {
    if ($_GET['ordem'] === 'a_z') {
        $query .= " ORDER BY nome ASC";
    } elseif ($_GET['ordem'] === 'z_a') {
        $query .= " ORDER BY nome DESC";
    }
}

// Filtro de Especialidade
if (!empty($_GET['especialidade'])) {
    $especialidades = implode(",", array_map(fn($e) => "'" . $e . "'", $_GET['especialidade']));
    $query .= " AND especialidade IN ($especialidades)";
}

// Filtro de Status do Atendimento
if (!empty($_GET['status'])) {
    $statusList = implode(",", array_map(fn($s) => "'" . $s . "'", $_GET['status']));
    $query .= " AND status IN ($statusList)";
}

// Filtro de Urgência
if (!empty($_GET['urgencia'])) {
    $query .= " AND urgencia = :urgencia";
    $params[':urgencia'] = $_GET['urgencia'];
}

// Filtro de Tipo de Consulta
if (!empty($_GET['tipo_consulta'])) {
    $tiposConsulta = implode(",", array_map(fn($t) => "'" . $t . "'", $_GET['tipo_consulta']));
    $query .= " AND tipo_consulta IN ($tiposConsulta)";
}

// Filtro de Médico Responsável
if (!empty($_GET['medico'])) {
    $medicos = implode(",", array_map(fn($m) => "'" . $m . "'", $_GET['medico']));
    $query .= " AND medico_responsavel IN ($medicos)";
}

// Filtro de Diagnóstico
if (!empty($_GET['diagnostico'])) {
    $diagnosticos = implode(",", array_map(fn($d) => "'" . $d . "'", $_GET['diagnostico']));
    $query .= " AND diagnostico IN ($diagnosticos)";
}

// Executando a query
try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao executar a consulta: " . $e->getMessage());
}

// Exibindo os resultados
foreach ($resultados as $resultado) {
    echo "<div class='card'>";
    echo "<h2>" . htmlspecialchars($resultado['nome']) . "</h2>";
    echo "<p>" . htmlspecialchars($resultado['descricao']) . "</p>";
    echo "<p>Email: " . htmlspecialchars($resultado['email']) . "</p>";
    echo "<p>Telefone: " . htmlspecialchars($resultado['telefone']) . "</p>";
    echo "</div>";
}
?>
