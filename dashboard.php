<<<<<<< HEAD
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f5f7fa;
            overflow: hidden;
            height: 100vh;
        }

        /* ========== LAYOUT PRINCIPAL ========== */
        .app-container {
            display: flex;
            height: 100vh;
            width: 100%;
        }

        /* ========== SIDEBAR ESQUERDA (ÁREA AZUL) ========== */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #1a73e8 0%, #0d5bba 100%);
            color: white;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
        }

        /* Logo (Área Vermelha) */
        .logo-area {
            padding: 30px 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 30px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            background: rgba(255, 255, 255, 0.2);
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .logo-text h2 {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .logo-text p {
            font-size: 10px;
            opacity: 0.8;
            margin-top: 4px;
        }

        /* Menu de Navegação */
        .nav-menu {
            flex: 1;
            padding: 0 20px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 18px;
            margin-bottom: 8px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            color: rgba(255, 255, 255, 0.8);
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.15);
            color: white;
        }

        .nav-item.active {
            background: rgba(255, 255, 255, 0.25);
            color: white;
            font-weight: 500;
        }

        .nav-item i {
            width: 24px;
            font-size: 20px;
        }

        .nav-item span {
            font-size: 15px;
        }

        /* Usuário Logado (Área Roxa) */
        .user-section {
            padding: 20px;
            margin: 20px;
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            border-radius: 16px;
            margin-top: auto;
            margin-bottom: 20px;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.25);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 12px;
        }

        .user-name {
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 4px;
        }

        .user-email {
            font-size: 11px;
            opacity: 0.8;
            margin-bottom: 12px;
            word-break: break-all;
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 8px 12px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            justify-content: center;
            transition: all 0.3s;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* ========== CONTEÚDO PRINCIPAL (ÁREA AMARELA/BRANCA) ========== */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: #f8fafc;
        }

        /* Header Superior */
        .main-header {
            background: white;
            padding: 20px 30px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #1e2a3a;
        }

        .header-actions {
            display: flex;
            gap: 15px;
        }

        .header-icon {
            width: 40px;
            height: 40px;
            background: #f1f5f9;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .header-icon:hover {
            background: #e2e8f0;
        }

        /* Área de Conteúdo Dinâmico */
        .content-area {
            flex: 1;
            overflow-y: auto;
            padding: 30px;
        }

        /* ========== CARDS E ESTILOS ========== */
        .welcome-card {
            background: linear-gradient(135deg, #851e32 0%, #5a1e2c 100%);
            color: white;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
        }

        .welcome-card h2 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            background: #fff0f0;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #851e32;
            margin-bottom: 15px;
        }

        .stat-card h3 {
            font-size: 28px;
            color: #1e2a3a;
            margin-bottom: 5px;
        }

        .stat-card p {
            color: #64748b;
            font-size: 14px;
        }

        .info-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .info-card h3 {
            color: #1e2a3a;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }

        .blog-post {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background 0.3s;
        }

        .blog-post:hover {
            background: #f8fafc;
        }

        .blog-title {
            font-weight: 600;
            color: #1e2a3a;
            margin-bottom: 5px;
        }

        .blog-date {
            font-size: 12px;
            color: #94a3b8;
        }

        .support-card {
            background: #f8fafc;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 15px;
        }

        /* ========== CHAT (ÁREA VERDE) ========== */
        .chat-sidebar {
            width: 350px;
            background: white;
            border-left: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            box-shadow: -4px 0 20px rgba(0, 0, 0, 0.05);
        }

        .chat-header {
            padding: 20px;
            border-bottom: 1px solid #e2e8f0;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .chat-header h3 {
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chat-header p {
            font-size: 12px;
            opacity: 0.9;
            margin-top: 5px;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .message {
            display: flex;
            gap: 12px;
            max-width: 90%;
        }

        .message.user {
            align-self: flex-end;
            flex-direction: row-reverse;
        }

        .message-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .message.user .message-avatar {
            background: #851e32;
            color: white;
        }

        .message.bot .message-avatar {
            background: #10b981;
            color: white;
        }

        .message-bubble {
            background: #f1f5f9;
            padding: 10px 15px;
            border-radius: 18px;
            font-size: 13px;
            line-height: 1.4;
            color: #1e2a3a;
        }

        .message.user .message-bubble {
            background: #851e32;
            color: white;
        }

        .chat-input-area {
            padding: 15px 20px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            gap: 10px;
        }

        .chat-input {
            flex: 1;
            padding: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 25px;
            outline: none;
            font-family: inherit;
        }

        .chat-input:focus {
            border-color: #851e32;
        }

        .chat-send {
            width: 45px;
            height: 45px;
            background: #851e32;
            border: none;
            border-radius: 50%;
            color: white;
            cursor: pointer;
            transition: all 0.3s;
        }

        .chat-send:hover {
            background: #5a1e2c;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        /* Responsive */
        @media (max-width: 1000px) {
            .chat-sidebar {
                width: 300px;
            }
        }

        @media (max-width: 800px) {
            .chat-sidebar {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- SIDEBAR ESQUERDA (ÁREA AZUL) -->
        <div class="sidebar">
            <div class="logo-area">
                <div class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <div class="logo-text">
                        <h2>CardioWeb</h2>
                        <p>Saúde & Monitoramento</p>
                    </div>
                </div>
            </div>

            <div class="nav-menu">
                <div class="nav-item <?php echo $page == 'inicio' ? 'active' : ''; ?>" onclick="changePage('inicio')">
                    <i class="fas fa-home"></i>
                    <span>Início</span>
                </div>
                <div class="nav-item <?php echo $page == 'blog' ? 'active' : ''; ?>" onclick="changePage('blog')">
                    <i class="fas fa-newspaper"></i>
                    <span>Blog</span>
                </div>
                <div class="nav-item <?php echo $page == 'informacoes' ? 'active' : ''; ?>" onclick="changePage('informacoes')">
                    <i class="fas fa-info-circle"></i>
                    <span>Informações</span>
                </div>
                <div class="nav-item <?php echo $page == 'suporte' ? 'active' : ''; ?>" onclick="changePage('suporte')">
                    <i class="fas fa-headset"></i>
                    <span>Suporte</span>
                </div>
            </div>

            <!-- ÁREA ROXA - USUÁRIO LOGADO -->
            <div class="user-section">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                </div>
                <div class="user-name"><?php echo htmlspecialchars($user_name); ?></div>
                <div class="user-email"><?php echo htmlspecialchars($user_email); ?></div>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </div>
        </div>

        <!-- CONTEÚDO PRINCIPAL (ÁREA AMARELA/BRANCA) -->
        <div class="main-content">
            <div class="main-header">
                <h1 class="page-title" id="pageTitle">Início</h1>
                <div class="header-actions">
                    <div class="header-icon"><i class="fas fa-bell"></i></div>
                    <div class="header-icon"><i class="fas fa-cog"></i></div>
                </div>
            </div>

            <div class="content-area" id="contentArea">
                <!-- Conteúdo será carregado dinamicamente -->
            </div>
        </div>

        <!-- CHAT SIDEBAR (ÁREA VERDE) -->
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
        // Função para trocar de página
        function changePage(page) {
            // Atualizar URL sem recarregar
            const url = new URL(window.location.href);
            url.searchParams.set('page', page);
            window.history.pushState({}, '', url);
            
            // Atualizar título
            const titles = {
                'inicio': 'Início',
                'blog': 'Blog',
                'informacoes': 'Informações',
                'suporte': 'Suporte'
            };
            document.getElementById('pageTitle').innerText = titles[page] || 'Início';
            
            // Atualizar menu ativo
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });
            document.querySelector(`.nav-item[onclick="changePage('${page}')"]`).classList.add('active');
            
            // Carregar conteúdo
            loadContent(page);
        }
        
        function loadContent(page) {
            const contentArea = document.getElementById('contentArea');
            
            if (page === 'inicio') {
                contentArea.innerHTML = `
                    <div class="welcome-card">
                        <h2>Bem-vindo de volta, <?php echo htmlspecialchars($user_name); ?>! 👋</h2>
                        <p>Monitore sua saúde cardiológica em tempo real e mantenha seus exames em dia.</p>
                    </div>
                    
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                            <h3>12</h3>
                            <p>Registros de saúde</p>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fas fa-heartbeat"></i></div>
                            <h3>72</h3>
                            <p>Batimentos/min</p>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                            <h3>2</h3>
                            <p>Consultas agendadas</p>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fas fa-trophy"></i></div>
                            <h3>85%</h3>
                            <p>Meta de saúde</p>
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <h3><i class="fas fa-heart"></i> Últimos Registros</h3>
                        <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f0f0f0;">
                            <span>Pressão Arterial</span>
                            <span><strong>120/80 mmHg</strong></span>
                            <span style="color: #10b981;">Normal</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f0f0f0;">
                            <span>Colesterol Total</span>
                            <span><strong>180 mg/dL</strong></span>
                            <span style="color: #10b981;">Normal</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 12px 0;">
                            <span>Glicemia</span>
                            <span><strong>95 mg/dL</strong></span>
                            <span style="color: #10b981;">Normal</span>
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <h3><i class="fas fa-calendar-alt"></i> Próximas Consultas</h3>
                        <div style="display: flex; align-items: center; gap: 15px; padding: 12px 0;">
                            <div style="min-width: 50px; text-align: center;">
                                <div style="font-size: 20px; font-weight: 700; color: #851e32;">15</div>
                                <div style="font-size: 11px; color: #666;">ABR</div>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-weight: 600;">Cardiologista - Dr. Carlos</div>
                                <div style="font-size: 12px; color: #666;">10:00 - Consulta presencial</div>
                            </div>
                            <div style="font-size: 11px; background: #e8f5e9; color: #2e7d32; padding: 4px 10px; border-radius: 20px;">Confirmado</div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 15px; padding: 12px 0;">
                            <div style="min-width: 50px; text-align: center;">
                                <div style="font-size: 20px; font-weight: 700; color: #851e32;">22</div>
                                <div style="font-size: 11px; color: #666;">ABR</div>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-weight: 600;">Exame de Rotina</div>
                                <div style="font-size: 12px; color: #666;">08:30 - Laboratório</div>
                            </div>
                            <div style="font-size: 11px; background: #fff3e0; color: #ff9800; padding: 4px 10px; border-radius: 20px;">Pendente</div>
                        </div>
                    </div>
                `;
            } 
            else if (page === 'blog') {
                contentArea.innerHTML = `
                    <div class="info-card">
                        <h3><i class="fas fa-newspaper"></i> Artigos Recentes</h3>
                        <div class="blog-post">
                            <div class="blog-title">7 hábitos para manter o coração saudável</div>
                            <div class="blog-date">15 de Abril, 2024 • 5 min de leitura</div>
                        </div>
                        <div class="blog-post">
                            <div class="blog-title">Alimentação e saúde cardiovascular: o que evitar</div>
                            <div class="blog-date">10 de Abril, 2024 • 8 min de leitura</div>
                        </div>
                        <div class="blog-post">
                            <div class="blog-title">Exercícios físicos recomendados para cardíacos</div>
                            <div class="blog-date">05 de Abril, 2024 • 6 min de leitura</div>
                        </div>
                        <div class="blog-post">
                            <div class="blog-title">Como monitorar sua pressão arterial em casa</div>
                            <div class="blog-date">01 de Abril, 2024 • 4 min de leitura</div>
                        </div>
                        <div class="blog-post">
                            <div class="blog-title">Tecnologia e saúde: apps para monitoramento cardíaco</div>
                            <div class="blog-date">28 de Março, 2024 • 7 min de leitura</div>
                        </div>
                    </div>
                `;
            }
            else if (page === 'informacoes') {
                contentArea.innerHTML = `
                    <div class="info-card">
                        <h3><i class="fas fa-info-circle"></i> Sobre o CardioWeb</h3>
                        <p>O CardioWeb é uma plataforma completa de monitoramento cardiológico que permite acompanhar sua saúde em tempo real, agendar consultas, acessar exames e receber orientações personalizadas.</p>
                    </div>
                    
                    <div class="info-card">
                        <h3><i class="fas fa-chart-line"></i> Funcionalidades</h3>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                            <div style="padding: 15px; background: #f8fafc; border-radius: 12px;">
                                <i class="fas fa-heartbeat" style="color: #851e32; font-size: 24px;"></i>
                                <div style="font-weight: 600; margin-top: 10px;">Monitoramento</div>
                                <div style="font-size: 12px; color: #666;">Acompanhe batimentos e pressão</div>
                            </div>
                            <div style="padding: 15px; background: #f8fafc; border-radius: 12px;">
                                <i class="fas fa-file-alt" style="color: #851e32; font-size: 24px;"></i>
                                <div style="font-weight: 600; margin-top: 10px;">Exames Online</div>
                                <div style="font-size: 12px; color: #666;">Acesse resultados de exames</div>
                            </div>
                            <div style="padding: 15px; background: #f8fafc; border-radius: 12px;">
                                <i class="fas fa-calendar-check" style="color: #851e32; font-size: 24px;"></i>
                                <div style="font-weight: 600; margin-top: 10px;">Agendamentos</div>
                                <div style="font-size: 12px; color: #666;">Marque consultas facilmente</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <h3><i class="fas fa-shield-alt"></i> Segurança e Privacidade</h3>
                        <p>Seus dados são protegidos com criptografia de ponta a ponta e seguimos rigorosamente a LGPD para garantir sua privacidade.</p>
                    </div>
                `;
            }
            else if (page === 'suporte') {
                contentArea.innerHTML = `
                    <div class="info-card">
                        <h3><i class="fas fa-headset"></i> Central de Suporte</h3>
                        <p>Estamos aqui para ajudar! Escolha uma opção abaixo:</p>
                    </div>
                    
                    <div class="support-card">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="width: 50px; height: 50px; background: #e8f5e9; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-phone" style="color: #2e7d32; font-size: 24px;"></i>
                            </div>
                            <div>
                                <div style="font-weight: 600;">Atendimento Telefônico</div>
                                <div style="font-size: 12px; color: #666;">Segunda a Sexta, 8h às 18h</div>
                                <div style="font-size: 14px; color: #851e32; margin-top: 5px;">(11) 4002-8922</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="support-card">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="width: 50px; height: 50px; background: #e3f2fd; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-envelope" style="color: #1976d2; font-size: 24px;"></i>
                            </div>
                            <div>
                                <div style="font-weight: 600;">E-mail</div>
                                <div style="font-size: 12px; color: #666;">Respondemos em até 24h</div>
                                <div style="font-size: 14px; color: #851e32; margin-top: 5px;">suporte@cardioweb.com</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="support-card">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="width: 50px; height: 50px; background: #fff3e0; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="fab fa-whatsapp" style="color: #25d366; font-size: 28px;"></i>
                            </div>
                            <div>
                                <div style="font-weight: 600;">WhatsApp</div>
                                <div style="font-size: 12px; color: #666;">Atendimento 24h</div>
                                <div style="font-size: 14px; color: #851e32; margin-top: 5px;">(11) 9 9999-9999</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <h3><i class="fas fa-question-circle"></i> Perguntas Frequentes</h3>
                        <div style="margin-top: 10px;">
                            <details style="margin-bottom: 10px;">
                                <summary style="cursor: pointer; font-weight: 500; padding: 10px; background: #f8fafc; border-radius: 8px;">Como agendar uma consulta?</summary>
                                <p style="padding: 10px; color: #666;">Acesse o menu "Consultas" e clique em "Agendar nova consulta". Escolha o médico e horário disponível.</p>
                            </details>
                            <details style="margin-bottom: 10px;">
                                <summary style="cursor: pointer; font-weight: 500; padding: 10px; background: #f8fafc; border-radius: 8px;">Como acessar meus exames?</summary>
                                <p style="padding: 10px; color: #666;">Os exames ficam disponíveis na seção "Exames" após liberação do médico responsável.</p>
                            </details>
                        </div>
                    </div>
                `;
            }
        }
        
        // ========== FUNÇÕES DO CHAT ==========
        function sendMessage() {
            const input = document.getElementById('chatInput');
            const message = input.value.trim();
            
            if (!message) return;
            
            // Adiciona mensagem do usuário
            addMessage(message, 'user');
            input.value = '';
            
            // Resposta do bot
            setTimeout(() => {
                const response = getBotResponse(message);
                addMessage(response, 'bot');
            }, 500);
        }
        
        function addMessage(text, sender) {
            const messagesContainer = document.getElementById('chatMessages');
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${sender}`;
            
            const avatar = sender === 'user' 
                ? '<div class="message-avatar"><i class="fas fa-user"></i></div>'
                : '<div class="message-avatar"><i class="fas fa-robot"></i></div>';
            
            messageDiv.innerHTML = avatar + `<div class="message-bubble">${text}</div>`;
            messagesContainer.appendChild(messageDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        
        function getBotResponse(message) {
            const msg = message.toLowerCase();
            
            if (msg.includes('olá') || msg.includes('oi') || msg.includes('opa')) {
                return 'Olá! Como posso ajudar você hoje? 💙';
            }
            if (msg.includes('pressão') || msg.includes('pressao')) {
                return 'A pressão arterial ideal é abaixo de 120/80 mmHg. Mantenha uma alimentação saudável e evite sal!';
            }
            if (msg.includes('colesterol')) {
                return 'O colesterol total deve ficar abaixo de 190 mg/dL. Exercícios físicos ajudam a controlar!';
            }
            if (msg.includes('consulta') || msg.includes('agendar')) {
                return 'Para agendar uma consulta, acesse o menu "Consultas" ou ligue para (11) 4002-8922.';
            }
            if (msg.includes('exame')) {
                return 'Seus exames ficam disponíveis na seção "Exames" após liberação médica.';
            }
            if (msg.includes('obrigado') || msg.includes('valeu')) {
                return 'Por nada! Estou aqui para ajudar. ❤️';
            }
            
            return 'Entendi! Para informações mais específicas, recomendo falar com um de nossos atendentes ou acessar nossa central de suporte. 💙';
        }
        
        // Carregar conteúdo inicial
        loadContent('<?php echo $page; ?>');
    </script>
</body>
=======
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - CardioWeb</title>
    <style>
        body {
            font-family: Arial;
            background: #851e32;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .box {<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: index.php');
    exit();
}

$user_name = $_SESSION['user_name'] ?? 'Usuário';
$user_email = $_SESSION['user_email'] ?? $_SESSION['user_telefone'] ?? 'usuario@email.com';
$page = $_GET['page'] ?? 'inicio';
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f0f2f5;
            overflow: hidden;
            height: 100vh;
        }

        .app-container {
            display: flex;
            height: 100vh;
            width: 100%;
        }

        /* SIDEBAR - ÁREA AZUL CLARO */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #e0f2fe 0%, #bae6fd 100%);
            display: flex;
            flex-direction: column;
        }

        /* LOGO - ÁREA VERMELHA */
        .logo-area {
            padding: 25px 20px;
            border-bottom: 2px solid rgba(133, 30, 50, 0.2);
            margin-bottom: 20px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            background: linear-gradient(135deg, #851e32, #5a1e2c);
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
        }

        .logo-text h2 {
            font-size: 22px;
            font-weight: 700;
            color: #851e32;
        }

        .logo-text p {
            font-size: 10px;
            color: #666;
        }

        /* MENU */
        .nav-menu {
            flex: 1;
            padding: 0 15px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 18px;
            margin-bottom: 8px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            color: #1e40af;
            font-weight: 500;
        }

        .nav-item:hover {
            background: rgba(133, 30, 50, 0.1);
            color: #851e32;
        }

        .nav-item.active {
            background: #851e32;
            color: white;
        }

        .nav-item i {
            width: 24px;
        }

        /* USUÁRIO - ÁREA ROXA */
        .user-section {
            padding: 20px;
            margin: 20px;
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            border-radius: 16px;
            margin-top: auto;
            margin-bottom: 20px;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            background: rgba(255,255,255,0.25);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: bold;
            color: white;
            margin-bottom: 12px;
        }

        .user-name {
            font-weight: 600;
            font-size: 16px;
            color: white;
            margin-bottom: 4px;
        }

        .user-email {
            font-size: 11px;
            color: rgba(255,255,255,0.8);
            margin-bottom: 12px;
        }

        .logout-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 8px 12px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            justify-content: center;
        }

        /* CONTEÚDO PRINCIPAL - ÁREA BRANCA/AMARELA */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: #fffef7;
        }

        .main-header {
            background: white;
            padding: 15px 25px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .search-bar {
            flex: 1;
            max-width: 400px;
            position: relative;
        }

        .search-bar i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .search-bar input {
            width: 100%;
            padding: 10px 20px 10px 45px;
            border: 1.5px solid #e2e8f0;
            border-radius: 30px;
            font-size: 14px;
        }

        .page-title {
            font-size: 20px;
            font-weight: 700;
            color: #1e2a3a;
            min-width: 120px;
        }

        .content-area {
            flex: 1;
            overflow-y: auto;
            padding: 25px;
        }

        /* CARDS */
        .welcome-card {
            background: linear-gradient(135deg, #851e32 0%, #5a1e2c 100%);
            color: white;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 25px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            background: #fff0f0;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #851e32;
            margin-bottom: 15px;
        }

        .stat-card h3 {
            font-size: 28px;
            color: #1e2a3a;
        }

        .info-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
        }

        .info-card h3 {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }

        /* CHAT - ÁREA VERDE */
        .chat-sidebar {
            width: 320px;
            background: white;
            border-left: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            padding: 20px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            background: #f9fafb;
        }

        .message {
            display: flex;
            gap: 10px;
            max-width: 90%;
        }

        .message.user {
            align-self: flex-end;
            flex-direction: row-reverse;
        }

        .message-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .message.user .message-avatar {
            background: #851e32;
            color: white;
        }

        .message.bot .message-avatar {
            background: #10b981;
            color: white;
        }

        .message-bubble {
            background: white;
            padding: 8px 12px;
            border-radius: 16px;
            font-size: 13px;
        }

        .message.user .message-bubble {
            background: #851e32;
            color: white;
        }

        .chat-input-area {
            padding: 15px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            gap: 10px;
        }

        .chat-input {
            flex: 1;
            padding: 10px;
            border: 1.5px solid #e2e8f0;
            border-radius: 25px;
            outline: none;
        }

        .chat-send {
            width: 40px;
            height: 40px;
            background: #851e32;
            border: none;
            border-radius: 50%;
            color: white;
            cursor: pointer;
        }

        @media (max-width: 800px) {
            .chat-sidebar {
                display: none;
            }
        }
    </style>
</head>
<body>
<div class="app-container">
    <!-- SIDEBAR AZUL -->
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
                <i class="fas fa-home"></i> <span>Início</span>
            </div>
            <div class="nav-item <?php echo $page == 'blog' ? 'active' : ''; ?>" onclick="changePage('blog')">
                <i class="fas fa-newspaper"></i> <span>Blog</span>
            </div>
            <div class="nav-item <?php echo $page == 'informacoes' ? 'active' : ''; ?>" onclick="changePage('informacoes')">
                <i class="fas fa-info-circle"></i> <span>Informações</span>
            </div>
            <div class="nav-item <?php echo $page == 'suporte' ? 'active' : ''; ?>" onclick="changePage('suporte')">
                <i class="fas fa-headset"></i> <span>Suporte</span>
            </div>
        </div>

        <!-- ÁREA ROXA - USUÁRIO -->
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
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Pesquisar..." onkeyup="searchContent()">
            </div>
            <h1 class="page-title" id="pageTitle">Início</h1>
            <div><i class="fas fa-bell" style="font-size: 20px; color: #666; cursor: pointer;"></i></div>
        </div>
        <div class="content-area" id="contentArea"></div>
    </div>

    <!-- CHAT VERDE -->
    <div class="chat-sidebar">
        <div class="chat-header">
            <h3><i class="fas fa-comment-dots"></i> Assistente</h3>
            <p>💬 Converse sobre sua saúde</p>
        </div>
        <div class="chat-messages" id="chatMessages">
            <div class="message bot">
                <div class="message-avatar"><i class="fas fa-robot"></i></div>
                <div class="message-bubble">Olá! Como posso ajudar? 💙</div>
            </div>
        </div>
        <div class="chat-input-area">
            <input type="text" class="chat-input" id="chatInput" placeholder="Digite sua mensagem..." onkeypress="if(event.key === 'Enter') sendMessage()">
            <button class="chat-send" onclick="sendMessage()"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
</div>

<script>
    function changePage(page) {
        const url = new URL(window.location.href);
        url.searchParams.set('page', page);
        window.history.pushState({}, '', url);
        
        const titles = { 'inicio': 'Início', 'blog': 'Blog', 'informacoes': 'Informações', 'suporte': 'Suporte' };
        document.getElementById('pageTitle').innerText = titles[page] || 'Início';
        
        document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));
        document.querySelector(`.nav-item[onclick="changePage('${page}')"]`).classList.add('active');
        
        loadContent(page);
    }
    
    function loadContent(page) {
        const area = document.getElementById('contentArea');
        
        if (page === 'inicio') {
            area.innerHTML = `
                <div class="welcome-card">
                    <h2>Bem-vindo de volta, <?php echo htmlspecialchars($user_name); ?>! 👋</h2>
                    <p>Monitore sua saúde cardiológica em tempo real.</p>
                </div>
                <div class="stats-grid">
                    <div class="stat-card"><div class="stat-icon"><i class="fas fa-chart-line"></i></div><h3>12</h3><p>Registros</p></div>
                    <div class="stat-card"><div class="stat-icon"><i class="fas fa-heartbeat"></i></div><h3>72</h3><p>Batimentos/min</p></div>
                    <div class="stat-card"><div class="stat-icon"><i class="fas fa-calendar-check"></i></div><h3>2</h3><p>Consultas</p></div>
                </div>
                <div class="info-card">
                    <h3><i class="fas fa-heart"></i> Últimos Registros</h3>
                    <div>Pressão Arterial: <strong>120/80 mmHg</strong> - Normal</div>
                    <div style="margin-top:10px">Colesterol: <strong>180 mg/dL</strong> - Normal</div>
                    <div style="margin-top:10px">Glicemia: <strong>95 mg/dL</strong> - Normal</div>
                </div>
            `;
        } else if (page === 'blog') {
            area.innerHTML = `<div class="info-card"><h3><i class="fas fa-newspaper"></i> Artigos</h3>
                <div style="padding:12px 0; border-bottom:1px solid #eee"><b>7 hábitos para o coração saudável</b><br><small>15/04/2024</small></div>
                <div style="padding:12px 0; border-bottom:1px solid #eee"><b>Alimentação e saúde cardiovascular</b><br><small>10/04/2024</small></div>
                <div style="padding:12px 0"><b>Exercícios para cardíacos</b><br><small>05/04/2024</small></div>
            </div>`;
        } else if (page === 'informacoes') {
            area.innerHTML = `<div class="info-card"><h3><i class="fas fa-info-circle"></i> Sobre</h3>
                <p>O CardioWeb é uma plataforma de monitoramento cardiológico que permite acompanhar sua saúde, agendar consultas e acessar exames.</p>
            </div>
            <div class="info-card"><h3><i class="fas fa-shield-alt"></i> Segurança</h3>
                <p>Seus dados são protegidos com criptografia e seguimos a LGPD.</p>
            </div>`;
        } else if (page === 'suporte') {
            area.innerHTML = `<div class="info-card"><h3><i class="fas fa-headset"></i> Suporte</h3>
                <p><i class="fas fa-phone"></i> Telefone: (11) 4002-8922</p>
                <p><i class="fas fa-envelope"></i> E-mail: suporte@cardioweb.com</p>
                <p><i class="fab fa-whatsapp"></i> WhatsApp: (11) 9 9999-9999</p>
            </div>`;
        }
    }
    
    function sendMessage() {
        const input = document.getElementById('chatInput');
        const msg = input.value.trim();
        if (!msg) return;
        
        const container = document.getElementById('chatMessages');
        const userDiv = document.createElement('div');
        userDiv.className = 'message user';
        userDiv.innerHTML = `<div class="message-avatar"><i class="fas fa-user"></i></div><div class="message-bubble">${msg}</div>`;
        container.appendChild(userDiv);
        
        input.value = '';
        container.scrollTop = container.scrollHeight;
        
        setTimeout(() => {
            let response = '';
            const m = msg.toLowerCase();
            if (m.includes('olá') || m.includes('oi')) response = 'Olá! Como posso ajudar? 💙';
            else if (m.includes('pressão')) response = 'A pressão ideal é abaixo de 120/80 mmHg.';
            else if (m.includes('colesterol')) response = 'O colesterol total deve ficar abaixo de 190 mg/dL.';
            else response = 'Entendi! Para mais informações, fale com nossos atendentes. 💙';
            
            const botDiv = document.createElement('div');
            botDiv.className = 'message bot';
            botDiv.innerHTML = `<div class="message-avatar"><i class="fas fa-robot"></i></div><div class="message-bubble">${response}</div>`;
            container.appendChild(botDiv);
            container.scrollTop = container.scrollHeight;
        }, 500);
    }
    
    function searchContent() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const items = document.querySelectorAll('.info-card, .stat-card, .welcome-card');
        items.forEach(item => {
            const text = item.innerText.toLowerCase();
            item.style.display = text.includes(search) ? 'block' : 'none';
        });
    }
    
    // Carregar página inicial
    loadContent('<?php echo $page; ?>');
</script>
</body>
</html>
            background: white;
            padding: 40px;
            border-radius: 10px;
            text-align: center;
        }
        .btn {
            background: #851e32;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="box">
        <h2>✅ Login realizado com sucesso!</h2>
        <p>Bem-vindo ao CardioWeb!</p>
        <a href="logout.php" class="btn">Sair</a>
    </div>
</body>
>>>>>>> 726677b42bba7bd6978a1db01e6f8f37c062b38d
</html>