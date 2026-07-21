<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

$action = $_GET['action'] ?? '';

try {
    $pdo = getConnection();
    
    switch($action) {
        case 'get_consultas':
            getConsultas($pdo);
            break;
        case 'get_agenda':
            getAgenda($pdo);
            break;
        case 'get_pacientes':
            getPacientes($pdo);
            break;
        case 'get_paciente_detalhes':
            getPacienteDetalhes($pdo);
            break;
        case 'aprovar_consulta':
            aprovarConsulta($pdo);
            break;
        case 'recusar_consulta':
            recusarConsulta($pdo);
            break;
        default:
            echo json_encode(['error' => 'Ação não encontrada']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

function getConsultas($pdo) {
    $stmt = $pdo->prepare("
        SELECT 
            c.*,
            u.nome_criptografado as paciente_nome,
            u.email as paciente_email
        FROM consultas c
        JOIN usuarios u ON c.id_paciente = u.id_usuario
        ORDER BY c.data_solicitacao DESC
    ");
    $stmt->execute();
    $consultas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Decodificar nomes
    foreach ($consultas as &$c) {
        $c['paciente_nome'] = base64_decode($c['paciente_nome']);
    }
    
    echo json_encode($consultas);
}

function getAgenda($pdo) {
    $medico = $_GET['medico'] ?? '';
    $mes = $_GET['mes'] ?? date('m');
    $ano = $_GET['ano'] ?? date('Y');
    
    $sql = "SELECT * FROM consultas 
            WHERE nome_medico LIKE ? 
            AND status = 'Confirmada'
            AND MONTH(data_consulta) = ? 
            AND YEAR(data_consulta) = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$medico%", $mes, $ano]);
    $consultas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Decodificar nomes dos pacientes
    foreach ($consultas as &$c) {
        $stmtPaciente = $pdo->prepare("SELECT nome_criptografado FROM usuarios WHERE id_usuario = ?");
        $stmtPaciente->execute([$c['id_paciente']]);
        $paciente = $stmtPaciente->fetch(PDO::FETCH_ASSOC);
        $c['paciente_nome'] = $paciente ? base64_decode($paciente['nome_criptografado']) : 'Desconhecido';
    }
    
    echo json_encode($consultas);
}

function getPacientes($pdo) {
    $stmt = $pdo->prepare("
        SELECT 
            u.id_usuario,
            u.nome_criptografado,
            u.email,
            u.data_nascimento,
            u.sexo_biologico,
            u.data_criacao,
            dp.peso,
            dp.altura,
            (
                SELECT COUNT(*) FROM consultas c 
                WHERE c.id_paciente = u.id_usuario 
                AND c.status = 'Confirmada'
            ) as total_consultas
        FROM usuarios u
        LEFT JOIN dados_paciente dp ON u.id_usuario = dp.id_paciente
        WHERE u.tipo_perfil = 'Paciente'
        ORDER BY u.nome_criptografado
    ");
    $stmt->execute();
    $pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Decodificar nomes
    foreach ($pacientes as &$p) {
        $p['nome_criptografado'] = base64_decode($p['nome_criptografado']);
    }
    
    echo json_encode($pacientes);
}

function getPacienteDetalhes($pdo) {
    $id = $_GET['id'] ?? '';
    if (empty($id)) {
        echo json_encode(['error' => 'ID do paciente não fornecido']);
        return;
    }
    
    // Dados do paciente
    $stmt = $pdo->prepare("
        SELECT 
            u.id_usuario,
            u.nome_criptografado,
            u.email,
            u.data_nascimento,
            u.sexo_biologico,
            u.data_criacao,
            dp.peso,
            dp.altura
        FROM usuarios u
        LEFT JOIN dados_paciente dp ON u.id_usuario = dp.id_paciente
        WHERE u.id_usuario = ? AND u.tipo_perfil = 'Paciente'
    ");
    $stmt->execute([$id]);
    $paciente = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$paciente) {
        echo json_encode(['error' => 'Paciente não encontrado']);
        return;
    }
    
    $paciente['nome_criptografado'] = base64_decode($paciente['nome_criptografado']);
    
    // Histórico médico
    $stmt = $pdo->prepare("
        SELECT * FROM historico_medico 
        WHERE id_paciente = ? 
        ORDER BY data_registro DESC
    ");
    $stmt->execute([$id]);
    $historico = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Exames
    $stmt = $pdo->prepare("
        SELECT * FROM exames_paciente 
        WHERE id_paciente = ? 
        ORDER BY data_exame DESC
    ");
    $stmt->execute([$id]);
    $exames = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Consultas
    $stmt = $pdo->prepare("
        SELECT * FROM consultas 
        WHERE id_paciente = ? 
        ORDER BY data_consulta DESC
    ");
    $stmt->execute([$id]);
    $consultas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'paciente' => $paciente,
        'historico' => $historico,
        'exames' => $exames,
        'consultas' => $consultas
    ]);
}

function aprovarConsulta($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? '';
    
    if (empty($id)) {
        echo json_encode(['error' => 'ID da consulta não fornecido']);
        return;
    }
    
    $stmt = $pdo->prepare("
        UPDATE consultas 
        SET status = 'Confirmada', data_atualizacao = NOW() 
        WHERE id_consulta = ? AND status = 'Pendente'
    ");
    $stmt->execute([$id]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Consulta confirmada com sucesso']);
    } else {
        echo json_encode(['error' => 'Consulta não encontrada ou já foi processada']);
    }
}

function recusarConsulta($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? '';
    $mensagem = $data['mensagem'] ?? '';
    
    if (empty($id)) {
        echo json_encode(['error' => 'ID da consulta não fornecido']);
        return;
    }
    
    $stmt = $pdo->prepare("
        UPDATE consultas 
        SET status = 'Recusada', mensagem_recusa = ?, data_atualizacao = NOW() 
        WHERE id_consulta = ? AND status = 'Pendente'
    ");
    $stmt->execute([$mensagem, $id]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Consulta recusada com sucesso']);
    } else {
        echo json_encode(['error' => 'Consulta não encontrada ou já foi processada']);
    }
}

function getConnection() {
    $host = 'localhost';
    $dbname = 'cardioweb';
    $username = 'root';
    $password = '';
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        throw new Exception("Erro de conexão: " . $e->getMessage());
    }
}
?>