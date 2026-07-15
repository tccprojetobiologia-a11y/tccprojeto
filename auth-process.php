<?php
/**
 * AUTH-PROCESS.PHP
 * Sistema centralizado de autenticação
 * Processa todos os tipos de login: Email/Senha, Google, Apple e SMS
 * COM INTEGRAÇÃO AO BANCO DE DADOS
 */

session_start();

// ========== INCLUIR CONFIGURAÇÃO DO BANCO ==========
require_once 'config/database.php';

// Detectar tipo de login
$login_type = isset($_POST['login_type']) ? $_POST['login_type'] : '';
$error = '';
$success = false;
$userData = null;

try {
    $pdo = getConnection();

    // ============== LOGIN POR EMAIL/SENHA ==============
    if ($login_type === 'email') {
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        
        if (empty($email) || empty($password)) {
            $error = 'E-mail e senha são obrigatórios';
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'E-mail inválido';
        } else {
            // Buscar usuário no banco
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Verificar senha (em produção use password_verify)
                // Por enquanto, comparação simples (hash em produção)
                if ($user['senha_hash'] === $password) {
                    $userData = $user;
                    $success = true;
                } else {
                    $error = 'Senha incorreta';
                }
            } else {
                $error = 'Usuário não encontrado';
            }
        }
    }

    // ============== LOGIN POR GOOGLE ==============
    else if ($login_type === 'google') {
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        
        if (empty($email)) {
            $error = 'Erro na autenticação do Google';
        } else {
            // Verificar se usuário existe, senão criar
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                // Criar usuário novo para login social
                $nomeCriptografado = base64_encode($name ?: explode('@', $email)[0]);
                $senhaHash = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("
                    INSERT INTO usuarios (id_usuario, nome_criptografado, email, senha_hash, tipo_perfil, data_nascimento, sexo_biologico, consentimento_lgpd)
                    VALUES (UUID(), ?, ?, ?, 'Paciente', CURDATE(), 'M', 1)
                ");
                $stmt->execute([$nomeCriptografado, $email, $senhaHash]);
                
                // Buscar o usuário criado
                $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            
            if ($user) {
                $userData = $user;
                $success = true;
            } else {
                $error = 'Erro ao criar/validar usuário Google';
            }
        }
    }

    // ============== LOGIN POR APPLE ==============
    else if ($login_type === 'apple') {
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        
        if (empty($email)) {
            $error = 'Erro na autenticação da Apple';
        } else {
            // Verificar se usuário existe, senão criar
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                $nomeCriptografado = base64_encode(explode('@', $email)[0]);
                $senhaHash = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("
                    INSERT INTO usuarios (id_usuario, nome_criptografado, email, senha_hash, tipo_perfil, data_nascimento, sexo_biologico, consentimento_lgpd)
                    VALUES (UUID(), ?, ?, ?, 'Paciente', CURDATE(), 'M', 1)
                ");
                $stmt->execute([$nomeCriptografado, $email, $senhaHash]);
                
                $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            
            if ($user) {
                $userData = $user;
                $success = true;
            } else {
                $error = 'Erro ao criar/validar usuário Apple';
            }
        }
    }

    // ============== LOGIN POR SMS ==============
    else if ($login_type === 'sms') {
        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
        $code = isset($_POST['code']) ? trim($_POST['code']) : '';
        
        if (empty($phone)) {
            $error = 'Telefone é obrigatório';
        } else if (empty($code)) {
            $error = 'Código de verificação é obrigatório';
        } else if ($code !== '123456') {
            $error = 'Código inválido! Use o código enviado por SMS (teste: 123456)';
        } else {
            // Buscar usuário pelo telefone (usando email como fallback para telefone)
            // Nota: Em produção, crie uma coluna 'telefone' na tabela usuarios
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email LIKE ?");
            $stmt->execute(['%' . $phone . '%']);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                // Criar usuário para SMS
                $nomeCriptografado = base64_encode('Usuário SMS ' . $phone);
                $senhaHash = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
                $emailSms = 'sms_' . preg_replace('/[^0-9]/', '', $phone) . '@cardioweb.com';
                
                $stmt = $pdo->prepare("
                    INSERT INTO usuarios (id_usuario, nome_criptografado, email, senha_hash, tipo_perfil, data_nascimento, sexo_biologico, consentimento_lgpd)
                    VALUES (UUID(), ?, ?, ?, 'Paciente', CURDATE(), 'M', 1)
                ");
                $stmt->execute([$nomeCriptografado, $emailSms, $senhaHash]);
                
                $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
                $stmt->execute([$emailSms]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            
            if ($user) {
                $userData = $user;
                $success = true;
            } else {
                $error = 'Erro ao validar SMS';
            }
        }
    }

    // ============== LOGIN INVÁLIDO ==============
    else {
        $error = 'Método de login inválido';
    }

} catch (Exception $e) {
    $error = 'Erro no sistema: ' . $e->getMessage();
}

// ============== PROCESSAR SUCESSO ==============
if ($success && $userData) {
    // Decodificar nome
    $nomeDecodificado = base64_decode($userData['nome_criptografado']);
    
    // Criar sessão
    $_SESSION['user_id'] = $userData['id_usuario'];
    $_SESSION['user_email'] = $userData['email'];
    $_SESSION['user_name'] = $nomeDecodificado ?: 'Usuário';
    $_SESSION['login_type'] = $login_type;
    $_SESSION['logado'] = true;
    
    // Definir role
    if (strtolower($userData['tipo_perfil']) === 'admin') {
        $_SESSION['user_role'] = 'admin';
        // Redirecionar para admin
        header('Location: admin/dashboard.php');
    } else {
        $_SESSION['user_role'] = 'paciente';
        // Redirecionar para dashboard do paciente
        header('Location: dashboard.php');
    }
    exit();
} else {
    // Retornar erro
    $_SESSION['login_error'] = $error;
    header('Location: index.php');
    exit();
}
?>