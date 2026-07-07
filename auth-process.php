<?php
/**
 * AUTH-PROCESS.PHP
 * Sistema centralizado de autenticação
 * Processa todos os tipos de login: Email/Senha, Google, Apple e SMS
 */

session_start();

// Detectar tipo de login
$login_type = isset($_POST['login_type']) ? $_POST['login_type'] : '';
$error = '';
$success = false;

// ============== LOGIN POR EMAIL/SENHA ==============\
if ($login_type === 'email') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (empty($email) || empty($password)) {
        $error = 'E-mail e senha são obrigatórios';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'E-mail inválido';
    } else {
        // TODO: Validar contra banco de dados
        // Por enquanto, aceitar qualquer email/senha
        $_SESSION['user_id'] = '1';
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = explode('@', $email)[0];
        $_SESSION['login_type'] = 'Email';
        $_SESSION['logado'] = true;

        // VERIFICAÇÃO DE ADMINISTRADOR
        if ($email === 'coordenacao@vidaviva.com') {
            $_SESSION['user_role'] = 'admin';
        } else {
            $_SESSION['user_role'] = 'paciente';
        }

        $success = true;
    }
}

// ============== LOGIN POR GOOGLE ==============\
else if ($login_type === 'google') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    
    if (empty($email)) {
        $error = 'Erro na autenticação do Google';
    } else {
        $_SESSION['user_id'] = '2';
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = explode('@', $email)[0];
        $_SESSION['login_type'] = 'Google';
        $_SESSION['logado'] = true;

        // Se entrar via Google com o e-mail da coordenação, vira Admin
        if ($email === 'coordenacao@vidaviva.com') {
            $_SESSION['user_role'] = 'admin';
        } else {
            $_SESSION['user_role'] = 'paciente';
        }

        $success = true;
    }
}

// ============== LOGIN POR APPLE ==============\
else if ($login_type === 'apple') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    
    if (empty($email)) {
        $error = 'Erro na autenticação da Apple';
    } else {
        $_SESSION['user_id'] = '3';
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = explode('@', $email)[0];
        $_SESSION['login_type'] = 'Apple';
        $_SESSION['logado'] = true;

        if ($email === 'coordenacao@vidaviva.com') {
            $_SESSION['user_role'] = 'admin';
        } else {
            $_SESSION['user_role'] = 'paciente';
        }

        $success = true;
    }
}

// ============== LOGIN POR SMS ==============\
else if ($login_type === 'sms') {
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $code = isset($_POST['code']) ? trim($_POST['code']) : '';
    
    if (empty($phone)) {
        $error = 'Telefone é obrigatório';
    } else if (empty($code)) {
        $error = 'Código de verificação é obrigatório';
    } else if ($code !== '123456') {
        // Código padrão para teste
        $error = 'Código inválido! Use o código enviado por SMS (teste: 123456)';
    } else {
        // TODO: Validar código contra banco de dados
        $_SESSION['user_id'] = '4';
        $_SESSION['user_phone'] = $phone;
        $_SESSION['user_name'] = 'Usuário SMS';
        $_SESSION['login_type'] = 'SMS';
        $_SESSION['logado'] = true;
        $_SESSION['user_role'] = 'paciente'; // SMS assume sempre paciente por padrão
        $success = true;
    }
}

// ============== RESPONDER E REDIRECIONAR ==============\
if ($success) {
    // Redireciona com base na função/role do utilizador
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
        header('Location: admin/dashboard_admin.php');
    } else {
        header('Location: dashboard.php');
    }
    exit();
} else {
    // Retornar erro para a página anterior via sessão
    $_SESSION['login_error'] = $error;
    header('Location: index.php');
    exit();
}
