<?php
require_once __DIR__ . '/config/auth_storage.php';
$doctors = get_doctors();
?>

<div style="display: flex; flex-direction: column; gap: 16px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h3 style="margin: 0; font-size: 24px; color: #1e2a3a;">Agenda dos médicos</h3>
            <p style="margin: 6px 0 0; color: #64748b;">Selecione o médico e visualize as consultas do mês para confirmar ou cancelar agendamentos.</p>
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
    const doctors = <?php echo json_encode($doctors, JSON_UNESCAPED_UNICODE); ?>;
    const agendaState = { mes: new Date().getMonth(), ano: new Date().getFullYear() };

    function getSelectedDoctor() {
        const select = document.getElementById('doctorSelect');
        if (!select) return doctors[0] || null;
        const selectedId = select.value || doctors[0]?.id || '';
        return doctors.find(doc => doc.id === selectedId) || doctors[0] || null;
    }

    async function carregarAgenda() {
        const container = document.getElementById('agenda-container');
        const titulo = document.getElementById('mes-atual');
        const doctor = getSelectedDoctor();
        if (!doctor) {
            container.innerHTML = '<p style="color:#64748b; text-align:center; padding:16px;">Nenhum médico disponível.</p>';
            return;
        }

        titulo.textContent = `${getNomeMes(agendaState.mes)} ${agendaState.ano}`;

        const response = await fetch('../api/admin_api.php?action=get_agenda');
        const agenda = await response.json();
        const consultasDoMedico = agenda.filter(item => item.medico === doctor.name);

        const primeiroDia = new Date(agendaState.ano, agendaState.mes, 1);
        const ultimoDia = new Date(agendaState.ano, agendaState.mes + 1, 0);
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
            const data = `${agendaState.ano}-${String(agendaState.mes + 1).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;
            const consultasDoDia = consultasDoMedico.filter(item => item.data_consulta === data);
            html += `<td style="padding: 10px; border:1px solid #e2e8f0; vertical-align: top; min-height:120px; background:${consultasDoDia.length ? '#fff7f7' : '#fff'};">`;
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
        const response = await fetch(`../api/admin_api.php?action=get_agenda_detalhes&medico=${encodeURIComponent(medico)}&data=${encodeURIComponent(data)}`);
        const consultas = await response.json();

        if (consultas.error) {
            document.getElementById('modal-conteudo').innerHTML = `<p style="color: #c00;">${consultas.error}</p>`;
            return;
        }

        document.getElementById('modal-titulo').textContent = `${medico} - ${formatarData(data)}`;

        let html = '';
        if (!consultas.length) {
            html = '<p style="color: #94a3b8; text-align: center; padding: 20px;">Nenhuma consulta neste dia.</p>';
        } else {
            html = '<div style="display: flex; flex-direction: column; gap: 12px;">';
            consultas.sort((a, b) => (a.time || '00:00').localeCompare(b.time || '00:00')).forEach((c) => {
                html += `
                    <div style="background: #f8fafc; padding: 16px; border-radius: 12px; border-left: 4px solid #851e32;">
                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px;">
                            <div>
                                <strong style="color: #1e2a3a; font-size: 16px;">${c.patient_name || 'Paciente'}</strong>
                                <div style="font-size: 12px; color: #64748b;">${c.status || 'Pendente'} • ${c.time || '09:00'}</div>
                            </div>
                            <span style="background: #851e32; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">${c.time || '09:00'}</span>
                        </div>
                        <div style="margin-top: 12px; display:flex; gap:8px; flex-wrap:wrap;">
                            <button type="button" onclick="confirmarConsulta('${c.id}')" style="padding:8px 12px; border:none; border-radius:8px; background:#16a34a; color:white; font-weight:600; cursor:pointer;">Confirmar</button>
                            <button type="button" onclick="cancelarConsulta('${c.id}')" style="padding:8px 12px; border:none; border-radius:8px; background:#ef4444; color:white; font-weight:600; cursor:pointer;">Cancelar</button>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
        }

        document.getElementById('modal-conteudo').innerHTML = html;
        document.getElementById('modal-dia').style.display = 'flex';
    }

    async function confirmarConsulta(id) {
        const response = await fetch('../api/admin_api.php?action=aprovar_consulta', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: `id=${encodeURIComponent(id)}`
        });
        const result = await response.json();
        if (result.success) {
            alert(result.message);
            carregarAgenda();
            fecharModal();
        } else {
            alert(result.message || 'Não foi possível confirmar.');
        }
    }

    async function cancelarConsulta(id) {
        const response = await fetch('../api/admin_api.php?action=recusar_consulta', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: `id=${encodeURIComponent(id)}`
        });
        const result = await response.json();
        if (result.success) {
            alert(result.message);
            carregarAgenda();
            fecharModal();
        } else {
            alert(result.message || 'Não foi possível cancelar.');
        }
    }

    function fecharModal() {
        document.getElementById('modal-dia').style.display = 'none';
    }

    function mudarMes(delta) {
        agendaState.mes += delta;
        while (agendaState.mes > 11) {
            agendaState.mes = 0;
            agendaState.ano++;
        }
        while (agendaState.mes < 0) {
            agendaState.mes = 11;
            agendaState.ano--;
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
    window.confirmarConsulta = confirmarConsulta;
    window.cancelarConsulta = cancelarConsulta;
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
