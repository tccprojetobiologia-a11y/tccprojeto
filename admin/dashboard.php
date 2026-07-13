<?php
session_start();
if (!isset($_SESSION['logado']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../index.php');
    exit();
}

$page = $_GET['page'] ?? 'confirmar-consultas';
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
            <button class="nav-item active" onclick="loadContent('confirmar-consultas')">
                <i class="fas fa-check-circle"></i> Confirmar Consultas
            </button>
            <button class="nav-item" onclick="loadContent('agenda-medicos')">
                <i class="fas fa-calendar-alt"></i> Agenda dos Médicos
            </button>
            <button class="nav-item" onclick="loadContent('pacientes')">
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
                <div style="text-align:center;padding:50px;color:#999;">
                    <i class="fas fa-spinner fa-spin" style="font-size:30px;color:#851e32;"></i>
                    <p>Carregando...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ============================================================
        // FUNÇÃO PARA CARREGAR CONTEÚDO
        // ============================================================
        function loadContent(section) {
            var titles = {
                'confirmar-consultas': 'Confirmar Consultas',
                'agenda-medicos': 'Agenda dos Médicos',
                'pacientes': 'Pacientes'
            };
            document.getElementById('pageTitle').innerText = titles[section] || section;
            
            document.querySelectorAll('.nav-item').forEach(function(el) {
                el.classList.remove('active');
            });
            document.querySelector('.nav-item[onclick="loadContent(\'' + section + '\')"]').classList.add('active');
            
            var contentArea = document.getElementById('contentArea');
            contentArea.innerHTML = '<div style="text-align:center;padding:50px;color:#999;"><i class="fas fa-spinner fa-spin" style="font-size:30px;color:#851e32;"></i><p>Carregando...</p></div>';
            
            fetch('admin-sections/' + section + '.php')
                .then(function(response) {
                    if (!response.ok) throw new Error('Erro ' + response.status);
                    return response.text();
                })
                .then(function(html) {
                    contentArea.innerHTML = html;
                    
                    // ================================================
                    // 🔥 CHAMAR A FUNÇÃO VÁRIAS VEZES ATÉ FUNCIONAR
                    // ================================================
                    if (section === 'confirmar-consultas') {
                        // Tentar a cada 200ms por até 3 segundos
                        var tentativas = 0;
                        var maxTentativas = 15;
                        var intervalo = setInterval(function() {
                            tentativas++;
                            console.log('⏳ Tentativa ' + tentativas + ' de ' + maxTentativas);
                            
                            if (typeof window.renderConsultas === 'function') {
                                console.log('✅ renderConsultas encontrada!');
                                window.renderConsultas();
                                clearInterval(intervalo);
                            } else if (tentativas >= maxTentativas) {
                                console.error('❌ renderConsultas NÃO foi carregada');
                                contentArea.innerHTML += '<p style="color:red;text-align:center;padding:20px;">❌ Erro ao carregar. Recarregue a página.</p>';
                                clearInterval(intervalo);
                            }
                        }, 200);
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
            var page = '<?php echo $page; ?>';
            loadContent(page);
        });
    </script>
</body>
</html>