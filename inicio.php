<?php
function getInicioHtml($user_name) {
    return <<<HTML
    <div class="welcome-card" style="background: linear-gradient(135deg, #7d1628 0%, #4d0617 100%); color: white; padding: 26px 30px; border-radius: 20px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(77,6,23,0.25);">
        <div style="position:relative; z-index:1;">
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:8px;">
                <span style="display:inline-block; width:4px; height:26px; background:#f0c14b; border-radius:4px;"></span>
                <h2 style="font-size:1.9rem; font-weight:800; letter-spacing:-0.04em; margin:0;">Olá, {$user_name}!</h2>
            </div>
            <p style="margin:0; color:rgba(255,255,255,0.85); font-size:0.95rem; max-width:420px; line-height:1.5;">Cuidar de você é nossa essência. Aqui você encontra seus exames, consultas e orientações personalizadas.</p>
        </div>
        <div style="position:relative; z-index:1; text-align:right;">
            <svg width="140" height="100" viewBox="0 0 140 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M70 88 C40 75, 10 60, 10 35 C10 18, 25 10, 40 15 C55 20, 65 32, 70 40 C75 32, 85 20, 100 15 C115 10, 130 18, 130 35 C130 60, 100 75, 70 88 Z" fill="#c9243f" stroke="#fff" stroke-width="2"/>
                <path d="M15 50 L35 50 L42 38 L52 62 L60 50 L90 50" stroke="#fff" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <div style="font-size:0.75rem; color:rgba(255,255,255,0.7); font-style:italic; margin-top:6px;">Sua saúde em boas mãos</div>
        </div>
    </div>

    <div class="stats-grid" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:18px; margin-bottom:22px;">
        <div class="stat-card" style="background:#fff; border-radius:16px; padding:18px 20px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                <div style="width:44px; height:44px; border-radius:12px; background:#fdeef0; display:flex; align-items:center; justify-content:center; color:#8d1e36; font-size:20px;"><i class="fas fa-heartbeat"></i></div>
                <i class="fas fa-chevron-right" style="color:#c9b8bc; font-size:13px;"></i>
            </div>
            <div style="font-size:2rem; font-weight:800; color:#1f1f23; letter-spacing:-0.04em;">12</div>
            <div style="font-size:0.9rem; color:#6a5c60;">Registros de saúde</div>
        </div>
        <div class="stat-card" style="background:#fff; border-radius:16px; padding:18px 20px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                <div style="width:44px; height:44px; border-radius:12px; background:#fdeef0; display:flex; align-items:center; justify-content:center; color:#8d1e36; font-size:20px;"><i class="fas fa-heart"></i></div>
                <i class="fas fa-chevron-right" style="color:#c9b8bc; font-size:13px;"></i>
            </div>
            <div style="font-size:2rem; font-weight:800; color:#1f1f23; letter-spacing:-0.04em;">72</div>
            <div style="font-size:0.9rem; color:#6a5c60;">Batimentos/min</div>
        </div>
        <div class="stat-card" style="background:#fff; border-radius:16px; padding:18px 20px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                <div style="width:44px; height:44px; border-radius:12px; background:#fdeef0; display:flex; align-items:center; justify-content:center; color:#8d1e36; font-size:20px;"><i class="fas fa-calendar-check"></i></div>
                <i class="fas fa-chevron-right" style="color:#c9b8bc; font-size:13px;"></i>
            </div>
            <div style="font-size:2rem; font-weight:800; color:#1f1f23; letter-spacing:-0.04em;">2</div>
            <div style="font-size:0.9rem; color:#6a5c60;">Consultas agendadas</div>
        </div>
        <div class="stat-card" style="background:#fff; border-radius:16px; padding:18px 20px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                <div style="width:44px; height:44px; border-radius:12px; background:#fdeef0; display:flex; align-items:center; justify-content:center; color:#8d1e36; font-size:20px;"><i class="fas fa-trophy"></i></div>
                <i class="fas fa-chevron-right" style="color:#c9b8bc; font-size:13px;"></i>
            </div>
            <div style="font-size:2rem; font-weight:800; color:#1f1f23; letter-spacing:-0.04em;">85%</div>
            <div style="font-size:0.9rem; color:#6a5c60;">Meta de saúde</div>
        </div>
    </div>

    <div class="info-card" style="background:#fff; border-radius:16px; padding:22px 24px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;">
            <h3 style="margin:0; font-size:1.1rem; display:flex; align-items:center; gap:10px; color:#371b22; font-weight:800;"><i class="fas fa-heartbeat" style="color:#7f1a2d;"></i> Últimos Registros</h3>
            <span style="font-size:0.82rem; font-weight:700; color:#8a6770; cursor:pointer;">Ver todos <i class="fas fa-chevron-right" style="font-size:11px;"></i></span>
        </div>
        <div style="display:grid; gap:0;">
            <div style="display:grid; grid-template-columns:1.5fr 1fr auto; gap:12px; padding:14px 0; border-bottom:1px solid #f1e4e6; align-items:center;">
                <div style="display:flex; align-items:center; gap:12px; color:#341a22; font-weight:600;"><span style="display:inline-flex; width:32px; height:32px; border-radius:50%; background:#f7e5e8; align-items:center; justify-content:center; color:#8d1e36; font-size:14px;"><i class="fas fa-heart"></i></span>Pressão Arterial</div>
                <div style="font-weight:700; color:#1d1d21;">120/80 mmHg</div>
                <div style="padding:5px 12px; border-radius:999px; background:#e8f5e9; color:#3d8f51; font-weight:700; font-size:0.76rem;">Normal</div>
            </div>
            <div style="display:grid; grid-template-columns:1.5fr 1fr auto; gap:12px; padding:14px 0; border-bottom:1px solid #f1e4e6; align-items:center;">
                <div style="display:flex; align-items:center; gap:12px; color:#341a22; font-weight:600;"><span style="display:inline-flex; width:32px; height:32px; border-radius:50%; background:#f7e5e8; align-items:center; justify-content:center; color:#8d1e36; font-size:14px;"><i class="fas fa-droplet"></i></span>Colesterol Total</div>
                <div style="font-weight:700; color:#1d1d21;">180 mg/dL</div>
                <div style="padding:5px 12px; border-radius:999px; background:#e8f5e9; color:#3d8f51; font-weight:700; font-size:0.76rem;">Normal</div>
            </div>
            <div style="display:grid; grid-template-columns:1.5fr 1fr auto; gap:12px; padding:14px 0; align-items:center;">
                <div style="display:flex; align-items:center; gap:12px; color:#341a22; font-weight:600;"><span style="display:inline-flex; width:32px; height:32px; border-radius:50%; background:#f7e5e8; align-items:center; justify-content:center; color:#8d1e36; font-size:14px;"><i class="fas fa-vial"></i></span>Glicemia</div>
                <div style="font-weight:700; color:#1d1d21;">95 mg/dL</div>
                <div style="padding:5px 12px; border-radius:999px; background:#e8f5e9; color:#3d8f51; font-weight:700; font-size:0.76rem;">Normal</div>
            </div>
        </div>
    </div>

    <div class="info-card" style="background:#fff; border-radius:16px; padding:22px 24px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;">
            <h3 style="margin:0; font-size:1.1rem; display:flex; align-items:center; gap:10px; color:#371b22; font-weight:800;"><i class="fas fa-calendar-alt" style="color:#7f1a2d;"></i> Agenda</h3>
            <span style="font-size:0.82rem; font-weight:700; color:#8a6770; cursor:pointer;">Ver todas <i class="fas fa-chevron-right" style="font-size:11px;"></i></span>
        </div>
        <div style="display:grid; gap:14px;">
            <div style="display:grid; grid-template-columns:64px 1fr auto; align-items:center; gap:16px;">
                <div style="text-align:center; border-radius:12px; background:#f6eaec; padding:10px 0; color:#7d1b2d;">
                    <div style="font-size:1.4rem; font-weight:800; line-height:1;">15</div>
                    <div style="font-size:0.7rem; letter-spacing:0.08em; font-weight:700; margin-top:4px;">ABR</div>
                </div>
                <div>
                    <div style="font-weight:700; color:#2d2024;">Cardiologista - Dr. Carlos</div>
                    <div style="font-size:0.82rem; color:#827176; margin-top:4px;"><i class="fas fa-clock" style="font-size:11px;"></i> 10:00 - Consulta presencial</div>
                </div>
                <div style="padding:5px 12px; border-radius:999px; background:#e8f5e9; color:#3d8f51; font-size:0.74rem; font-weight:700;">Confirmada</div>
            </div>
            <div style="display:grid; grid-template-columns:64px 1fr auto; align-items:center; gap:16px;">
                <div style="text-align:center; border-radius:12px; background:#f6eaec; padding:10px 0; color:#7d1b2d;">
                    <div style="font-size:1.4rem; font-weight:800; line-height:1;">22</div>
                    <div style="font-size:0.7rem; letter-spacing:0.08em; font-weight:700; margin-top:4px;">ABR</div>
                </div>
                <div>
                    <div style="font-weight:700; color:#2d2024;">Exame de Rotina</div>
                    <div style="font-size:0.82rem; color:#827176; margin-top:4px;"><i class="fas fa-clock" style="font-size:11px;"></i> 08:30 - Laboratório</div>
                </div>
                <div style="padding:5px 12px; border-radius:999px; background:#fff3e0; color:#b77812; font-size:0.74rem; font-weight:700;">Agendada</div>
            </div>
        </div>
    </div>
HTML;
}