<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>CardioWeb - Login | Plataforma de Saúde</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
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
            border-radius: 12px;
            width: 90%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: modalFadeIn 0.3s ease;
            overflow: hidden;
        }
        
        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid #e5e5ea;
            background: white;
        }
        
        .modal-header h3 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        
        .modal-header h3 i {
            margin-right: 8px;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #999;
        }
        
        .modal-close:hover {
            color: #333;
        }
        
        .modal-body {
            padding: 20px;
            background: white;
            min-height: 200px;
        }
        
        .form-modal h2 {
            font-size: 22px;
            text-align: center;
            margin-bottom: 10px;
        }
        
        .form-modal p {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .form-modal .input-group {
            margin-bottom: 15px;
        }
        
        .form-modal input, .form-modal select {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            box-sizing: border-box;
        }
        
        .form-modal input:focus, .form-modal select:focus {
            outline: none;
            border-color: #851e32;
        }
        
        .form-modal button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            background: #851e32;
            color: white;
            margin-top: 10px;
        }
        
        .form-modal button:hover {
            background: #6a182c;
        }
        
        .form-modal .cancel-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .form-modal .cancel-link a {
            color: #666;
            text-decoration: none;
            font-size: 14px;
        }
        
        .form-modal .cancel-link a:hover {
            color: #851e32;
        }
        
        .step {
            display: none;
        }
        
        .step.active {
            display: block;
        }
        
        .timer {
            text-align: center;
            font-size: 12px;
            color: #666;
            margin-top: 10px;
        }
        
        .code-hint {
            background: #f5f5f5;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            margin-top: 15px;
            font-size: 12px;
        }
        
        .account-option {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px;
            border: 1px solid #e5e5ea;
            border-radius: 12px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .account-option:hover {
            background: #f8f9fa;
        }
        
        .account-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }
        
        .account-info {
            flex: 1;
        }
        
        .account-name {
            font-weight: 500;
            font-size: 15px;
        }
        
        .account-email {
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>

    <div class="login-container">
        <div class="brand-side">
            <div class="logo-area">
                <div class="logo-icon">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <div class="logo-text">
                    <h2>CardioWeb</h2>
                    <p>Saúde & Monitoramento Cardiológico</p>
                </div>
            </div>
            <div class="hero-message">
                <h1>Cuidar de você<br>é nossa essência</h1>
                <p>Acesso seguro aos seus exames, consultas e orientações personalizadas em um só lugar.</p>
                <div class="health-stats">
                    <div class="stat"><i class="fas fa-chart-line"></i> +120k pacientes</div>
                    <div class="stat"><i class="fas fa-user-md"></i> Especialistas 24h</div>
                    <div class="stat"><i class="fas fa-lock"></i> Dados protegidos</div>
                </div>
                <div class="testimonial">
                    <i class="fas fa-quote-left" style="margin-right: 6px; font-size: 0.7rem;"></i> 
                    Plataforma intuitiva e segura. Me sintonizou com minha rotina de saúde.
                    <br>— Mariana S.
                </div>
            </div>
        </div>

        <div class="form-side">
            <div class="form-header">
                <h3>Acesse sua conta</h3>
                <p>Informe suas credenciais para entrar</p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div style="background: #fee; color: #c00; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 14px;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['success'])): ?>
                <div style="background: #e8f5e9; color: #2e7d32; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 14px;">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>

            <form action="auth-process.php" method="POST">
                <input type="hidden" name="login_type" value="email">
                <div class="input-group">
                    <div class="input-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" class="input-field" placeholder="seuemail@exemplo.com" required>
                    </div>
                </div>
                <div class="input-group">
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" class="input-field" placeholder="Senha" required>
                    </div>
                </div>
                <div class="forgot-row">
                    <a href="#" class="forgot-link" onclick="openForgotModal(); return false;">Esqueceu a senha?</a>
                </div>
                <button type="submit" class="btn-login">
                    <i class="fas fa-arrow-right-to-bracket"></i> Entrar
                </button>
            </form>

            <div class="divider">
                <span>ou acesse com</span>
            </div>

            <div class="social-login">
                <button type="button" class="social-btn" onclick="openGoogleModal()">
                    <i class="fab fa-google"></i> Google
                </button>
                <button type="button" class="social-btn" onclick="openAppleModal()">
                    <i class="fab fa-apple"></i> Apple
                </button>
                <button type="button" class="social-btn" onclick="openSmsModal()">
                    <i class="fas fa-mobile-alt"></i> SMS
                </button>
            </div>

            <div class="register-prompt">
                Não tem uma conta? <a href="register.php">Criar conta gratuita</a>
            </div>
        </div>
    </div>

    <!-- Modal para Google -->
    <div id="googleModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h3><i class="fab fa-google"></i> Google</h3>
                <button class="modal-close" onclick="closeGoogleModal()">&times;</button>
            </div>
            <div class="modal-body" id="googleModalBody"></div>
        </div>
    </div>

    <!-- Modal para Apple -->
    <div id="appleModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h3><i class="fab fa-apple"></i> Apple</h3>
                <button class="modal-close" onclick="closeAppleModal()">&times;</button>
            </div>
            <div class="modal-body" id="appleModalBody"></div>
        </div>
    </div>

    <!-- Modal para SMS -->
    <div id="smsModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h3><i class="fas fa-mobile-alt"></i> SMS</h3>
                <button class="modal-close" onclick="closeSmsModal()">&times;</button>
            </div>
            <div class="modal-body" id="smsModalBody"></div>
        </div>
    </div>

    <!-- Modal para Esqueceu a Senha -->
    <div id="forgotModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h3><i class="fas fa-key"></i> Recuperar senha</h3>
                <button class="modal-close" onclick="closeForgotModal()">&times;</button>
            </div>
            <div class="modal-body" id="forgotModalBody"></div>
        </div>
    </div>

    <script>
        // ==================== GOOGLE ====================
        function openGoogleModal() {
            const modal = document.getElementById('googleModal');
            const modalBody = document.getElementById('googleModalBody');
            
            const accounts = [
                { name: 'Carolina Silva', email: 'carolina.silva@gmail.com', avatar: 'C', color: '#4285f4' },
                { name: 'Tainara Santos', email: 'tainara.santos@gmail.com', avatar: 'T', color: '#ea4335' },
                { name: 'João Paulo', email: 'joao.paulo@gmail.com', avatar: 'J', color: '#34a853' }
            ];
            
            modalBody.innerHTML = `
                <div class="form-modal">
                    <div style="text-align: center; margin-bottom: 20px;">
                        <i class="fab fa-google" style="font-size: 48px; color: #4285f4;"></i>
                    </div>
                    <h2>Escolha uma conta</h2>
                    <p>Prosseguir para CardioWeb</p>
                    
                    ${accounts.map(acc => `
                        <div class="account-option" onclick="loginGoogle('${acc.email}', '${acc.name}')">
                            <div class="account-avatar" style="background: ${acc.color};">${acc.avatar}</div>
                            <div class="account-info">
                                <div class="account-name">${acc.name}</div>
                                <div class="account-email">${acc.email}</div>
                            </div>
                        </div>
                    `).join('')}
                    
                    <button onclick="openGoogleForm()" style="width:100%; padding:12px; border:1px solid #ddd; background:#f5f5f5; color:#333; margin-top: 10px; border-radius: 8px; cursor: pointer;">
                        <i class="fas fa-plus"></i> Usar outra conta
                    </button>
                    
                    <div class="cancel-link">
                        <a href="#" onclick="closeGoogleModal(); return false;">Cancelar</a>
                    </div>
                </div>
            `;
            
            modal.classList.add('active');
        }
        
        function openGoogleForm() {
            const modalBody = document.getElementById('googleModalBody');
            modalBody.innerHTML = `
                <div class="form-modal">
                    <div style="text-align: center; margin-bottom: 20px;">
                        <i class="fab fa-google" style="font-size: 48px; color: #4285f4;"></i>
                    </div>
                    <h2>Fazer login com Google</h2>
                    <p>Prosseguir para CardioWeb</p>
                    <div class="input-group">
                        <input type="email" id="googleEmail" placeholder="E-mail" required>
                    </div>
                    <div class="input-group">
                        <input type="password" id="googlePassword" placeholder="Senha" required>
                    </div>
                    <button onclick="loginGoogle(document.getElementById('googleEmail').value, 'Usuário Google')">Continuar</button>
                    <div class="cancel-link">
                        <a href="#" onclick="openGoogleModal(); return false;">Voltar</a>
                    </div>
                </div>
            `;
        }
        
        function loginGoogle(email, name) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'auth-process.php';
            form.innerHTML = `
                <input type="hidden" name="login_type" value="google">
                <input type="hidden" name="email" value="${email}">
                <input type="hidden" name="name" value="${name}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
        
        function closeGoogleModal() {
            document.getElementById('googleModal').classList.remove('active');
        }
        
        // ==================== APPLE ====================
        function openAppleModal() {
            const modal = document.getElementById('appleModal');
            const modalBody = document.getElementById('appleModalBody');
            
            modalBody.innerHTML = `
                <div class="form-modal">
                    <div style="text-align: center; margin-bottom: 20px;">
                        <i class="fab fa-apple" style="font-size: 48px; color: #000;"></i>
                    </div>
                    <h2>Apple Account</h2>
                    <p>Use sua Apple Account para entrar</p>
                    <div class="input-group">
                        <input type="email" id="appleEmail" placeholder="E-mail" required>
                    </div>
                    <div class="input-group">
                        <input type="password" id="applePassword" placeholder="Senha" required>
                    </div>
                    <button onclick="loginApple()">Continuar</button>
                    <div class="cancel-link">
                        <a href="#" onclick="closeAppleModal(); return false;">Cancelar</a>
                    </div>
                </div>
            `;
            
            modal.classList.add('active');
        }
        
        function loginApple() {
            const email = document.getElementById('appleEmail').value;
            if (!email) {
                alert('Preencha o e-mail');
                return;
            }
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'auth-process.php';
            form.innerHTML = `
                <input type="hidden" name="login_type" value="apple">
                <input type="hidden" name="email" value="${email}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
        
        function closeAppleModal() {
            document.getElementById('appleModal').classList.remove('active');
        }
        
        // ==================== SMS ====================
        function openSmsModal() {
            const modal = document.getElementById('smsModal');
            const modalBody = document.getElementById('smsModalBody');
            
            modalBody.innerHTML = `
                <div class="form-modal">
                    <div style="text-align: center; margin-bottom: 20px;">
                        <i class="fas fa-mobile-alt" style="font-size: 48px; color: #851e32;"></i>
                    </div>
                    <div id="smsStep1" class="step active">
                        <h2>Login por SMS</h2>
                        <p>Digite seu telefone para receber um código</p>
                        <div class="input-group">
                            <input type="tel" id="smsPhone" placeholder="(11) 99999-9999" required>
                        </div>
                        <button onclick="sendSmsCode()">Enviar código</button>
                    </div>
                    <div id="smsStep2" class="step">
                        <h2>Verificar código</h2>
                        <p>Digite o código recebido por SMS</p>
                        <div class="input-group">
                            <input type="text" id="smsCode" placeholder="Digite o código" maxlength="6" required>
                        </div>
                        <button onclick="verifySmsCode()">Verificar</button>
                        <div class="code-hint">Código de teste: <strong>123456</strong></div>
                    </div>
                    <div class="cancel-link">
                        <a href="#" onclick="closeSmsModal(); return false;">Cancelar</a>
                    </div>
                </div>
            `;
            
            modal.classList.add('active');
        }
        
        function sendSmsCode() {
            const phone = document.getElementById('smsPhone').value;
            if (!phone) {
                alert('Digite seu telefone');
                return;
            }
            document.getElementById('smsStep1').classList.remove('active');
            document.getElementById('smsStep2').classList.add('active');
        }
        
        function verifySmsCode() {
            const code = document.getElementById('smsCode').value;
            const phone = document.getElementById('smsPhone').value;
            
            if (code !== '123456') {
                alert('Código inválido! Use o código exibido (teste: 123456)');
                return;
            }
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'auth-process.php';
            form.innerHTML = `
                <input type="hidden" name="login_type" value="sms">
                <input type="hidden" name="phone" value="${phone}">
                <input type="hidden" name="code" value="${code}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
        
        function closeSmsModal() {
            document.getElementById('smsModal').classList.remove('active');
        }
        
        // ==================== RECUPERAR SENHA ====================
        function openForgotModal() {
            const modal = document.getElementById('forgotModal');
            const modalBody = document.getElementById('forgotModalBody');
            
            modalBody.innerHTML = `
                <div class="form-modal">
                    <h2>Recuperar senha</h2>
                    <p>Digite seu e-mail para receber um código de verificação</p>
                    <div class="input-group">
                        <input type="email" id="resetEmail" placeholder="Seu e-mail" required>
                    </div>
                    <button onclick="sendResetCode()">Enviar código</button>
                    <div class="cancel-link">
                        <a href="#" onclick="closeForgotModal(); return false;">Cancelar</a>
                    </div>
                </div>
            `;
            
            modal.classList.add('active');
        }
        
        function sendResetCode() {
            const email = document.getElementById('resetEmail').value;
            if (!email) {
                alert('Digite seu e-mail');
                return;
            }
            alert('Código enviado para: ' + email);
            closeForgotModal();
        }
        
        function closeForgotModal() {
            document.getElementById('forgotModal').classList.remove('active');
        }
        
        // Fechar modais ao clicar fora
        ['googleModal', 'appleModal', 'smsModal', 'forgotModal'].forEach(id => {
            document.getElementById(id).addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>
