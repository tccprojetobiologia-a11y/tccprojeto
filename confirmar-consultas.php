<div style="margin-bottom:30px;">
    <h3 style="font-size:24px;color:#1e2a3a;">Consultas</h3>
    <p style="color:#64748b;">Aprove ou recuse solicitações de agendamento.</p>
</div>

<div style="display:flex;gap:12px;margin-bottom:24px;border-bottom:2px solid #f0f0f0;padding-bottom:8px;flex-wrap:wrap;">
    <button onclick="window.switchTab('Pendente')" id="tab-Pendente" style="padding:8px 20px;border:none;background:transparent;font-weight:600;color:#851e32;border-bottom:3px solid #851e32;cursor:pointer;font-size:15px;">Pendentes</button>
    <button onclick="window.switchTab('Confirmada')" id="tab-Confirmada" style="padding:8px 20px;border:none;background:transparent;font-weight:600;color:#64748b;border-bottom:3px solid transparent;cursor:pointer;font-size:15px;">Confirmadas</button>
    <button onclick="window.switchTab('Recusada')" id="tab-Recusada" style="padding:8px 20px;border:none;background:transparent;font-weight:600;color:#64748b;border-bottom:3px solid transparent;cursor:pointer;font-size:15px;">Recusadas</button>
</div>

<div id="lista-Pendente" style="display:block;"></div>
<div id="lista-Confirmada" style="display:none;"></div>
<div id="lista-Recusada" style="display:none;"></div>

<div id="modal-recusar" style="position:fixed;inset:0;background:rgba(15,23,42,0.55);display:none;align-items:center;justify-content:center;z-index:9999;">
    <div style="background:#fff;border-radius:18px;width:min(560px,90vw);padding:24px;box-shadow:0 20px 50px rgba(0,0,0,0.25);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
            <h4 style="margin:0;color:#1e2a3a;font-size:22px;">Justificativa da recusa</h4>
            <button type="button" onclick="fecharModalRecusa()" style="background:#f1f5f9;border:none;border-radius:999px;width:34px;height:34px;cursor:pointer;font-size:22px;color:#1e2a3a;">×</button>
        </div>
        <label for="motivo-recusa" style="display:block;margin-bottom:10px;font-weight:600;color:#1e2a3a;">Descreva o motivo da recusa do paciente</label>
        <textarea id="motivo-recusa" rows="5" style="width:100%;padding:12px;border:1px solid #dfe3e8;border-radius:12px;resize:vertical;font-family:inherit;font-size:14px;" placeholder="Ex.: Horário indisponível para este médico."></textarea>
        <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:18px;">
            <button type="button" onclick="fecharModalRecusa()" style="padding:10px 16px;border:none;border-radius:10px;background:#e2e8f0;color:#1e2a3a;font-weight:600;cursor:pointer;">Cancelar</button>
            <button type="button" id="confirmar-recusa-btn" style="padding:10px 16px;border:none;border-radius:10px;background:#851e32;color:white;font-weight:600;cursor:pointer;">Salvar justificativa</button>
        </div>
    </div>
</div>

<script>
    window.consultasCache = [];
    window.consultaParaRecusar = null;

    window.switchTab = function(tab) {
        document.querySelectorAll('#lista-Pendente, #lista-Confirmada, #lista-Recusada').forEach(function(el) {
            el.style.display = 'none';
        });

        var target = document.getElementById('lista-' + tab);
        if (target) target.style.display = 'block';

        document.querySelectorAll('#tab-Pendente, #tab-Confirmada, #tab-Recusada').forEach(function(btn) {
            btn.style.color = '#64748b';
            btn.style.borderBottom = '3px solid transparent';
        });

        var activeBtn = document.getElementById('tab-' + tab);
        if (activeBtn) {
            activeBtn.style.color = '#851e32';
            activeBtn.style.borderBottom = '3px solid #851e32';
        }
    };

    function normalizeStatus(status) {
        var value = String(status || '').trim();
        if (value === 'Cancelada') return 'Recusada';
        if (value === 'Realizada') return 'Confirmada';
        return value;
    }

    window.renderConsultas = function() {
        fetch('../api/admin_api.php?action=get_consultas')
            .then(function(response) {
                if (!response.ok) throw new Error('HTTP ' + response.status);
                return response.json();
            })
            .then(function(data) {
                if (data.error) throw new Error(data.error);
                window.consultasCache = Array.isArray(data) ? data : [];
                renderizarLista('Pendente');
                renderizarLista('Confirmada');
                renderizarLista('Recusada');
            })
            .catch(function(error) {
                console.error('Erro ao carregar consultas:', error);
                document.querySelectorAll('#lista-Pendente, #lista-Confirmada, #lista-Recusada').forEach(function(el) {
                    el.innerHTML = '<p style="color:red;text-align:center;padding:20px;">Erro ao carregar as consultas.</p>';
                });
            });
    };

    function renderizarLista(status) {
        var lista = window.consultasCache.filter(function(c) {
            return normalizeStatus(c.status) === status;
        });
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
            html += '<h4 style="font-size:18px;color:#1e2a3a;margin-bottom:8px;">' + (c.patient_name || c.paciente_nome || 'Paciente') + '</h4>';
            html += '<p style="color:#64748b;font-size:14px;margin-bottom:6px;"><i class="fas fa-user-md" style="color:#6366f1;"></i> ' + (c.nome_medico || c.medico || 'Médico') + ' (' + (c.especialidade || 'Consulta') + ')</p>';
            html += '<p style="color:#1e2a3a;font-size:14px;font-weight:500;"><i class="far fa-calendar-alt" style="color:#6366f1;"></i> ' + formatarData(c.date || c.data_consulta) + ' às ' + (c.time || c.hora_consulta || '09:00') + '</p>';

            if (status === 'Pendente') {
                html += '<div style="display:flex;gap:12px;margin-top:16px;border-top:1px solid #f0f0f0;padding-top:16px;">';
                html += '<button onclick="window.aprovarConsulta(\'' + (c.id || c.id_consulta) + '\')" style="flex:1;background:#851e32;color:white;border:none;padding:12px;border-radius:10px;cursor:pointer;font-weight:600;">Aceitar</button>';
                html += '<button onclick="window.recusarConsulta(\'' + (c.id || c.id_consulta) + '\')" style="flex:1;background:transparent;color:#ef4444;border:1px solid #ef4444;padding:12px;border-radius:10px;cursor:pointer;font-weight:600;">Recusar</button>';
                html += '</div>';
            }

            if (c.mensagem_recusa) {
                html += '<div style="margin-top:10px;padding:10px;background:#f8fafc;border-radius:8px;font-size:13px;"><strong>Justificativa:</strong> ' + c.mensagem_recusa + '</div>';
            }

            if (status === 'Recusada' && !c.mensagem_recusa) {
                html += '<div style="margin-top:10px;padding:10px;background:#f8fafc;border-radius:8px;font-size:13px;"><strong>Justificativa:</strong> Não informada.</div>';
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
                alert('Consulta confirmada com sucesso.');
                window.renderConsultas();
            } else {
                alert(result.message || 'Não foi possível confirmar.');
            }
        })
        .catch(function(error) {
            alert('Erro: ' + error.message);
        });
    };

    window.fecharModalRecusa = function() {
        var modal = document.getElementById('modal-recusar');
        var textarea = document.getElementById('motivo-recusa');
        if (modal) modal.style.display = 'none';
        if (textarea) textarea.value = '';
        window.consultaParaRecusar = null;
    };

    function confirmarRecusa() {
        var mensagem = document.getElementById('motivo-recusa')?.value || '';
        if (!mensagem.trim()) {
            alert('É necessário informar a justificativa da recusa.');
            return;
        }
        if (!window.consultaParaRecusar) {
            alert('Consulta não selecionada.');
            return;
        }

        fetch('../api/admin_api.php?action=recusar_consulta', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: window.consultaParaRecusar, mensagem: mensagem.trim() })
        })
        .then(function(response) { return response.json(); })
        .then(function(result) {
            if (result.success) {
                alert('Consulta recusada com justificativa registrada.');
                window.fecharModalRecusa();
                window.renderConsultas();
            } else {
                alert(result.message || 'Não foi possível recusar.');
            }
        })
        .catch(function(error) {
            alert('Erro: ' + error.message);
        });
    }

    window.recusarConsulta = function(id) {
        window.consultaParaRecusar = id;
        var modal = document.getElementById('modal-recusar');
        var textarea = document.getElementById('motivo-recusa');
        if (modal) modal.style.display = 'flex';
        if (textarea) {
            textarea.value = 'Horário indisponível para este médico.';
            textarea.focus();
        }
    };

    var confirmarRecusaBtn = document.getElementById('confirmar-recusa-btn');
    if (confirmarRecusaBtn) {
        confirmarRecusaBtn.addEventListener('click', confirmarRecusa);
    }

    if (typeof window.renderConsultas === 'function') {
        window.renderConsultas();
    }
</script>