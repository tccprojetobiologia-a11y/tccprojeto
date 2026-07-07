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
            min-height: 250px;
        }
        
        .form-modal h2 {
            font-size: 22px;
            text-align: center;
            margin-bottom: 10px;
        }
        
        .form-modal p {
            text-align: center;
            color: #666;
            margin-bottom: 25px;
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
        
        .resend-btn {
            background: transparent;
            color: #851e32;
            border: 1px solid #851e32;
            margin-top: 10px;
        }
        
        .resend-btn:hover {
            background: #851e32;
            color: white;
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
        
        .google-logo-modal {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .google-logo-modal i {
            font-size: 48px;
            color: #4285f4;
        }
        
        .privacy-text {
            font-size: 11px;
            color: #999;
            text-align: center;
            margin-top: 20px;
        }
        
        .privacy-text a {
            color: #4285f4;
            text-decoration: none;
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
                <div style="background: #fee; color: #c00; padding: 10px; border-radius: 8px; margin-bottom: 15px;">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['success'])): ?>
                <div style="background: #e8f5e9; color: #2e7d32; padding: 10px; border-radius: 8px; margin-bottom: 15px;">
                    <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>

            <form action="process_login.php" method="POST">
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
                <button class="social-btn" onclick="openGoogleModal()">
                    <i class="fab fa-google"></i> Google
                </button>
                <button class="social-btn" onclick="openAppleModal()">
                    <i class="fab fa-apple"></i> Apple
                </button>
                <button class="social-btn" onclick="openSmsModal()">
                    <i class="fas fa-mobile-alt"></i> SMS
                </button>
            </div>

            <div class="register-prompt">
                Não tem uma conta? <a href="register.php">Criar conta gratuita</a>
            </div>
        </div>
    </div>

    <div id="toastMsg" class="toast-message">✔️ Ação realizada</div>

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
        let resetTimerInterval;
        let resetTimeLeft = 60;
        
        // ==================== MODAL GOOGLE ====================
        function openGoogleModal() {
            const modal = document.getElementById('googleModal');
            const modalBody = document.getElementById('googleModalBody');
            
            // Contas simuladas
            const accounts = [
                { name: 'Carolina Silva', email: 'carolina.silva@gmail.com', avatar: 'C', color: '#4285f4' },
                { name: 'Tainara Santos', email: 'tainara.santos@gmail.com', avatar: 'T', color: '#ea4335' },

            ];
            
            modalBody.innerHTML = `
                <div class="form-modal">
                    <div class="google-logo-modal">
                        <i class="fab fa-google"></i>
                    </div>
                    <h2>Escolha uma conta</h2>
                    <p>Prosseguir para CardioWeb.com</p>
                    
                    ${accounts.map(acc => `
                        <div class="account-option" onclick="selectGoogleAccount('${acc.email}', '${acc.name}')">
                            <div class="account-avatar" style="background: ${acc.color};">${acc.avatar}</div>
                            <div class="account-info">
                                <div class="account-name">${acc.name}</div>
                                <div class="account-email">${acc.email}</div>
                            </div>
                        </div>
                    `).join('')}
                    
                    <div class="account-option" onclick="addNewGoogleAccount()" style="margin-top: 10px;">
                        <div style="width: 40px; height: 40px; background: #f5f5f5; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-plus" style="color: #666;"></i>
                        </div>
                        <div class="account-info">
                            <div class="account-name">Usar outra conta</div>
                        </div>
                    </div>
                    
                    <div class="privacy-text">
                        Consulte a <a href="#">Política de Privacidade</a> e os 
                        <a href="#">Termos de Serviço</a> do CardioWeb antes de usá-lo.
                    </div>
                    
                    <div class="cancel-link">
                        <a href="#" onclick="closeGoogleModal(); return false;">Cancelar</a>
                    </div>
                </div>
            `;
            
            modal.classList.add('active');
        }
        
        function selectGoogleAccount(email, name) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'process_google_login.php';
            form.style.display = 'none';

            const inputEmail = document.createElement('input');
            inputEmail.type = 'hidden';
            inputEmail.name = 'email';
            inputEmail.value = email;
            form.appendChild(inputEmail);

            const inputName = document.createElement('input');
            inputName.type = 'hidden';
            inputName.name = 'name';
            inputName.value = name;
            form.appendChild(inputName);

            document.body.appendChild(form);
            form.submit();
        }
        
        function addNewGoogleAccount() {
            closeGoogleModal();
            setTimeout(() => {
                openGoogleLoginForm();
            }, 300);
        }
        
        function openGoogleLoginForm() {
            const modal = document.getElementById('googleModal');
            const modalBody = document.getElementById('googleModalBody');
            
            modalBody.innerHTML = `
                <div class="form-modal">
                    <div class="google-logo-modal">
                        <i class="fab fa-google"></i>
                    </div>
                    <h2>Fazer login com Google</h2>
                    <p>Prosseguir para CardioWeb</p>
                    <div class="input-group">
                        <input type="email" id="newGoogleEmail" placeholder="E-mail" required>
                    </div>
                    <div class="input-group">
                        <input type="password" id="newGooglePassword" placeholder="Senha" required>
                    </div>
                    <button onclick="loginNewGoogleAccount()">Continuar</button>
                    <div class="cancel-link">
                        <a href="#" onclick="openGoogleModal(); return false;">Voltar</a>
                    </div>
                </div>
            `;
            
            modal.classList.add('active');
        }
        
        function loginNewGoogleAccount() {
            const email = document.getElementById('newGoogleEmail').value;
            if(email) {
                const name = email.split('@')[0];
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'process_google_login.php';
                form.style.display = 'none';

                const inputEmail = document.createElement('input');
                inputEmail.type = 'hidden';
                inputEmail.name = 'email';
                inputEmail.value = email;
                form.appendChild(inputEmail);

                const inputName = document.createElement('input');
                inputName.type = 'hidden';
                inputName.name = 'name';
                inputName.value = name;
                form.appendChild(inputName);

                document.body.appendChild(form);
                form.submit();
            } else {
                alert('Preencha o e-mail');
            }
        }
        
        function closeGoogleModal() {
            document.getElementById('googleModal').classList.remove('active');
        }
        
        // ==================== MODAL APPLE ====================
        function openAppleModal() {
            const modal = document.getElementById('appleModal');
            const modalBody = document.getElementById('appleModalBody');
            
            modalBody.innerHTML = `
                <div class="form-modal">
                    <div style="text-align: center; margin-bottom: 20px;">
                        <i class="fab fa-apple" style="font-size: 48px; color: #000;"></i>
                    </div>
                    <h2>Apple Account</h2>
                    <p>Use sua Apple Account para entrar no CardioWeb</p>
                    <div class="input-group">
                        <input type="email" id="appleEmail" placeholder="E-mail ou telefone" required>
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
            if(email) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'process_apple_login.php';
                form.style.display = 'none';

                const inputEmail = document.createElement('input');
                inputEmail.type = 'hidden';
                inputEmail.name = 'email';
                inputEmail.value = email;
                form.appendChild(inputEmail);

                document.body.appendChild(form);
                form.submit();
            } else {
                alert('Preencha os campos');
            }
        }
        
        function closeAppleModal() {
            document.getElementById('appleModal').classList.remove('active');
        }
        
        // ==================== MODAL SMS ====================
        function openSmsModal() {
            const modal = document.getElementById('smsModal');
            const modalBody = document.getElementById('smsModalBody');
            
            modalBody.innerHTML = `
                <div class="form-modal">
                    <div style="text-align: center; margin-bottom: 20px;">
                        <i class="fas fa-mobile-alt" style="font-size: 48px; color: #851e32;"></i>
                    </div>
                    <div id="smsStep1" class="step active">
                        <div class="input-group">
                            <input type="tel" id="smsPhone" placeholder="(11) 99999-9999" required>
                        </div>
                        <button onclick="sendSmsLoginCode()">Enviar código</button>
                    </div>
                    <div id="smsStep2" class="step">
                        <div class="input-group">
                            <input type="text" id="smsCode" placeholder="Digite o código" maxlength="6" required>
                        </div>
                        <button onclick="verifySmsCode()">Verificar</button>
                        <div class="timer" id="smsTimer">60 segundos para reenviar</div>
                        <div class="code-hint">Código de teste: <strong>123456</strong></div>
                    </div>
                    <div class="cancel-link">
                        <a href="#" onclick="closeSmsModal(); return false;">Cancelar</a>
                    </div>
                </div>
            `;
            
            modal.classList.add('active');
        }
        
        function sendSmsLoginCode() {
            const phone = document.getElementById('smsPhone').value;
            if(!phone) {
                alert('Digite seu telefone');
                return;
            }
            const code = Math.floor(100000 + Math.random() * 900000);
            alert(`Código de verificação: ${code}\n(Em produção, isso seria enviado por SMS)`);
            document.getElementById('smsStep1').classList.remove('active');
            document.getElementById('smsStep2').classList.add('active');
            
            let timeLeft = 60;
            const timerElement = document.getElementById('smsTimer');
            const interval = setInterval(function() {
                timeLeft--;
                if(timerElement) timerElement.textContent = timeLeft + ' segundos para reenviar';
                if(timeLeft <= 0) clearInterval(interval);
            }, 1000);
        }
        
        function verifySmsCode() {
            const code = document.getElementById('smsCode').value;
            const phone = document.getElementById('smsPhone').value;
            if(code == '123456' && phone) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'process_sms_login.php';
                form.style.display = 'none';

                const inputPhone = document.createElement('input');
                inputPhone.type = 'hidden';
                inputPhone.name = 'phone';
                inputPhone.value = phone;
                form.appendChild(inputPhone);

                const inputCode = document.createElement('input');
                inputCode.type = 'hidden';
                inputCode.name = 'code';
                inputCode.value = code;
                form.appendChild(inputCode);

                document.body.appendChild(form);
                form.submit();
            } else {
                alert('Código inválido! Use: 123456');
            }
        }
        
        function closeSmsModal() {
            document.getElementById('smsModal').classList.remove('active');
        }
        
        // ==================== ESQUECEU A SENHA ====================
        function openForgotModal() {
            const modal = document.getElementById('forgotModal');
            const modalBody = document.getElementById('forgotModalBody');
            
            modalBody.innerHTML = `
                <div class="form-modal">
                    <h2>Recuperar senha</h2>
                    <p>Digite seu e-mail ou telefone para receber um código de verificação</p>
                    <div id="forgotStep1">
                        <div class="input-group">
                            <input type="text" id="resetContact" placeholder="E-mail ou telefone (com DDD)" required>
                        </div>
                        <div class="input-group">
                            <select id="contactType">
                                <option value="email">Enviar código por E-mail</option>
                                <option value="sms">Enviar código por SMS</option>
                            </select>
                        </div>
                        <button onclick="sendResetCode()">Enviar código</button>
                        <div class="cancel-link">
                            <a href="#" onclick="closeForgotModal(); return false;">Cancelar</a>
                        </div>
                    </div>
                </div>
            `;
            
            modal.classList.add('active');
        }
        
        function sendResetCode() {
            const contact = document.getElementById('resetContact').value;
            const contactType = document.getElementById('contactType').value;
            
            if(!contact) {
                alert('Digite seu e-mail ou telefone');
                return;
            }
            
            const code = Math.floor(100000 + Math.random() * 900000);
            
            if(contactType === 'email') {
                alert(`Código de verificação enviado para o e-mail: ${contact}\nCódigo: ${code}`);
            } else {
                alert(`Código de verificação enviado por SMS para: ${contact}\nCódigo: ${code}`);
            }
            
            sessionStorage.setItem('resetCode', code);
            sessionStorage.setItem('resetContact', contact);
            sessionStorage.setItem('resetType', contactType);
            
            showCodeVerificationStep();
        }
        
        function showCodeVerificationStep() {
            const modalBody = document.getElementById('forgotModalBody');
            
            modalBody.innerHTML = `
                <div class="form-modal">
                    <h2>Verifique seu código</h2>
                    <p>Digite o código que enviamos para você</p>
                    <div id="forgotStep2">
                        <div class="input-group">
                            <input type="text" id="resetCode" placeholder="Digite o código de 6 dígitos" maxlength="6" required>
                        </div>
                        <button onclick="verifyResetCode()">Verificar código</button>
                        <div class="timer">
                            <span id="resetTimer">60</span> segundos para reenviar
                        </div>
                        <button class="resend-btn" onclick="resendResetCode()" id="resendResetBtn" disabled>Reenviar código</button>
                        <div class="cancel-link">
                            <a href="#" onclick="closeForgotModal(); return false;">Cancelar</a>
                        </div>
                    </div>
                </div>
            `;
            
            startResetTimer();
        }
        
        function startResetTimer() {
            resetTimeLeft = 60;
            const timerElement = document.getElementById('resetTimer');
            const resendBtn = document.getElementById('resendResetBtn');
            
            if(resetTimerInterval) clearInterval(resetTimerInterval);
            
            resetTimerInterval = setInterval(function() {
                resetTimeLeft--;
                if(timerElement) timerElement.textContent = resetTimeLeft;
                
                if(resetTimeLeft <= 0) {
                    clearInterval(resetTimerInterval);
                    if(resendBtn) {
                        resendBtn.disabled = false;
                        resendBtn.style.opacity = '1';
                    }
                }
            }, 1000);
        }
        
        function resendResetCode() {
            const contact = sessionStorage.getItem('resetContact');
            const contactType = sessionStorage.getItem('resetType');
            const newCode = Math.floor(100000 + Math.random() * 900000);
            
            if(contactType === 'email') {
                alert(`Novo código enviado para o e-mail: ${contact}\nCódigo: ${newCode}`);
            } else {
                alert(`Novo código enviado por SMS para: ${contact}\nCódigo: ${newCode}`);
            }
            
            sessionStorage.setItem('resetCode', newCode);
            
            clearInterval(resetTimerInterval);
            startResetTimer();
            
            const resendBtn = document.getElementById('resendResetBtn');
            if(resendBtn) {
                resendBtn.disabled = true;
                resendBtn.style.opacity = '0.5';
            }
        }
        
        function verifyResetCode() {
            const enteredCode = document.getElementById('resetCode').value;
            const savedCode = sessionStorage.getItem('resetCode');
            
            if(enteredCode == savedCode) {
                showNewPasswordStep();
            } else {
                alert('Código incorreto! Tente novamente.');
            }
        }
        
        function showNewPasswordStep() {
            const modalBody = document.getElementById('forgotModalBody');
            
            modalBody.innerHTML = `
                <div class="form-modal">
                    <h2>Criar nova senha</h2>
                    <p>Digite sua nova senha</p>
                    <div id="forgotStep3">
                        <div class="input-group">
                            <input type="password" id="newPassword" placeholder="Nova senha" required>
                        </div>
                        <div class="input-group">
                            <input type="password" id="confirmPassword" placeholder="Confirmar senha" required>
                        </div>
                        <button onclick="resetPassword()">Alterar senha</button>
                        <div class="cancel-link">
                            <a href="#" onclick="closeForgotModal(); return false;">Cancelar</a>
                        </div>
                    </div>
                </div>
            `;
        }
        
        function resetPassword() {
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if(!newPassword || !confirmPassword) {
                alert('Preencha todos os campos');
                return;
            }
            
            if(newPassword.length < 6) {
                alert('A senha deve ter no mínimo 6 caracteres');
                return;
            }
            
            if(newPassword !== confirmPassword) {
                alert('As senhas não coincidem');
                return;
            }
            
            alert('Senha alterada com sucesso! Faça login com sua nova senha.');
            closeForgotModal();
            
            sessionStorage.removeItem('resetCode');
            sessionStorage.removeItem('resetContact');
            sessionStorage.removeItem('resetType');
        }
        
        function closeForgotModal() {
            const modal = document.getElementById('forgotModal');
            modal.classList.remove('active');
            if(resetTimerInterval) clearInterval(resetTimerInterval);
        }
        
        // Fechar modais ao clicar fora
        document.getElementById('googleModal').addEventListener('click', function(e) {
            if(e.target === this) closeGoogleModal();
        });
        
        document.getElementById('appleModal').addEventListener('click', function(e) {
            if(e.target === this) closeAppleModal();
        });
        
        document.getElementById('smsModal').addEventListener('click', function(e) {
            if(e.target === this) closeSmsModal();
        });
        
        document.getElementById('forgotModal').addEventListener('click', function(e) {
            if(e.target === this) closeForgotModal();
        });
        
        function showToast(message) {
            const toast = document.getElementById('toastMsg');
            toast.textContent = message;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        }
    </script>
</body>
</html>