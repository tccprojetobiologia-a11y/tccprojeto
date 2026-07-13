<div style="margin-bottom: 30px;">
    <h3 style="font-size: 24px; font-weight: 600; color: #1e2a3a;">Pacientes</h3>
    <p style="color: #64748b; font-size: 14px; margin-top: 4px;">Visualize e gerencie as informações dos pacientes.</p>
</div>

<div style="display: grid; grid-template-columns: 350px 1fr; gap: 24px;">
    <!-- Lista de Pacientes -->
    <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); max-height: 600px; overflow-y: auto;">
        <h4 style="color: #1e2a3a; margin-bottom: 16px; font-size: 16px;">Lista de Pacientes</h4>
        <div id="lista-pacientes"></div>
    </div>
    
    <!-- Ficha do Paciente -->
    <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); max-height: 600px; overflow-y: auto;">
        <h4 style="color: #1e2a3a; margin-bottom: 16px; font-size: 16px;">Ficha do Paciente</h4>
        <div id="ficha-paciente">
            <p style="color: #94a3b8; text-align: center; padding: 40px 0;">Selecione um paciente para ver a ficha.</p>
        </div>
    </div>
</div>

<script>
    window.renderPacientes = async function() {
        try {
            const response = await fetch('../api/admin_api.php?action=get_pacientes');
            const pacientes = await response.json();
            
            if (pacientes.error) {
                document.getElementById('lista-pacientes').innerHTML = `<p style="color: #c00; text-align: center; padding: 20px 0;">Erro: ${pacientes.error}</p>`;
                return;
            }
            
            const listaDiv = document.getElementById('lista-pacientes');
            
            if (!pacientes || pacientes.length === 0) {
                listaDiv.innerHTML = '<p style="color: #94a3b8; text-align: center; padding: 20px 0;">Nenhum paciente cadastrado.</p>';
                return;
            }
            
            let html = '';
            pacientes.forEach((p, index) => {
                const nome = p.nome_criptografado || 'Sem nome';
                html += `
                    <div onclick="selecionarPaciente('${p.id_usuario}', ${index})" style="padding: 12px 16px; margin-bottom: 8px; background: #f8fafc; border-radius: 10px; cursor: pointer; transition: all 0.3s; border-left: 3px solid transparent;" 
                        id="paciente-${index}">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 40px; height: 40px; background: #851e32; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 16px;">
                                ${nome.charAt(0)}
                            </div>
                            <div>
                                <div style="font-weight: 600; color: #1e2a3a;">${nome}</div>
                                <div style="font-size: 12px; color: #64748b;">${p.email || 'Sem email'}</div>
                                <div style="font-size: 11px; color: #94a3b8;">${p.idade || '?'} anos • ${p.total_consultas || 0} consultas</div>
                            </div>
                        </div>
                    </div>
                `;
            });
            listaDiv.innerHTML = html;
        } catch (error) {
            console.error('Erro ao carregar pacientes:', error);
            document.getElementById('lista-pacientes').innerHTML = '<p style="color: #c00; text-align: center; padding: 20px 0;">Erro ao carregar pacientes.</p>';
        }
    };

    window.selecionarPaciente = async function(id, index) {
        // Destacar selecionado
        document.querySelectorAll('#lista-pacientes > div').forEach(el => {
            el.style.borderLeftColor = 'transparent';
            el.style.background = '#f8fafc';
        });
        const selectedEl = document.getElementById(`paciente-${index}`);
        if (selectedEl) {
            selectedEl.style.borderLeftColor = '#851e32';
            selectedEl.style.background = '#f1f5f9';
        }
        
        try {
            const response = await fetch(`../api/admin_api.php?action=get_paciente_detalhes&id=${id}`);
            const data = await response.json();
            
            if (data.error) {
                document.getElementById('ficha-paciente').innerHTML = `<p style="color: #c00; text-align: center; padding: 20px 0;">${data.error}</p>`;
                return;
            }
            
            const p = data.paciente;
            const historico = data.historico || [];
            const exames = data.exames || [];
            const consultas = data.consultas || [];
            
            const fichaDiv = document.getElementById('ficha-paciente');
            fichaDiv.innerHTML = `
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <!-- Dados Pessoais -->
                    <div style="padding-bottom: 16px; border-bottom: 2px solid #f0f0f0;">
                        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 12px;">
                            <div style="width: 60px; height: 60px; background: #851e32; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 24px;">
                                ${p.nome_criptografado ? p.nome_criptografado.charAt(0) : '?'}
                            </div>
                            <div>
                                <h3 style="color: #1e2a3a; font-size: 20px;">${p.nome_criptografado || 'Sem nome'}</h3>
                                <p style="color: #64748b; font-size: 14px;">${p.email || 'Sem email'}</p>
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
                            <div style="background: #f8fafc; padding: 10px; border-radius: 8px;">
                                <div style="font-size: 11px; color: #94a3b8;">Idade</div>
                                <div style="font-weight: 600; color: #1e2a3a; font-size: 14px;">${p.idade || 'N/A'} anos</div>
                            </div>
                            <div style="background: #f8fafc; padding: 10px; border-radius: 8px;">
                                <div style="font-size: 11px; color: #94a3b8;">Peso</div>
                                <div style="font-weight: 600; color: #1e2a3a; font-size: 14px;">${p.peso ? p.peso + ' kg' : 'N/A'}</div>
                            </div>
                            <div style="background: #f8fafc; padding: 10px; border-radius: 8px;">
                                <div style="font-size: 11px; color: #94a3b8;">Altura</div>
                                <div style="font-weight: 600; color: #1e2a3a; font-size: 14px;">${p.altura ? p.altura + ' m' : 'N/A'}</div>
                            </div>
                        </div>
                        <div style="margin-top: 8px; font-size: 12px; color: #64748b;">
                            Sexo: ${p.sexo_biologico === 'M' ? 'Masculino' : p.sexo_biologico === 'F' ? 'Feminino' : 'N/A'}
                        </div>
                    </div>
                    
                    <!-- Consultas Agendadas -->
                    <div>
                        <h5 style="color: #1e2a3a; font-size: 14px; margin-bottom: 8px;">Consultas</h5>
                        ${consultas.length > 0 ? `
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                ${consultas.slice(0, 5).map(c => `
                                    <div style="background: #f8fafc; padding: 10px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                                        <div>
                                            <div style="font-weight: 500; font-size: 13px; color: #1e2a3a;">${c.nome_medico}</div>
                                            <div style="font-size: 12px; color: #64748b;">${formatarData(c.data_consulta)} às ${c.hora_consulta}</div>
                                        </div>
                                        <span style="background: ${c.status === 'Confirmada' ? '#4ade80' : c.status === 'Pendente' ? '#fbbf24' : '#f87171'}; color: white; padding: 2px 10px; border-radius: 12px; font-size: 11px; font-weight: 600;">${c.status}</span>
                                    </div>
                                `).join('')}
                                ${consultas.length > 5 ? `<p style="font-size: 12px; color: #94a3b8;">+ ${consultas.length - 5} outras consultas</p>` : ''}
                            </div>
                        ` : `<p style="color: #94a3b8; font-size: 13px;">Nenhuma consulta registrada.</p>`}
                    </div>
                    
                    <!-- Exames -->
                    <div>
                        <h5 style="color: #1e2a3a; font-size: 14px; margin-bottom: 8px;">Exames</h5>
                        ${exames.length > 0 ? `
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                ${exames.slice(0, 5).map(e => `
                                    <div style="background: #f8fafc; padding: 10px; border-radius: 8px;">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <div style="font-weight: 500; font-size: 13px; color: #1e2a3a;">${e.tipo_exame}</div>
                                            <div style="font-size: 11px; color: #64748b;">${formatarData(e.data_exame)}</div>
                                        </div>
                                        ${e.resultado ? `<div style="font-size: 12px; color: #475569; margin-top: 4px;">${e.resultado}</div>` : ''}
                                    </div>
                                `).join('')}
                                ${exames.length > 5 ? `<p style="font-size: 12px; color: #94a3b8;">+ ${exames.length - 5} outros exames</p>` : ''}
                            </div>
                        ` : `<p style="color: #94a3b8; font-size: 13px;">Nenhum exame cadastrado.</p>`}
                    </div>
                    
                    <!-- Histórico Médico -->
                    <div>
                        <h5 style="color: #1e2a3a; font-size: 14px; margin-bottom: 8px;">Histórico Médico</h5>
                        ${historico.length > 0 ? `
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                ${historico.slice(0, 5).map(h => `
                                    <div style="background: #f8fafc; padding: 10px; border-radius: 8px; border-left: 3px solid #851e32;">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <span style="font-weight: 600; font-size: 12px; color: #851e32;">${h.tipo}</span>
                                            <span style="font-size: 11px; color: #64748b;">${formatarData(h.data_registro)}</span>
                                        </div>
                                        <div style="font-size: 13px; color: #1e2a3a; margin-top: 4px;">${h.descricao}</div>
                                        ${h.profissional_responsavel ? `<div style="font-size: 11px; color: #64748b; margin-top: 4px;">Responsável: ${h.profissional_responsavel}</div>` : ''}
                                    </div>
                                `).join('')}
                                ${historico.length > 5 ? `<p style="font-size: 12px; color: #94a3b8;">+ ${historico.length - 5} outros registros</p>` : ''}
                            </div>
                        ` : `<p style="color: #94a3b8; font-size: 13px;">Nenhum histórico médico cadastrado.</p>`}
                    </div>
                </div>
            `;
        } catch (error) {
            console.error('Erro ao carregar detalhes do paciente:', error);
            document.getElementById('ficha-paciente').innerHTML = '<p style="color: #c00; text-align: center; padding: 20px 0;">Erro ao carregar detalhes do paciente.</p>';
        }
    };

    function formatarData(data) {
        if (!data) return 'N/A';
        const d = new Date(data + 'T00:00:00');
        return d.toLocaleDateString('pt-BR');
    }

    // Inicializar
    document.addEventListener('DOMContentLoaded', function() {
        window.renderPacientes();
    });
</script>