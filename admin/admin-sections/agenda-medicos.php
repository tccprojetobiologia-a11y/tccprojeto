<div style="margin-bottom: 30px;">
    <h3 style="font-size: 24px; font-weight: 600; color: #1e2a3a;">Agenda dos Médicos</h3>
    <p style="color: #64748b; font-size: 14px; margin-top: 4px;">Visualize as consultas confirmadas por dia e médico.</p>
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

<script>
    window.renderAgenda = function() {
        // Recarregar dados do localStorage
        function carregarDados() {
            let dados = localStorage.getItem('cardioweb_dados');
            if (dados) {
                try {
                    return JSON.parse(dados);
                } catch(e) {}
            }
            return { agendaMedicos: { 'Dr. Roberto Mendes': [], 'Dra. Aline Costa': [] } };
        }
        let dados = carregarDados();
        window.agendaMedicos = dados.agendaMedicos;
        const agenda = window.agendaMedicos || {};

        function renderCalendario(medicoNome, containerId) {
            const container = document.getElementById(containerId);
            if (!container) return;
            const mes = 5; // Junho
            const ano = 2026;
            const primeiroDia = new Date(ano, mes, 1).getDay();
            const diasNoMes = new Date(ano, mes + 1, 0).getDate();
            const consultas = agenda[medicoNome] || [];

            let html = '<table style="width:100%; border-collapse:collapse; font-size:14px;">';
            html += '<thead><tr><th style="padding:8px; text-align:center; background:#f8fafc;">Dom</th><th style="padding:8px; text-align:center; background:#f8fafc;">Seg</th><th style="padding:8px; text-align:center; background:#f8fafc;">Ter</th><th style="padding:8px; text-align:center; background:#f8fafc;">Qua</th><th style="padding:8px; text-align:center; background:#f8fafc;">Qui</th><th style="padding:8px; text-align:center; background:#f8fafc;">Sex</th><th style="padding:8px; text-align:center; background:#f8fafc;">Sáb</th></tr></thead><tbody><tr>';

            for (let i = 0; i < primeiroDia; i++) {
                html += '<td style="padding:8px; text-align:center; color:#cbd5e1;"></td>';
            }

            for (let dia = 1; dia <= diasNoMes; dia++) {
                const dataStr = `${ano}-${String(mes+1).padStart(2,'0')}-${String(dia).padStart(2,'0')}`;
                const consultasDia = consultas.filter(c => c.data === dataStr);
                const temConsulta = consultasDia.length > 0;
                const bgColor = temConsulta ? '#fee2e2' : 'transparent';
                const textColor = temConsulta ? '#991b1b' : '#1e2a3a';

                html += `<td style="padding:8px; text-align:center; background:${bgColor}; color:${textColor}; border-radius:4px; font-weight:${temConsulta ? '600' : '400'};">
                    ${dia}
                    ${temConsulta ? `<br><small style="font-size:10px; color:#6b7280;">${consultasDia.map(c => c.paciente + ' ' + c.hora).join('<br>')}</small>` : ''}
                </td>`;

                if ((primeiroDia + dia) % 7 === 0) {
                    html += '</tr><tr>';
                }
            }

            const totalDias = primeiroDia + diasNoMes;
            const resto = totalDias % 7;
            if (resto > 0) {
                for (let i = 0; i < (7 - resto); i++) {
                    html += '<td style="padding:8px; text-align:center; color:#cbd5e1;"></td>';
                }
            }

            html += '</tr></tbody></table>';
            container.innerHTML = html;
        }

        renderCalendario('Dr. Roberto Mendes', 'calendario-roberto');
        renderCalendario('Dra. Aline Costa', 'calendario-aline');
    };

    document.addEventListener('DOMContentLoaded', function() {
        window.renderAgenda();
    });
</script>