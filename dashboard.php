<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: index.php');
    exit();
}

$user_name = $_SESSION['user_name'] ?? 'Usuário';
$user_email = $_SESSION['user_email'] ?? $_SESSION['user_telefone'] ?? 'usuario@email.com';
$login_type = $_SESSION['login_type'] ?? 'Padrão';

// Conteúdo padrão (Início)
$page = $_GET['page'] ?? 'inicio';
require_once __DIR__ . '/dashboard-sections/blog.php';
require_once __DIR__ . '/dashboard-sections/consultas.php';
require_once __DIR__ . '/dashboard-sections/exames.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CardioWeb - Painel Principal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ===== SEU CSS EXISTENTE (mantenha o mesmo) ===== */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f6ecee; overflow: hidden; height: 100vh; }
        .app-container { display: flex; height: 100vh; width: 100%; }
        .sidebar { width: 280px; background: linear-gradient(180deg, #4c0719 0%, #7e1b31 100%); color: white; display: flex; flex-direction: column; box-shadow: 4px 0 20px rgba(0,0,0,0.12); overflow-y: auto; }
        .logo-area { padding: 30px 25px; border-bottom: 1px solid rgba(255,255,255,0.12); margin-bottom: 30px; }
        .logo { display: flex; align-items: center; gap: 12px; }
        .logo-icon { background: rgba(255,255,255,0.2); width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 28px; }
        .logo-text h2 { font-size: 22px; font-weight: 700; letter-spacing: -0.5px; }
        .logo-text p { font-size: 10px; opacity: 0.8; margin-top: 4px; }
        .nav-menu { flex: 1; padding: 0 20px; }
        .nav-item { display: flex; align-items: center; gap: 14px; padding: 14px 18px; margin-bottom: 8px; border-radius: 12px; cursor: pointer; transition: all 0.3s; color: rgba(255,255,255,0.8); }
        .nav-item:hover { background: rgba(255,255,255,0.12); color: white; }
        .nav-item.active { background: rgba(255,255,255,0.18); color: white; font-weight: 500; }
        .nav-item i { width: 24px; font-size: 20px; }
        .nav-item span { font-size: 15px; }
        .user-section { padding: 20px; margin: 20px; background: linear-gradient(135deg, #7a1d34 0%, #5c1230 100%); border-radius: 16px; margin-top: auto; margin-bottom: 20px; }
        .user-avatar { width: 50px; height: 50px; background: rgba(255,255,255,0.25); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 22px; font-weight: bold; margin-bottom: 12px; }
        .user-name { font-weight: 600; font-size: 16px; margin-bottom: 4px; }
        .user-email { font-size: 11px; opacity: 0.8; margin-bottom: 12px; word-break: break-all; }
        .logout-btn { background: rgba(255,255,255,0.2); color: white; padding: 8px 12px; border-radius: 10px; text-decoration: none; font-size: 13px; display: flex; align-items: center; gap: 8px; justify-content: center; transition: all 0.3s; }
        .logout-btn:hover { background: rgba(255,255,255,0.3); }
        .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; background: #f8fafc; }
        .main-header { background: white; padding: 20px 30px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
        .page-title { font-size: 24px; font-weight: 700; color: #1e2a3a; }
        .header-actions { display: flex; gap: 15px; }
        .header-icon { width: 40px; height: 40px; background: #f1f5f9; border-radius: 10px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s; }
        .header-icon:hover { background: #e2e8f0; }
        .content-area { flex: 1; overflow-y: auto; padding: 30px; }
        .welcome-card { background: linear-gradient(135deg, #851e32 0%, #5a1e2c 100%); color: white; padding: 30px; border-radius: 20px; margin-bottom: 30px; }
        .welcome-card h2 { font-size: 28px; margin-bottom: 10px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); transition: all 0.3s; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .stat-icon { width: 50px; height: 50px; background: #fff0f0; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: #851e32; margin-bottom: 15px; }
        .stat-card h3 { font-size: 28px; color: #1e2a3a; margin-bottom: 5px; }
        .stat-card p { color: #64748b; font-size: 14px; }
        .info-card { background: white; border-radius: 16px; padding: 25px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .info-card h3 { color: #1e2a3a; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #f0f0f0; }
        .blog-post { padding: 15px; border-bottom: 1px solid #f0f0f0; cursor: pointer; transition: background 0.3s; }
        .blog-post:hover { background: #f8fafc; }
        .blog-title { font-weight: 600; color: #1e2a3a; margin-bottom: 5px; }
        .blog-date { font-size: 12px; color: #94a3b8; }
        .support-card { background: #f8fafc; padding: 20px; border-radius: 12px; margin-bottom: 15px; }
        .article-container { max-width: 900px; margin: 0 auto; background: white; border-radius: 16px; padding: 40px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .article-back-btn { display: inline-flex; align-items: center; gap: 8px; color: #851e32; margin-bottom: 20px; cursor: pointer; padding: 8px 12px; border-radius: 8px; transition: all 0.3s; font-weight: 500; }
        .article-back-btn:hover { background: #f8fafc; }
        .article-header { margin-bottom: 30px; border-bottom: 2px solid #f0f0f0; padding-bottom: 20px; }
        .article-title { font-size: 32px; font-weight: 700; color: #1e2a3a; margin-bottom: 10px; line-height: 1.3; }
        .article-meta { display: flex; gap: 20px; color: #666; font-size: 14px; }
        .article-meta-item { display: flex; align-items: center; gap: 5px; }
        .article-content { line-height: 1.8; color: #333; font-size: 16px; }
        .article-content p { margin-bottom: 20px; text-align: justify; }
        .article-content h3 { font-size: 20px; font-weight: 700; color: #1e2a3a; margin: 30px 0 15px 0; }
        .article-image-inline { max-width: 300px; height: auto; border-radius: 12px; margin: 15px 15px 15px 0; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .article-image-inline.right { float: right; margin-left: 15px; margin-right: 0; }
        .cursor-pointer { cursor: pointer; }
        .chat-sidebar { width: 350px; background: #fff5f6; border-left: 1px solid #f3d8de; display: flex; flex-direction: column; box-shadow: -4px 0 20px rgba(0,0,0,0.05); }
        .chat-header { padding: 20px; border-bottom: 1px solid #f3d8de; background: linear-gradient(135deg, #7a1e31 0%, #a22a44 100%); color: white; }
        .chat-header h3 { font-size: 18px; display: flex; align-items: center; gap: 10px; }
        .chat-header p { font-size: 12px; opacity: 0.9; margin-top: 5px; }
        .chat-messages { flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 15px; }
        .message { display: flex; gap: 12px; max-width: 90%; }
        .message.user { align-self: flex-end; flex-direction: row-reverse; }
        .message-avatar { width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }
        .message.user .message-avatar { background: #851e32; color: white; }
        .message.bot .message-avatar { background: #10b981; color: white; }
        .message-bubble { background: #f1f5f9; padding: 10px 15px; border-radius: 18px; font-size: 13px; line-height: 1.4; color: #1e2a3a; }
        .message.user .message-bubble { background: #851e32; color: white; }
        .chat-input-area { padding: 15px 20px; border-top: 1px solid #e2e8f0; display: flex; gap: 10px; }
        .chat-input { flex: 1; padding: 12px; border: 1px solid #e2e8f0; border-radius: 25px; outline: none; font-family: inherit; }
        .chat-input:focus { border-color: #851e32; }
        .chat-send { width: 45px; height: 45px; background: #851e32; border: none; border-radius: 50%; color: white; cursor: pointer; transition: all 0.3s; }
        .chat-send:hover { background: #5a1e2c; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 3px; }
        .modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center; z-index: 1000; animation: fadeIn 0.3s ease-in; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .modal-content { background: white; border-radius: 20px; width: 90%; max-width: 500px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); animation: slideUp 0.3s ease-out; }
        @keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .modal-header { padding: 25px; border-bottom: 2px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; }
        .modal-header h2 { font-size: 22px; font-weight: 700; color: #1e2a3a; margin: 0; display: flex; align-items: center; gap: 10px; }
        .modal-close { background: none; border: none; font-size: 28px; color: #999; cursor: pointer; transition: color 0.3s; }
        .modal-close:hover { color: #851e32; }
        .modal-body { padding: 30px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #1e2a3a; font-size: 14px; }
        .form-group input, .form-group select, .form-group textarea { font-family: inherit; border: 1px solid #ddd; border-radius: 8px; transition: border-color 0.3s; width: 100%; padding: 12px; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #851e32; box-shadow: 0 0 0 3px rgba(133,30,50,0.1); }
        .contact-btn { background: #851e32; color: white; border: none; padding: 12px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; font-family: inherit; }
        .contact-btn:hover { background: #5a1e2c; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(133,30,50,0.3); }
        .contact-btn:active { transform: translateY(0); }
        @media (max-width: 1000px) { .chat-sidebar { width: 300px; } }
        @media (max-width: 800px) { .chat-sidebar { display: none; } }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- SIDEBAR -->
        <div class="sidebar">
            <div class="logo-area">
                <div class="logo">
                    <div class="logo-icon"><i class="fas fa-heartbeat"></i></div>
                    <div class="logo-text">
                        <h2>CardioWeb</h2>
                        <p>Saúde & Monitoramento</p>
                    </div>
                </div>
            </div>
            <div class="nav-menu">
                <div class="nav-item <?php echo $page == 'inicio' ? 'active' : ''; ?>" onclick="changePage('inicio')">
                    <i class="fas fa-home"></i><span>Início</span>
                </div>
                <div class="nav-item <?php echo $page == 'blog' ? 'active' : ''; ?>" onclick="changePage('blog')">
                    <i class="fas fa-newspaper"></i><span>Blog</span>
                </div>
                <div class="nav-item <?php echo $page == 'consultas' ? 'active' : ''; ?>" onclick="changePage('consultas')">
                    <i class="fas fa-calendar-check"></i><span>Consultas</span>
                </div>
                <div class="nav-item <?php echo $page == 'exames' ? 'active' : ''; ?>" onclick="changePage('exames')">
                    <i class="fas fa-flask"></i><span>Exames</span>
                </div>
                <div class="nav-item <?php echo $page == 'informacoes' ? 'active' : ''; ?>" onclick="changePage('informacoes')">
                    <i class="fas fa-info-circle"></i><span>Informações</span>
                </div>
                <div class="nav-item <?php echo $page == 'suporte' ? 'active' : ''; ?>" onclick="changePage('suporte')">
                    <i class="fas fa-headset"></i><span>Suporte</span>
                </div>
            </div>
            <div class="user-section">
                <div class="user-avatar"><?php echo strtoupper(substr($user_name, 0, 1)); ?></div>
                <div class="user-name"><?php echo htmlspecialchars($user_name); ?></div>
                <div class="user-email"><?php echo htmlspecialchars($user_email); ?></div>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Sair</a>
            </div>
        </div>

        <!-- CONTEÚDO PRINCIPAL -->
        <div class="main-content">
            <div class="main-header">
                <h1 class="page-title" id="pageTitle">Início</h1>
                <div class="header-actions">
                    <div class="header-icon"><i class="fas fa-bell"></i></div>
                    <div class="header-icon"><i class="fas fa-cog"></i></div>
                </div>
            </div>
            <div class="content-area" id="contentArea">
                <!-- Conteúdo dinâmico -->
            </div>
        </div>

        <!-- MODAL NOVA CONSULTA (com horários disponíveis) -->
        <div id="consultaModal" class="modal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h2><i class="fas fa-calendar-plus"></i> Agendar Nova Consulta</h2>
                    <button class="modal-close" onclick="closeConsultaModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="consultaForm" onsubmit="agendarConsulta(event)">
                        <div class="form-group">
                            <label><i class="fas fa-user-md"></i> Médico:</label>
                            <select id="medicSelect" required style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; font-size:14px;">
                                <option value="">Selecione um médico</option>
                                <option value="Dr. Roberto Mendes|Cardiologia">Dr. Roberto Mendes - Cardiologia</option>
                                <option value="Dra. Aline Costa|Arritmologia">Dra. Aline Costa - Arritmologia</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-calendar-alt"></i> Data:</label>
                            <input type="date" id="dataConsulta" required style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; font-size:14px;">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-clock"></i> Horário:</label>
                            <select id="horaConsulta" required style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; font-size:14px;">
                                <option value="">Selecione um horário</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-stethoscope"></i> Tipo de Consulta:</label>
                            <select id="tipoConsulta" required style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; font-size:14px;">
                                <option value="">Selecione o tipo</option>
                                <option value="Presencial">Presencial</option>
                                <option value="Online">Online</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-file-alt"></i> Observações (opcional):</label>
                            <textarea id="obsConsulta" placeholder="Descreva os sintomas ou motivo da consulta..." style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; font-size:14px; resize:vertical; min-height:80px;"></textarea>
                        </div>
                        <div style="display:flex; gap:10px; margin-top:20px;">
                            <button type="submit" class="contact-btn" style="flex:1;"><i class="fas fa-check"></i> Confirmar Agendamento</button>
                            <button type="button" class="contact-btn" style="flex:1; background:#6c757d;" onclick="closeConsultaModal()"><i class="fas fa-times"></i> Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- CHAT -->
        <div class="chat-sidebar">
            <div class="chat-header">
                <h3><i class="fas fa-comment-dots"></i> Assistente CardioWeb</h3>
                <p>💬 Converse comigo sobre sua saúde!</p>
            </div>
            <div class="chat-messages" id="chatMessages">
                <div class="message bot">
                    <div class="message-avatar"><i class="fas fa-robot"></i></div>
                    <div class="message-bubble">Olá! Eu sou o assistente do CardioWeb. Como posso ajudar você hoje? 💙</div>
                </div>
            </div>
            <div class="chat-input-area">
                <input type="text" class="chat-input" id="chatInput" placeholder="Digite sua mensagem..." onkeypress="if(event.key === 'Enter') sendMessage()">
                <button class="chat-send" onclick="sendMessage()"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </div>

    <script>
        // ========== INICIALIZAÇÃO DE DADOS GLOBAIS (compartilhados com admin) ==========
        window.agendaMedicos = window.agendaMedicos || {
            'Dr. Roberto Mendes': [],
            'Dra. Aline Costa': []
        };
        window.consultas = window.consultas || { pendentes: [], confirmadas: [], recusadas: [] };

        // ========== DADOS DOS ARTIGOS ==========
        const articlesData = <?php echo json_encode(getBlogArticles()); ?>;

        // ========== FUNÇÕES DE NAVEGAÇÃO ==========
        function changePage(page) {
            const url = new URL(window.location.href);
            url.searchParams.set('page', page);
            window.history.pushState({}, '', url);
            const titles = {
                'inicio': 'Início',
                'blog': 'Blog',
                'consultas': 'Consultas',
                'exames': 'Exames',
                'informacoes': 'Informações',
                'suporte': 'Suporte'
            };
            document.getElementById('pageTitle').innerText = titles[page] || 'Início';
            document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));
            document.querySelector(`.nav-item[onclick="changePage('${page}')"]`).classList.add('active');
            loadContent(page);
        }

        function openArticle(articleId, event) {
            if (event) event.stopPropagation();
            const article = articlesData[articleId];
            if (!article) return;
            const contentArea = document.getElementById('contentArea');
            contentArea.innerHTML = `
                <div class="article-container">
                    <div class="article-back-btn" onclick="changePage('blog')"><i class="fas fa-arrow-left"></i> Voltar aos artigos</div>
                    <div class="article-header"><h1 class="article-title">${article.title}</h1><div class="article-meta"><div class="article-meta-item"><i class="fas fa-calendar"></i> ${article.date}</div></div></div>
                    <div class="article-content">${article.content}</div>
                    <div style="margin-top:40px; padding-top:20px; border-top:2px solid #f0f0f0;"><div class="article-back-btn" onclick="changePage('blog')"><i class="fas fa-arrow-left"></i> Voltar aos artigos</div></div>
                </div>
            `;
            document.getElementById('pageTitle').innerText = 'Artigo';
        }

        function loadContent(page) {
            const contentArea = document.getElementById('contentArea');
            if (page === 'inicio') {
                contentArea.innerHTML = `
                    <div class="welcome-card"><h2>Bem-vindo de volta, <?php echo htmlspecialchars($user_name); ?>! 👋</h2><p>Monitore sua saúde cardiológica em tempo real e mantenha seus exames em dia.</p></div>
                    <div class="stats-grid">
                        <div class="stat-card"><div class="stat-icon"><i class="fas fa-chart-line"></i></div><h3>12</h3><p>Registros de saúde</p></div>
                        <div class="stat-card"><div class="stat-icon"><i class="fas fa-heartbeat"></i></div><h3>72</h3><p>Batimentos/min</p></div>
                        <div class="stat-card"><div class="stat-icon"><i class="fas fa-calendar-check"></i></div><h3>2</h3><p>Consultas agendadas</p></div>
                        <div class="stat-card"><div class="stat-icon"><i class="fas fa-trophy"></i></div><h3>85%</h3><p>Meta de saúde</p></div>
                    </div>
                    <div class="info-card"><h3><i class="fas fa-heart"></i> Últimos Registros</h3>
                        <div style="display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid #f0f0f0;"><span>Pressão Arterial</span><span><strong>120/80 mmHg</strong></span><span style="color:#10b981;">Normal</span></div>
                        <div style="display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid #f0f0f0;"><span>Colesterol Total</span><span><strong>180 mg/dL</strong></span><span style="color:#10b981;">Normal</span></div>
                        <div style="display:flex; justify-content:space-between; padding:12px 0;"><span>Glicemia</span><span><strong>95 mg/dL</strong></span><span style="color:#10b981;">Normal</span></div>
                    </div>
                    <div class="info-card"><h3><i class="fas fa-calendar-alt"></i> Próximas Consultas</h3>
                        <div style="display:flex; align-items:center; gap:15px; padding:12px 0;"><div style="min-width:50px; text-align:center;"><div style="font-size:20px; font-weight:700; color:#851e32;">15</div><div style="font-size:11px; color:#666;">ABR</div></div><div style="flex:1;"><div style="font-weight:600;">Cardiologista - Dr. Carlos</div><div style="font-size:12px; color:#666;">10:00 - Consulta presencial</div></div><div style="font-size:11px; background:#e8f5e9; color:#2e7d32; padding:4px 10px; border-radius:20px;">Confirmado</div></div>
                        <div style="display:flex; align-items:center; gap:15px; padding:12px 0;"><div style="min-width:50px; text-align:center;"><div style="font-size:20px; font-weight:700; color:#851e32;">22</div><div style="font-size:11px; color:#666;">ABR</div></div><div style="flex:1;"><div style="font-weight:600;">Exame de Rotina</div><div style="font-size:12px; color:#666;">08:30 - Laboratório</div></div><div style="font-size:11px; background:#fff3e0; color:#ff9800; padding:4px 10px; border-radius:20px;">Pendente</div></div>
                    </div>
                `;
            } else if (page === 'blog') {
                let html = `<div class="info-card"><h3><i class="fas fa-newspaper"></i> Artigos Recentes</h3>`;
                Object.keys(articlesData).forEach(id => {
                    const a = articlesData[id];
                    html += `<div class="blog-post cursor-pointer" onclick="openArticle('${id}', event)"><div class="blog-title">${a.title}</div><div class="blog-date">${a.date}</div></div>`;
                });
                html += `</div>`;
                contentArea.innerHTML = html;
            } else if (page === 'consultas') {
                contentArea.innerHTML = <?php echo json_encode(getConsultasHtml()); ?>;
            } else if (page === 'exames') {
                contentArea.innerHTML = <?php echo json_encode(getExamesHtml()); ?>;
            } else if (page === 'informacoes') {
                contentArea.innerHTML = `
                    <div class="info-card"><h3><i class="fas fa-info-circle"></i> Sobre o CardioWeb</h3><p>O CardioWeb é uma plataforma completa de monitoramento cardiológico que permite acompanhar sua saúde em tempo real, agendar consultas, acessar exames e receber orientações personalizadas.</p></div>
                    <div class="info-card"><h3><i class="fas fa-chart-line"></i> Funcionalidades</h3><div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px,1fr)); gap:15px;"><div style="padding:15px; background:#f8fafc; border-radius:12px;"><i class="fas fa-heartbeat" style="color:#851e32; font-size:24px;"></i><div style="font-weight:600; margin-top:10px;">Monitoramento</div><div style="font-size:12px; color:#666;">Acompanhe batimentos e pressão</div></div><div style="padding:15px; background:#f8fafc; border-radius:12px;"><i class="fas fa-file-alt" style="color:#851e32; font-size:24px;"></i><div style="font-weight:600; margin-top:10px;">Exames Online</div><div style="font-size:12px; color:#666;">Acesse resultados de exames</div></div><div style="padding:15px; background:#f8fafc; border-radius:12px;"><i class="fas fa-calendar-check" style="color:#851e32; font-size:24px;"></i><div style="font-weight:600; margin-top:10px;">Agendamentos</div><div style="font-size:12px; color:#666;">Marque consultas facilmente</div></div></div></div>
                    <div class="info-card"><h3><i class="fas fa-shield-alt"></i> Segurança e Privacidade</h3><p>Seus dados são protegidos com criptografia de ponta a ponta e seguimos rigorosamente a LGPD para garantir sua privacidade.</p></div>
                `;
            } else if (page === 'suporte') {
                contentArea.innerHTML = `
                    <div class="info-card"><h3><i class="fas fa-headset"></i> Central de Suporte</h3><p>Estamos aqui para ajudar! Escolha uma opção abaixo:</p></div>
                    <div class="support-card"><div style="display:flex; align-items:center; gap:15px;"><div style="width:50px; height:50px; background:#e8f5e9; border-radius:12px; display:flex; align-items:center; justify-content:center;"><i class="fas fa-phone" style="color:#2e7d32; font-size:24px;"></i></div><div><div style="font-weight:600;">Atendimento Telefônico</div><div style="font-size:12px; color:#666;">Segunda a Sexta, 8h às 18h</div><div style="font-size:14px; color:#851e32; margin-top:5px;">(11) 4002-8922</div></div></div></div>
                    <div class="support-card"><div style="display:flex; align-items:center; gap:15px;"><div style="width:50px; height:50px; background:#e3f2fd; border-radius:12px; display:flex; align-items:center; justify-content:center;"><i class="fas fa-envelope" style="color:#1976d2; font-size:24px;"></i></div><div><div style="font-weight:600;">E-mail</div><div style="font-size:12px; color:#666;">Respondemos em até 24h</div><div style="font-size:14px; color:#851e32; margin-top:5px;">suporte@cardioweb.com</div></div></div></div>
                    <div class="support-card"><div style="display:flex; align-items:center; gap:15px;"><div style="width:50px; height:50px; background:#fff3e0; border-radius:12px; display:flex; align-items:center; justify-content:center;"><i class="fab fa-whatsapp" style="color:#25d366; font-size:28px;"></i></div><div><div style="font-weight:600;">WhatsApp</div><div style="font-size:12px; color:#666;">Atendimento 24h</div><div style="font-size:14px; color:#851e32; margin-top:5px;">(11) 9 9999-9999</div></div></div></div>
                    <div class="info-card"><h3><i class="fas fa-question-circle"></i> Perguntas Frequentes</h3>
                        <details style="margin-bottom:10px;"><summary style="cursor:pointer; font-weight:500; padding:10px; background:#f8fafc; border-radius:8px;">Como agendar uma consulta?</summary><p style="padding:10px; color:#666;">Acesse o menu "Consultas" e clique em "Agendar nova consulta". Escolha o médico e horário disponível.</p></details>
                        <details style="margin-bottom:10px;"><summary style="cursor:pointer; font-weight:500; padding:10px; background:#f8fafc; border-radius:8px;">Como acessar meus exames?</summary><p style="padding:10px; color:#666;">Os exames ficam disponíveis na seção "Exames" após liberação do médico responsável.</p></details>
                    </div>
                `;
            }
        }

        // ========== CHAT ==========
        function sendMessage() {
            const input = document.getElementById('chatInput');
            const msg = input.value.trim();
            if (!msg) return;
            addMessage(msg, 'user');
            input.value = '';
            setTimeout(() => {
                const response = getBotResponse(msg);
                addMessage(response, 'bot');
            }, 500);
        }
        function addMessage(text, sender) {
            const container = document.getElementById('chatMessages');
            const div = document.createElement('div');
            div.className = `message ${sender}`;
            const avatar = sender === 'user' ? '<div class="message-avatar"><i class="fas fa-user"></i></div>' : '<div class="message-avatar"><i class="fas fa-robot"></i></div>';
            div.innerHTML = avatar + `<div class="message-bubble">${text}</div>`;
            container.appendChild(div);
            container.scrollTop = container.scrollHeight;
        }
        function getBotResponse(msg) {
            const m = msg.toLowerCase();
            if (m.includes('olá') || m.includes('oi')) return 'Olá! Como posso ajudar? 💙';
            if (m.includes('pressão')) return 'A pressão ideal é abaixo de 120/80 mmHg. Mantenha uma alimentação saudável!';
            if (m.includes('consulta')) return 'Para agendar uma consulta, acesse o menu "Consultas" ou ligue para (11) 4002-8922.';
            if (m.includes('exame')) return 'Seus exames ficam disponíveis na seção "Exames" após liberação médica.';
            return 'Entendi! Para mais informações, leia nossos artigos no blog ou acesse o suporte. 💙';
        }

        // ========== MODAL DE AGENDAMENTO COM HORÁRIOS DISPONÍVEIS ==========
        function openConsultaModal() {
            const modal = document.getElementById('consultaModal');
            modal.style.display = 'flex';
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('dataConsulta').min = today;
            const horaSelect = document.getElementById('horaConsulta');
            horaSelect.innerHTML = '<option value="">Selecione um horário</option>';
            document.getElementById('medicSelect').onchange = carregarHorariosDisponiveis;
            document.getElementById('dataConsulta').onchange = carregarHorariosDisponiveis;
        }

        function closeConsultaModal() {
            document.getElementById('consultaModal').style.display = 'none';
            document.getElementById('consultaForm').reset();
            document.getElementById('horaConsulta').innerHTML = '<option value="">Selecione um horário</option>';
        }

        function carregarHorariosDisponiveis() {
            const medicoSelect = document.getElementById('medicSelect');
            const data = document.getElementById('dataConsulta').value;
            const horaSelect = document.getElementById('horaConsulta');
            horaSelect.innerHTML = '<option value="">Selecione um horário</option>';
            if (!medicoSelect.value || !data) return;

            const medicoNome = medicoSelect.value.split('|')[0];
            const agenda = window.agendaMedicos[medicoNome] || [];
            const ocupados = agenda.filter(c => c.data === data).map(c => c.hora);
            const todosHorarios = ['08:00', '09:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00'];
            const disponiveis = todosHorarios.filter(h => !ocupados.includes(h));

            if (disponiveis.length === 0) {
                horaSelect.innerHTML = '<option value="">Nenhum horário disponível nesta data</option>';
                return;
            }
            disponiveis.forEach(h => {
                const opt = document.createElement('option');
                opt.value = h;
                opt.textContent = h;
                horaSelect.appendChild(opt);
            });
        }

        document.addEventListener('click', function(event) {
            const modal = document.getElementById('consultaModal');
            if (modal && event.target === modal) closeConsultaModal();
        });

        function agendarConsulta(event) {
            event.preventDefault();
            const medicSelect = document.getElementById('medicSelect');
            const dataConsulta = document.getElementById('dataConsulta');
            const horaConsulta = document.getElementById('horaConsulta');
            const tipoConsulta = document.getElementById('tipoConsulta');
            const obsConsulta = document.getElementById('obsConsulta');

            if (!medicSelect.value || !dataConsulta.value || !horaConsulta.value || !tipoConsulta.value) {
                alert('Por favor, preencha todos os campos obrigatórios!');
                return;
            }

            const [nomeMedico, especialidade] = medicSelect.value.split('|');
            const data = new Date(dataConsulta.value);
            const dataFormatada = data.toLocaleDateString('pt-BR', { year: 'numeric', month: '2-digit', day: '2-digit' });

            const novaConsulta = {
                id: Date.now(),
                paciente: '<?php echo htmlspecialchars($user_name); ?>',
                medico: nomeMedico,
                especialidade: especialidade,
                data: dataConsulta.value,
                hora: horaConsulta.value,
                status: 'pendente'
            };
            window.consultas.pendentes.push(novaConsulta);

            const grid = document.getElementById('consultasGrid');
            if (grid) {
                const card = document.createElement('div');
                card.className = 'stat-card';
                card.innerHTML = `
                    <div class="stat-icon"><i class="fas fa-user-md"></i></div>
                    <h3>${nomeMedico}</h3>
                    <p>${especialidade}</p>
                    <div style="margin-top:10px; font-size:14px;">
                        <i class="fas fa-calendar-alt"></i> ${dataFormatada}<br>
                        <i class="fas fa-clock"></i> ${horaConsulta.value}<br>
                        <i class="fas fa-stethoscope"></i> ${tipoConsulta.value}<br>
                        <span style="color:#ff9800;">🕓 Pendente</span>
                    </div>
                `;
                grid.insertBefore(card, grid.firstChild);
            }

            closeConsultaModal();
            alert(`✓ Consulta solicitada com sucesso!\nAguardando confirmação do médico.\n\nMédico: ${nomeMedico}\nEspecialidade: ${especialidade}\nData: ${dataFormatada}\nHorário: ${horaConsulta.value}\nTipo: ${tipoConsulta.value}`);
        }

        // ========== CARREGAR CONTEÚDO INICIAL ==========
        loadContent('<?php echo $page; ?>');
    </script>
</body>
</html>