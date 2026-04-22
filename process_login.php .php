<<<<<<< HEAD
<?php
session_start();

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Login fixo para teste
if($email == 'teste@vidaviva.com' && $password == '123456') {
    $_SESSION['user_id'] = '1';
    $_SESSION['user_email'] = $email;
    $_SESSION['user_name'] = 'Usuário Teste';
    $_SESSION['logado'] = true;
    $_SESSION['login_type'] = 'Padrão';
    
    // Redireciona para dashboard.php (CORRIGIDO)
    header('Location: dashboard.php');
    exit();
} else {
    header('Location: index.php?error=E-mail ou senha incorretos! Use: teste@vidaviva.com / 123456');
    exit();
}
=======
<?php
session_start();

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if($email == 'teste@vidaviva.com' && $password == '123456') {
    $_SESSION['user_id'] = '1';
    $_SESSION['user_email'] = $email;
    $_SESSION['user_name'] = 'Usuário Teste';
    $_SESSION['logado'] = true;
    
    header('Location: dashboard.html');
    exit();
} else {
    header('Location: index.php?error=E-mail ou senha incorretos! Use: teste@vidaviva.com / 123456');
    exit();
}
>>>>>>> 726677b42bba7bd6978a1db01e6f8f37c062b38d
?>