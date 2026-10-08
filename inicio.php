<?php
function getInicioHtml($user_name) {
    return <<<HTML
                <div class="monitoring-shell">
                    <div class="welcome-card" style="background: linear-gradient(135deg, rgba(98, 13, 28, 0.96) 0%, rgba(62, 8, 18, 0.98) 100%); color: white; padding: 20px 24px 18px; border-radius: 18px; margin-bottom: 22px; position: relative; overflow: hidden; box-shadow: 0 22px 40px rgba(81, 16, 30, 0.12);">
                        <div style="position:absolute; inset:0; background: radial-gradient(circle at 72% 30%, rgba(255,255,255,0.10), transparent 23%);"></div>
                        <div style="position: relative; z-index: 1; display: flex; align-items: center; justify-content: space-between; gap: 18px;">
                            <div style="flex:1; min-width:0;">
                                <div style="display:flex; align-items:center; gap:10px; margin-bottom: 8px;">
                                    <span style="width: 4px; height: 24px; background: rgba(255,255,255,0.9); border-radius: 12px; display:inline-block;"></span>
                                    <h2 style="display:inline-block; font-size: 2rem; margin:0; letter-spacing:-0.06em; font-weight:800;">Olá, {$user_name}!</h2>
                                </div>
                                <p style="margin: 0; color: rgba(255,255,255,0.86); font-size: 1rem; line-height: 1.5; max-width: 510px;">Cuidar de você e do seu coração começa com exames, consultas e orientação personalizadas.</p>
                            </div>
                            <div style="flex-shrink:0; display:flex; align-items:center; justify-content:center; width: 220px; height: 94px; position:relative;">
                                <svg viewBox="0 0 260 140" width="200" height="90" aria-label="Coração com estetoscópio" style="overflow:visible;">
                                    <g transform="translate(20,8)">
                                        <path d="M70 90 C 50 80, 20 70, 18 45 C 16 19, 40 10, 58 20 C 70 28, 74 34, 82 42 C 90 34, 94 28, 106 20 C 124 10, 148 19, 146 45 C 144 70, 114 80, 94 90" fill="rgba(255,255,255,0.96)" stroke="rgba(255,255,255,0.2)" stroke-width="2"/>
                                        <path d="M0 30 L42 30 M16 16 L16 44" stroke="rgba(255,255,255,0.94)" stroke-width="7" stroke-linecap="round"/>
                                        <path d="M42 30 C62 30, 70 16, 86 30" stroke="rgba(255,255,255,0.94)" stroke-width="7" fill="none" stroke-linecap="round"/>
                                        <path d="M122 30 L180 30" stroke="rgba(255,255,255,0.94)" stroke-width="7" stroke-linecap="round"/>
                                        <path d="M146 18 L146 42" stroke="rgba(255,255,255,0.94)" stroke-width="7" stroke-linecap="round"/>
                                        <path d="M106 30 Q132 42 162 30" stroke="rgba(255,255,255,0.94)" stroke-width="7" fill="none" stroke-linecap="round"/>
                                        <path d="M146 62 C154 54, 180 46, 183 56 C186 66, 178 74, 166 78" stroke="rgba(255,255,255,0.94)" stroke-width="4" fill="none" stroke-linecap="round"/>
                                        <path d="M108 68 L128 92" stroke="rgba(255,255,255,0.94)" stroke-width="4" stroke-linecap="round"/>
                                        <path d="M128 92 L154 92" stroke="rgba(255,255,255,0.94)" stroke-width="4" stroke-linecap="round"/>
                                    </g>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="stats-grid" style="display:grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 18px; margin-bottom: 22px;">
                        <div class="stat-card" style="background:#f8f5f5; border:1px solid rgba(120,26,43,0.08); border-radius:16px; padding:16px 18px; box-shadow:none;">
                            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
                                <div style="width:42px;height:42px;border-radius:12px;background:#f1e2e5;color:#7f1a2d;display:flex;align-items:center;justify-content:center;"><i class="fas fa-heartbeat"></i></div>
                                <i class="fas fa-chevron-right" style="color:#8b6a70; font-size:13px;"></i>
                            </div>
                            <div style="font-size: 2.1rem; font-weight:800; color:#1f1f23; letter-spacing:-0.04em;">12</div>
                            <div style="font-size:0.96rem; color:#6a5c60;">Registros de saúde</div>
                        </div>
                        <div class="stat-card" style="background:#f8f5f5; border:1px solid rgba(120,26,43,0.08); border-radius:16px; padding:16px 18px; box-shadow:none;">
                            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
                                <div style="width:42px;height:42px;border-radius:12px;background:#f1e2e5;color:#7f1a2d;display:flex;align-items:center;justify-content:center;"><i class="fas fa-wave-square"></i></div>
                                <i class="fas fa-chevron-right" style="color:#8b6a70; font-size:13px;"></i>
                            </div>
                            <div style="font-size: 2.1rem; font-weight:800; color:#1f1f23; letter-spacing:-0.04em;">72</div>
                            <div style="font-size:0.96rem; color:#6a5c60;">Batimentos/min</div>
                        </div>
                        <div class="stat-card" style="background:#f8f5f5; border:1px solid rgba(120,26,43,0.08); border-radius:16px; padding:16px 18px; box-shadow:none;">
                            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
                                <div style="width:42px;height:42px;border-radius:12px;background:#f1e2e5;color:#7f1a2d;display:flex;align-items:center;justify-content:center;"><i class="fas fa-calendar-days"></i></div>
                                <i class="fas fa-chevron-right" style="color:#8b6a70; font-size:13px;"></i>
                            </div>
                            <div style="font-size: 2.1rem; font-weight:800; color:#1f1f23; letter-spacing:-0.04em;">2</div>
                            <div style="font-size:0.96rem; color:#6a5c60;">Consultas agendadas</div>
                        </div>
                        <div class="stat-card" style="background:#f8f5f5; border:1px solid rgba(120,26,43,0.08); border-radius:16px; padding:16px 18px; box-shadow:none;">
                            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
                                <div style="width:42px;height:42px;border-radius:12px;background:#f1e2e5;color:#7f1a2d;display:flex;align-items:center;justify-content:center;"><i class="fas fa-trophy"></i></div>
                                <i class="fas fa-chevron-right" style="color:#8b6a70; font-size:13px;"></i>
                            </div>
                            <div style="font-size: 2.1rem; font-weight:800; color:#1f1f23; letter-spacing:-0.04em;">85%</div>
                            <div style="font-size:0.96rem; color:#6a5c60;">Meta de saúde</div>
                        </div>
                    </div>

                    <div class="info-card" style="background:#f8f5f5; border:1px solid rgba(120,26,43,0.08); border-radius:16px; padding:18px 18px 12px; margin-bottom: 18px; box-shadow:none;">
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 14px;">
                            <h3 style="margin:0; font-size: 1.05rem; display:flex; align-items:center; gap:8px; color:#371b22; font-weight:800;"><i class="fas fa-heartbeat" style="color:#7f1a2d;"></i> Últimos Registros</h3>
                            <span style="font-size: 0.82rem; font-weight:700; color:#8a6770; cursor:pointer;">Ver todos <i class="fas fa-chevron-right" style="font-size:11px;"></i></span>
                        </div>
                        <div style="display:grid; gap:0; background:transparent;">
                            <div style="display:grid; grid-template-columns: 1.5fr 1fr auto; gap:12px; padding: 12px 0; border-bottom: 1px solid #f1e4e6; align-items:center;">
                                <div style="display:flex; align-items:center; gap:12px; color:#341a22; font-weight:600;"><span style="display:inline-flex; width:30px; height:30px; border-radius:50%; background:#f7e5e8; align-items:center; justify-content:center; color:#8d1e36; font-size:15px;"><i class="fas fa-heart" style="font-size:14px;"></i></span>Pressão Arterial</div>
                                <div style="font-weight:700; color:#1d1d21; text-align:center;">120/80 mmHg</div>
                                <div style="padding: 5px 10px; border-radius: 999px; background: rgba(110,180,120,0.12); color:#3d8f51; font-weight:700; font-size: 0.76rem; text-align:center;">Normal</div>
                            </div>
                            <div style="display:grid; grid-template-columns: 1.5fr 1fr auto; gap:12px; padding: 12px 0; border-bottom: 1px solid #f1e4e6; align-items:center;">
                                <div style="display:flex; align-items:center; gap:12px; color:#341a22; font-weight:600;"><span style="display:inline-flex; width:30px; height:30px; border-radius:50%; background:#f7e5e8; align-items:center; justify-content:center; color:#8d1e36; font-size:15px;"><i class="fas fa-droplet" style="font-size:13px;"></i></span>Colesterol Total</div>
                                <div style="font-weight:700; color:#1d1d21; text-align:center;">180 mg/dL</div>
                                <div style="padding: 5px 10px; border-radius: 999px; background: rgba(110,180,120,0.12); color:#3d8f51; font-weight:700; font-size: 0.76rem; text-align:center;">Normal</div>
                            </div>
                            <div style="display:grid; grid-template-columns: 1.5fr 1fr auto; gap:12px; padding: 12px 0; align-items:center;">
                                <div style="display:flex; align-items:center; gap:12px; color:#341a22; font-weight:600;"><span style="display:inline-flex; width:30px; height:30px; border-radius:50%; background:#f7e5e8; align-items:center; justify-content:center; color:#8d1e36; font-size:15px;"><i class="fas fa-vial" style="font-size:13px;"></i></span>Glicemia</div>
                                <div style="font-weight:700; color:#1d1d21; text-align:center;">95 mg/dL</div>
                                <div style="padding: 5px 10px; border-radius: 999px; background: rgba(110,180,120,0.12); color:#3d8f51; font-weight:700; font-size: 0.76rem; text-align:center;">Normal</div>
                            </div>
                        </div>
                    </div>

                    <div class="info-card" style="background:#f8f5f5; border:1px solid rgba(120,26,43,0.08); border-radius:16px; padding:18px 18px 10px; box-shadow:none;">
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 14px;">
                            <h3 style="margin:0; font-size: 1.05rem; display:flex; align-items:center; gap:8px; color:#371b22; font-weight:800;"><i class="fas fa-calendar-alt" style="color:#7f1a2d;"></i> Agenda</h3>
                            <span style="font-size: 0.82rem; font-weight:700; color:#8a6770; cursor:pointer;">Ver todas <i class="fas fa-chevron-right" style="font-size:11px;"></i></span>
                        </div>
                        <div style="display:grid; gap:12px;">
                            <div style="display:grid; grid-template-columns: 66px 1fr auto; align-items:center; gap:14px; padding: 8px 0;">
                                <div style="text-align:center; border-radius:12px; background:#f6eaec; padding:10px 0; color:#7d1b2d;">
                                    <div style="font-size: 1.5rem; font-weight:800; line-height:1;">15</div>
                                    <div style="font-size: 0.7rem; letter-spacing:0.08em; font-weight:700; margin-top:4px;">ABR</div>
                                </div>
                                <div>
                                    <div style="font-weight:700; color:#2d2024;">Cardiologista - Dr. Carlos</div>
                                    <div style="font-size:0.82rem; color:#827176; margin-top:4px;">10:00 - Consulta presencial</div>
                                </div>
                                <div style="padding:5px 10px; border-radius:999px; background:rgba(115,185,120,0.12); color:#3d8f51; font-size:0.74rem; font-weight:700;">Confirmada</div>
                            </div>
                            <div style="display:grid; grid-template-columns: 66px 1fr auto; align-items:center; gap:14px; padding: 8px 0;">
                                <div style="text-align:center; border-radius:12px; background:#f6eaec; padding:10px 0; color:#7d1b2d;">
                                    <div style="font-size: 1.5rem; font-weight:800; line-height:1;">22</div>
                                    <div style="font-size: 0.7rem; letter-spacing:0.08em; font-weight:700; margin-top:4px;">ABR</div>
                                </div>
                                <div>
                                    <div style="font-weight:700; color:#2d2024;">Exame de Rotina</div>
                                    <div style="font-size:0.82rem; color:#827176; margin-top:4px;">08:30 - Laboratório</div>
                                </div>
                                <div style="padding:5px 10px; border-radius:999px; background:rgba(255,195,66,0.14); color:#b77812; font-size:0.74rem; font-weight:700;">Pendente</div>
                            </div>
                        </div>
                    </div>
                </div>
HTML;
}
