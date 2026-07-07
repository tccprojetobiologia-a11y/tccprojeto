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

$pageTitles = [
    'inicio' => 'Início',
    'consultas' => 'Consultas',
    'exames' => 'Exames',
    'informacoes' => 'Informações',
    'blog' => 'Blog',
    'suporte' => 'Suporte'
];

$pageTitle = $pageTitles[$page] ?? ucfirst($page);

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
        
        case 'blog':
            include __DIR__ . '/dashboard-sections/blog.php';
            return getBlogSectionHtml();
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

// carregar funções do chat para renderizar painel lateral direito
include __DIR__ . '/dashboard-sections/chat.php';
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
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f6ecee; }
        .app-container { display: flex; min-height: 100vh; width: 100%; }
        .sidebar { width: 280px; background: linear-gradient(180deg, #4c0719 0%, #7e1b31 100%); color: white; display: flex; flex-direction: column; padding-bottom: 20px; box-shadow: 4px 0 20px rgba(0,0,0,0.12); }
        .logo-area { padding: 28px 22px; border-bottom: 1px solid rgba(255,255,255,0.06); }
        .logo { display: flex; align-items: center; gap: 12px; }
        .logo-icon { background: rgba(255,255,255,0.18); width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; }
        .logo-text h2 { font-size: 20px; margin: 0; }
        .logo-text p { font-size: 12px; opacity: 0.9; margin-top: 4px; }
        .nav-menu { padding: 18px 18px 0 18px; display: flex; flex-direction: column; gap: 10px; }
        .nav-item { display: flex; align-items: center; gap: 14px; padding: 12px 14px; margin-bottom: 6px; border-radius: 12px; cursor: pointer; transition: all .18s; color: rgba(255,255,255,0.9); background: transparent; font-size: 15px; text-align: left; border: none; }
        .nav-item:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .nav-item.active { background: rgba(255,255,255,0.12); color: #fff; font-weight: 600; }
        .nav-menu i { width: 20px; text-align: center; }
        .user-section { margin-top: auto; padding: 18px; margin: 18px; background: linear-gradient(135deg, #7a1d34 0%, #5c1230 100%); border-radius: 14px; }
        .user-avatar { width: 46px; height: 46px; background: rgba(255,255,255,0.22); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 700; margin-bottom: 8px; }
        .user-name { font-weight: 600; font-size: 15px; color: #fff; margin-bottom: 4px; }
        .user-email { font-size: 12px; color: rgba(255,255,255,0.9); word-break: break-all; }

        .main-content { flex: 1; display: flex; flex-direction: column; background: #f8fafc; }
        .main-header { background: white; padding: 20px 26px; border-bottom: 1px solid #e6edf3; display: flex; justify-content: space-between; align-items: center; }
        .page-title { font-size: 22px; font-weight: 700; color: #1e2a3a; }
        .header-actions { display: flex; gap: 12px; }
        .header-icon { width: 40px; height: 40px; background: #f1f5f9; border-radius: 10px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #2d3e50; }

        .content-wrapper { display: flex; gap: 20px; padding: 26px; align-items: stretch; }
        .content-area { flex: 1; overflow-y: auto; }

        /* Chat panel on the right */
        .chat-panel { width: 360px; background: transparent; display: flex; flex-direction: column; }
        .chat-sidebar { background: white; border-radius: 12px; padding: 14px; box-shadow: 0 6px 18px rgba(16,24,40,0.06); display: flex; flex-direction: column; height: 100%; }
        .chat-header h3 { margin: 0; font-size: 16px; color: #1e293b; }
        .chat-header p { margin: 6px 0 12px; font-size: 13px; color: #64748b; }
        .chat-messages { flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; padding-right: 6px; }
        .message { display: flex; gap: 10px; align-items: flex-end; }
        .message.user { justify-content: flex-end; }
        .message-bubble { background: #f1f5f9; padding: 10px 12px; border-radius: 12px; max-width: 75%; color: #0f172a; }
        .message.user .message-bubble { background: #851e32; color: white; }
        .message-avatar { width: 36px; height: 36px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .chat-input-area { display: flex; gap: 8px; margin-top: 12px; }
        .chat-input { flex: 1; padding: 10px 12px; border-radius: 10px; border: 1px solid #e6eef5; }
        .chat-send { background: #851e32; color: white; border: none; padding: 10px 12px; border-radius: 10px; cursor: pointer; }

        @media (max-width: 992px) {
            .app-container { flex-direction: column; }
            .sidebar { width: 100%; display: flex; flex-direction: row; overflow-x: auto; }
            .logo-area { display: none; }
            .content-wrapper { padding: 18px; flex-direction: column; }
            .chat-panel { width: 100%; order: 3; }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <div class="sidebar">
            <div class="logo-area">
                <div class="logo">
                    <div class="logo-icon"><i class="fas fa-heartbeat"></i></div>
                    <div class="logo-text"><h2>CardioWeb</h2><p>Painel do Paciente</p></div>
                </div>
            </div>
            <nav class="nav-menu">
                <button class="nav-item <?php echo $page === 'inicio' ? 'active' : ''; ?>" onclick="location.href='dashboard_paciente.php?page=inicio'">
                    <i class="fas fa-home"></i><span>Início</span>
                </button>
                <button class="nav-item <?php echo $page === 'consultas' ? 'active' : ''; ?>" onclick="location.href='dashboard_paciente.php?page=consultas'">
                    <i class="fas fa-calendar-check"></i><span>Consultas</span>
                </button>
                <button class="nav-item <?php echo $page === 'exames' ? 'active' : ''; ?>" onclick="location.href='dashboard_paciente.php?page=exames'">
                    <i class="fas fa-vials"></i><span>Exames</span>
                </button>
                <button class="nav-item <?php echo $page === 'informacoes' ? 'active' : ''; ?>" onclick="location.href='dashboard_paciente.php?page=informacoes'">
                    <i class="fas fa-info-circle"></i><span>Informações</span>
                </button>
                <button class="nav-item <?php echo $page === 'blog' ? 'active' : ''; ?>" onclick="location.href='dashboard_paciente.php?page=blog'">
                    <i class="fas fa-newspaper"></i><span>Blog</span>
                </button>
                <!-- Chat lateral agora é painel fixo; removido da navegação lateral -->
                <button class="nav-item <?php echo $page === 'suporte' ? 'active' : ''; ?>" onclick="location.href='dashboard_paciente.php?page=suporte'">
                    <i class="fas fa-headset"></i><span>Suporte</span>
                </button>
            </nav>
            <div class="user-section">
                <div class="user-avatar"><?php echo strtoupper(substr($user_name, 0, 1)); ?></div>
                <div class="user-name"><?php echo $user_name; ?></div>
                <div class="user-email"><?php echo $user_email; ?></div>
                <div style="margin-top:10px;"><a href="logout.php" class="logout-btn" style="text-decoration:none; color:#fff;">Sair</a></div>
            </div>
        </div>

        <div class="main-content">
            <header class="main-header">
                <h1 class="page-title"><?php echo htmlspecialchars($pageTitle); ?></h1>
                <div class="header-actions">
                    <div class="header-icon"><i class="fas fa-bell"></i></div>
                    <div class="header-icon"><i class="fas fa-cog"></i></div>
                </div>
            </header>

            <div class="content-wrapper">
                <div class="content-area">
                    <?php echo $contentHtml; ?>
                </div>

                <div class="chat-panel">
                    <?php echo getChatSidebarHtml(); ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openArticle(key) {
            fetch('dashboard-sections/blog.php?article=' + encodeURIComponent(key))
                .then(response => {
                    if (!response.ok) throw new Error('Artigo não encontrado');
                    return response.text();
                })
                .then(html => {
                    document.querySelector('.content-area').innerHTML = html;
                })
                .catch(err => {
                    document.querySelector('.content-area').innerHTML = '<div class="info-card"><h3>Erro</h3><p>Não foi possível carregar o artigo.</p></div>';
                    console.error(err);
                });
        }

        <?php echo getChatScript(); ?>
    </script>
</body>
</html>
