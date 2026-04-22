<<<<<<< HEAD
<?php
session_start();

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if($email && $password) {
    $_SESSION['user_id'] = '2';
    $_SESSION['user_email'] = $email;
    $_SESSION['user_name'] = 'Usuário Google';
    $_SESSION['logado'] = true;
    
    header('Location: dashboard.html');
    exit();
} else {
    header('Location: index.php?error=E-mail e senha obrigatórios');
    exit();
}
=======
<?php
session_start();

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if($email && $password) {
    $_SESSION['user_id'] = '2';
    $_SESSION['user_email'] = $email;
    $_SESSION['user_name'] = 'Usuário Google';
    $_SESSION['logado'] = true;
    
    header('Location: dashboard.html');
    exit();
} else {
    header('Location: index.php?error=E-mail e senha obrigatórios');
    exit();
}
>>>>>>> 726677b42bba7bd6978a1db01e6f8f37c062b38d
?>