<?php
session_start();

$email = $_POST['email'] ?? '';
$name = $_POST['name'] ?? '';

if ($email) {
    $_SESSION['user_id'] = md5($email);
    $_SESSION['user_email'] = $email;
    $_SESSION['user_name'] = $name;
    $_SESSION['logado'] = true;
    $_SESSION['login_type'] = 'Google';
    $_SESSION['user_role'] = ($email === 'coordenacao@vidaviva.com') ? 'admin' : 'paciente';

    if ($_SESSION['user_role'] === 'admin') {
        header('Location: admin/dashboard_admin.php');
    } else {
        header('Location: dashboard_paciente.php');
    }
    exit();
} else {
    header('Location: index.php?error=Email não fornecido');
    exit();
}
?>
