<<<<<<< HEAD
<?php
session_start();

$telefone = $_POST['telefone'] ?? '';
$codigo = $_POST['codigo'] ?? '';

if($codigo == '123456') {
    $_SESSION['user_id'] = '4';
    $_SESSION['user_telefone'] = $telefone;
    $_SESSION['user_name'] = 'Usuário SMS';
    $_SESSION['logado'] = true;
    $_SESSION['login_type'] = 'SMS';
    
    // Redireciona para dashboard.php (CORRIGIDO)
    header('Location: dashboard.php');
    exit();
} else {
    header('Location: index.php?error=Código inválido! Use: 123456');
    exit();
}
=======
<?php
session_start();

$telefone = $_POST['telefone'] ?? '';
$codigo = $_POST['codigo'] ?? '';

if($codigo == '123456') {
    $_SESSION['user_id'] = '4';
    $_SESSION['user_telefone'] = $telefone;
    $_SESSION['user_name'] = 'Usuário SMS';
    $_SESSION['logado'] = true;
    
    header('Location: dashboard.html');
    exit();
} else {
    header('Location: index.php?error=Código inválido! Use: 123456');
    exit();
}
>>>>>>> 726677b42bba7bd6978a1db01e6f8f37c062b38d
?>