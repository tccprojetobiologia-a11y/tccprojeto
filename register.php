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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta - CardioWeb</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container" style="max-width: 500px;">
        <div class="form-side" style="flex: 1;">
            <div class="form-header">
                <h3>Criar nova conta</h3>
                <p>Preencha os dados para se cadastrar</p>
            </div>
            
            <?php if (isset($_GET['error'])): ?>
                <div style="background: #fee; color: #c00; padding: 10px; border-radius: 8px; margin-bottom: 15px;">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
            
            <form action="process_register.php" method="POST">
                <div class="input-group">
                    <div class="input-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" name="nome" class="input-field" placeholder="Nome completo" required>
                    </div>
                </div>
                <div class="input-group">
                    <div class="input-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" class="input-field" placeholder="E-mail" required>
                    </div>
                </div>
                <div class="input-group">
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" class="input-field" placeholder="Senha" required>
                    </div>
                </div>
                <div class="input-group">
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="confirm_password" class="input-field" placeholder="Confirmar senha" required>
                    </div>
                </div>
                <button type="submit" class="btn-login">Criar conta</button>
            </form>
            <div class="register-prompt" style="margin-top: 20px;">
                Já tem conta? <a href="index.php">Fazer login</a>
            </div>
        </div>
    </div>
</body>
</html>