<div style="margin-bottom:30px;">
    <h3 style="font-size:24px;color:#1e2a3a;">Gerenciar Consultas</h3>
    <p style="color:#64748b;">Aprove ou recuse solicitações.</p>
</div>

<div style="display:flex;gap:12px;margin-bottom:24px;border-bottom:2px solid #f0f0f0;padding-bottom:8px;">
    <button onclick="window.switchTab('Pendente')" id="tab-Pendente" style="padding:8px 20px;border:none;background:transparent;font-weight:600;color:#851e32;border-bottom:3px solid #851e32;cursor:pointer;font-size:15px;">Pendentes</button>
    <button onclick="window.switchTab('Confirmada')" id="tab-Confirmada" style="padding:8px 20px;border:none;background:transparent;font-weight:600;color:#64748b;border-bottom:3px solid transparent;cursor:pointer;font-size:15px;">Confirmadas</button>
    <button onclick="window.switchTab('Recusada')" id="tab-Recusada" style="padding:8px 20px;border:none;background:transparent;font-weight:600;color:#64748b;border-bottom:3px solid transparent;cursor:pointer;font-size:15px;">Recusadas</button>
</div>

<div id="lista-Pendente" style="display:block;"></div>
<div id="lista-Confirmada" style="display:none;"></div>
<div id="lista-Recusada" style="display:none;"></div>

<script>
    // ============================================================
    // FUNÇÕES GLOBAIS (window.)
    // ============================================================
    window.consultasCache = [];

    window.switchTab = function(tab) {
        document.querySelectorAll('#lista-Pendente, #lista-Confirmada, #lista-Recusada').forEach(function(el) {
            el.style.display = 'none';
        });
        document.getElementById('lista-' + tab).style.display = 'block';
        
        document.querySelectorAll('#tab-Pendente, #tab-Confirmada, #tab-Recusada').forEach(function(btn) {
            btn.style.color = '#64748b';
            btn.style.borderBottom = '3px solid transparent';
        });
        document.getElementById('tab-' + tab).style.color = '#851e32';
        document.getElementById('tab-' + tab).style.borderBottom = '3px solid #851e32';
    };

    window.renderConsultas = function() {
        console.log('🔄 Carregando consultas da API...');
        
        fetch('../api/admin_api.php?action=get_consultas')
            .then(function(response) { 
                if (!response.ok) throw new Error('HTTP ' + response.status);
                return response.json(); 
            })
            .then(function(data) {
                if (data.error) throw new Error(data.error);
                window.consultasCache = data;
                console.log('✅ Dados carregados:', data.length + ' consultas');
                
                renderizarLista('Pendente');
                renderizarLista('Confirmada');
                renderizarLista('Recusada');
            })
            .catch(function(error) {
                console.error('❌ Erro:', error);
                document.querySelectorAll('#lista-Pendente, #lista-Confirmada, #lista-Recusada').forEach(function(el) {
                    el.innerHTML = '<p style="color:red;text-align:center;padding:20px;">❌ Erro: ' + error.message + '</p>';
                });
            });
    };

    function renderizarLista(status) {
        var lista = window.consultasCache.filter(function(c) { return c.status === status; });
        var container = document.getElementById('lista-' + status);
        if (!container) return;
        
        if (lista.length === 0) {
            var msg = status === 'Pendente' ? 'pendente' : status === 'Confirmada' ? 'confirmada' : 'recusada';
            container.innerHTML = '<p style="color:#94a3b8;text-align:center;padding:40px 0;">Nenhuma consulta ' + msg + '.</p>';
            return;
        }
        
        var html = '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:24px;">';
        lista.forEach(function(c) {
            var cor = status === 'Pendente' ? '#fbbf24' : status === 'Confirmada' ? '#4ade80' : '#f87171';
            var fundo = status === 'Pendente' ? 'rgba(245,158,11,0.15)' : status === 'Confirmada' ? 'rgba(34,197,94,0.15)' : 'rgba(239,68,68,0.15)';
            var label = status === 'Pendente' ? 'Aguardando' : status === 'Confirmada' ? 'Confirmada' : 'Recusada';
            
            html += '<div style="background:white;border-radius:16px;padding:24px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">';
            html += '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">';
            html += '<span style="background:' + fundo + ';color:' + cor + ';padding:4px 12px;border-radius:20px;font-weight:600;font-size:12px;">' + label + '</span>';
            html += '</div>';
            html += '<h4 style="font-size:18px;color:#1e2a3a;margin-bottom:8px;">' + (c.paciente_nome || 'Paciente') + '</h4>';
            html += '<p style="color:#64748b;font-size:14px;margin-bottom:6px;"><i class="fas fa-user-md" style="color:#6366f1;"></i> ' + c.nome_medico + ' (' + c.especialidade + ')</p>';
            html += '<p style="color:#1e2a3a;font-size:14px;font-weight:500;"><i class="far fa-calendar-alt" style="color:#6366f1;"></i> ' + formatarData(c.data_consulta) + ' às ' + c.hora_consulta + '</p>';
            
            if (status === 'Pendente') {
                html += '<div style="display:flex;gap:12px;margin-top:16px;border-top:1px solid #f0f0f0;padding-top:16px;">';
                html += '<button onclick="window.aprovarConsulta(' + c.id_consulta + ')" style="flex:1;background:#851e32;color:white;border:none;padding:12px;border-radius:10px;cursor:pointer;font-weight:600;">Aceitar</button>';
                html += '<button onclick="window.recusarConsulta(' + c.id_consulta + ')" style="flex:1;background:transparent;color:#ef4444;border:1px solid #ef4444;padding:12px;border-radius:10px;cursor:pointer;font-weight:600;">Cancelar</button>';
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

    window.aprovarConsulta = function(id) {
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
                window.renderConsultas();

                // after confirming, open the agenda view and highlight the appointment
                (async function() {
                    try {
                        // fetch all consultas to find the confirmed one
                        const resp = await fetch('../api/admin_api.php?action=get_consultas');
                        const all = await resp.json();
                        const found = all.find(function(x){ return String(x.id_consulta || x.id) === String(id); });
                        if (!found) return;
                        const doctorName = found.medico || found.nome_medico || '';
                        const date = found.data_consulta || found.date || '';

                        // load agenda section
                        if (typeof carregar === 'function') carregar('agenda-medicos');

                        // wait for the agenda to render and set selection
                        const waitFor = (selector, timeout = 3000) => new Promise((resolve) => {
                            const start = Date.now();
                            const iv = setInterval(() => {
                                const el = document.querySelector(selector);
                                if (el) { clearInterval(iv); resolve(el); }
                                if (Date.now() - start > timeout) { clearInterval(iv); resolve(null); }
                            }, 200);
                        });

                        const select = await waitFor('#doctorSelect', 4000);
                        if (!select) return;
                        // choose option by name if possible
                        let opt = Array.from(select.options).find(o => (o.textContent || '').includes(doctorName));
                        if (!opt) opt = select.options[0];
                        select.value = opt.value;
                        select.dispatchEvent(new Event('change'));

                        // wait a bit then open modal for the day
                        setTimeout(function(){ if (typeof abrirModal === 'function') abrirModal(doctorName, date); }, 700);
                    } catch (err) {
                        console.warn('Não foi possível abrir agenda automaticamente:', err);
                    }
                })();
            } else {
                alert('❌ ' + result.error);
            }
        })
        .catch(function(error) {
            alert('Erro: ' + error.message);
        });
    };

    window.recusarConsulta = function(id) {
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
                window.renderConsultas();
            } else {
                alert('❌ ' + result.error);
            }
        })
        .catch(function(error) {
            alert('Erro: ' + error.message);
        });
    };

    // ============================================================
    // 🔥 EXECUTAR IMEDIATAMENTE
    // ============================================================
    console.log('✅ confirmar-consultas.php carregado!');
    
    // Se a função não foi chamada pelo dashboard, chama aqui
    if (typeof window.renderConsultas === 'function') {
        window.renderConsultas();
    }
</script>