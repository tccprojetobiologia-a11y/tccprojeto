<div class="section-header" style="margin-bottom: 25px;">
    <h3 style="font-size: 22px; font-weight: 600; color: #2c3e50; margin: 0;">Solicitações Pendentes</h3>
    <p style="color: #7f8c8d; font-size: 14px; margin: 5px 0 0 0;">Valide os agendamentos realizados pelos pacientes.</p>
</div>

<div class="stats-grid">
    <div class="stat-card" id="card-1" style="display: flex; flex-direction: column; align-items: flex-start; gap: 12px; padding: 24px; position: relative;">
        <div style="display: flex; justify-content: space-between; width: 100%; align-items: center;">
            <div class="stat-icon" style="margin: 0; background: #fff3e0; color: #ff9800;"><i class="fas fa-clock"></i></div>
            <span id="status-1" style="background:#fff3e0; color:#ff9800; padding:4px 10px; border-radius:5px; font-weight:600; font-size:12px;">Aguardando</span>
        </div>
        <div style="width: 100%;">
            <h3 style="margin: 5px 0; font-size: 18px; font-weight: 600; color: #2c3e50;">Carlos Silva</h3>
            <p style="margin: 4px 0; font-size: 14px; color: #555;"><i class="fas fa-user-md" style="color: #851e32; margin-right: 5px;"></i> Dr. Roberto Mendes (Cardiologia)</p>
            <p style="margin: 4px 0; font-size: 14px; color: #333; font-weight: 500;"><i class="far fa-calendar-alt" style="margin-right: 5px;"></i> 22/06/2026 às 14:30</p>
        </div>
        <div id="actions-1" style="display: flex; gap: 10px; width: 100%; margin-top: 10px; border-top: 1px solid #f1f1f1; padding-top: 15px;">
            <button onclick="aprovarConsulta(1, 'Carlos Silva')" style="flex: 1; background: #2e7d32; color: white; border: none; padding: 10px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 14px;">Confirmar</button>
            <button onclick="rejeitarConsulta(1, 'Carlos Silva')" style="flex: 1; background: #c62828; color: white; border: none; padding: 10px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 14px;">Recusar</button>
        </div>
    </div>

    <div class="stat-card" id="card-2" style="display: flex; flex-direction: column; align-items: flex-start; gap: 12px; padding: 24px; position: relative;">
        <div style="display: flex; justify-content: space-between; width: 100%; align-items: center;">
            <div class="stat-icon" style="margin: 0; background: #fff3e0; color: #ff9800;"><i class="fas fa-clock"></i></div>
            <span id="status-2" style="background:#fff3e0; color:#ff9800; padding:4px 10px; border-radius:5px; font-weight:600; font-size:12px;">Aguardando</span>
        </div>
        <div style="width: 100%;">
            <h3 style="margin: 5px 0; font-size: 18px; font-weight: 600; color: #2c3e50;">Maria Oliveira</h3>
            <p style="margin: 4px 0; font-size: 14px; color: #555;"><i class="fas fa-user-md" style="color: #851e32; margin-right: 5px;"></i> Dra. Aline Costa (Arritmologia)</p>
            <p style="margin: 4px 0; font-size: 14px; color: #333; font-weight: 500;"><i class="far fa-calendar-alt" style="margin-right: 5px;"></i> 24/06/2026 às 09:00</p>
        </div>
        <div id="actions-2" style="display: flex; gap: 10px; width: 100%; margin-top: 10px; border-top: 1px solid #f1f1f1; padding-top: 15px;">
            <button onclick="aprovarConsulta(2, 'Maria Oliveira')" style="flex: 1; background: #2e7d32; color: white; border: none; padding: 10px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 14px;">Confirmar</button>
            <button onclick="rejeitarConsulta(2, 'Maria Oliveira')" style="flex: 1; background: #c62828; color: white; border: none; padding: 10px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 14px;">Recusar</button>
        </div>
    </div>
</div>