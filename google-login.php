<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login com Google - CardioWeb</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container { width: 100%; max-width: 450px; padding: 20px; }
        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 32px;
        }
        .google-logo { text-align: center; margin-bottom: 32px; }
        .google-logo i { font-size: 48px; color: #4285f4; }
        h1 { font-size: 24px; text-align: center; margin-bottom: 8px; color: #202124; }
        .subtitle { text-align: center; color: #5f6368; margin-bottom: 32px; font-size: 14px; }
        .input-group { margin-bottom: 20px; }
        .input-group input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #dadce0;
            border-radius: 8px;
            font-size: 16px;
        }
        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            background: #851e32;
            color: white;
        }
        .btn:hover { background: #6a182c; }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 24px;
            color: #5f6368;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="google-logo"><i class="fab fa-google"></i></div>
            <h1>Fazer login com Google</h1>
            <p class="subtitle">Prosseguir para CardioWeb</p>
            <form action="process_google.php" method="POST">
                <div class="input-group"><input type="email" name="email" placeholder="E-mail" required></div>
                <div class="input-group"><input type="password" name="password" placeholder="Senha" required></div>
                <button type="submit" class="btn">Continuar</button>
            </form>
            <a href="index.php" class="back-link">← Voltar ao login</a>
        </div>
    </div>
</body>
</html>