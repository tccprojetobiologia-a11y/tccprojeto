<<<<<<< HEAD
<?php
session_start();

// Recebe o token do Google
$credential = $_POST['credential'] ?? '';

if(empty($credential)) {
    echo json_encode(['success' => false, 'error' => 'Token não recebido']);
    exit();
}

// Decodifica o token JWT
$parts = explode('.', $credential);
if(count($parts) !== 3) {
    echo json_encode(['success' => false, 'error' => 'Token inválido']);
    exit();
}

$payload = json_decode(base64_decode($parts[1]), true);

if($payload && isset($payload['email'])) {
    // Login bem sucedido
    $_SESSION['user_id'] = $payload['sub'];
    $_SESSION['user_email'] = $payload['email'];
    $_SESSION['user_name'] = $payload['name'] ?? $payload['email'];
    $_SESSION['user_picture'] = $payload['picture'] ?? '';
    $_SESSION['logado'] = true;
    $_SESSION['login_type'] = 'google';
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
}
=======
<?php
session_start();

// Recebe o token do Google
$credential = $_POST['credential'] ?? '';

if(empty($credential)) {
    echo json_encode(['success' => false, 'error' => 'Token não recebido']);
    exit();
}

// Decodifica o token JWT
$parts = explode('.', $credential);
if(count($parts) !== 3) {
    echo json_encode(['success' => false, 'error' => 'Token inválido']);
    exit();
}

$payload = json_decode(base64_decode($parts[1]), true);

if($payload && isset($payload['email'])) {
    // Login bem sucedido
    $_SESSION['user_id'] = $payload['sub'];
    $_SESSION['user_email'] = $payload['email'];
    $_SESSION['user_name'] = $payload['name'] ?? $payload['email'];
    $_SESSION['user_picture'] = $payload['picture'] ?? '';
    $_SESSION['logado'] = true;
    $_SESSION['login_type'] = 'google';
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
}
>>>>>>> 726677b42bba7bd6978a1db01e6f8f37c062b38d
?>