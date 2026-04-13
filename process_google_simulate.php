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
?>