<?php
session_start();
require_once 'config/database.php';

$login_type = isset($_POST['login_type']) ? $_POST['login_type'] : '';
$error = '';
$success = false;
$userData = null;

try {
    $pdo = getConnection();

    // ========== LOGIN POR EMAIL/SENHA ==========
    if ($login_type === 'email') {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            throw new Exception('E-mail e senha são obrigatórios');
        }

        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $user['senha_hash'] === $password) {
            $userData = $user;
            $success = true;
        } else {
            throw new Exception('E-mail ou senha incorretos');
        }
    }

    // ========== LOGIN POR GOOGLE ==========
    elseif ($login_type === 'google') {
        $email = trim($_POST['email'] ?? '');
        $name = trim($_POST['name'] ?? '');

        if (empty($email)) {
            throw new Exception('E-mail do Google é obrigatório');
        }

        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $nomeCriptografado = base64_encode($name ?: explode('@', $email)[0]);
            $senhaHash = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (id_usuario, nome_criptografado, email, senha_hash, tipo_perfil, data_nascimento, sexo_biologico, consentimento_lgpd) VALUES (UUID(), ?, ?, ?, 'Paciente', CURDATE(), 'M', 1)");
            $stmt->execute([$nomeCriptografado, $email, $senhaHash]);
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        if ($user) {
            $userData = $user;
            $success = true;
        } else {
            throw new Exception('Erro ao autenticar com Google');
        }
    }

    // ========== LOGIN POR APPLE ==========
    elseif ($login_type === 'apple') {
        $email = trim($_POST['email'] ?? '');

        if (empty($email)) {
            throw new Exception('E-mail da Apple é obrigatório');
        }

        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $nomeCriptografado = base64_encode(explode('@', $email)[0]);
            $senhaHash = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (id_usuario, nome_criptografado, email, senha_hash, tipo_perfil, data_nascimento, sexo_biologico, consentimento_lgpd) VALUES (UUID(), ?, ?, ?, 'Paciente', CURDATE(), 'M', 1)");
            $stmt->execute([$nomeCriptografado, $email, $senhaHash]);
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        if ($user) {
            $userData = $user;
            $success = true;
        } else {
            throw new Exception('Erro ao autenticar com Apple');
        }
    }

    // ========== LOGIN POR SMS ==========
    elseif ($login_type === 'sms') {
        $phone = trim($_POST['phone'] ?? '');
        $code = trim($_POST['code'] ?? '');

        if (empty($phone)) {
            throw new Exception('Telefone é obrigatório');
        }
        if (empty($code)) {
            throw new Exception('Código é obrigatório');
        }
        if ($code !== '123456') {
            throw new Exception('Código inválido (teste: 123456)');
        }

        // Buscar ou criar usuário
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email LIKE ?");
        $stmt->execute(['%' . $phone . '%']);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $emailSms = 'sms_' . preg_replace('/[^0-9]/', '', $phone) . '@cardioweb.com';
            $nomeCriptografado = base64_encode('Usuário SMS ' . $phone);
            $senhaHash = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (id_usuario, nome_criptografado, email, senha_hash, tipo_perfil, data_nascimento, sexo_biologico, consentimento_lgpd) VALUES (UUID(), ?, ?, ?, 'Paciente', CURDATE(), 'M', 1)");
            $stmt->execute([$nomeCriptografado, $emailSms, $senhaHash]);
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
            $stmt->execute([$emailSms]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        if ($user) {
            $userData = $user;
            $success = true;
        } else {
            throw new Exception('Erro ao autenticar por SMS');
        }
    }

    // ========== LOGIN INVÁLIDO ==========
    else {
        throw new Exception('Método de login inválido');
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

// ========== PROCESSAR SUCESSO ==========
if ($success && $userData) {
    $nomeDecodificado = base64_decode($userData['nome_criptografado']);
    $_SESSION['user_id'] = $userData['id_usuario'];
    $_SESSION['user_email'] = $userData['email'];
    $_SESSION['user_name'] = $nomeDecodificado ?: 'Usuário';
    $_SESSION['login_type'] = $login_type;
    $_SESSION['logado'] = true;

    if (strtolower($userData['tipo_perfil']) === 'admin') {
        $_SESSION['user_role'] = 'admin';
        header('Location: admin/dashboard.php');
    } else {
        $_SESSION['user_role'] = 'paciente';
        header('Location: dashboard_paciente.php');
    }
    exit();
} else {
    $_SESSION['login_error'] = $error;
    header('Location: index.php');
    exit();
}
?>