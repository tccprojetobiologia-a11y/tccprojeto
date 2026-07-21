<?php
session_start();
if (!isset($_SESSION['logado']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../index.php');
    exit();
}

$user_name = $_SESSION['user_name'] ?? 'Admin';
$user_email = $_SESSION['user_email'] ?? 'admin@cardioweb.com';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>CardioWeb - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f6ecee; height: 100vh; }
        .app-container { display: flex; height: 100vh; }
        .sidebar { width: 250px; background: #4c0719; color: white; padding: 20px; overflow-y: auto; flex-shrink: 0; }
        .logo-area { margin-bottom: 30px; }
        .logo { display: flex; align-items: center; gap: 10px; }
        .logo-icon { background: rgba(255,255,255,0.2); padding: 10px; border-radius: 10px; }
        .nav-item { display: block; width: 100%; padding: 12px; margin-bottom: 8px; background: transparent; color: white; border: none; text-align: left; cursor: pointer; border-radius: 8px; font-size: 15px; }
        .nav-item:hover { background: rgba(255,255,255,0.1); }
        .nav-item.active { background: rgba(255,255,255,0.2); }
        .nav-item i { margin-right: 10px; }
        .user-section { margin-top: 50px; padding: 20px; background: rgba(255,255,255,0.1); border-radius: 12px; }
        .user-avatar { width: 40px; height: 40px; background: rgba(255,255,255,0.3); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-bottom: 10px; }
        .logout-btn { display: block; margin-top: 10px; color: white; text-decoration: none; background: rgba(255,255,255,0.2); padding: 8px; border-radius: 8px; text-align: center; }
        .main-content { flex: 1; display: flex; flex-direction: column; background: #f8fafc; }
        .main-header { background: white; padding: 20px 30px; border-bottom: 1px solid #ddd; }
        .page-title { font-size: 24px; font-weight: bold; }
        .content-area { flex: 1; padding: 30px; overflow-y: auto; }
        .loading { text-align: center; padding: 50px; color: #999; }
        .loading i { font-size: 30px; color: #851e32; }
    </style>
</head>
<body>
    <div class="app-container">
        <div class="sidebar">
            <div class="logo-area">
                <div class="logo">
                    <div class="logo-icon"><i class="fas fa-heartbeat"></i></div>
                    <div><h2>CardioWeb</h2><small>Administração</small></div>
                </div>
            </div>
            <button class="nav-item active" onclick="carregar('confirmar-consultas')">
                <i class="fas fa-check-circle"></i> Confirmar Consultas
            </button>
            <button class="nav-item" onclick="carregar('agenda-medicos')">
                <i class="fas fa-calendar-alt"></i> Agenda dos Médicos
            </button>
            <button class="nav-item" onclick="carregar('pacientes')">
                <i class="fas fa-users"></i> Pacientes
            </button>
            <div class="user-section">
                <div class="user-avatar"><?php echo strtoupper(substr($user_name, 0, 1)); ?></div>
                <div><?php echo htmlspecialchars($user_name); ?></div>
                <small><?php echo htmlspecialchars($user_email); ?></small>
                <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Sair</a>
            </div>
        </div>
        <div class="main-content">
            <div class="main-header">
                <h1 class="page-title" id="pageTitle">Confirmar Consultas</h1>
            </div>
            <div class="content-area" id="contentArea">
                <div class="loading"><i class="fas fa-spinner fa-spin"></i><p>Carregando...</p></div>
            </div>
        </div>
    </div>

    <script>
        // ============================================================
        // FUNÇÃO PARA CARREGAR CONTEÚDO
        // ============================================================
        function carregar(section) {
            var titulos = {
                'confirmar-consultas': 'Confirmar Consultas',
                'agenda-medicos': 'Agenda dos Médicos',
                'pacientes': 'Pacientes'
            };
            document.getElementById('pageTitle').innerText = titulos[section] || section;
            
            document.querySelectorAll('.nav-item').forEach(function(el) {
                el.classList.remove('active');
            });
            document.querySelector('.nav-item[onclick="carregar(\'' + section + '\')"]').classList.add('active');
            
            var contentArea = document.getElementById('contentArea');
            contentArea.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i><p>Carregando...</p></div>';
            
            // Forçar limpar cache
            fetch('admin-sections/' + section + '.php?t=' + Date.now())
                .then(function(response) {
                    if (!response.ok) throw new Error('Erro ' + response.status);
                    return response.text();
                })
                .then(function(html) {
                    contentArea.innerHTML = html;
                    
                    // ================================================
                    // EXECUTAR O SCRIPT APÓS CARREGAR
                    // ================================================
                    var scripts = contentArea.querySelectorAll('script');
                    scripts.forEach(function(script) {
                        var newScript = document.createElement('script');
                        if (script.src) {
                            newScript.src = script.src;
                        } else {
                            newScript.textContent = script.textContent;
                        }
                        document.body.appendChild(newScript);
                    });
                    
                    // Se a seção for consultas, chamar a função
                    if (section === 'confirmar-consultas') {
                        if (typeof window.renderConsultas === 'function') {
                            window.renderConsultas();
                        } else {
                            // Tentar novamente após 500ms
                            setTimeout(function() {
                                if (typeof window.renderConsultas === 'function') {
                                    window.renderConsultas();
                                }
                            }, 500);
                        }
                    } else if (section === 'agenda-medicos') {
                        if (typeof window.renderAgenda === 'function') {
                            window.renderAgenda();
                        }
                    } else if (section === 'pacientes') {
                        if (typeof window.renderPacientes === 'function') {
                            window.renderPacientes();
                        }
                    }
                })
                .catch(function(error) {
                    contentArea.innerHTML = '<div style="padding:20px;color:red;text-align:center;">Erro: ' + error.message + '</div>';
                });
        }

        // ============================================================
        // INICIAR
        // ============================================================
        document.addEventListener('DOMContentLoaded', function() {
            carregar('confirmar-consultas');
        });
    </script>
</body>
</html>