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

// ============== LOGIN POR EMAIL/SENHA ==============
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
        $success = true;
    }
}

// ============== LOGIN POR GOOGLE ==============
else if ($login_type === 'google') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    
    if (empty($email)) {
        $error = 'E-mail do Google é obrigatório';
    } else {
        // TODO: Validar token do Google
        $_SESSION['user_id'] = '2';
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = $name ?: explode('@', $email)[0];
        $_SESSION['login_type'] = 'Google';
        $_SESSION['logado'] = true;
        $success = true;
    }
}

// ============== LOGIN POR APPLE ==============
else if ($login_type === 'apple') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    
    if (empty($email)) {
        $error = 'E-mail do Apple é obrigatório';
    } else {
        // TODO: Validar token do Apple
        $_SESSION['user_id'] = '3';
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = explode('@', $email)[0];
        $_SESSION['login_type'] = 'Apple';
        $_SESSION['logado'] = true;
        $success = true;
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
        // Código padrão para teste
        $error = 'Código inválido! Use o código enviado por SMS (teste: 123456)';
    } else {
        // TODO: Validar código contra banco de dados
        $_SESSION['user_id'] = '4';
        $_SESSION['user_phone'] = $phone;
        $_SESSION['user_name'] = 'Usuário SMS';
        $_SESSION['login_type'] = 'SMS';
        $_SESSION['logado'] = true;
        $success = true;
    }
}

// ============== RESPONDER ==============
if ($success) {
    // Redirecionar para dashboard
    header('Location: dashboard.php');
    exit();
} else if ($error) {
    // Redirecionar para login com erro
    header('Location: index.php?error=' . urlencode($error));
    exit();
} else {
    // Nenhum tipo de login detectado
    header('Location: index.php?error=Tipo de login inválido');
    exit();
}
?>
