<?php
session_start();

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if ($email && $password) {
    $_SESSION['user_id'] = '1';
    $_SESSION['user_email'] = $email;
    $_SESSION['user_name'] = 'Usuário Teste';
    $_SESSION['logado'] = true;
    $_SESSION['login_type'] = 'Padrão';
    $_SESSION['user_role'] = 'paciente';

    header('Location: dashboard_paciente.php');
    exit();
}

header('Location: index.php?error=E-mail ou senha incorretos! Use: teste@vidaviva.com / 123456');
exit();
?>