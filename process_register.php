<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit();
}

$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if (empty($nome) || empty($email) || empty($password)) {
    header('Location: register.php?error=Todos os campos são obrigatórios');
    exit();
}

if ($password !== $confirm) {
    header('Location: register.php?error=As senhas não coincidem');
    exit();
}

if (strlen($password) < 6) {
    header('Location: register.php?error=A senha deve ter no mínimo 6 caracteres');
    exit();
}

// Em produção, salvar no banco de dados
// Por enquanto, apenas simula o cadastro

header('Location: index.php?success=Conta criada com sucesso! Faça login');
exit();
?>