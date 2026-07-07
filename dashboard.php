<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: index.php');
    exit();
}

if (($_SESSION['user_role'] ?? '') === 'admin') {
    header('Location: admin/dashboard_admin.php');
    exit();
}

$page = $_GET['page'] ?? 'inicio';
$user_name = htmlspecialchars($_SESSION['user_name'] ?? 'Paciente', ENT_QUOTES, 'UTF-8');
$user_email = htmlspecialchars($_SESSION['user_email'] ?? 'paciente@cardio.com', ENT_QUOTES, 'UTF-8');

function renderSection($page, $user_name) {
    switch ($page) {
        case 'consultas':
            include __DIR__ . '/dashboard-sections/consultas.php';
            return getConsultasHtml();
        case 'exames':
            include __DIR__ . '/dashboard-sections/exames.php';
            return getExamesHtml();
        case 'informacoes':
            include __DIR__ . '/dashboard-sections/informacoes.php';
            return getInformacoesHtml();
        case 'chat':
            include __DIR__ . '/dashboard-sections/chat.php';
            return getChatHtml();
        case 'blog':
            include __DIR__ . '/dashboard-sections/blog.php';
            return getBlogHtml();
        case 'suporte':
            include __DIR__ . '/dashboard-sections/suporte.php';
            return getSuporteHtml();
        case 'inicio':
        default:
            include __DIR__ . '/dashboard-sections/inicio.php';
            return getInicioHtml($user_name);
    }
}

$contentHtml = renderSection($page, $user_name);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CardioWeb - Painel do Paciente</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f6ecee; margin: 0; }
        .dashboard-container { display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: #871f30; color: white; display: flex; flex-direction: column; padding: 30px 20px; }
        .sidebar .logo { display: flex; align-items: center; gap: 12px; margin-bottom: 30px; }
        .sidebar .logo-icon { width: 44px; height: 44px; border-radius: 14px; background: rgba(255,255,255,0.18); display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .sidebar h2 { font-size: 20px; margin: 0; }
        .sidebar p { margin: 4px 0 0; font-size: 13px; color: rgba(255,255,255,0.76); }
        .nav-menu { margin-top: 30px; display: flex; flex-direction: column; gap: 10px; }
        .nav-menu a { color: white; text-decoration: none; display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 14px; transition: background .2s; }
        .nav-menu a:hover, .nav-menu a.active { background: rgba(255,255,255,0.12); }
        .nav-menu i { width: 20px; text-align: center; }
        .profile-card { margin-top: auto; padding: 18px; background: rgba(255,255,255,0.08); border-radius: 18px; }
        .profile-card strong { display: block; margin-bottom: 6px; }
        .profile-card span { font-size: 13px; color: rgba(255,255,255,0.8); }
        .main { flex: 1; background: #f8fafc; padding: 28px; }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .topbar h1 { font-size: 26px; margin: 0; color: #1e2a3a; }
        .topbar .user-info { text-align: right; }
        .topbar .user-info p { margin: 0; color: #64748b; font-size: 13px; }
        .content-area { display: grid; gap: 24px; }
        @media (max-width: 992px) {
            .dashboard-container { flex-direction: column; }
            .sidebar { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="logo">
                <div class="logo-icon"><i class="fas fa-heartbeat"></i></div>
                <div>
                    <h2>CardioWeb</h2>
                    <p>Painel do Paciente</p>
                </div>
            </div>

            <nav class="nav-menu">
                <a href="dashboard.php?page=inicio" class="<?php echo $page === 'inicio' ? 'active' : ''; ?>"><i class="fas fa-home"></i> Início</a>
                <a href="dashboard.php?page=consultas" class="<?php echo $page === 'consultas' ? 'active' : ''; ?>"><i class="fas fa-calendar-check"></i> Consultas</a>
                <a href="dashboard.php?page=exames" class="<?php echo $page === 'exames' ? 'active' : ''; ?>"><i class="fas fa-vials"></i> Exames</a>
                <a href="dashboard.php?page=informacoes" class="<?php echo $page === 'informacoes' ? 'active' : ''; ?>"><i class="fas fa-info-circle"></i> Informações</a>
                <a href="dashboard.php?page=blog" class="<?php echo $page === 'blog' ? 'active' : ''; ?>"><i class="fas fa-newspaper"></i> Blog</a>
                <a href="dashboard.php?page=chat" class="<?php echo $page === 'chat' ? 'active' : ''; ?>"><i class="fas fa-comments"></i> Chat</a>
                <a href="dashboard.php?page=suporte" class="<?php echo $page === 'suporte' ? 'active' : ''; ?>"><i class="fas fa-headset"></i> Suporte</a>
            </nav>

            <div class="profile-card">
                <strong><?php echo $user_name; ?></strong>
                <span><?php echo $user_email; ?></span>
                <div style="margin-top: 12px;"><a href="logout.php" style="color: #fff; text-decoration: none; font-size: 13px;">Sair</a></div>
            </div>
        </aside>

        <main class="main">
            <div class="topbar">
                <h1>Olá, <?php echo $user_name; ?>!</h1>
                <div class="user-info">
                    <p>Painel do paciente</p>
                </div>
            </div>
            <div class="content-area">
                <?php echo $contentHtml; ?>
            </div>
        </main>
    </div>
</body>
</html>
