<<<<<<< HEAD
<?php
session_start();

$email = $_POST['email'] ?? '';
$name = $_POST['name'] ?? '';

if($email) {
    $_SESSION['user_id'] = md5($email);
    $_SESSION['user_email'] = $email;
    $_SESSION['user_name'] = $name;
    $_SESSION['logado'] = true;
    $_SESSION['login_type'] = 'Google';
    
    // Redireciona para dashboard.php (CORRIGIDO)
    header('Location: dashboard.php');
    exit();
} else {
    header('Location: index.php?error=Email não fornecido');
    exit();
}
