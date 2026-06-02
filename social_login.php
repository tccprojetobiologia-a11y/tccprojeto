<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $provider = $_POST['provider'] ?? '';
    
    // LOGIN COM GOOGLE
    if ($provider === 'google') {
        $email = $_POST['email'] ?? '';
        
        if (!empty($email)) {
            $_SESSION['user_id'] = $email;
            $_SESSION['user_name'] = explode('@', $email)[0];
            $_SESSION['user_email'] = $email;
            $_SESSION['login_type'] = 'Google';
            $_SESSION['logado'] = true;
            
            header('Location: dashboard.php');
            exit();
        } else {
            header('Location: index.php?error=Falha no login com Google');
            exit();
        }
    }
    
    // LOGIN COM APPLE
    elseif ($provider === 'apple') {
        $email = $_POST['email'] ?? '';
        
        if (!empty($email)) {
            $_SESSION['user_id'] = $email;
            $_SESSION['user_name'] = explode('@', $email)[0];
            $_SESSION['user_email'] = $email;
            $_SESSION['login_type'] = 'Apple';
            $_SESSION['logado'] = true;
            
            header('Location: dashboard.php');
            exit();
        } else {
            header('Location: index.php?error=Falha no login com Apple');
            exit();
        }
    }
    
    // LOGIN COM SMS
    elseif ($provider === 'sms') {
        $phone = $_POST['phone'] ?? '';
        $code = $_POST['code'] ?? '';
        
        if ($code === '123456' && !empty($phone)) {
            $_SESSION['user_id'] = $phone;
            $_SESSION['user_name'] = 'Usuario_SMS';
            $_SESSION['user_telefone'] = $phone;
            $_SESSION['login_type'] = 'SMS';
            $_SESSION['logado'] = true;
            
            header('Location: dashboard.php');
            exit();
        } else {
            header('Location: index.php?error=Código SMS inválido. Use: 123456');
            exit();
        }
    }
    
    else {
        header('Location: index.php?error=Método de login inválido');
        exit();
    }
} else {
    header('Location: index.php');
    exit();
}
?>