<div style="margin-bottom: 18px;">
    <h3 style="font-size: 24px; font-weight: 600; color: #1e2a3a;">Agenda dos Médicos</h3>
    <p style="color: #64748b; font-size: 14px; margin-top: 4px;">Selecione um médico para ver a agenda e adicione consultas.</p>
</div>

<div style="display:flex; gap:12px; align-items:center; margin-bottom:16px;">
    <select id="medicoSelect" style="padding:10px; border:1px solid #ddd; border-radius:10px; min-width:260px;"></select>
    <button id="refreshAgendaBtn" style="padding:10px 14px; background:#e2e8f0; color:#1e2a3a; border:none; border-radius:10px; cursor:pointer;">Atualizar</button>
    <button id="openAddAppointment" style="padding:10px 14px; background:#851e32; color:white; border:none; border-radius:10px; cursor:pointer;">Adicionar consulta</button>
</div>

<div id="calendario-medico" style="background:white; border-radius:16px; padding:18px; box-shadow:0 1px 3px rgba(0,0,0,0.05);"></div>

<!-- Modal de agendamento -->
<div id="admin-add-appointment-modal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.55); z-index:9999; align-items:center; justify-content:center; padding:24px;">
    <div style="background:white; border-radius:18px; width:min(720px,96vw); padding:18px; position:relative;">
        <button id="admin-add-appointment-close" style="position:absolute; top:12px; right:12px; width:34px; height:34px; border:none; border-radius:999px; background:#e2e8f0; cursor:pointer; font-size:20px;">×</button>
        <h4 style="margin:0 0 12px; color:#1e2a3a;">Agendar consulta (admin)</h4>
        <form id="admin-add-appointment-form" style="display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:8px;">
            <input type="hidden" name="action" value="create_appointment">
            <select name="doctor_id" id="admin-doctor-id" required style="padding:10px; border:1px solid #ddd; border-radius:8px;">
                <option value="">Selecione o médico</option>
            </select>
            <input type="text" name="patient_name" placeholder="Nome do paciente" required style="padding:10px; border:1px solid #ddd; border-radius:8px;">
            <input type="number" name="patient_age" placeholder="Idade" style="padding:10px; border:1px solid #ddd; border-radius:8px;">
            <input type="text" name="cpf" placeholder="CPF" style="padding:10px; border:1px solid #ddd; border-radius:8px;">
            <input type="text" name="phone" placeholder="Telefone" style="padding:10px; border:1px solid #ddd; border-radius:8px;">
            <input type="date" name="date" id="admin-date" required style="padding:10px; border:1px solid #ddd; border-radius:8px;">
            <input type="time" name="time" value="09:00" style="padding:10px; border:1px solid #ddd; border-radius:8px;">
            <select name="status" style="padding:10px; border:1px solid #ddd; border-radius:8px;">
                <option value="Pendente">Pendente</option>
                <option value="Confirmada">Confirmada</option>
            </select>
            <textarea name="symptoms" placeholder="Observações" rows="3" style="grid-column:1 / -1; padding:10px; border:1px solid #ddd; border-radius:8px;"></textarea>
            <div style="grid-column:1 / -1; display:flex; gap:8px; justify-content:flex-end;">
                <button type="button" id="admin-add-cancel" style="padding:10px 14px; background:#e2e8f0; color:#1e2a3a; border:none; border-radius:8px; cursor:pointer;">Cancelar</button>
                <button type="submit" style="padding:10px 14px; background:#851e32; color:white; border:none; border-radius:8px; cursor:pointer;">Agendar</button>
            </div>
        </form>
    </div>
</div>

<script>
    window.renderAgenda = async function() {
        const medicoSelect = document.getElementById('medicoSelect');
        const calendario = document.getElementById('calendario-medico');

        if (!medicoSelect || !calendario) return;

        async function loadMedicos() {
            try {
                const res = await fetch('../api/admin_api.php?action=get_medicos');
                const medicos = await res.json();
                medicoSelect.innerHTML = '';
                medicos.forEach(function(m){
                    const opt = document.createElement('option');
                    opt.value = m.id || '';
                    opt.textContent = m.name + (m.specialty ? ' - ' + m.specialty : '');
                    opt.dataset.name = m.name || '';
                    medicoSelect.appendChild(opt);
                });
                // populate modal doctor select
                const adminDoctor = document.getElementById('admin-doctor-id');
                if (adminDoctor) {
                    adminDoctor.innerHTML = '<option value="">Selecione o médico</option>';
                    medicos.forEach(function(m){
                        const o = document.createElement('option'); o.value = m.id || ''; o.textContent = m.name + (m.specialty ? ' - ' + m.specialty : ''); adminDoctor.appendChild(o);
                    });
                }
            } catch (e) {
                medicoSelect.innerHTML = '<option value="">Erro ao carregar médicos</option>';
            }
        }

        function buildCalendarFor(doctorId, doctorName) {
            const today = new Date();
            const mes = today.getMonth();
            const ano = today.getFullYear();
            const primeiroDia = new Date(ano, mes, 1).getDay();
            const diasNoMes = new Date(ano, mes + 1, 0).getDate();

            calendario.innerHTML = '<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;"><div style="font-weight:700; color:#1e2a3a;">' + (doctorName || 'Médico não selecionado') + '</div><div style="color:#64748b;">' + today.toLocaleString('pt-BR', { month: 'long', year: 'numeric' }) + '</div></div>';

            let table = '<table style="width:100%; border-collapse:collapse; font-size:14px;">';
            table += '<thead><tr><th style="padding:8px; text-align:center; background:#f8fafc;">Dom</th><th style="padding:8px; text-align:center; background:#f8fafc;">Seg</th><th style="padding:8px; text-align:center; background:#f8fafc;">Ter</th><th style="padding:8px; text-align:center; background:#f8fafc;">Qua</th><th style="padding:8px; text-align:center; background:#f8fafc;">Qui</th><th style="padding:8px; text-align:center; background:#f8fafc;">Sex</th><th style="padding:8px; text-align:center; background:#f8fafc;">Sáb</th></tr></thead><tbody><tr>';

            for (let i = 0; i < primeiroDia; i++) table += '<td style="padding:8px; text-align:center; color:#cbd5e1;"></td>';

            for (let dia = 1; dia <= diasNoMes; dia++) {
                const dataStr = `${ano}-${String(mes+1).padStart(2,'0')}-${String(dia).padStart(2,'0')}`;
                table += `<td style="padding:8px; text-align:center; border-radius:6px; vertical-align:top;">`;
                table += `<div style="font-weight:700; margin-bottom:6px;">${dia}</div>`;
                table += `<div data-day="${dataStr}" data-doctor-id="${doctorId}" style="display:flex; flex-direction:column; gap:6px;"></div>`;
                table += `</td>`;
                if ((primeiroDia + dia) % 7 === 0) table += '</tr><tr>';
            }

            const totalDias = primeiroDia + diasNoMes;
            const resto = totalDias % 7;
            if (resto > 0) for (let i = 0; i < (7 - resto); i++) table += '<td style="padding:8px; text-align:center; color:#cbd5e1;"></td>';

            table += '</tr></tbody></table>';
            calendario.innerHTML += table;
        }

        async function loadAppointmentsFor(doctorId) {
            try {
                const res = await fetch('../api/admin_api.php?action=get_agenda');
                const agenda = await res.json();
                const rows = Array.from(document.querySelectorAll('#calendario-medico [data-day]'));
                rows.forEach(function(container) { container.innerHTML = ''; });

                const doctorName = (document.querySelector('#medicoSelect option:checked') || {}).dataset?.name || '';
                const filtered = (Array.isArray(agenda) ? agenda : []).filter(a => a.medico === doctorName);
                filtered.forEach(function(item){
                    const day = item.data_consulta || item.date || '';
                    const target = document.querySelector('#calendario-medico [data-day="' + day + '"]');
                    if (target) {
                        const el = document.createElement('div');
                        el.style.padding = '6px';
                        el.style.borderRadius = '8px';
                        el.style.background = '#fee2e2';
                        el.style.color = '#991b1b';
                        el.style.fontSize = '12px';
                        el.style.cursor = 'pointer';
                        el.innerHTML = `<strong style="font-weight:700;">${item.hora_consulta || item.hora || ''}</strong><div style="font-size:11px; color:#6b7280;">${item.paciente_nome || item.patient_name || ''}</div>`;
                        el.addEventListener('click', function(ev){ ev.stopPropagation(); openDayDetails(item.medico, day); });
                        target.appendChild(el);
                    }
                });
            } catch (e) {
                console.warn('Erro ao carregar agenda', e);
            }
        }

        function openDayDetails(doctorName, dateKey) {
            // Reuse existing API modal behaviour from medicos list if present
            if (typeof openDoctorAgendaDay === 'function') {
                openDoctorAgendaDay(doctorName, dateKey);
                document.getElementById('doctor-agenda-modal').style.display = 'flex';
                return;
            }
            alert('Abrir detalhes: ' + doctorName + ' • ' + dateKey);
        }

        // inicializar
        await loadMedicos();
        medicoSelect.addEventListener('change', function(){
            const sel = medicoSelect.options[medicoSelect.selectedIndex];
            buildCalendarFor(sel ? sel.value : '', sel ? sel.dataset.name : '');
            loadAppointmentsFor(sel ? sel.value : '');
        });

        document.getElementById('refreshAgendaBtn').addEventListener('click', function(){
            medicoSelect.dispatchEvent(new Event('change'));
        });

        document.getElementById('openAddAppointment').addEventListener('click', function(){
            const adminModal = document.getElementById('admin-add-appointment-modal');
            const adminDoctor = document.getElementById('admin-doctor-id');
            const sel = medicoSelect.options[medicoSelect.selectedIndex];
            if (sel) {
                adminDoctor.value = sel.value || '';
                document.getElementById('admin-date').value = new Date().toISOString().slice(0,10);
            }
            adminModal.style.display = 'flex';
        });

        document.getElementById('admin-add-appointment-close').addEventListener('click', function(){ document.getElementById('admin-add-appointment-modal').style.display = 'none'; });
        document.getElementById('admin-add-cancel').addEventListener('click', function(){ document.getElementById('admin-add-appointment-modal').style.display = 'none'; });

        document.getElementById('admin-add-appointment-form').addEventListener('submit', async function(e){
            e.preventDefault();
            const form = e.target;
            const fd = new FormData(form);
            const payload = {};
            fd.forEach((v,k)=> payload[k]=v);
            try {
                const res = await fetch('../api/admin_api.php?action=create_appointment', {
                    method: 'POST',
                    headers: { 'Content-Type':'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data && data.success) {
                    document.getElementById('admin-add-appointment-modal').style.display = 'none';
                    medicoSelect.dispatchEvent(new Event('change'));
                    alert('Consulta agendada com sucesso.');
                } else {
                    alert((data && data.message) ? data.message : 'Falha ao agendar.');
                }
            } catch (err) {
                alert('Erro ao enviar agendamento.');
            }
        });

        // quando carregar pela primeira vez, selecionar primeiro médico
        if (medicoSelect.options.length) {
            medicoSelect.selectedIndex = 0;
            medicoSelect.dispatchEvent(new Event('change'));
        }
    };

    // chamar quando o script for carregado dentro do admin
    document.addEventListener('DOMContentLoaded', function(){ if (typeof window.renderAgenda === 'function') window.renderAgenda(); });
</script>