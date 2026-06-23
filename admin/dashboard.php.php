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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css"> </head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <i class="fas fa-heartbeat"></i> CardioWeb <span style="font-size: 10px; background: #851e32; color: white; padding: 2px 6px; border-radius: 10px; margin-left: 5px;">ADMIN</span>
                </div>
            </div>
            
            <nav class="sidebar-menu">
                <a href="#" onclick="loadContent('consultas'); return false;" id="menu-consultas" class="menu-item">
                    <i class="fas fa-check-circle"></i> Confirmar Consultas
                </a>
                <a href="#" onclick="loadContent('agendas'); return false;" id="menu-agendas" class="menu-item">
                    <i class="fas fa-calendar-alt"></i> Agenda dos Médicos
                </a>
                <a href="../logout.php" class="menu-item" style="margin-top: auto; color: #c62828;">
                    <i class="fas fa-sign-out-alt"></i> Sair do Painel
                </a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="top-bar">
                <div class="welcome-msg">
                    <h2>Painel de Gestão</h2>
                    <p>Olá, <?php echo htmlspecialchars(ucfirst($user_name)); ?>. Gerencie solicitações e horários abaixo.</p>
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
        // Sistema de navegação idêntico ao seu dashboard de pacientes
        function loadContent(section) {
            // Atualizar classes ativas no menu
            document.querySelectorAll('.menu-item').forEach(item => item.classList.remove('active'));
            const activeMenu = document.getElementById(`menu-${section}`);
            if(activeMenu) activeMenu.classList.add('active');
            
            const mainContent = document.getElementById('mainContent');
            
            // Carregar via fetch as seções organizadas
            fetch(`admin-sections/${section}.php`)
                .then(response => response.text())
                .then(html => {
                    mainContent.innerHTML = html;
                })
                .catch(error => {
                    mainContent.innerHTML = '<p>Erro ao carregar a seção.</p>';
                });
        }

        // Ações dos botões de aprovação
        function aprovarConsulta(id, paciente) {
            alert('✓ Consulta de ' + paciente + ' CONFIRMADA com sucesso!');
            const statusBadge = document.getElementById('status-' + id);
            const actionButtons = document.getElementById('actions-' + id);
            if(statusBadge) statusBadge.innerHTML = '<span style="color:#2e7d32; font-weight:600;">🕓 Confirmada</span>';
            if(actionButtons) actionButtons.style.display = 'none';
        }

        function rejeitarConsulta(id, paciente) {
            if(confirm('Deseja realmente recusar o agendamento de ' + paciente + '?')) {
                alert('✕ Consulta cancelada.');
                const card = document.getElementById('card-' + id);
                if(card) card.style.opacity = '0.4';
            }
        }

        // Carregar a aba inicial padrão
        document.addEventListener('DOMContentLoaded', () => {
            loadContent('<?php echo $page; ?>');
        });
    </script>
</body>
</html>