<!-- Estrutura para renderização via JavaScript -->
<div style="margin-bottom: 30px;">
    <h3 style="font-size: 24px; font-weight: 600; color: #1e2a3a;">Gerenciar Consultas</h3>
    <p style="color: #64748b; font-size: 14px; margin-top: 4px;">Aprove ou recuse solicitações e acompanhe o histórico.</p>
</div>

<!-- Abas -->
<div style="display: flex; gap: 12px; margin-bottom: 24px; border-bottom: 2px solid #f0f0f0; padding-bottom: 8px;">
    <button onclick="switchTab('pendentes')" id="tab-pendentes" class="tab-btn active" style="padding: 8px 20px; border: none; background: transparent; font-weight: 600; color: #851e32; border-bottom: 3px solid #851e32; cursor: pointer; font-family: inherit; font-size: 15px;">Pendentes</button>
    <button onclick="switchTab('confirmadas')" id="tab-confirmadas" class="tab-btn" style="padding: 8px 20px; border: none; background: transparent; font-weight: 600; color: #64748b; border-bottom: 3px solid transparent; cursor: pointer; font-family: inherit; font-size: 15px;">Confirmadas</button>
    <button onclick="switchTab('recusadas')" id="tab-recusadas" class="tab-btn" style="padding: 8px 20px; border: none; background: transparent; font-weight: 600; color: #64748b; border-bottom: 3px solid transparent; cursor: pointer; font-family: inherit; font-size: 15px;">Recusadas</button>
</div>

<!-- Containers para cada lista -->
<div id="lista-pendentes" class="tab-content" style="display: block;"></div>
<div id="lista-confirmadas" class="tab-content" style="display: none;"></div>
<div id="lista-recusadas" class="tab-content" style="display: none;"></div>

<script>
    // Função para renderizar as listas
    window.renderConsultas = function() {
        const pendentes = window.consultas.pendentes || [];
        const confirmadas = window.consultas.confirmadas || [];
        const recusadas = window.consultas.recusadas || [];

        // Renderizar pendentes
        const pendentesDiv = document.getElementById('lista-pendentes');
        if (pendentes.length === 0) {
            pendentesDiv.innerHTML = '<p style="color: #94a3b8; text-align: center; padding: 40px 0;">Nenhuma consulta pendente.</p>';
        } else {
            let html = '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px;">';
            pendentes.forEach(c => {
                html += `
                    <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; flex-direction: column; gap: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div style="width: 40px; height: 40px; background: rgba(245, 158, 11, 0.1); color: #fbbf24; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                                <i class="fas fa-clock"></i>
                            </div>
                            <span style="background: rgba(245, 158, 11, 0.15); color: #fbbf24; padding: 4px 12px; border-radius: 20px; font-weight: 600; font-size: 12px;">Aguardando</span>
                        </div>
                        <div>
                            <h4 style="font-size: 18px; font-weight: 600; color: #1e2a3a; margin-bottom: 8px;">${c.paciente}</h4>
                            <p style="color: #64748b; font-size: 14px; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-user-md" style="color: #6366f1;"></i> ${c.medico} (${c.especialidade})
                            </p>
                            <p style="color: #1e2a3a; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                                <i class="far fa-calendar-alt" style="color: #6366f1;"></i> ${new Date(c.data).toLocaleDateString('pt-BR')} às ${c.hora}
                            </p>
                        </div>
                        <div style="display: flex; gap: 12px; margin-top: 8px; border-top: 1px solid #f0f0f0; padding-top: 16px;">
                            <button onclick="aprovarConsulta(${c.id})" style="flex: 1; background: #851e32; color: white; border: none; padding: 12px; border-radius: 10px; font-weight: 600; cursor: pointer; font-size: 14px;">Confirmar</button>
                            <button onclick="recusarConsulta(${c.id})" style="flex: 1; background: transparent; color: #ef4444; border: 1px solid #ef4444; padding: 12px; border-radius: 10px; font-weight: 600; cursor: pointer; font-size: 14px;">Recusar</button>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            pendentesDiv.innerHTML = html;
        }

        // Renderizar confirmadas
        const confirmadasDiv = document.getElementById('lista-confirmadas');
        if (confirmadas.length === 0) {
            confirmadasDiv.innerHTML = '<p style="color: #94a3b8; text-align: center; padding: 40px 0;">Nenhuma consulta confirmada.</p>';
        } else {
            let html = '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px;">';
            confirmadas.forEach(c => {
                html += `
                    <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; flex-direction: column; gap: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div style="width: 40px; height: 40px; background: rgba(34, 197, 94, 0.1); color: #4ade80; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <span style="background: rgba(34, 197, 94, 0.15); color: #4ade80; padding: 4px 12px; border-radius: 20px; font-weight: 600; font-size: 12px;">Confirmada</span>
                        </div>
                        <div>
                            <h4 style="font-size: 18px; font-weight: 600; color: #1e2a3a; margin-bottom: 8px;">${c.paciente}</h4>
                            <p style="color: #64748b; font-size: 14px; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-user-md" style="color: #6366f1;"></i> ${c.medico} (${c.especialidade})
                            </p>
                            <p style="color: #1e2a3a; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                                <i class="far fa-calendar-alt" style="color: #6366f1;"></i> ${new Date(c.data).toLocaleDateString('pt-BR')} às ${c.hora}
                            </p>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            confirmadasDiv.innerHTML = html;
        }

        // Renderizar recusadas
        const recusadasDiv = document.getElementById('lista-recusadas');
        if (recusadas.length === 0) {
            recusadasDiv.innerHTML = '<p style="color: #94a3b8; text-align: center; padding: 40px 0;">Nenhuma consulta recusada.</p>';
        } else {
            let html = '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px;">';
            recusadas.forEach(c => {
                html += `
                    <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; flex-direction: column; gap: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div style="width: 40px; height: 40px; background: rgba(239, 68, 68, 0.1); color: #f87171; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <span style="background: rgba(239, 68, 68, 0.15); color: #f87171; padding: 4px 12px; border-radius: 20px; font-weight: 600; font-size: 12px;">Recusada</span>
                        </div>
                        <div>
                            <h4 style="font-size: 18px; font-weight: 600; color: #1e2a3a; margin-bottom: 8px;">${c.paciente}</h4>
                            <p style="color: #64748b; font-size: 14px; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-user-md" style="color: #6366f1;"></i> ${c.medico} (${c.especialidade})
                            </p>
                            <p style="color: #1e2a3a; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                                <i class="far fa-calendar-alt" style="color: #6366f1;"></i> ${new Date(c.data).toLocaleDateString('pt-BR')} às ${c.hora}
                            </p>
                            ${c.mensagem ? `<div style="margin-top: 10px; padding: 10px; background: #f8fafc; border-radius: 8px; font-size: 13px; color: #475569;"><strong>Mensagem:</strong> ${c.mensagem}</div>` : ''}
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            recusadasDiv.innerHTML = html;
        }
    };

    // Controle de abas
    window.switchTab = function(tab) {
        document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.style.color = '#64748b';
            btn.style.borderBottom = '3px solid transparent';
        });
        document.getElementById('lista-' + tab).style.display = 'block';
        const btn = document.getElementById('tab-' + tab);
        btn.style.color = '#851e32';
        btn.style.borderBottom = '3px solid #851e32';
    };

    // Inicializar a renderização ao carregar
    document.addEventListener('DOMContentLoaded', function() {
        // Garantir que apenas pendentes esteja visível
        document.getElementById('lista-pendentes').style.display = 'block';
        document.getElementById('lista-confirmadas').style.display = 'none';
        document.getElementById('lista-recusadas').style.display = 'none';
        window.renderConsultas();
    });
</script>