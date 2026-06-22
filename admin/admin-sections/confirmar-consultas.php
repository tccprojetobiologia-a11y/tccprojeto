<h3 style="margin-bottom:20px; font-weight: 600; color: #2c3e50;">Solicitações de Consultas</h3>

<div class="stats-grid">
    <div class="stat-card" style="flex-direction: column; align-items: flex-start; gap: 10px; padding: 20px;" id="card-1">
        <div style="display: flex; justify-content: space-between; width: 100%; align-items: center;">
            <span style="background:#fff3e0; color:#ff9800; padding:4px 8px; border-radius:4px; font-weight:bold; font-size:12px;">存放 Aguardando</span>
        </div>
        <div>
            <h4 style="margin: 10px 0 5px 0; font-size: 16px; font-weight: 600;">Paciente: Carlos Silva</h4>
            <p style="font-size:14px; color:#555; margin: 4px 0;"><i class="fas fa-user-md"></i> Dr. Roberto Mendes (Cardiologia)</p>
            <p style="font-size:14px; color:#333; margin: 4px 0;"><i class="far fa-calendar-alt"></i> 22/06/2026 - 14:30</p>
        </div>
        <div style="display:flex; gap:10px; margin-top:10px; width:100%; border-top:1px solid #eee; padding-top:10px;">
            <button onclick="alert('Consulta de Carlos Silva confirmada!')" style="background:#2e7d32; color:white; border:none; padding:8px 15px; border-radius:6px; cursor:pointer; font-weight: 500;">Confirmar</button>
            <button onclick="document.getElementById('card-1').style.opacity='0.4'; alert('Consulta recusada.');" style="background:#c62828; color:white; border:none; padding:8px 15px; border-radius:6px; cursor:pointer; font-weight: 500;">Recusar</button>
        </div>
    </div>

    <div class="stat-card" style="flex-direction: column; align-items: flex-start; gap: 10px; padding: 20px;" id="card-2">
        <div style="display: flex; justify-content: space-between; width: 100%; align-items: center;">
            <span style="background:#fff3e0; color:#ff9800; padding:4px 8px; border-radius:4px; font-weight:bold; font-size:12px;">存放 Aguardando</span>
        </div>
        <div>
            <h4 style="margin: 10px 0 5px 0; font-size: 16px; font-weight: 600;">Paciente: Maria Oliveira</h4>
            <p style="font-size:14px; color:#555; margin: 4px 0;"><i class="fas fa-user-md"></i> Dra. Aline Costa (Arritmologia)</p>
            <p style="font-size:14px; color:#333; margin: 4px 0;"><i class="far fa-calendar-alt"></i> 24/06/2026 - 09:00</p>
        </div>
        <div style="display:flex; gap:10px; margin-top:10px; width:100%; border-top:1px solid #eee; padding-top:10px;">
            <button onclick="alert('Consulta de Maria Oliveira confirmada!')" style="background:#2e7d32; color:white; border:none; padding:8px 15px; border-radius:6px; cursor:pointer; font-weight: 500;">Confirmar</button>
            <button onclick="document.getElementById('card-2').style.opacity='0.4'; alert('Consulta recusada.');" style="background:#c62828; color:white; border:none; padding:8px 15px; border-radius:6px; cursor:pointer; font-weight: 500;">Recusar</button>
        </div>
    </div>
</div>