<div style="margin-bottom: 30px;">
    <h3 style="font-size: 24px; font-weight: 600; color: #1e2a3a;">Agenda dos Médicos</h3>
    <p style="color: #64748b; font-size: 14px; margin-top: 4px;">Visualize as consultas confirmadas por dia e médico. Clique em um dia para ver os detalhes.</p>
</div>

<!-- Seleção de Mês -->
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

<!-- Modal para detalhes do dia -->
<div id="modal-dia" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 16px; padding: 30px; max-width: 500px; width: 90%; max-height: 80%; overflow-y: auto;">
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
            const response = await fetch(`../api/admin_api.php?action=get_agenda&medico=${encodeURIComponent(medicoNome)}&mes=${mesAtual + 1}&ano=${anoAtual}`);
            const consultas = await response.json();
            
            agendaCache[medicoNome] = consultas;
            renderCalendario(medicoNome, containerId, consultas);
        } catch (error) {
            console.error('Erro ao carregar agenda:', error);
            container.innerHTML = '<p style="color: #c00; text-align: center; padding: 20px;">Erro ao carregar agenda.</p>';
        }
    }

    function renderCalendario(medicoNome, containerId, consultas) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        const primeiroDia = new Date(anoAtual, mesAtual, 1).getDay();
        const diasNoMes = new Date(anoAtual, mesAtual + 1, 0).getDate();
        
        let html = '<table style="width:100%; border-collapse:collapse; font-size:14px;">';
        html += '<thead><tr><th style="padding:8px; text-align:center; background:#f8fafc;">Dom</th><th style="padding:8px; text-align:center; background:#f8fafc;">Seg</th><th style="padding:8px; text-align:center; background:#f8fafc;">Ter</th><th style="padding:8px; text-align:center; background:#f8fafc;">Qua</th><th style="padding:8px; text-align:center; background:#f8fafc;">Qui</th><th style="padding:8px; text-align:center; background:#f8fafc;">Sex</th><th style="padding:8px; text-align:center; background:#f8fafc;">Sáb</th></tr></thead><tbody><tr>';

        for (let i = 0; i < primeiroDia; i++) {
            html += '<td style="padding:8px; text-align:center; color:#cbd5e1;"></td>';
        }

        for (let dia = 1; dia <= diasNoMes; dia++) {
            const dataStr = `${anoAtual}-${String(mesAtual+1).padStart(2,'0')}-${String(dia).padStart(2,'0')}`;
            const consultasDia = consultas.filter(c => c.data_consulta === dataStr);
            const temConsulta = consultasDia.length > 0;
            const bgColor = temConsulta ? '#fee2e2' : 'transparent';
            const textColor = temConsulta ? '#991b1b' : '#1e2a3a';

            html += `<td style="padding:8px; text-align:center; background:${bgColor}; color:${textColor}; border-radius:4px; font-weight:${temConsulta ? '600' : '400'}; cursor:${temConsulta ? 'pointer' : 'default'};" 
                ${temConsulta ? `onclick="abrirModal('${medicoNome}','${dataStr}')"` : ''}>
                ${dia}
                ${temConsulta ? `<br><small style="font-size:10px; color:#6b7280;">${consultasDia.length} consulta(s)</small>` : ''}
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

    window.abrirModal = async function(medico, data) {
        try {
            const response = await fetch(`../api/admin_api.php?action=get_agenda_detalhes&medico=${encodeURIComponent(medico)}&data=${data}`);
            const consultas = await response.json();
            
            document.getElementById('modal-titulo').textContent = `${medico} - ${formatarData(data)}`;
            
            let html = '';
            if (consultas.length === 0) {
                html = '<p style="color: #94a3b8; text-align: center; padding: 20px;">Nenhuma consulta neste dia.</p>';
            } else {
                html = '<div style="display: flex; flex-direction: column; gap: 12px;">';
                consultas.sort((a, b) => a.hora_consulta.localeCompare(b.hora_consulta)).forEach(c => {
                    html += `
                        <div style="background: #f8fafc; padding: 16px; border-radius: 12px; border-left: 4px solid #851e32;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <strong style="color: #1e2a3a; font-size: 16px;">${c.paciente_nome || 'Paciente'}</strong>
                                    <div style="font-size: 12px; color: #64748b;">${c.especialidade}</div>
                                </div>
                                <span style="background: #851e32; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">${c.hora_consulta}</span>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
            }
            
            document.getElementById('modal-conteudo').innerHTML = html;
            document.getElementById('modal-dia').style.display = 'flex';
        } catch (error) {
            console.error('Erro ao carregar detalhes:', error);
            alert('Erro ao carregar detalhes da agenda.');
        }
    };<div style="margin-bottom: 30px;">
    <h3 style="font-size: 24px; font-weight: 600; color: #1e2a3a;">Agenda dos Médicos</h3>
    <p style="color: #64748b; font-size: 14px; margin-top: 4px;">Visualize as consultas confirmadas por dia e médico. Clique em um dia para ver os detalhes.</p>
</div>

<!-- Seleção de Mês -->
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

<!-- Modal para detalhes do dia -->
<div id="modal-dia" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 16px; padding: 30px; max-width: 500px; width: 90%; max-height: 80%; overflow-y: auto;">
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
            const response = await fetch(`../api/admin_api.php?action=get_agenda&medico=${encodeURIComponent(medicoNome)}&mes=${mesAtual + 1}&ano=${anoAtual}`);
            const result = await response.json();
            
            if (result.error) {
                container.innerHTML = `<p style="color: #c00; text-align: center; padding: 20px;">Erro: ${result.error}</p>`;
                return;
            }
            
            renderCalendario(medicoNome, containerId, result);
        } catch (error) {
            console.error('Erro ao carregar agenda:', error);
            container.innerHTML = '<p style="color: #c00; text-align: center; padding: 20px;">Erro ao carregar agenda.</p>';
        }
    }

    function renderCalendario(medicoNome, containerId, consultas) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        const primeiroDia = new Date(anoAtual, mesAtual, 1).getDay();
        const diasNoMes = new Date(anoAtual, mesAtual + 1, 0).getDate();
        
        let html = '<table style="width:100%; border-collapse:collapse; font-size:14px;">';
        html += '<thead><tr><th style="padding:8px; text-align:center; background:#f8fafc;">Dom</th><th style="padding:8px; text-align:center; background:#f8fafc;">Seg</th><th style="padding:8px; text-align:center; background:#f8fafc;">Ter</th><th style="padding:8px; text-align:center; background:#f8fafc;">Qua</th><th style="padding:8px; text-align:center; background:#f8fafc;">Qui</th><th style="padding:8px; text-align:center; background:#f8fafc;">Sex</th><th style="padding:8px; text-align:center; background:#f8fafc;">Sáb</th></tr></thead><tbody><tr>';

        for (let i = 0; i < primeiroDia; i++) {
            html += '<td style="padding:8px; text-align:center; color:#cbd5e1;"></td>';
        }

        for (let dia = 1; dia <= diasNoMes; dia++) {
            const dataStr = `${anoAtual}-${String(mesAtual+1).padStart(2,'0')}-${String(dia).padStart(2,'0')}`;
            const consultasDia = consultas.filter(c => c.data_consulta === dataStr);
            const temConsulta = consultasDia.length > 0;
            const bgColor = temConsulta ? '#fee2e2' : 'transparent';
            const textColor = temConsulta ? '#991b1b' : '#1e2a3a';

            html += `<td style="padding:8px; text-align:center; background:${bgColor}; color:${textColor}; border-radius:4px; font-weight:${temConsulta ? '600' : '400'}; cursor:${temConsulta ? 'pointer' : 'default'};" 
                ${temConsulta ? `onclick="abrirModal('${medicoNome}','${dataStr}')"` : ''}>
                ${dia}
                ${temConsulta ? `<br><small style="font-size:10px; color:#6b7280;">${consultasDia.length} consulta(s)</small>` : ''}
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

    window.abrirModal = async function(medico, data) {
        try {
            const response = await fetch(`../api/admin_api.php?action=get_agenda_detalhes&medico=${encodeURIComponent(medico)}&data=${data}`);
            const consultas = await response.json();
            
            if (consultas.error) {
                document.getElementById('modal-conteudo').innerHTML = `<p style="color: #c00;">${consultas.error}</p>`;
                return;
            }
            
            document.getElementById('modal-titulo').textContent = `${medico} - ${formatarData(data)}`;
            
            let html = '';
            if (consultas.length === 0) {
                html = '<p style="color: #94a3b8; text-align: center; padding: 20px;">Nenhuma consulta neste dia.</p>';
            } else {
                html = '<div style="display: flex; flex-direction: column; gap: 12px;">';
                consultas.sort((a, b) => a.hora_consulta.localeCompare(b.hora_consulta)).forEach(c => {
                    html += `
                        <div style="background: #f8fafc; padding: 16px; border-radius: 12px; border-left: 4px solid #851e32;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <strong style="color: #1e2a3a; font-size: 16px;">${c.paciente_nome || 'Paciente'}</strong>
                                    <div style="font-size: 12px; color: #64748b;">${c.especialidade}</div>
                                </div>
                                <span style="background: #851e32; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">${c.hora_consulta}</span>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
            }
            
            document.getElementById('modal-conteudo').innerHTML = html;
            document.getElementById('modal-dia').style.display = 'flex';
        } catch (error) {
            console.error('Erro ao carregar detalhes:', error);
            alert('Erro ao carregar detalhes da agenda.');
        }
    };

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
        const nomes = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 
                      'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        return nomes[mes];
    }

    function formatarData(data) {
        const d = new Date(data + 'T00:00:00');
        return d.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' });
    }

    // Fechar modal ao clicar fora
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('modal-dia').addEventListener('click', function(e) {
            if (e.target === this) fecharModal();
        });
        
        window.renderAgenda();
    });
</script>

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
        const nomes = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 
                      'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        return nomes[mes];
    }

    function formatarData(data) {
        const d = new Date(data + 'T00:00:00');
        return d.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' });
    }

    // Fechar modal ao clicar fora
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('modal-dia').addEventListener('click', function(e) {
            if (e.target === this) fecharModal();
        });
        
        window.renderAgenda();
    });
</script>