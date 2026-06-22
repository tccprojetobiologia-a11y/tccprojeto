<?php
session_start();
// Segurança: Se não for admin, expulsa para a página de login
if (!isset($_SESSION['logado']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../index.php');
    exit();
}

$page = $_GET['page'] ?? 'consultas';
$user_name = $_SESSION['user_name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>CardioWeb - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css"> </head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo"><i class="fas fa-heartbeat"></i> CardioWeb <span style="font-size:10px; background:#851e32; padding:2px 5px; border-radius:5px;">ADMIN</span></div>
            </div>
            <nav class="sidebar-menu">
                <a href="?page=consultas" class="menu-item <?php echo $page === 'consultas' ? 'active' : ''; ?>"><i class="fas fa-check-circle"></i> Confirmar Consultas</a>
                <a href="?page=agendas" class="menu-item <?php echo $page === 'agendas' ? 'active' : ''; ?>"><i class="fas fa-calendar-alt"></i> Agenda dos Médicos</a>
                <a href="../logout.php" class="menu-item" style="color:#c62828; margin-top:auto;"><i class="fas fa-sign-out-alt"></i> Sair</a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="top-bar">
                <div class="welcome-msg"><h2>Painel Administrativo</h2><p>Olá, <?php echo htmlspecialchars($user_name); ?>.</p></div>
            </header>

            <div class="content-body" style="padding: 20px;">
                <?php 
                if ($page === 'agendas') {
                    include 'admin-sections/agenda-medicos.php';
                } else {
                    include 'admin-sections/confirmar-consultas.php';
                }
                ?>
            </div>
        </main>
    </div>
</body>
</html>