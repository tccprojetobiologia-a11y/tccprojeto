<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: index.php');
    exit();
}

if (($_SESSION['user_role'] ?? '') === 'admin') {
    header('Location: admin/dashboard_admin.php');
    exit();
}

header('Location: dashboard_paciente.php');
exit();
?>
