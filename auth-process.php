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
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'E-mail inválido';
    } else {
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

// ============== LOGIN POR GOOGLE ==============
elseif ($login_type === 'google') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    
    if (empty($email)) {
        $error = 'Erro na autenticação do Google';
    } else {
        $_SESSION['user_id'] = '2';
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = explode('@', $email)[0];
        $_SESSION['login_type'] = 'Google';
        $_SESSION['logado'] = true;

        if ($email === 'coordenacao@vidaviva.com') {
            $_SESSION['user_role'] = 'admin';
        } else {
            $_SESSION['user_role'] = 'paciente';
        }

        $success = true;
    }
}

// ============== LOGIN POR APPLE ==============
elseif ($login_type === 'apple') {
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

// ============== LOGIN POR SMS ==============
elseif ($login_type === 'sms') {
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $code = isset($_POST['code']) ? trim($_POST['code']) : '';
    
    if (empty($phone)) {
        $error = 'Telefone é obrigatório';
    } elseif (empty($code)) {
        $error = 'Código de verificação é obrigatório';
    } elseif ($code !== '123456') {
        $error = 'Código inválido! Use o código enviado por SMS (teste: 123456)';
    } else {
        $_SESSION['user_id'] = '4';
        $_SESSION['user_phone'] = $phone;
        $_SESSION['user_name'] = 'Usuário SMS';
        $_SESSION['login_type'] = 'SMS';
        $_SESSION['logado'] = true;
        $_SESSION['user_role'] = 'paciente';
        $success = true;
    }
}

// ============== RESPONDER E REDIRECIONAR ==============
if ($success) {
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
        header('Location: admin/dashboard_admin.php');
    } else {
        header('Location: dashboard_paciente.php');
    }
    exit();
} else {
    $_SESSION['login_error'] = $error;
    header('Location: index.php');
    exit();
}
?>