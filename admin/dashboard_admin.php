<?php
session_start();

// Segurança: apenas administradores
if (!isset($_SESSION['logado']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../index.php');
    exit();
}

$page = $_GET['page'] ?? 'confirmar-consultas';
$user_name = $_SESSION['user_name'] ?? 'Admin';
$user_email = $_SESSION['user_email'] ?? 'admin@cardioweb.com';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CardioWeb - Painel Administrativo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ===== CSS COMPLETO (mesmo do paciente) ===== */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f6ecee; overflow: hidden; height: 100vh; }
        .app-container { display: flex; height: 100vh; width: 100%; }
        .sidebar { width: 280px; background: linear-gradient(180deg, #4c0719 0%, #7e1b31 100%); color: white; display: flex; flex-direction: column; box-shadow: 4px 0 20px rgba(0,0,0,0.12); overflow-y: auto; flex-shrink: 0; }
        .logo-area { padding: 30px 25px; border-bottom: 1px solid rgba(255,255,255,0.12); margin-bottom: 30px; }
        .logo { display: flex; align-items: center; gap: 12px; }
        .logo-icon { background: rgba(255,255,255,0.2); width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 28px; }
        .logo-text h2 { font-size: 22px; font-weight: 700; letter-spacing: -0.5px; }
        .logo-text p { font-size: 10px; opacity: 0.8; margin-top: 4px; }
        .nav-menu { flex: 1; padding: 0 20px; }
        .nav-item { display: flex; align-items: center; gap: 14px; padding: 14px 18px; margin-bottom: 8px; border-radius: 12px; cursor: pointer; transition: all 0.3s; color: rgba(255,255,255,0.8); text-decoration: none; border: none; background: transparent; width: 100%; font-size: 15px; font-family: inherit; }
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
        .header-icon { width: 40px; height: 40px; background: #f1f5f9; border-radius: 10px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s; color: #2d3e50; }
        .header-icon:hover { background: #e2e8f0; }
        .content-area { flex: 1; overflow-y: auto; padding: 30px; }
        .info-card { background: white; border-radius: 16px; padding: 25px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .info-card h3 { color: #1e2a3a; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #f0f0f0; }
        .contact-btn { background: #851e32; color: white; border: none; padding: 12px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; font-family: inherit; }
        .contact-btn:hover { background: #5a1e2c; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(133,30,50,0.3); }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 3px; }
        @media (max-width: 768px) {
            .sidebar { width: 80px; }
            .logo-text p, .logo-text h2, .nav-item span, .user-name, .user-email, .logout-btn span { display: none; }
            .nav-item { justify-content: center; padding: 14px 0; }
            .nav-item i { margin: 0; }
            .user-section { padding: 10px; margin: 10px; text-align: center; }
            .user-avatar { margin: 0 auto 8px; }
            .logout-btn { font-size: 0; padding: 8px; }
            .logout-btn i { font-size: 18px; }
            .main-header { padding: 15px 20px; }
            .page-title { font-size: 18px; }
            .content-area { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <div class="sidebar">
            <div class="logo-area">
                <div class="logo">
                    <div class="logo-icon"><i class="fas fa-heartbeat"></i></div>
                    <div class="logo-text"><h2>CardioWeb</h2><p>Administração</p></div>
                </div>
            </div>
            <nav class="nav-menu">
                <button class="nav-item <?php echo $page == 'confirmar-consultas' ? 'active' : ''; ?>" onclick="loadContent('confirmar-consultas')">
                    <i class="fas fa-check-circle"></i><span>Confirmar Consultas</span>
                </button>
                <button class="nav-item <?php echo $page == 'agenda-medicos' ? 'active' : ''; ?>" onclick="loadContent('agenda-medicos')">
                    <i class="fas fa-calendar-alt"></i><span>Agenda dos Médicos</span>
                </button>
            </nav>
            <div class="user-section">
                <div class="user-avatar"><?php echo strtoupper(substr($user_name, 0, 1)); ?></div>
                <div class="user-name"><?php echo htmlspecialchars($user_name); ?></div>
                <div class="user-email"><?php echo htmlspecialchars($user_email); ?></div>
                <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> <span>Sair</span></a>
            </div>
        </div>
        <div class="main-content">
            <header class="main-header">
                <h1 class="page-title" id="pageTitle">Confirmar Consultas</h1>
                <div class="header-actions">
                    <div class="header-icon"><i class="fas fa-bell"></i></div>
                    <div class="header-icon"><i class="fas fa-cog"></i></div>
                </div>
            </header>
            <div class="content-area" id="contentArea">
                <!-- Carregado via AJAX -->
            </div>
        </div>
    </div>

    <script>
        // ========== FUNÇÕES DE ACESSO AO LOCALSTORAGE ==========
        function carregarDados() {
            const storedConsultas = localStorage.getItem('consultas');
            const storedAgenda = localStorage.getItem('agendaMedicos');
            
            if (storedConsultas) {
                window.consultas = JSON.parse(storedConsultas);
            } else {
                // Dados iniciais para demonstração (apenas se nunca houver nada)
                window.consultas = {
                    pendentes: [],
                    confirmadas: [],
                    recusadas: []
                };
                localStorage.setItem('consultas', JSON.stringify(window.consultas));
            }
            
            if (storedAgenda) {
                window.agendaMedicos = JSON.parse(storedAgenda);
            } else {
                window.agendaMedicos = {
                    'Dr. Roberto Mendes': [],
                    'Dra. Aline Costa': []
                };
                localStorage.setItem('agendaMedicos', JSON.stringify(window.agendaMedicos));
            }
        }

        function salvarDados() {
            localStorage.setItem('consultas', JSON.stringify(window.consultas));
            localStorage.setItem('agendaMedicos', JSON.stringify(window.agendaMedicos));
        }

        // Carregar dados ao iniciar
        carregarDados();

        // ========== FUNÇÕES DE APROVAÇÃO/RECUSA ==========
        window.aprovarConsulta = function(id) {
            const consulta = window.consultas.pendentes.find(c => c.id === id);
            if (!consulta) return;
            
            // Remover dos pendentes
            window.consultas.pendentes = window.consultas.pendentes.filter(c => c.id !== id);
            // Adicionar aos confirmados
            consulta.status = 'confirmada';
            window.consultas.confirmadas.push(consulta);
            
            // Adicionar à agenda do médico
            if (!window.agendaMedicos[consulta.medico]) {
                window.agendaMedicos[consulta.medico] = [];
            }
            window.agendaMedicos[consulta.medico].push({
                data: consulta.data,
                hora: consulta.hora,
                paciente: consulta.paciente
            });
            
            // Salvar no localStorage
            salvarDados();
            
            // Recarregar a seção para atualizar a UI
            loadContent('confirmar-consultas');
            alert('✓ Consulta de ' + consulta.paciente + ' confirmada e adicionada à agenda!');
        };

        window.recusarConsulta = function(id) {
            const consulta = window.consultas.pendentes.find(c => c.id === id);
            if (!consulta) return;
            
            const mensagem = prompt('Digite a mensagem para o paciente (motivo da recusa ou sugestão de novo horário):', 'Horário indisponível. Sugerimos outro horário.');
            if (mensagem === null) return;
            
            // Remover dos pendentes
            window.consultas.pendentes = window.consultas.pendentes.filter(c => c.id !== id);
            // Adicionar aos recusados
            consulta.status = 'recusada';
            consulta.mensagem = mensagem || 'Sem mensagem';
            window.consultas.recusadas.push(consulta);
            
            // Salvar no localStorage
            salvarDados();
            
            loadContent('confirmar-consultas');
            alert('✉️ Mensagem enviada ao paciente: "' + mensagem + '"');
        };

        // ========== CARREGAR CONTEÚDO ==========
        const titles = {
            'confirmar-consultas': 'Confirmar Consultas',
            'agenda-medicos': 'Agenda dos Médicos'
        };

        function loadContent(section) {
            const url = new URL(window.location.href);
            url.searchParams.set('page', section);
            window.history.pushState({}, '', url);
            document.getElementById('pageTitle').innerText = titles[section] || section;
            
            document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));
            document.querySelector(`.nav-item[onclick="loadContent('${section}')"]`).classList.add('active');

            const contentArea = document.getElementById('contentArea');
            fetch(`admin-sections/${section}.php`)
                .then(response => {
                    if (!response.ok) throw new Error('Seção não encontrada');
                    return response.text();
                })
                .then(html => {
                    contentArea.innerHTML = html;
                    // Disparar a renderização após o carregamento
                    if (section === 'confirmar-consultas') {
                        if (typeof window.renderConsultas === 'function') window.renderConsultas();
                    } else if (section === 'agenda-medicos') {
                        if (typeof window.renderAgenda === 'function') window.renderAgenda();
                    }
                })
                .catch(error => {
                    contentArea.innerHTML = `
                        <div class="info-card">
                            <h3><i class="fas fa-exclamation-triangle"></i> Erro</h3>
                            <p style="color: #c00;">Não foi possível carregar a seção. Verifique se o arquivo admin-sections/${section}.php existe.</p>
                        </div>
                    `;
                    console.error(error);
                });
        }

        // Carregar página inicial
        document.addEventListener('DOMContentLoaded', () => {
            const page = '<?php echo $page; ?>';
            loadContent(page);
        });
    </script>
</body>
</html>