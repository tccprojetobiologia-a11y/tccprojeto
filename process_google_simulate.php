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
=======
<?php
session_start();

$email = $_POST['email'] ?? '';
$name = $_POST['name'] ?? '';

if($email) {
    $_SESSION['user_id'] = md5($email);
    $_SESSION['user_email'] = $email;
    $_SESSION['user_name'] = $name;
    $_SESSION['logado'] = true;
    $_SESSION['login_type'] = 'google';
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Email não fornecido']);
}
>>>>>>> 726677b42bba7bd6978a1db01e6f8f37c062b38d
?>