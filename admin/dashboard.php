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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CardioWeb - Painel Administrativo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Reset e Base baseados na Foto (Dark Theme) */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: #0d0e12; color: #ffffff; min-height: 100vh; display: flex; }
        
        .dashboard-container { display: flex; width: 100%; min-height: 100vh; }
        
        /* Menu Lateral Estilo Premium Dark */
        .sidebar { width: 260px; background: #13151b; border-right: 1px solid #1f232d; display: flex; flex-direction: column; padding: 20px; }
        .sidebar-header { padding: 10px 0 30px 0; display: flex; align-items: center; }
        .sidebar-logo { font-size: 20px; font-weight: 700; color: #ffffff; display: flex; align-items: center; gap: 10px; }
        .sidebar-logo i { color: #6366f1; }
        .sidebar-logo .badge { font-size: 10px; background: rgba(99, 102, 241, 0.2); color: #818cf8; padding: 3px 8px; border-radius: 20px; font-weight: 600; }
        
        .sidebar-menu { display: flex; flex-direction: column; gap: 8px; flex: 1; }
        .menu-item { display: flex; align-items: center; gap: 12px; padding: 14px 16px; color: #94a3b8; text-decoration: none; border-radius: 10px; font-size: 15px; font-weight: 500; transition: all 0.2s ease; cursor: pointer; }
        .menu-item:hover { background: #1c1f26; color: #ffffff; }
        .menu-item.active { background: #6366f1; color: #ffffff; box-shadow: 0 4px 14px rgba(99, 102, 241, 0.4); }
        .menu-item i { font-size: 18px; width: 20px; text-align: center; }
        
        /* Área de Conteúdo Principal */
        .main-content { flex: 1; display: flex; flex-direction: column; min-height: 100vh; background: #0d0e12; }
        
        /* Top Bar Fluida e Limpa */
        .top-bar { height: 80px; padding: 0 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #1f232d; background: #13151b; }
        .welcome-msg h2 { font-size: 20px; font-weight: 600; color: #ffffff; }
        .welcome-msg p { font-size: 13px; color: #64748b; margin-top: 2px; }
        
        .user-profile { display: flex; align-items: center; gap: 12px; }
        .avatar { width: 42px; height: 42px; background: #6366f1; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 16px; border: 2px solid #1f232d; }
        
        /* Corpo do Painel */
        .content-body { padding: 40px; flex: 1; overflow-y: auto; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <i class="fas fa-heartbeat"></i> CardioWeb <span class="badge">ADMIN</span>
                </div>
            </div>
            
            <nav class="sidebar-menu">
                <a onclick="loadContent('consultas'); return false;" id="menu-consultas" class="menu-item">
                    <i class="fas fa-check-circle"></i> Confirmar Consultas
                </a>
                <a onclick="loadContent('agendas'); return false;" id="menu-agendas" class="menu-item">
                    <i class="fas fa-calendar-alt"></i> Agenda dos Médicos
                </a>
                <a href="../logout.php" class="menu-item" style="margin-top: auto; color: #ef4444;">
                    <i class="fas fa-sign-out-alt"></i> Sair do Painel
                </a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="top-bar">
                <div class="welcome-msg">
                    <h2>Painel de Gestão</h2>
                    <p>Olá, <?php echo htmlspecialchars(ucfirst($user_name)); ?>. Bem-vindo de volta.</p>
                </div>
                <div class="user-profile">
                    <div class="avatar"><?php echo strtoupper(substr($user_name, 0, 1)); ?></div>
                </div>
            </header>

            <div class="content-body" id="mainContent">
                </div>
        </main>
    </div>

    <script>
        function loadContent(section) {
            document.querySelectorAll('.menu-item').forEach(item => item.classList.remove('active'));
            const activeMenu = document.getElementById(`menu-${section}`);
            if(activeMenu) activeMenu.classList.add('active');
            
            const mainContent = document.getElementById('mainContent');
            
            fetch(`admin-sections/${section}.php`)
                .then(response => response.text())
                .then(html => {
                    mainContent.innerHTML = html;
                })
                .catch(error => {
                    mainContent.innerHTML = '<p style="color: #ef4444;">Erro ao carregar a seção.</p>';
                });
        }

        function aprovarConsulta(id, paciente) {
            alert('✓ Consulta de ' + paciente + ' CONFIRMADA!');
            const statusBadge = document.getElementById('status-' + id);
            const actionButtons = document.getElementById('actions-' + id);
            if(statusBadge) {
                statusBadge.innerHTML = 'Confirmada';
                statusBadge.style.background = 'rgba(34, 197, 94, 0.15)';
                statusBadge.style.color = '#4ade80';
            }
            if(actionButtons) actionButtons.style.display = 'none';
        }

        function rejeitarConsulta(id, paciente) {
            if(confirm('Recusar consulta de ' + paciente + '?')) {
                const card = document.getElementById('card-' + id);
                if(card) card.style.opacity = '0.3';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadContent('<?php echo $page; ?>');
        });
    </script>
</body>
</html>