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
    $pdo = null;
    try {
        $pdo = getConnection();
    } catch (Exception $e) {
        $pdo = null;
    }

    // ============== LOGIN POR EMAIL/SENHA ==============
    if ($login_type === 'email') {
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        
        if (empty($email) || empty($password)) {
            $error = 'E-mail e senha são obrigatórios';
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'E-mail inválido';
        } else if ($pdo) {
            // Login apenas com usuário já cadastrado no banco de dados
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE LOWER(email) = LOWER(?) LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $storedHash = (string) ($user['senha_hash'] ?? '');

                if ($storedHash !== '' && password_verify($password, $storedHash)) {
                    $userData = $user;
                    $success = true;
                } else if ($storedHash !== '' && $storedHash === $password) {
                    // Compatibilidade com senhas legadas em texto puro
                    $userData = $user;
                    $success = true;
                } else {
                    $error = 'Senha incorreta';
                }
            } else {
                $error = 'Este e-mail não está cadastrado no sistema.';
            }
        } else {
            $error = 'Não foi possível conectar ao banco de dados.';
        }
    }

    // ============== LOGIN POR GOOGLE ==============
    else if ($login_type === 'google') {
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        
        if (empty($email)) {
            $error = 'Erro na autenticação do Google';
        } else {
            // Login Google apenas com e-mail já cadastrado no banco
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE LOWER(email) = LOWER(?) LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $userData = $user;
                $success = true;
            } else {
                $error = 'Este e-mail do Google não está cadastrado no sistema.';
            }
        }
    }

    // ============== LOGIN POR APPLE ==============
    else if ($login_type === 'apple') {
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        
        if (empty($email)) {
            $error = 'Erro na autenticação da Apple';
        } else {
            // Login Apple apenas com e-mail já cadastrado no banco
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE LOWER(email) = LOWER(?) LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $userData = $user;
                $success = true;
            } else {
                $error = 'Este e-mail da Apple não está cadastrado no sistema.';
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
            // SMS só pode entrar se houver um usuário já cadastrado no banco de dados
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email LIKE ? LIMIT 1");
            $stmt->execute(['%' . preg_replace('/[^0-9]/', '', $phone) . '%']);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $userData = $user;
                $success = true;
            } else {
                $error = 'Este telefone não está cadastrado no sistema.';
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
    // Criar sessão
    $_SESSION['user_id'] = $userData['id'] ?? $userData['id_usuario'] ?? uniqid();
    $_SESSION['user_email'] = $userData['email'];
    $_SESSION['user_name'] = $userData['name'] ?? (isset($userData['nome_criptografado']) ? base64_decode($userData['nome_criptografado']) : 'Usuário');
    $_SESSION['login_type'] = $login_type;
    $_SESSION['logado'] = true;
    
    // Definir role
    $_SESSION['user_role'] = 'paciente';
    header('Location: dashboard.php');
    exit();
} else {
    // Retornar erro
    $_SESSION['login_error'] = $error;
    header('Location: index.php');
    exit();
}
?>