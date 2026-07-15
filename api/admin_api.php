<?php
// api/admin_api.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

try {
    $pdo = getConnection();
    
    switch($action) {
        // ========== CONSULTAS ==========
        case 'get_consultas':
            getConsultas($pdo);
            break;
        case 'aprovar_consulta':
            if ($method === 'POST') aprovarConsulta($pdo);
            else sendError('Método não permitido', 405);
            break;
        case 'recusar_consulta':
            if ($method === 'POST') recusarConsulta($pdo);
            else sendError('Método não permitido', 405);
            break;
            
        // ========== AGENDA ==========
        case 'get_agenda':
            getAgenda($pdo);
            break;
        case 'get_agenda_detalhes':
            getAgendaDetalhes($pdo);
            break;
            
        // ========== PACIENTES ==========
        case 'get_pacientes':
            getPacientes($pdo);
            break;
        case 'get_paciente_detalhes':
            getPacienteDetalhes($pdo);
            break;
            
        // ========== MÉDICOS ==========
        case 'get_medicos':
            getMedicos($pdo);
            break;
            
        default:
            sendError('Ação não encontrada', 404);
    }
} catch (Exception $e) {
    sendError($e->getMessage(), 500);
}

// ========== FUNÇÕES DE CONSULTAS ==========
function getConsultas($pdo) {
    $status = $_GET['status'] ?? '';
    $sql = "
        SELECT 
            c.*,
            u.nome_criptografado as paciente_nome,
            u.email as paciente_email,
            u.data_nascimento as paciente_nascimento
        FROM consultas c
        JOIN usuarios u ON c.id_paciente = u.id_usuario
    ";
    
    if ($status) {
        $sql .= " WHERE c.status = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$status]);
    } else {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }
    
    $consultas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Decodificar nomes
    foreach ($consultas as &$c) {
        $c['paciente_nome'] = base64_decode($c['paciente_nome']);
        $c['idade'] = calcularIdade($c['paciente_nascimento']);
        unset($c['paciente_nascimento']);
    }
    
    echo json_encode($consultas);
}

function aprovarConsulta($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? '';
    
    if (empty($id)) {
        sendError('ID da consulta não fornecido');
        return;
    }
    
    try {
        $pdo->beginTransaction();
        
        // Atualizar status
        $stmt = $pdo->prepare("
            UPDATE consultas 
            SET status = 'Confirmada', data_atualizacao = NOW() 
            WHERE id_consulta = ? AND status = 'Pendente'
        ");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() === 0) {
            $pdo->rollBack();
            sendError('Consulta não encontrada ou já foi processada');
            return;
        }
        
        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Consulta confirmada com sucesso']);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        sendError($e->getMessage());
    }
}

function recusarConsulta($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? '';
    $mensagem = $data['mensagem'] ?? 'Horário indisponível. Sugerimos outro horário.';
    
    if (empty($id)) {
        sendError('ID da consulta não fornecido');
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
        sendError('Consulta não encontrada ou já foi processada');
    }
}

// ========== FUNÇÕES DE AGENDA ==========
function getAgenda($pdo) {
    $medico = $_GET['medico'] ?? '';
    $mes = $_GET['mes'] ?? date('m');
    $ano = $_GET['ano'] ?? date('Y');
    
    $sql = "SELECT * FROM consultas 
            WHERE nome_medico = ? 
            AND status = 'Confirmada'
            AND MONTH(data_consulta) = ? 
            AND YEAR(data_consulta) = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$medico, $mes, $ano]);
    $consultas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Buscar nomes dos pacientes
    foreach ($consultas as &$c) {
        $stmtPaciente = $pdo->prepare("SELECT nome_criptografado FROM usuarios WHERE id_usuario = ?");
        $stmtPaciente->execute([$c['id_paciente']]);
        $paciente = $stmtPaciente->fetch(PDO::FETCH_ASSOC);
        $c['paciente_nome'] = $paciente ? base64_decode($paciente['nome_criptografado']) : 'Desconhecido';
    }
    
    echo json_encode($consultas);
}

function getAgendaDetalhes($pdo) {
    $medico = $_GET['medico'] ?? '';
    $data = $_GET['data'] ?? '';
    
    if (empty($medico) || empty($data)) {
        sendError('Médico e data são obrigatórios');
        return;
    }
    
    $sql = "SELECT * FROM consultas 
            WHERE nome_medico = ? 
            AND data_consulta = ?
            AND status = 'Confirmada'
            ORDER BY hora_consulta";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$medico, $data]);
    $consultas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Buscar nomes dos pacientes
    foreach ($consultas as &$c) {
        $stmtPaciente = $pdo->prepare("SELECT nome_criptografado FROM usuarios WHERE id_usuario = ?");
        $stmtPaciente->execute([$c['id_paciente']]);
        $paciente = $stmtPaciente->fetch(PDO::FETCH_ASSOC);
        $c['paciente_nome'] = $paciente ? base64_decode($paciente['nome_criptografado']) : 'Desconhecido';
    }
    
    echo json_encode($consultas);
}

// ========== FUNÇÕES DE PACIENTES ==========
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
    
    foreach ($pacientes as &$p) {
        $p['nome_criptografado'] = base64_decode($p['nome_criptografado']);
        $p['idade'] = calcularIdade($p['data_nascimento']);
    }
    
    echo json_encode($pacientes);
}

function getPacienteDetalhes($pdo) {
    $id = $_GET['id'] ?? '';
    if (empty($id)) {
        sendError('ID do paciente não fornecido');
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
        sendError('Paciente não encontrado');
        return;
    }
    
    $paciente['nome_criptografado'] = base64_decode($paciente['nome_criptografado']);
    $paciente['idade'] = calcularIdade($paciente['data_nascimento']);
    
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

// ========== FUNÇÕES DE MÉDICOS ==========
function getMedicos($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM medicos WHERE ativo = 1 ORDER BY nome");
    $stmt->execute();
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

// ========== FUNÇÕES AUXILIARES ==========
function calcularIdade($dataNascimento) {
    if (empty($dataNascimento)) return null;
    $hoje = new DateTime();
    $nascimento = new DateTime($dataNascimento);
    $idade = $hoje->diff($nascimento);
    return $idade->y;
}

function sendError($message, $code = 400) {
    http_response_code($code);
    echo json_encode(['error' => $message]);
    exit;
}

function sendSuccess($data, $message = '') {
    echo json_encode(['success' => true, 'message' => $message, 'data' => $data]);
    exit;
}
?>