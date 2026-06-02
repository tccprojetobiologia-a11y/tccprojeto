<?php
function getInicioHtml($user_name) {
    return <<<HTML
                <div class="welcome-card" style="background: linear-gradient(135deg, #8b2a3e 0%, #5a1e2c 100%); color: white; padding: 30px; border-radius: 20px; margin-bottom: 30px;">
                    <h2>Bem-vindo de volta, {$user_name}! 👋</h2>
                    <p>Monitore sua saúde cardiológica em tempo real e mantenha seus exames em dia.</p>
                </div>
                <div class="stats-grid">
                    <div class="stat-card"><div class="stat-icon"><i class="fas fa-chart-line"></i></div><h3>12</h3><p>Registros de saúde</p></div>
                    <div class="stat-card"><div class="stat-icon"><i class="fas fa-heartbeat"></i></div><h3>72</h3><p>Batimentos/min</p></div>
                    <div class="stat-card"><div class="stat-icon"><i class="fas fa-calendar-check"></i></div><h3>2</h3><p>Consultas agendadas</p></div>
                    <div class="stat-card"><div class="stat-icon"><i class="fas fa-trophy"></i></div><h3>85%</h3><p>Meta de saúde</p></div>
                </div>
                <div class="info-card"><h3><i class="fas fa-heart"></i> Últimos Registros</h3>
                    <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f0e0d8;"><span>Pressão Arterial</span><span><strong>120/80 mmHg</strong></span><span style="color: #2e7d32;">Normal</span></div>
                    <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f0e0d8;"><span>Colesterol Total</span><span><strong>180 mg/dL</strong></span><span style="color: #2e7d32;">Normal</span></div>
                    <div style="display: flex; justify-content: space-between; padding: 12px 0;"><span>Glicemia</span><span><strong>95 mg/dL</strong></span><span style="color: #2e7d32;">Normal</span></div>
                </div>
                <div class="info-card"><h3><i class="fas fa-calendar-alt"></i> Próximas Consultas</h3>
                    <div style="display: flex; align-items: center; gap: 15px; padding: 12px 0;"><div style="min-width: 50px; text-align: center;"><div style="font-size: 20px; font-weight: 700; color: #8b2a3e;">15</div><div style="font-size: 11px; color: #8a7569;">MAI</div></div><div style="flex: 1;"><div style="font-weight: 600;">Cardiologista - Dr. Carlos</div><div style="font-size: 12px; color: #8a7569;">10:00 - Consulta presencial</div></div><div style="font-size: 11px; background: #e8f5e9; color: #2e7d32; padding: 4px 10px; border-radius: 20px;">Confirmado</div></div>
                    <div style="display: flex; align-items: center; gap: 15px; padding: 12px 0;"><div style="min-width: 50px; text-align: center;"><div style="font-size: 20px; font-weight: 700; color: #8b2a3e;">22</div><div style="font-size: 11px; color: #8a7569;">MAI</div></div><div style="flex: 1;"><div style="font-weight: 600;">Exame de Rotina</div><div style="font-size: 12px; color: #8a7569;">08:30 - Laboratório</div></div><div style="font-size: 11px; background: #fff3e0; color: #ff9800; padding: 4px 10px; border-radius: 20px;">Pendente</div></div>
                </div>
HTML;
}
