<?php
session_start();
header('X-Frame-Options: SAMEORIGIN');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login via SMS - CardioWeb</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: #ffffff;
            padding: 20px;
            height: 100vh;
            overflow-y: auto;
        }
        
        .container {
            width: 100%;
            max-width: 100%;
        }
        
        .sms-logo {
            text-align: center;
            margin-bottom: 24px;
        }
        
        .sms-logo i {
            font-size: 48px;
            color: #851e32;
        }
        
        h1 {
            font-size: 22px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 8px;
            color: #1d1c1e;
        }
        
        .subtitle {
            text-align: center;
            color: #6c6c70;
            margin-bottom: 24px;
            font-size: 14px;
        }
        
        .input-group {
            margin-bottom: 16px;
        }
        
        .input-group input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #e5e5ea;
            border-radius: 10px;
            font-size: 15px;
            text-align: center;
            box-sizing: border-box;
        }
        
        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            background: #851e32;
            color: white;
        }
        
        .btn:hover {
            background: #6a182c;
        }
        
        .code-hint {
            background: #f0f0f0;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            margin-top: 16px;
            font-size: 12px;
            color: #666;
        }
        
        .footer-note {
            text-align: center;
            margin-top: 20px;
            font-size: 11px;
            color: #6c6c70;
        }
        
        .footer-note a {
            color: #851e32;
            text-decoration: none;
        }
        
        .error {
            background: #fee;
            color: #c00;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 16px;
            text-align: center;
            font-size: 13px;
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
            margin-top: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sms-logo">
            <i class="fas fa-mobile-alt"></i>
        </div>
        
        <h1>CardioWeb</h1>
        <p class="subtitle">Entre com seu número de telefone</p>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="error">❌ <?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>
        
        <!-- Passo 1: Número de telefone -->
        <div id="step1" class="step active">
            <div class="input-group">
                <input type="tel" id="phone" placeholder="(11) 99999-9999" required>
            </div>
            <button class="btn" onclick="sendCode()">
                <i class="fas fa-paper-plane"></i> Enviar código
            </button>
        </div>
        
        <!-- Passo 2: Código de verificação -->
        <div id="step2" class="step">
            <form action="process_sms.php" method="POST" target="_top">
                <div class="input-group">
                    <input type="hidden" name="telefone" id="telefoneHidden">
                    <input type="text" name="codigo" placeholder="000000" maxlength="6" required autocomplete="off">
                </div>
                <button type="submit" class="btn">
                    <i class="fas fa-check"></i> Verificar
                </button>
            </form>
            <div class="timer">
                <span id="timer">60</span> segundos para reenviar
            </div>
            <button class="btn" onclick="resendCode()" id="resendBtn" disabled style="background: #ccc; margin-top: 8px;">
                Reenviar código
            </button>
            <div class="code-hint">
                <i class="fas fa-info-circle"></i> Código de teste: <strong>123456</strong>
            </div>
        </div>
        
        <div class="footer-note">
            <a href="#" onclick="window.parent.closeSocialModal(); return false;">Cancelar</a>
        </div>
    </div>

    <script>
        let timerInterval;
        let timeLeft = 60;
        
        function sendCode() {
            const phone = document.getElementById('phone').value;
            if (!phone) {
                alert('Digite seu número de telefone');
                return;
            }
            
            const code = Math.floor(100000 + Math.random() * 900000);
            alert(`Código de verificação: ${code}\n(Em produção, isso seria enviado por SMS)`);
            
            document.getElementById('telefoneHidden').value = phone;
            document.getElementById('step1').classList.remove('active');
            document.getElementById('step2').classList.add('active');
            
            startTimer();
        }
        
        function startTimer() {
            timeLeft = 60;
            const timerElement = document.getElementById('timer');
            const resendBtn = document.getElementById('resendBtn');
            
            timerInterval = setInterval(function() {
                timeLeft--;
                timerElement.textContent = timeLeft;
                
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    resendBtn.disabled = false;
                    resendBtn.style.background = '#851e32';
                    timerElement.textContent = '0';
                }
            }, 1000);
        }
        
        function resendCode() {
            const phone = document.getElementById('telefoneHidden').value;
            const newCode = Math.floor(100000 + Math.random() * 900000);
            alert(`Novo código: ${newCode}`);
            
            clearInterval(timerInterval);
            const resendBtn = document.getElementById('resendBtn');
            resendBtn.disabled = true;
            resendBtn.style.background = '#ccc';
            startTimer();
        }
    </script>
</body>
</html>