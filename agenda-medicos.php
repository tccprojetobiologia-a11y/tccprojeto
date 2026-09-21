<?php
require_once __DIR__ . '/config/auth_storage.php';
$doctors = get_doctors();
?>

<div style="display: flex; flex-direction: column; gap: 16px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h3 style="margin: 0; font-size: 24px; color: #1e2a3a;">Agenda dos médicos</h3>
            <p style="margin: 6px 0 0; color: #64748b;">Selecione o médico e veja por dia quais horários estão ocupados e quais estão livres.</p>
        </div>
    </div>

    <div style="display:flex; align-items:center; gap:16px; flex-wrap:wrap; margin-bottom:12px;">
        <label for="doctorSelect" style="font-weight:600; color:#1e2a3a;">Médico:</label>
        <select id="doctorSelect" style="padding: 10px 14px; border:1px solid #dfe3e8; border-radius:10px; min-width: 260px; background:#fff; color:#1e2a3a;">
            <?php foreach ($doctors as $doctor): ?>
                <option value="<?php echo htmlspecialchars($doctor['id'] ?? ''); ?>"><?php echo htmlspecialchars($doctor['name'] ?? 'Médico'); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="background: #fff; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); padding: 16px;">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 12px;">
            <button onclick="mudarMes(-1)" style="background:#f1f5f9; color:#1e2a3a; border:none; border-radius:10px; padding:10px 14px; font-weight:700; cursor:pointer;">←</button>
            <h4 id="mes-atual" style="margin:0; font-size:22px; color:#1e2a3a;">Carregando...</h4>
            <button onclick="mudarMes(1)" style="background:#f1f5f9; color:#1e2a3a; border:none; border-radius:10px; padding:10px 14px; font-weight:700; cursor:pointer;">→</button>
        </div>
        <div id="agenda-container" style="overflow-x:auto;"></div>
    </div>
</div>

<div id="modal-dia" style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.55); display: none; align-items: center; justify-content: center; z-index: 999;">
    <div style="background:#fff; width:min(760px, 90vw); border-radius:20px; padding:24px; position:relative; box-shadow:0 20px 50px rgba(0,0,0,0.25);">
        <button onclick="fecharModal()" style="position:absolute; right:20px; top:18px; border:none; background:#f1f5f9; color:#1e2a3a; border-radius:999px; width:36px; height:36px; cursor:pointer; font-size:18px;">×</button>
        <h4 id="modal-titulo" style="margin:0 0 18px; color:#1e2a3a; font-size:22px;">Detalhes</h4>
        <div id="modal-conteudo"></div>
    </div>
</div>

<script>
    window.doctors = <?php echo json_encode($doctors, JSON_UNESCAPED_UNICODE); ?>;
    window.agendaState = { mes: new Date().getMonth(), ano: new Date().getFullYear() };
    window.currentAgendaContext = { medico: '', data: '' };

    function getSelectedDoctor() {
        const select = document.getElementById('doctorSelect');
        if (!select) return (window.doctors || [])[0] || null;
        const selectedId = select.value || (window.doctors[0]?.id || '');
        return (window.doctors || []).find(doc => doc.id === selectedId) || (window.doctors || [])[0] || null;
    }

    async function carregarAgenda() {
        const container = document.getElementById('agenda-container');
        const titulo = document.getElementById('mes-atual');
        const doctor = getSelectedDoctor();
        if (!doctor) {
            container.innerHTML = '<p style="color:#64748b; text-align:center; padding:16px;">Nenhum médico disponível.</p>';
            return;
        }

        titulo.textContent = `${getNomeMes(window.agendaState.mes)} ${window.agendaState.ano}`;

        const response = await fetch('../api/admin_api.php?action=get_agenda');
        const agenda = await response.json();
        const consultasDoMedico = agenda.filter(item => item.medico === doctor.name);

        const primeiroDia = new Date(window.agendaState.ano, window.agendaState.mes, 1);
        const ultimoDia = new Date(window.agendaState.ano, window.agendaState.mes + 1, 0);
        const inicioSemana = (primeiroDia.getDay() + 6) % 7;
        const diasDoMes = ultimoDia.getDate();

        let html = '<table style="width: 100%; border-collapse: collapse; min-width: 720px;"><thead><tr>';
        ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'].forEach((dia) => {
            html += `<th style="padding: 12px; background:#f8fafc; color:#1e2a3a; border:1px solid #e2e8f0;">${dia}</th>`;
        });
        html += '</tr></thead><tbody><tr>';

        for (let i = 0; i < inicioSemana; i++) {
            html += '<td style="padding: 12px; border:1px solid #e2e8f0; background:#f8fafc; min-height:120px;"></td>';
        }

        for (let dia = 1; dia <= diasDoMes; dia++) {
            const data = `${window.agendaState.ano}-${String(window.agendaState.mes + 1).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;
            const consultasDoDia = consultasDoMedico.filter(item => item.data_consulta === data);
            if (consultasDoDia.length > 0) {
                html += `<td style="padding: 10px; border:1px solid #e2e8f0; vertical-align: top; min-height:120px; background:#fff7f7;">`;
            } else {
                html += `<td data-empty-day="${data}" style="padding: 10px; border:1px solid #e2e8f0; vertical-align: top; min-height:120px; background:#fff; cursor:pointer;">`;
            }
            html += `<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">`;
            html += `<span style="font-weight:700; color:#1e2a3a;">${dia}</span>`;
            if (consultasDoDia.length > 0) {
                html += `<span style="background:#e0f2fe; color:#0f172a; border-radius:999px; padding:2px 8px; font-size:11px;">${consultasDoDia.length}</span>`;
            }
            html += '</div>';

            if (consultasDoDia.length > 0) {
                consultasDoDia.slice(0, 3).forEach((item) => {
                    html += `<button type="button" onclick="abrirModal('${String(doctor.name || 'Médico').replace(/'/g, "\\'")}', '${data}')" style="display:block; width:100%; text-align:left; background:#851e32; color:white; border:none; border-radius:8px; padding:6px 8px; margin-bottom:6px; cursor:pointer; font-size:12px;">${item.paciente_nome || 'Paciente'}</button>`;
                });
                if (consultasDoDia.length > 3) {
                    html += `<div style="font-size:11px; color:#64748b; text-align:right;">+${consultasDoDia.length - 3}</div>`;
                }
            }

            html += '</td>';

            if ((dia + inicioSemana) % 7 === 0) {
                html += '</tr><tr>';
            }
        }

        const ultimaPosicao = (diasDoMes + inicioSemana) % 7;
        if (ultimaPosicao !== 0) {
            for (let i = ultimaPosicao; i < 7; i++) {
                html += '<td style="padding: 12px; border:1px solid #e2e8f0; background:#f8fafc; min-height:120px;"></td>';
            }
        }

        html += '</tr></tbody></table>';
        container.innerHTML = html;
    }

    async function abrirModal(medico, data) {
        window.currentAgendaContext = { medico: medico, data: data };
        const response = await fetch(`../api/admin_api.php?action=get_agenda_detalhes&medico=${encodeURIComponent(medico)}&data=${encodeURIComponent(data)}`);
        const slots = await response.json();

        if (slots.error) {
            document.getElementById('modal-conteudo').innerHTML = `<p style="color: #c00;">${slots.error}</p>`;
            document.getElementById('modal-dia').style.display = 'flex';
            return;
        }

        const doctor = getSelectedDoctor();
        document.getElementById('modal-titulo').textContent = `${medico} - ${formatarData(data)}`;

        let html = '<form id="day-slots-form" style="display:flex; flex-direction:column; gap:12px;">';
        if (!Array.isArray(slots) || slots.length === 0) {
            html += '<p style="color: #94a3b8; text-align: center; padding: 20px;">Nenhum horário registrado neste dia.</p>';
        } else {
            slots.forEach(function(slot, idx) {
                const horario = slot.hora || '';
                const ocupado = !!slot.ocupado;
                const apptId = slot.appointment_id || '';

                html += '<div class="slot-row" style="display:grid; grid-template-columns:120px 1fr 160px 110px; gap:8px; align-items:center; padding:12px; border-radius:10px; border:1px solid #eef2f7; background:' + (ocupado ? '#fff7ed' : '#f8fafc') + ';">';
                html += '<input name="slot_time_' + idx + '" data-slot-time-index="' + idx + '" value="' + horario + '" style="padding:8px; border:1px solid #ddd; border-radius:8px; width:100px;">';
                html += '<input name="slot_patient_' + idx + '" data-slot-patient-index="' + idx + '" placeholder="Nome do paciente" value="' + (slot.paciente_nome || '') + '" style="padding:8px; border:1px solid #ddd; border-radius:8px;">';
                html += '<select name="slot_status_' + idx + '" data-slot-status-index="' + idx + '" style="padding:8px; border:1px solid #ddd; border-radius:8px;">';
                html += '<option value="Disponível"' + (ocupado ? '' : ' selected') + '>Disponível</option>';
                html += '<option value="Pendente"' + (slot.status === 'Pendente' ? ' selected' : '') + '>Pendente</option>';
                html += '<option value="Confirmada"' + (slot.status === 'Confirmada' ? ' selected' : '') + '>Confirmada</option>';
                html += '<option value="Recusada"' + (slot.status === 'Recusada' ? ' selected' : '') + '>Recusada</option>';
                html += '</select>';
                html += '<div style="display:flex; gap:8px;">';
                if (ocupado) {
                    html += '<button type="button" data-save-slot-id="' + apptId + '" data-slot-index="' + idx + '" style="padding:8px 12px; background:#0ea5a4; color:white; border:none; border-radius:8px; cursor:pointer;">Salvar</button>';
                    html += '<button type="button" data-delete-slot-id="' + apptId + '" style="padding:8px 12px; background:#ef4444; color:white; border:none; border-radius:8px; cursor:pointer;">Cancelar</button>';
                } else {
                    html += '<button type="button" data-create-slot-index="' + idx + '" style="padding:8px 12px; background:#851e32; color:white; border:none; border-radius:8px; cursor:pointer;">Adicionar</button>';
                }
                html += '</div>';
                html += '</div>';
            });
        }

        html += '</form>';
        document.getElementById('modal-conteudo').innerHTML = html;
        document.getElementById('modal-dia').style.display = 'flex';
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
                motivoBox.style.display = 'block';
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
                    abrirModal(window.currentAgendaContext.medico, window.currentAgendaContext.data);
                }
            } else {
                alert((result && result.message) || 'Não foi possível atualizar a consulta.');
            }
        } catch (error) {
            alert('Erro ao atualizar a consulta: ' + error.message);
        }
    });

    // Click on empty day cell to quick-create an appointment
    document.addEventListener('click', async function(e) {
        const emptyCell = e.target.closest('[data-empty-day]');
        if (!emptyCell) return;
        const date = emptyCell.getAttribute('data-empty-day');
        const doctor = getSelectedDoctor();
        if (!doctor) return alert('Selecione um médico primeiro.');

        const time = prompt('Informe o horário (HH:MM):', '09:00');
        if (!time) return;
        const patient = prompt('Nome do paciente:', 'Paciente');
        if (!patient) return;

        const payload = {
            doctor_id: doctor.id || '',
            patient_name: patient,
            patient_age: 0,
            cpf: '',
            phone: '',
            date: date,
            time: time,
            status: 'Pendente',
            symptoms: 'Agendamento rápido pelo admin.'
        };

        try {
            const res = await fetch('../api/admin_api.php?action=create_appointment', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (data && data.success) {
                alert('Consulta criada com sucesso.');
                carregarAgenda();
            } else {
                alert((data && data.message) ? data.message : 'Falha ao criar consulta.');
            }
        } catch (err) {
            alert('Erro ao criar consulta: ' + err.message);
        }
    });

    // Edit appointment (change time/date)
    document.addEventListener('click', async function(e) {
        const editBtn = e.target.closest('[data-edit-appointment-id]');
        if (!editBtn) return;
        const appointmentId = editBtn.getAttribute('data-edit-appointment-id');
        if (!appointmentId) return alert('ID da consulta não encontrado.');

        const newTime = prompt('Informe novo horário (HH:MM):', '09:00');
        if (!newTime) return;
        const newDate = prompt('Informe nova data (AAAA-MM-DD) ou deixe em branco para manter:', '');

        const payload = { id: appointmentId };
        if (newTime) payload.time = newTime;
        if (newDate) payload.date = newDate;

        try {
            const res = await fetch('../api/admin_api.php?action=update_paciente', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (result && result.success) {
                alert('Consulta atualizada.');
                if (window.currentAgendaContext && window.currentAgendaContext.medico && window.currentAgendaContext.data) {
                    abrirModal(window.currentAgendaContext.medico, window.currentAgendaContext.data);
                } else {
                    carregarAgenda();
                }
            } else {
                alert((result && result.message) || 'Falha ao atualizar.');
            }
        } catch (err) {
            alert('Erro ao atualizar: ' + err.message);
        }
    });

    // Handlers for modal save/create/delete buttons
    document.addEventListener('click', async function(e) {
        // Save existing appointment
        const saveBtn = e.target.closest('[data-save-slot-id]');
        if (saveBtn) {
            const apptId = saveBtn.getAttribute('data-save-slot-id');
            const idx = saveBtn.getAttribute('data-slot-index');
            const timeInput = document.querySelector('[data-slot-time-index="' + idx + '"]');
            const patientInput = document.querySelector('[data-slot-patient-index="' + idx + '"]');
            const statusInput = document.querySelector('[data-slot-status-index="' + idx + '"]');
            const updates = {};
            if (timeInput) updates.time = timeInput.value;
            if (patientInput) updates.patient_name = patientInput.value;
            if (statusInput) updates.status = statusInput.value;

            try {
                const res = await fetch('../api/admin_api.php?action=update_paciente', {
                    method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(Object.assign({ id: apptId }, updates))
                });
                const data = await res.json();
                if (data && data.success) {
                    alert('Consulta atualizada.');
                    abrirModal(window.currentAgendaContext.medico, window.currentAgendaContext.data);
                } else {
                    alert((data && data.message) || 'Falha ao atualizar.');
                }
            } catch (err) {
                alert('Erro ao atualizar: ' + err.message);
            }
            return;
        }

        // Create appointment in empty slot
        const createBtn = e.target.closest('[data-create-slot-index]');
        if (createBtn) {
            const idx = createBtn.getAttribute('data-create-slot-index');
            const timeInput = document.querySelector('[data-slot-time-index="' + idx + '"]');
            const patientInput = document.querySelector('[data-slot-patient-index="' + idx + '"]');
            const statusInput = document.querySelector('[data-slot-status-index="' + idx + '"]');
            const doctor = getSelectedDoctor();
            if (!doctor) return alert('Selecione um médico.');
            const payload = {
                doctor_id: doctor.id || '',
                patient_name: patientInput ? patientInput.value : 'Paciente',
                patient_age: 0,
                cpf: '', phone: '',
                date: window.currentAgendaContext.data,
                time: timeInput ? timeInput.value : '',
                status: statusInput ? statusInput.value : 'Pendente',
                symptoms: 'Agendamento pelo admin.'
            };
            try {
                const res = await fetch('../api/admin_api.php?action=create_appointment', {
                    method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data && data.success) {
                    alert('Consulta criada.');
                    abrirModal(window.currentAgendaContext.medico, window.currentAgendaContext.data);
                    carregarAgenda();
                } else {
                    alert((data && data.message) || 'Falha ao criar.');
                }
            } catch (err) {
                alert('Erro: ' + err.message);
            }
            return;
        }

        // Cancel appointment (mark as Recusada)
        const delBtn = e.target.closest('[data-delete-slot-id]');
        if (delBtn) {
            const apptId = delBtn.getAttribute('data-delete-slot-id');
            if (!confirm('Cancelar esta consulta?')) return;
            try {
                const res = await fetch('../api/admin_api.php?action=atualizar_status_consulta', {
                    method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id: apptId, status: 'Recusada', motivo: 'Cancelado pelo admin' })
                });
                const data = await res.json();
                if (data && data.success) {
                    alert('Consulta cancelada.');
                    abrirModal(window.currentAgendaContext.medico, window.currentAgendaContext.data);
                    carregarAgenda();
                } else {
                    alert((data && data.message) || 'Falha ao cancelar.');
                }
            } catch (err) {
                alert('Erro: ' + err.message);
            }
            return;
        }
    });

    function fecharModal() {
        document.getElementById('modal-dia').style.display = 'none';
    }

    function mudarMes(delta) {
        window.agendaState.mes += delta;
        while (window.agendaState.mes > 11) {
            window.agendaState.mes = 0;
            window.agendaState.ano++;
        }
        while (window.agendaState.mes < 0) {
            window.agendaState.mes = 11;
            window.agendaState.ano--;
        }
        carregarAgenda();
    }

    function getNomeMes(mes) {
        const nomes = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        return nomes[mes];
    }

    function formatarData(data) {
        const d = new Date(data + 'T00:00:00');
        return d.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' });
    }

    document.getElementById('doctorSelect')?.addEventListener('change', function() {
        carregarAgenda();
    });

    window.abrirModal = abrirModal;
    window.fecharModal = fecharModal;
    window.renderAgenda = carregarAgenda;

    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('modal-dia');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) fecharModal();
            });
        }
        carregarAgenda();
    });
</script>
