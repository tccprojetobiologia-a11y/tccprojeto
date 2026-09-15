<div style="margin-bottom: 30px;">
    <h3 style="font-size: 24px; font-weight: 600; color: #1e2a3a;">Agenda dos Médicos</h3>
    <p style="color: #64748b; font-size: 14px; margin-top: 4px;">Clique em qualquer dia para ver todos os horários e saber quais têm paciente agendado ou estão disponíveis.</p>
</div>

<div style="display: flex; gap: 16px; margin-bottom: 20px; align-items: center; flex-wrap: wrap;">
    <button onclick="mudarMes(-1)" style="background: #851e32; color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer;">◀</button>
    <span id="mesAtual" style="font-size: 18px; font-weight: 600; color: #1e2a3a; min-width: 150px; text-align: center;"></span>
    <button onclick="mudarMes(1)" style="background: #851e32; color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer;">▶</button>
</div>

<div style="display: flex; flex-direction: column; gap: 32px;">
    <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <h4 style="color: #1e2a3a; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-user-md" style="color: #6366f1;"></i> Dr. Roberto Mendes - Cardiologia
        </h4>
        <div id="calendario-roberto" style="overflow-x: auto;"></div>
    </div>
    <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <h4 style="color: #1e2a3a; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-user-md" style="color: #f87171;"></i> Dra. Aline Costa - Arritmologia
        </h4>
        <div id="calendario-aline" style="overflow-x: auto;"></div>
    </div>
</div>

<div id="modal-dia" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 16px; padding: 30px; max-width: 600px; width: 90%; max-height: 82%; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 id="modal-titulo" style="color: #1e2a3a; font-size: 20px;">Consultas do Dia</h3>
            <button onclick="fecharModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #64748b;">&times;</button>
        </div>
        <div id="modal-conteudo"></div>
    </div>
</div>

<script>
    let mesAtual = new Date().getMonth();
    let anoAtual = new Date().getFullYear();
    let agendaCache = {};
    window.currentAgendaContext = { medico: '', data: '' };

    window.renderAgenda = async function() {
        const medico1 = 'Dr. Roberto Mendes';
        const medico2 = 'Dra. Aline Costa';

        document.getElementById('mesAtual').textContent = getNomeMes(mesAtual) + ' ' + anoAtual;

        await carregarAgendaMedico(medico1, 'calendario-roberto');
        await carregarAgendaMedico(medico2, 'calendario-aline');
    };

    async function carregarAgendaMedico(medicoNome, containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;

        try {
            const response = await fetch('../api/admin_api.php?action=get_agenda');
            const consultas = await response.json();
            const filtro = Array.isArray(consultas) ? consultas.filter(function(item) {
                return (item.medico || '') === medicoNome;
            }) : [];

            agendaCache[medicoNome] = filtro;
            renderCalendario(medicoNome, containerId, filtro);
        } catch (error) {
            console.error('Erro ao carregar agenda:', error);
            container.innerHTML = '<p style="color: #c00; text-align: center; padding: 20px;">Erro ao carregar agenda.</p>';
        }
    }

    function renderCalendario(medicoNome, containerId, consultas) {
        const container = document.getElementById(containerId);
        if (!container) return;
        const dayNames = ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'];
        const year = anoAtual;
        const month = mesAtual;
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        let html = '<div style="display:grid; grid-template-columns: repeat(7, 1fr); gap:8px;">';
        // headers
        dayNames.forEach(function(d) {
            html += `<div style="padding:8px; text-align:center; background:#f8fafc; border-radius:10px; font-weight:700;">${d}</div>`;
        });

        // empty cells
        for (let i = 0; i < firstDay; i++) {
            html += `<div style="padding:12px; min-height:80px; background:transparent;"></div>`;
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const dataStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const consultasDia = consultas.filter(function(c) { return (c.data_consulta || '') === dataStr; });
            const temConsulta = consultasDia.length > 0;
            const dotColor = temConsulta ? (consultasDia.every(a => a.status === 'Realizada') ? '#22c55e' : '#ef4444') : 'transparent';

            if (temConsulta) {
                html += `<div style="padding:12px; min-height:80px; background:#fff7f7; border-radius:10px; cursor:pointer; border:1px solid #fee2e2;" onclick="abrirModal('${medicoNome}','${dataStr}')">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;"><strong>${day}</strong><span style="font-size:11px;color:#64748b;">${consultasDia.length} consulta(s)</span></div>
                    <div style="flex:1;"></div>
                    <div style="display:flex; justify-content:center;"><span style="width:10px;height:10px;border-radius:50%;background:${dotColor};"></span></div>
                </div>`;
            } else {
                html += `<div style="padding:12px; min-height:80px; background:transparent; border-radius:10px; cursor:pointer; border:1px dashed transparent;" onclick="abrirAgendamento('${medicoNome}','${dataStr}')">
                    <div><strong>${day}</strong></div>
                </div>`;
            }
        }

        html += '</div>';
        container.innerHTML = html;
    }

    window.abrirModal = async function(medico, data) {
        window.currentAgendaContext = { medico: medico, data: data };

        try {
            const response = await fetch(`../api/admin_api.php?action=get_agenda_detalhes&medico=${encodeURIComponent(medico)}&data=${data}`);
            const slots = await response.json();

            if (slots && slots.error) {
                document.getElementById('modal-conteudo').innerHTML = '<p style="color: #c00;">' + slots.error + '</p>';
                document.getElementById('modal-dia').style.display = 'flex';
                return;
            }

            document.getElementById('modal-titulo').textContent = medico + ' - ' + formatarData(data);

            let html = '<div style="display:flex; flex-direction:column; gap:12px;">';
            if (!Array.isArray(slots) || slots.length === 0) {
                html += '<p style="color: #94a3b8; text-align: center; padding: 20px;">Nenhum horário registrado neste dia.</p>';
            } else {
                slots.forEach(function(slot) {
                    const horario = slot.hora || '00:00';
                    if (slot.ocupado) {
                        html += '<div class="slot-item" style="background:#fff7ed; border:1px solid #fed7aa; border-radius:12px; padding:16px;">';
                        html += '<div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:8px;">';
                        html += '<strong style="font-size:16px; color:#1e2a3a;">' + horario + '</strong>';
                        html += '<span style="background:#851e32; color:white; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600;">' + (slot.status || 'Agendado') + '</span>';
                        html += '</div>';
                        html += '<div style="color:#1e2a3a; font-weight:600; margin-bottom:4px;">Paciente: ' + (slot.paciente_nome || 'Paciente') + '</div>';
                        html += '<div style="color:#64748b; font-size:13px; margin-bottom:12px;">Observação: ' + (slot.symptoms || 'Sem observações.') + '</div>';
                        html += '<div style="display:flex; gap:10px; flex-wrap:wrap;">';
                        html += '<button type="button" data-agenda-action="Concluída" data-appointment-id="' + (slot.appointment_id || '') + '" style="padding:10px 14px; background:#16a34a; color:white; border:none; border-radius:10px; cursor:pointer; font-weight:600;">Consulta concluída</button>';
                        html += '<button type="button" data-agenda-action="Não concluída" data-appointment-id="' + (slot.appointment_id || '') + '" style="padding:10px 14px; background:#ef4444; color:white; border:none; border-radius:10px; cursor:pointer; font-weight:600;">Não concluída</button>';
                        html += '</div>';
                        html += '<div class="motivo-box" style="margin-top:12px; display:none;">';
                        html += '<label style="display:block; margin-bottom:8px; font-weight:600; color:#1e2a3a;">Motivo da não conclusão</label>';
                        html += '<textarea rows="3" style="width:100%; border:1px solid #dfe3e8; border-radius:10px; padding:12px; resize:vertical; font-family:inherit;"></textarea>';
                        html += '</div>';
                        html += '</div>';
                    } else {
                        html += '<div style="padding:16px; border:1px solid #e2e8f0; background:#f8fafc; border-radius:12px; display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">';
                        html += '<strong style="font-size:16px; color:#1e2a3a;">' + horario + '</strong>';
                        html += '<span style="background:#e2e8f0; color:#475569; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600;">Sem paciente</span>';
                        html += '</div>';
                    }
                });
            }

            html += '</div>';
            document.getElementById('modal-conteudo').innerHTML = html;
            document.getElementById('modal-dia').style.display = 'flex';
        } catch (error) {
            console.error('Erro ao carregar detalhes:', error);
            alert('Erro ao carregar detalhes da agenda.');
        }
    };

    // Abrir formulário de agendamento rápido ao clicar em dia vazio
    window.abrirAgendamento = function(medico, data) {
        window.currentAgendaContext = { medico: medico, data: data };
        document.getElementById('modal-titulo').textContent = 'Agendar para ' + medico + ' • ' + formatarData(data);
        const html = `
            <div style="display:flex; flex-direction:column; gap:12px;">
                <label>Nome do paciente</label>
                <input type="text" id="agend-paciente-nome" style="padding:10px; border:1px solid #e5e7eb; border-radius:8px;">
                <label>Idade</label>
                <input type="number" id="agend-paciente-idade" style="padding:10px; border:1px solid #e5e7eb; border-radius:8px;">
                <label>CPF</label>
                <input type="text" id="agend-paciente-cpf" style="padding:10px; border:1px solid #e5e7eb; border-radius:8px;">
                <label>Telefone</label>
                <input type="text" id="agend-paciente-telefone" style="padding:10px; border:1px solid #e5e7eb; border-radius:8px;">
                <label>Horário</label>
                <input type="time" id="agend-paciente-hora" value="09:00" style="padding:10px; border:1px solid #e5e7eb; border-radius:8px;">
                <label>Tipo</label>
                <select id="agend-tipo" style="padding:10px; border:1px solid #e5e7eb; border-radius:8px;"><option value="Consulta">Consulta</option><option value="Reunião">Reunião</option></select>
                <label>Observações</label>
                <textarea id="agend-observacoes" rows="3" style="padding:10px; border:1px solid #e5e7eb; border-radius:8px;"></textarea>
                <div style="display:flex; gap:8px; justify-content:flex-end;">
                    <button type="button" onclick="fecharModal()" style="padding:10px 14px; background:#e2e8f0; border:none; border-radius:8px;">Cancelar</button>
                    <button type="button" onclick="submitAgendamento()" style="padding:10px 14px; background:#851e32; color:white; border:none; border-radius:8px;">Agendar</button>
                </div>
            </div>
        `;
        document.getElementById('modal-conteudo').innerHTML = html;
        document.getElementById('modal-dia').style.display = 'flex';
    };

    async function submitAgendamento() {
        const ctx = window.currentAgendaContext || {};
        const medico = ctx.medico;
        const data = ctx.data;
        const nome = document.getElementById('agend-paciente-nome')?.value || '';
        const idade = document.getElementById('agend-paciente-idade')?.value || '';
        const cpf = document.getElementById('agend-paciente-cpf')?.value || '';
        const tel = document.getElementById('agend-paciente-telefone')?.value || '';
        const hora = document.getElementById('agend-paciente-hora')?.value || '09:00';
        const tipo = document.getElementById('agend-tipo')?.value || 'Consulta';
        const obs = document.getElementById('agend-observacoes')?.value || '';

        if (!medico || !data || !nome) {
            alert('Preencha pelo menos o nome do paciente.');
            return;
        }

        const payload = {
            id_paciente: '',
            nome_paciente: nome,
            medico: medico,
            especialidade: tipo,
            data: data,
            hora: hora,
            observacoes: obs
        };

        try {
            const res = await fetch('../api/solicitar_consulta.php', {
                method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (result && result.error) {
                alert('Erro: ' + result.error);
                return;
            }

            // Sincronizar com auth_storage local para refletir no painel do médico
            await fetch('../admin/sync_appointment.php', {
                method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({
                    doctor_name: medico,
                    patient_name: nome,
                    patient_age: idade,
                    cpf: cpf,
                    phone: tel,
                    date: data,
                    time: hora,
                    status: 'Confirmada',
                    symptoms: obs
                })
            });

            alert('Agendamento criado com sucesso.');
            if (window.currentAgendaContext && window.currentAgendaContext.medico && window.currentAgendaContext.data) {
                window.abrirModal(window.currentAgendaContext.medico, window.currentAgendaContext.data);
            }
            window.renderAgenda();
        } catch (error) {
            console.error('Erro ao criar agendamento:', error);
            alert('Erro ao criar agendamento. Veja console.');
        }
    }

    document.addEventListener('click', async function(event) {
        const actionButton = event.target.closest('[data-agenda-action]');
        if (!actionButton) return;

        const appointmentId = actionButton.getAttribute('data-appointment-id');
        const action = actionButton.getAttribute('data-agenda-action');
        if (!appointmentId) {
            alert('Consulta não localizada para atualizar.');
            return;
        }

        let motivo = '';
        const slotBox = actionButton.closest('.slot-item');
        const motivoBox = slotBox ? slotBox.querySelector('.motivo-box') : null;
        const textarea = motivoBox ? motivoBox.querySelector('textarea') : null;

        if (action === 'Não concluída') {
            if (!textarea) {
                alert('Não foi possível localizar o motivo da não conclusão.');
                return;
            }
            if (!textarea.value.trim()) {
                if (motivoBox) {
                    motivoBox.style.display = 'block';
                }
                alert('Descreva o motivo da não conclusão da consulta.');
                return;
            }
            motivo = textarea.value.trim();
        }

        const payload = {
            action: 'atualizar_status_consulta',
            id: appointmentId,
            status: action,
            motivo: motivo
        };

        try {
            const response = await fetch('../api/admin_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const result = await response.json();

            if (result && result.success) {
                alert('Status da consulta atualizado com sucesso.');
                if (window.currentAgendaContext && window.currentAgendaContext.medico && window.currentAgendaContext.data) {
                    window.abrirModal(window.currentAgendaContext.medico, window.currentAgendaContext.data);
                }
            } else {
                alert((result && result.message) || 'Não foi possível atualizar a consulta.');
            }
        } catch (error) {
            alert('Erro ao atualizar a consulta: ' + error.message);
        }
    });

    window.fecharModal = function() {
        document.getElementById('modal-dia').style.display = 'none';
    };

    function mudarMes(delta) {
        mesAtual += delta;
        if (mesAtual > 11) {
            mesAtual = 0;
            anoAtual++;
        } else if (mesAtual < 0) {
            mesAtual = 11;
            anoAtual--;
        }
        window.renderAgenda();
    }

    function getNomeMes(mes) {
        const nomes = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        return nomes[mes];
    }

    function formatarData(data) {
        const d = new Date(data + 'T00:00:00');
        return d.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' });
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('modal-dia').addEventListener('click', function(e) {
            if (e.target === this) fecharModal();
        });

        window.renderAgenda();
    });
</script>