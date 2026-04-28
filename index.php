<?php
session_start();

// Se já estiver logado, vai para o dashboard
if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
    header('Location: dashboard.php');
    exit();
}

// Processar login normal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['normal_login'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Login de teste
    if($email == 'teste@vidaviva.com' && $password == '123456') {
        $_SESSION['user_id'] = '1';
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = 'Usuário Teste';
        $_SESSION['logado'] = true;
        $_SESSION['login_type'] = 'Padrão';
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "E-mail ou senha incorretos! Use: teste@vidaviva.com / 123456";
    }
}

// Processar login social (Google, Apple, SMS) via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['google_login'])) {
        $_SESSION['user_id'] = md5($_POST['email']);
        $_SESSION['user_email'] = $_POST['email'];
        $_SESSION['user_name'] = $_POST['name'];
        $_SESSION['logado'] = true;
        $_SESSION['login_type'] = 'Google';
        header('Location: dashboard.php');
        exit();
    }
    
    if (isset($_POST['apple_login'])) {
        $_SESSION['user_id'] = '3';
        $_SESSION['user_email'] = $_POST['email'];
        $_SESSION['user_name'] = 'Usuário Apple';
        $_SESSION['logado'] = true;
        $_SESSION['login_type'] = 'Apple';
        header('Location: dashboard.php');
        exit();
    }
    
    if (isset($_POST['sms_login'])) {
        $_SESSION['user_id'] = '4';
        $_SESSION['user_telefone'] = $_POST['telefone'];
        $_SESSION['user_name'] = 'Usuário SMS';
        $_SESSION['logado'] = true;
        $_SESSION['login_type'] = 'SMS';
        header('Location: dashboard.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CardioWeb - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        /* Container principal - ÚNICO QUADRO CENTRALIZADO */
        .login-wrapper {
            width: 100%;
            max-width: 480px;
        }

        .login-card {
            background: white;
            border-radius: 24px;
            padding: 48px 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Logo */
        .logo {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #851e32, #5a1e2c);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }

        .logo-icon i {
            font-size: 36px;
            color: white;
        }

        .logo h1 {
            font-size: 28px;
            color: #1e2a3a;
            letter-spacing: -0.5px;
        }

        .logo p {
            font-size: 13px;
            color: #666;
            margin-top: 4px;
        }

        /* Título do formulário */
        .form-title {
            text-align: center;
            margin-bottom: 32px;
        }

        .form-title h2 {
            font-size: 24px;
            color: #1e2a3a;
            margin-bottom: 8px;
        }

        .form-title p {
            font-size: 14px;
            color: #666;
        }

        /* Campos de input */
        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
        }

        .input-field {
            width: 100%;
            padding: 14px 16px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }

        .input-field:focus {
            outline: none;
            border-color: #851e32;
            box-shadow: 0 0 0 3px rgba(133, 30, 50, 0.1);
        }

        /* Botão principal */
        .btn-login {
            width: 100%;
            background: #851e32;
            color: white;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 8px;
        }

        .btn-login:hover {
            background: #6a182c;
            transform: translateY(-1px);
        }

        /* Link esqueceu senha */
        .forgot-link {
            text-align: right;
            margin: 12px 0 24px;
        }

        .forgot-link a {
            color: #851e32;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
        }

        .forgot-link a:hover {
            text-decoration: underline;
        }

        /* Divisor */
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 24px 0;
            color: #94a3b8;
            font-size: 12px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }

        .divider span {
            padding: 0 16px;
        }

        /* Botões sociais */
        .social-buttons {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }

        .social-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            background: white;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .social-btn:hover {
            border-color: #851e32;
            background: #fef2f2;
        }

        .social-btn.google i { color: #4285f4; }
        .social-btn.apple i { color: #000; }
        .social-btn.sms i { color: #851e32; }

        /* Link cadastro */
        .register-prompt {
            text-align: center;
            margin-top: 24px;
            font-size: 14px;
            color: #666;
        }

        .register-prompt a {
            color: #851e32;
            font-weight: 600;
            text-decoration: none;
        }

        .register-prompt a:hover {
            text-decoration: underline;
        }

        /* Mensagem de erro */
        .error-message {
            background: #fee;
            color: #c00;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 13px;
            text-align: center;
        }

        /* Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-container {
            background: white;
            border-radius: 16px;
            width: 90%;
            max-width: 400px;
            max-height: 90vh;
            overflow-y: auto;
            animation: fadeIn 0.2s ease;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 1px solid #e2e8f0;
        }

        .modal-header h3 {
            font-size: 18px;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #999;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-body input {
            width: 100%;
            padding: 12px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .modal-body button {
            width: 100%;
            padding: 12px;
            background: #851e32;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
        }

        .step { display: none; }
        .step.active { display: block; }

        .code-hint {
            background: #f5f5f5;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            font-size: 12px;
            margin-top: 15px;
        }

        .account-option {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .account-option:hover {
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <h1>CardioWeb</h1>
                <p>Saúde & Monitoramento Cardiológico</p>
            </div>

            <div class="form-title">
                <h2>Acesse sua conta</h2>
                <p>Informe suas credenciais para entrar</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="error-message">❌ <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="input-group">
                    <label>E-mail</label>
                    <input type="email" name="email" class="input-field" placeholder="seuemail@exemplo.com" required>
                </div>
                <div class="input-group">
                    <label>Senha</label>
                    <input type="password" name="password" class="input-field" placeholder="••••••••" required>
                </div>
                <div class="forgot-link">
                    <a onclick="openForgotModal()">Esqueceu a senha?</a>
                </div>
                <button type="submit" name="normal_login" value="1" class="btn-login">
                    <i class="fas fa-arrow-right-to-bracket"></i> Entrar
                </button>
            </form>

            <div class="divider">
                <span>ou acesse com</span>
            </div>

            <div class="social-buttons">
                <button class="social-btn google" onclick="openGoogleModal()">
                    <i class="fab fa-google"></i> Google
                </button>
                <button class="social-btn apple" onclick="openAppleModal()">
                    <i class="fab fa-apple"></i> Apple
                </button>
                <button class="social-btn sms" onclick="openSmsModal()">
                    <i class="fas fa-mobile-alt"></i> SMS
                </button>
            </div>

            <div class="register-prompt">
                Não tem uma conta? <a onclick="openRegisterModal()">Criar conta gratuita</a>
            </div>
        </div>
    </div>

    <!-- Modal Google -->
    <div id="googleModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h3><i class="fab fa-google"></i> Google</h3>
                <button class="modal-close" onclick="closeGoogleModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div style="text-align: center; margin-bottom: 20px;">
                    <i class="fab fa-google" style="font-size: 48px; color: #4285f4;"></i>
                </div>
                <h3 style="text-align: center; margin-bottom: 10px;">Escolha uma conta</h3>
                <p style="text-align: center; font-size: 14px; color: #666; margin-bottom: 20px;">Prosseguir para CardioWeb</p>
                
                <div class="account-option" onclick="selectGoogleAccount('carolina.silva@gmail.com', 'Carolina Silva')">
                    <div style="width: 40px; height: 40px; background: #4285f4; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">C</div>
                    <div>
                        <div style="font-weight: 500;">Carolina Silva</div>
                        <div style="font-size: 12px; color: #666;">carolina.silva@gmail.com</div>
                    </div>
                </div>
                
                <div class="account-option" onclick="selectGoogleAccount('tainara.santos@gmail.com', 'Tainara Santos')">
                    <div style="width: 40px; height: 40px; background: #ea4335; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">T</div>
                    <div>
                        <div style="font-weight: 500;">Tainara Santos</div>
                        <div style="font-size: 12px; color: #666;">tainara.santos@gmail.com</div>
                    </div>
                </div>
                
                <div class="account-option" onclick="showGoogleLoginForm()">
                    <div style="width: 40px; height: 40px; background: #f5f5f5; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-plus" style="color: #666;"></i>
                    </div>
                    <div>
                        <div style="font-weight: 500;">Usar outra conta</div>
                    </div>
                </div>
                
                <div class="cancel-link" style="text-align: center; margin-top: 20px;">
                    <a onclick="closeGoogleModal()" style="color: #666; cursor: pointer;">Cancelar</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Apple -->
    <div id="appleModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h3><i class="fab fa-apple"></i> Apple</h3>
                <button class="modal-close" onclick="closeAppleModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div style="text-align: center; margin-bottom: 20px;">
                    <i class="fab fa-apple" style="font-size: 48px; color: #000;"></i>
                </div>
                <h3 style="text-align: center; margin-bottom: 10px;">Apple Account</h3>
                <p style="text-align: center; font-size: 14px; color: #666; margin-bottom: 20px;">Use sua Apple Account para entrar no CardioWeb</p>
                <input type="email" id="appleEmail" placeholder="E-mail ou telefone">
                <input type="password" id="applePassword" placeholder="Senha">
                <button onclick="loginApple()">Continuar</button>
                <div style="text-align: center; margin-top: 15px;">
                    <a onclick="closeAppleModal()" style="color: #666; cursor: pointer;">Cancelar</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal SMS -->
    <div id="smsModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h3><i class="fas fa-mobile-alt"></i> SMS</h3>
                <button class="modal-close" onclick="closeSmsModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div style="text-align: center; margin-bottom: 20px;">
                    <i class="fas fa-mobile-alt" style="font-size: 48px; color: #851e32;"></i>
                </div>
                <div id="smsStep1" class="step active">
                    <input type="tel" id="smsPhone" placeholder="(11) 99999-9999">
                    <button onclick="sendSmsCode()">Enviar código</button>
                </div>
                <div id="smsStep2" class="step">
                    <input type="text" id="smsCode" placeholder="Digite o código">
                    <button onclick="verifySmsCode()">Verificar</button>
                    <div class="code-hint">Código de teste: <strong>123456</strong></div>
                </div>
                <div style="text-align: center; margin-top: 15px;">
                    <a onclick="closeSmsModal()" style="color: #666; cursor: pointer;">Cancelar</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Esqueceu Senha -->
    <div id="forgotModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h3><i class="fas fa-key"></i> Recuperar senha</h3>
                <button class="modal-close" onclick="closeForgotModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div id="forgotStep1" class="step active">
                    <input type="text" id="resetContact" placeholder="E-mail ou telefone">
                    <button onclick="sendResetCode()">Enviar código</button>
                </div>
                <div id="forgotStep2" class="step">
                    <input type="text" id="resetCode" placeholder="Digite o código">
                    <button onclick="verifyResetCode()">Verificar código</button>
                </div>
                <div id="forgotStep3" class="step">
                    <input type="password" id="newPassword" placeholder="Nova senha">
                    <input type="password" id="confirmPassword" placeholder="Confirmar senha">
                    <button onclick="resetPassword()">Alterar senha</button>
                </div>
                <div style="text-align: center; margin-top: 15px;">
                    <a onclick="closeForgotModal()" style="color: #666; cursor: pointer;">Cancelar</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Registrar -->
    <div id="registerModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h3><i class="fas fa-user-plus"></i> Criar conta</h3>
                <button class="modal-close" onclick="closeRegisterModal()">&times;</button>
            </div>
            <div class="modal-body">
                <input type="text" id="regName" placeholder="Nome completo">
                <input type="email" id="regEmail" placeholder="E-mail">
                <input type="password" id="regPassword" placeholder="Senha">
                <input type="password" id="regConfirmPassword" placeholder="Confirmar senha">
                <button onclick="registerUser()">Criar conta</button>
                <div style="text-align: center; margin-top: 15px;">
                    <a onclick="closeRegisterModal()" style="color: #666; cursor: pointer;">Cancelar</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        let generatedCode = '';
        let resetCode = '';

        // Google
        function openGoogleModal() {
            document.getElementById('googleModal').classList.add('active');
        }
        function closeGoogleModal() {
            document.getElementById('googleModal').classList.remove('active');
        }
        function selectGoogleAccount(email, name) {
            fetch('', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'google_login=1&email=' + encodeURIComponent(email) + '&name=' + encodeURIComponent(name)
            }).then(() => { window.location.href = ''; });
        }
        function showGoogleLoginForm() {
            closeGoogleModal();
            setTimeout(() => {
                const modal = document.getElementById('googleModal');
                const body = modal.querySelector('.modal-body');
                body.innerHTML = `
                    <div style="text-align: center; margin-bottom: 20px;">
                        <i class="fab fa-google" style="font-size: 48px; color: #4285f4;"></i>
                    </div>
                    <h3 style="text-align: center;">Fazer login com Google</h3>
                    <input type="email" id="newGoogleEmail" placeholder="E-mail">
                    <input type="password" id="newGooglePassword" placeholder="Senha">
                    <button onclick="loginNewGoogleAccount()">Continuar</button>
                    <div style="text-align: center; margin-top: 15px;">
                        <a onclick="location.reload()" style="cursor: pointer;">Voltar</a>
                    </div>
                `;
                modal.classList.add('active');
            }, 200);
        }
        function loginNewGoogleAccount() {
            const email = document.getElementById('newGoogleEmail').value;
            if(email) selectGoogleAccount(email, email.split('@')[0]);
            else alert('Preencha o e-mail');
        }

        // Apple
        function openAppleModal() {
            document.getElementById('appleModal').classList.add('active');
        }
        function closeAppleModal() {
            document.getElementById('appleModal').classList.remove('active');
        }
        function loginApple() {
            const email = document.getElementById('appleEmail').value;
            if(email) {
                fetch('', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'apple_login=1&email=' + encodeURIComponent(email) + '&name=Usuário Apple'
                }).then(() => { window.location.href = ''; });
            } else alert('Preencha o e-mail');
        }

        // SMS
        function openSmsModal() {
            document.getElementById('smsModal').classList.add('active');
            document.getElementById('smsStep1').classList.add('active');
            document.getElementById('smsStep2').classList.remove('active');
        }
        function closeSmsModal() {
            document.getElementById('smsModal').classList.remove('active');
        }
        function sendSmsCode() {
            const phone = document.getElementById('smsPhone').value;
            if(!phone) { alert('Digite seu telefone'); return; }
            generatedCode = Math.floor(100000 + Math.random() * 900000);
            alert(`Código: ${generatedCode}`);
            document.getElementById('smsStep1').classList.remove('active');
            document.getElementById('smsStep2').classList.add('active');
        }
        function verifySmsCode() {
            const code = document.getElementById('smsCode').value;
            if(code == generatedCode || code == '123456') {
                const phone = document.getElementById('smsPhone').value;
                fetch('', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'sms_login=1&telefone=' + encodeURIComponent(phone)
                }).then(() => { window.location.href = ''; });
            } else alert('Código inválido!');
        }

        // Esqueceu senha
        function openForgotModal() {
            document.getElementById('forgotModal').classList.add('active');
            document.getElementById('forgotStep1').classList.add('active');
            document.getElementById('forgotStep2').classList.remove('active');
            document.getElementById('forgotStep3').classList.remove('active');
        }
        function closeForgotModal() {
            document.getElementById('forgotModal').classList.remove('active');
        }
        function sendResetCode() {
            const contact = document.getElementById('resetContact').value;
            if(!contact) { alert('Digite seu e-mail ou telefone'); return; }
            resetCode = Math.floor(100000 + Math.random() * 900000);
            alert(`Código: ${resetCode}`);
            document.getElementById('forgotStep1').classList.remove('active');
            document.getElementById('forgotStep2').classList.add('active');
        }
        function verifyResetCode() {
            const code = document.getElementById('resetCode').value;
            if(code == resetCode) {
                document.getElementById('forgotStep2').classList.remove('active');
                document.getElementById('forgotStep3').classList.add('active');
            } else alert('Código incorreto!');
        }
        function resetPassword() {
            const newPass = document.getElementById('newPassword').value;
            const confirmPass = document.getElementById('confirmPassword').value;
            if(!newPass || !confirmPass) alert('Preencha todos os campos');
            else if(newPass.length < 6) alert('Mínimo 6 caracteres');
            else if(newPass !== confirmPass) alert('As senhas não coincidem');
            else { alert('Senha alterada com sucesso!'); closeForgotModal(); }
        }

        // Registrar
        function openRegisterModal() {
            document.getElementById('registerModal').classList.add('active');
        }
        function closeRegisterModal() {
            document.getElementById('registerModal').classList.remove('active');
        }
        function registerUser() {
            const name = document.getElementById('regName').value;
            const email = document.getElementById('regEmail').value;
            const password = document.getElementById('regPassword').value;
            const confirm = document.getElementById('regConfirmPassword').value;
            if(!name || !email || !password) alert('Preencha todos os campos');
            else if(password.length < 6) alert('Mínimo 6 caracteres');
            else if(password !== confirm) alert('As senhas não coincidem');
            else { alert('Conta criada com sucesso! Faça login.'); closeRegisterModal(); }
        }
    </script>
</body>
</html>