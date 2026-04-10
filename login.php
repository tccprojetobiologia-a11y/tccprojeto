<?php
// Se estiver sendo carregado dentro do iframe, remove headers desnecessários
$isIframe = isset($_SERVER['HTTP_SEC_FETCH_DEST']) && $_SERVER['HTTP_SEC_FETCH_DEST'] === 'iframe';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login com Google - CardioWeb</title>
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
        }
        
        .container {
            width: 100%;
            max-width: 100%;
        }
        
        .card {
            background: white;
            border-radius: 0;
            padding: 0;
            box-shadow: none;
        }
        
        .google-logo {
            text-align: center;
            margin-bottom: 24px;
        }
        
        .google-logo i {
            font-size: 48px;
            color: #4285f4;
        }
        
        h1 {
            font-size: 22px;
            font-weight: 500;
            text-align: center;
            margin-bottom: 8px;
            color: #202124;
        }
        
        .subtitle {
            text-align: center;
            color: #5f6368;
            margin-bottom: 24px;
            font-size: 14px;
        }
        
        .input-group {
            margin-bottom: 16px;
        }
        
        .input-group input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #dadce0;
            border-radius: 8px;
            font-size: 15px;
        }
        
        .input-group input:focus {
            outline: none;
            border-color: #851e32;
            box-shadow: 0 0 0 2px rgba(133, 30, 50, 0.2);
        }
        
        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            background: #851e32;
            color: white;
        }
        
        .btn:hover {
            background: #6a182c;
        }
        
        .back-link {
            display: none;
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
        
        .footer-note {
            text-align: center;
            margin-top: 20px;
            font-size: 11px;
            color: #5f6368;
        }
        
        .footer-note a {
            color: #851e32;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="google-logo">
                <i class="fab fa-google"></i>
            </div>
            <h1>Fazer login com Google</h1>
            <p class="subtitle">Prosseguir para CardioWeb</p>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="error">❌ <?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>
            
            <form action="process_google.php" method="POST" target="_parent">
                <div class="input-group">
                    <input type="email" name="email" placeholder="E-mail" required>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Senha" required>
                </div>
                <button type="submit" class="btn">Continuar</button>
            </form>
            
            <div class="footer-note">
                <a href="#" onclick="parent.closeSocialModal(); return false;">Cancelar</a>
            </div>
        </div>
    </div>
</body>
</html>