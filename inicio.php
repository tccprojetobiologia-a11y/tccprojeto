<?php
function getInicioHtml($user_name) {
    return <<<HTML
                <div class="welcome-card" style="background: linear-gradient(135deg, rgba(112, 18, 34, 0.96), rgba(82, 14, 24, 0.98)); color: white; padding: 30px 28px; border-radius: 20px; margin-bottom: 26px; min-height: 150px; position: relative; overflow: hidden; box-shadow: 0 20px 40px rgba(86, 12, 20, 0.18);">
                    <div style="position:absolute; inset:0; background: radial-gradient(circle at 70% 30%, rgba(255,255,255,0.16), transparent 28%);"></div>
                    <div style="position:relative; z-index:1; display:flex; align-items:center; justify-content:space-between; gap:28px;">
                        <div style="flex:1; max-width:70%;">
                            <div style="width: 4px; height: 34px; background: rgba(255,255,255,0.9); border-radius: 20px; display:inline-block; vertical-align:middle; margin-right: 12px;"></div>
                            <h2 style="display:inline-block; font-size: 1.8rem; margin:0; letter-spacing:-0.04em;">Olá, {$user_name}!</h2>
                            <p style="margin: 12px 0 0; color: rgba(255,255,255,0.88); font-size: 1rem; line-height: 1.6; max-width: 560px;">Cuide de você e do seu coração com exames, consultas e orientações personalizadas.</p>
                        </div>
                        <div style="flex:0 0 220px; display:flex; align-items:center; justify-content:center; position:relative; height:110px;">
                            <svg viewBox="0 0 220 120" width="180" height="90" aria-label="Coração e estetoscópio" style="overflow:visible;">
                                <g transform="translate(10,8)">
                                    <path d="M70 90 C 50 80, 20 70, 18 45 C 16 19, 40 10, 58 20 C 70 28, 74 34, 82 42 C 90 34, 94 28, 106 20 C 124 10, 148 19, 146 45 C 144 70, 114 80, 94 90" fill="rgba(255,255,255,0.92)" stroke="rgba(255,255,255,0.3)" stroke-width="2"/>
                                    <path d="M0 30 L42 30 M16 16 L16 44" stroke="rgba(255,255,255,0.9)" stroke-width="7" stroke-linecap="round"/>
                                    <path d="M42 30 C62 30, 70 16, 86 30" stroke="rgba(255,255,255,0.9)" stroke-width="7" fill="none" stroke-linecap="round"/>
                                    <path d="M122 30 L180 30" stroke="rgba(255,255,255,0.9)" stroke-width="7" stroke-linecap="round"/>
                                    <path d="M146 18 L146 42" stroke="rgba(255,255,255,0.9)" stroke-width="7" stroke-linecap="round"/>
                                    <path d="M106 30 Q132 42 162 30" stroke="rgba(255,255,255,0.9)" stroke-width="7" fill="none" stroke-linecap="round"/>
                                    <path d="M147 62 C155 54, 178 46, 182 56 C186 66, 178 74, 166 78" stroke="rgba(255,255,255,0.9)" stroke-width="4" fill="none" stroke-linecap="round"/>
                                    <path d="M100 68 L122 96" stroke="rgba(255,255,255,0.9)" stroke-width="4" stroke-linecap="round"/>
                                    <path d="M125 96 L150 96" stroke="rgba(255,255,255,0.9)" stroke-width="4" stroke-linecap="round"/>
                                </g>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="stats-grid">
                    <div class="stat-card"><div class="stat-icon"><i class="fas fa-heartbeat"></i></div><h3>12</h3><p>Registros de saúde</p></div>
                    <div class="stat-card"><div class="stat-icon"><i class="fas fa-chart-line"></i></div><h3>72</h3><p>Batimentos/min</p></div>
                    <div class="stat-card"><div class="stat-icon"><i class="fas fa-calendar-check"></i></div><h3>2</h3><p>Consultas agendadas</p></div>
                    <div class="stat-card"><div class="stat-icon"><i class="fas fa-trophy"></i></div><h3>85%</h3><p>Meta de saúde</p></div>
                </div>
                <div class="info-card"><h3><i class="fas fa-heart"></i> Últimos Registros</h3>
                    <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f0e0d8;"><span>Pressão Arterial</span><span><strong>120/80 mmHg</strong></span><span style="color: #2e7d32;">Normal</span></div>
                    <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f0e0d8;"><span>Colesterol Total</span><span><strong>180 mg/dL</strong></span><span style="color: #2e7d32;">Normal</span></div>
                    <div style="display: flex; justify-content: space-between; padding: 12px 0;"><span>Glicemia</span><span><strong>95 mg/dL</strong></span><span style="color: #2e7d32;">Normal</span></div>
                </div>
                <div class="info-card"><h3><i class="fas fa-calendar-alt"></i> Agenda</h3>
                    <div style="display: flex; align-items: center; gap: 15px; padding: 12px 0;"><div style="min-width: 50px; text-align: center;"><div style="font-size: 20px; font-weight: 700; color: #8b2a3e;">15</div><div style="font-size: 11px; color: #8a7569;">ABR</div></div><div style="flex: 1;"><div style="font-weight: 600;">Cardiologista - Dr. Carlos</div><div style="font-size: 12px; color: #8a7569;">10:00 - Consulta presencial</div></div><div style="font-size: 11px; background: #e8f5e9; color: #2e7d32; padding: 4px 10px; border-radius: 20px;">Confirmado</div></div>
                    <div style="display: flex; align-items: center; gap: 15px; padding: 12px 0;"><div style="min-width: 50px; text-align: center;"><div style="font-size: 20px; font-weight: 700; color: #8b2a3e;">22</div><div style="font-size: 11px; color: #8a7569;">ABR</div></div><div style="flex: 1;"><div style="font-weight: 600;">Exame de Rotina</div><div style="font-size: 12px; color: #8a7569;">08:30 - Laboratório</div></div><div style="font-size: 11px; background: #fff3e0; color: #ff9800; padding: 4px 10px; border-radius: 20px;">Pendente</div></div>
                </div>
HTML;
}
