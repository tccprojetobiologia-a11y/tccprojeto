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
        .card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .hidden { display: none !important; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px; }
        .consulta-card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .btn-aceitar { background: #851e32; color: white; border: none; padding: 12px; border-radius: 10px; cursor: pointer; font-weight: 600; flex: 1; }
        .btn-aceitar:hover { background: #6a182c; }
        .btn-recusar { background: transparent; color: #ef4444; border: 1px solid #ef4444; padding: 12px; border-radius: 10px; cursor: pointer; font-weight: 600; flex: 1; }
        .btn-recusar:hover { background: #fee; }
        .tab-container { display: flex; gap: 12px; margin-bottom: 24px; border-bottom: 2px solid #f0f0f0; padding-bottom: 8px; }
        .tab-btn { padding: 8px 20px; border: none; background: transparent; font-weight: 600; color: #64748b; border-bottom: 3px solid transparent; cursor: pointer; font-size: 15px; }
        .tab-btn.active { color: #851e32; border-bottom: 3px solid #851e32; }
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
            <button class="nav-item active" onclick="mostrarSecao('consultas')">
                <i class="fas fa-check-circle"></i> Confirmar Consultas
            </button>
            <button class="nav-item" onclick="mostrarSecao('agenda')">
                <i class="fas fa-calendar-alt"></i> Agenda dos Médicos
            </button>
            <button class="nav-item" onclick="mostrarSecao('pacientes')">
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
                <!-- ========== SEÇÃO CONSULTAS ========== -->
                <div id="sec-consultas">
                    <div style="margin-bottom:30px;">
                        <h3 style="font-size:24px;color:#1e2a3a;">Gerenciar Consultas</h3>
                        <p style="color:#64748b;">Aprove ou recuse solicitações.</p>
                    </div>

                    <div class="tab-container">
                        <button class="tab-btn active" onclick="trocarAba('Pendente')" id="tabPendente">Pendentes</button>
                        <button class="tab-btn" onclick="trocarAba('Confirmada')" id="tabConfirmada">Confirmadas</button>
                        <button class="tab-btn" onclick="trocarAba('Recusada')" id="tabRecusada">Recusadas</button>
                    </div>

                    <div id="listaPendente"><div class="loading"><i class="fas fa-spinner fa-spin"></i><p>Carregando...</p></div></div>
                    <div id="listaConfirmada" class="hidden"></div>
                    <div id="listaRecusada" class="hidden"></div>
                </div>

                <!-- ========== SEÇÃO AGENDA ========== -->
                <div id="sec-agenda" class="hidden">
                    <div style="margin-bottom:30px;">
                        <h3 style="font-size:24px;color:#1e2a3a;">Agenda dos Médicos</h3>
                        <p style="color:#64748b;">Visualize as consultas confirmadas.</p>
                    </div>
                    <div id="agendaContent"><div class="loading"><i class="fas fa-spinner fa-spin"></i><p>Carregando...</p></div></div>
                </div>

                <!-- ========== SEÇÃO PACIENTES ========== -->
                <div id="sec-pacientes" class="hidden">
                    <div style="margin-bottom:30px;">
                        <h3 style="font-size:24px;color:#1e2a3a;">Pacientes</h3>
                        <p style="color:#64748b;">Visualize os pacientes cadastrados.</p>
                    </div>
                    <div id="pacientesContent"><div class="loading"><i class="fas fa-spinner fa-spin"></i><p>Carregando...</p></div></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ============================================================
        // TODAS AS FUNÇÕES EM UM ÚNICO LUGAR
        // ============================================================
        var consultasCache = [];
        var abaAtual = 'Pendente';

        // ============================================================
        // NAVEGAÇÃO ENTRE SEÇÕES
        // ============================================================
        function mostrarSecao(secao) {
            document.getElementById('sec-consultas').classList.add('hidden');
            document.getElementById('sec-agenda').classList.add('hidden');
            document.getElementById('sec-pacientes').classList.add('hidden');
            document.getElementById('sec-' + secao).classList.remove('hidden');

            document.querySelectorAll('.nav-item').forEach(function(el) {
                el.classList.remove('active');
            });
            document.querySelector('.nav-item[onclick="mostrarSecao(\'' + secao + '\')"]').classList.add('active');

            var titulos = {
                'consultas': 'Confirmar Consultas',
                'agenda': 'Agenda dos Médicos',
                'pacientes': 'Pacientes'
            };
            document.getElementById('pageTitle').innerText = titulos[secao] || secao;

            if (secao === 'consultas') {
                renderConsultas();
            } else if (secao === 'agenda') {
                renderAgenda();
            } else if (secao === 'pacientes') {
                renderPacientes();
            }
        }

        // ============================================================
        // CONSULTAS
        // ============================================================
        function trocarAba(aba) {
            abaAtual = aba;
            document.getElementById('listaPendente').classList.add('hidden');
            document.getElementById('listaConfirmada').classList.add('hidden');
            document.getElementById('listaRecusada').classList.add('hidden');
            document.getElementById('lista' + aba).classList.remove('hidden');

            document.querySelectorAll('.tab-btn').forEach(function(el) {
                el.classList.remove('active');
            });
            document.getElementById('tab' + aba).classList.add('active');
        }

        function renderConsultas() {
            console.log('🔄 Carregando consultas da API...');
            document.getElementById('listaPendente').innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i><p>Carregando...</p></div>';

            fetch('../api/admin_api.php?action=get_consultas')
                .then(function(response) {
                    if (!response.ok) throw new Error('HTTP ' + response.status);
                    return response.json();
                })
                .then(function(data) {
                    if (data.error) throw new Error(data.error);
                    consultasCache = data;
                    console.log('✅ Dados carregados:', data.length + ' consultas');

                    renderizarLista('Pendente');
                    renderizarLista('Confirmada');
                    renderizarLista('Recusada');
                })
                .catch(function(error) {
                    console.error('❌ Erro:', error);
                    document.querySelectorAll('#listaPendente, #listaConfirmada, #listaRecusada').forEach(function(el) {
                        el.innerHTML = '<p style="color:red;text-align:center;padding:20px;">❌ Erro: ' + error.message + '</p>';
                    });
                });
        }

        function renderizarLista(status) {
            var lista = consultasCache.filter(function(c) { return c.status === status; });
            var container = document.getElementById('lista' + status);
            if (!container) return;

            if (lista.length === 0) {
                var msg = status === 'Pendente' ? 'pendente' : status === 'Confirmada' ? 'confirmada' : 'recusada';
                container.innerHTML = '<p style="color:#94a3b8;text-align:center;padding:40px 0;">Nenhuma consulta ' + msg + '.</p>';
                return;
            }

            var html = '<div class="grid">';
            lista.forEach(function(c) {
                var cor = status === 'Pendente' ? '#fbbf24' : status === 'Confirmada' ? '#4ade80' : '#f87171';
                var fundo = status === 'Pendente' ? 'rgba(245,158,11,0.15)' : status === 'Confirmada' ? 'rgba(34,197,94,0.15)' : 'rgba(239,68,68,0.15)';
                var label = status === 'Pendente' ? 'Aguardando' : status === 'Confirmada' ? 'Confirmada' : 'Recusada';

                html += '<div class="consulta-card">';
                html += '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">';
                html += '<span style="background:' + fundo + ';color:' + cor + ';padding:4px 12px;border-radius:20px;font-weight:600;font-size:12px;">' + label + '</span>';
                html += '</div>';
                html += '<h4 style="font-size:18px;color:#1e2a3a;margin-bottom:8px;">' + (c.paciente_nome || 'Paciente') + '</h4>';
                html += '<p style="color:#64748b;font-size:14px;margin-bottom:6px;"><i class="fas fa-user-md" style="color:#6366f1;"></i> ' + c.nome_medico + ' (' + c.especialidade + ')</p>';
                html += '<p style="color:#1e2a3a;font-size:14px;font-weight:500;"><i class="far fa-calendar-alt" style="color:#6366f1;"></i> ' + formatarData(c.data_consulta) + ' às ' + c.hora_consulta + '</p>';

                if (status === 'Pendente') {
                    html += '<div style="display:flex;gap:12px;margin-top:16px;border-top:1px solid #f0f0f0;padding-top:16px;">';
                    html += '<button class="btn-aceitar" onclick="aprovarConsulta(' + c.id_consulta + ')">Aceitar</button>';
                    html += '<button class="btn-recusar" onclick="recusarConsulta(' + c.id_consulta + ')">Cancelar</button>';
                    html += '</div>';
                }

                if (c.mensagem_recusa) {
                    html += '<div style="margin-top:10px;padding:10px;background:#f8fafc;border-radius:8px;font-size:13px;"><strong>Mensagem:</strong> ' + c.mensagem_recusa + '</div>';
                }

                html += '</div>';
            });
            html += '</div>';
            container.innerHTML = html;
        }

        function formatarData(data) {
            if (!data) return 'N/A';
            var d = new Date(data + 'T00:00:00');
            return d.toLocaleDateString('pt-BR');
        }

        function aprovarConsulta(id) {
            if (!confirm('Confirmar esta consulta?')) return;

            fetch('../api/admin_api.php?action=aprovar_consulta', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            })
            .then(function(response) { return response.json(); })
            .then(function(result) {
                if (result.success) {
                    alert('✓ Consulta confirmada!');
                    renderConsultas();
                } else {
                    alert('❌ ' + result.error);
                }
            })
            .catch(function(error) {
                alert('Erro: ' + error.message);
            });
        }

        function recusarConsulta(id) {
            var mensagem = prompt('Digite a mensagem para o paciente:', 'Horário indisponível.');
            if (mensagem === null) return;

            fetch('../api/admin_api.php?action=recusar_consulta', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id, mensagem: mensagem })
            })
            .then(function(response) { return response.json(); })
            .then(function(result) {
                if (result.success) {
                    alert('✉️ Mensagem enviada!');
                    renderConsultas();
                } else {
                    alert('❌ ' + result.error);
                }
            })
            .catch(function(error) {
                alert('Erro: ' + error.message);
            });
        }

        // ============================================================
        // AGENDA (SIMPLIFICADA)
        // ============================================================
        function renderAgenda() {
            var container = document.getElementById('agendaContent');
            container.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i><p>Carregando agenda...</p></div>';

            var medico = 'Dr. Roberto Mendes';
            var mes = new Date().getMonth() + 1;
            var ano = new Date().getFullYear();

            fetch('../api/admin_api.php?action=get_agenda&medico=' + encodeURIComponent(medico) + '&mes=' + mes + '&ano=' + ano)
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if (data.error) throw new Error(data.error);
                    var html = '<div class="card"><h4>Agenda - ' + medico + '</h4>';
                    if (data.length === 0) {
                        html += '<p style="color:#94a3b8;text-align:center;padding:20px;">Nenhuma consulta agendada.</p>';
                    } else {
                        html += '<div class="grid">';
                        data.forEach(function(c) {
                            html += '<div class="consulta-card" style="border-left:4px solid #851e32;">';
                            html += '<h4>' + (c.paciente_nome || 'Paciente') + '</h4>';
                            html += '<p>' + c.especialidade + '</p>';
                            html += '<p><strong>' + c.hora_consulta + '</strong> - ' + formatarData(c.data_consulta) + '</p>';
                            html += '</div>';
                        });
                        html += '</div>';
                    }
                    html += '</div>';
                    container.innerHTML = html;
                })
                .catch(function(error) {
                    container.innerHTML = '<p style="color:red;text-align:center;padding:20px;">Erro: ' + error.message + '</p>';
                });
        }

        // ============================================================
        // PACIENTES (SIMPLIFICADA)
        // ============================================================
        function renderPacientes() {
            var container = document.getElementById('pacientesContent');
            container.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i><p>Carregando pacientes...</p></div>';

            fetch('../api/admin_api.php?action=get_pacientes')
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if (data.error) throw new Error(data.error);
                    var html = '<div class="card"><h4>Lista de Pacientes</h4>';
                    if (data.length === 0) {
                        html += '<p style="color:#94a3b8;text-align:center;padding:20px;">Nenhum paciente cadastrado.</p>';
                    } else {
                        html += '<div class="grid">';
                        data.forEach(function(p) {
                            html += '<div class="consulta-card" style="border-left:4px solid #6366f1;">';
                            html += '<h4>' + (p.nome_criptografado || 'Sem nome') + '</h4>';
                            html += '<p>' + (p.email || 'Sem email') + '</p>';
                            html += '<p>Idade: ' + (p.idade || '?') + ' anos | ' + (p.total_consultas || 0) + ' consultas</p>';
                            html += '</div>';
                        });
                        html += '</div>';
                    }
                    html += '</div>';
                    container.innerHTML = html;
                })
                .catch(function(error) {
                    container.innerHTML = '<p style="color:red;text-align:center;padding:20px;">Erro: ' + error.message + '</p>';
                });
        }

        // ============================================================
        // INICIAR
        // ============================================================
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Iniciando painel admin...');
            // Forçar a seção de consultas
            mostrarSecao('consultas');
        });
    </script>
</body>
</html>