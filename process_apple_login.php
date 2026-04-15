<?php
session_start();

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if($email && $password) {
    $_SESSION['user_id'] = '3';
    $_SESSION['user_email'] = $email;
    $_SESSION['user_name'] = 'Usuário Apple';
    $_SESSION['logado'] = true;
    $_SESSION['login_type'] = 'Apple';
    
    // Redireciona para dashboard.php (CORRIGIDO)
    header('Location: dashboard.php');
    exit();
} else {
    header('Location: index.php?error=E-mail e senha obrigatórios');
    exit();
}
?>