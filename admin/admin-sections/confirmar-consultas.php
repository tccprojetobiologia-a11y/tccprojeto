<div style="margin-bottom: 30px;">
    <h3 style="font-size: 24px; font-weight: 600; color: #1e2a3a;">Solicitações Pendentes</h3>
    <p style="color: #64748b; font-size: 14px; margin-top: 4px;">Gerencie as novas consultas solicitadas no sistema.</p>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px;">
    <!-- Card 1 -->
    <div id="card-1" style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; flex-direction: column; gap: 16px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div style="width: 40px; height: 40px; background: rgba(99, 102, 241, 0.1); color: #818cf8; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                <i class="fas fa-clock"></i>
            </div>
            <span id="status-1" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24; padding: 4px 12px; border-radius: 20px; font-weight: 600; font-size: 12px;">Aguardando</span>
        </div>
        <div>
            <h4 style="font-size: 18px; font-weight: 600; color: #1e2a3a; margin-bottom: 8px;">Carlos Silva</h4>
            <p style="color: #64748b; font-size: 14px; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-user-md" style="color: #6366f1;"></i> Dr. Roberto Mendes (Cardiologia)
            </p>
            <p style="color: #1e2a3a; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                <i class="far fa-calendar-alt" style="color: #6366f1;"></i> 22/06/2026 às 14:30
            </p>
        </div>
        <div id="actions-1" style="display: flex; gap: 12px; margin-top: 8px; border-top: 1px solid #f0f0f0; padding-top: 16px;">
            <button onclick="aprovarConsulta(1, 'Carlos Silva')" style="flex: 1; background: #851e32; color: white; border: none; padding: 12px; border-radius: 10px; font-weight: 600; cursor: pointer; font-size: 14px; transition: background 0.2s;">Confirmar</button>
            <button onclick="rejeitarConsulta(1, 'Carlos Silva')" style="flex: 1; background: transparent; color: #ef4444; border: 1px solid #ef4444; padding: 12px; border-radius: 10px; font-weight: 600; cursor: pointer; font-size: 14px; transition: background 0.2s;">Recusar</button>
        </div>
    </div>

    <!-- Card 2 -->
    <div id="card-2" style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; flex-direction: column; gap: 16px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div style="width: 40px; height: 40px; background: rgba(99, 102, 241, 0.1); color: #818cf8; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                <i class="fas fa-clock"></i>
            </div>
            <span id="status-2" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24; padding: 4px 12px; border-radius: 20px; font-weight: 600; font-size: 12px;">Aguardando</span>
        </div>
        <div>
            <h4 style="font-size: 18px; font-weight: 600; color: #1e2a3a; margin-bottom: 8px;">Maria Oliveira</h4>
            <p style="color: #64748b; font-size: 14px; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-user-md" style="color: #6366f1;"></i> Dra. Aline Costa (Arritmologia)
            </p>
            <p style="color: #1e2a3a; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                <i class="far fa-calendar-alt" style="color: #6366f1;"></i> 24/06/2026 às 09:00
            </p>
        </div>
        <div id="actions-2" style="display: flex; gap: 12px; margin-top: 8px; border-top: 1px solid #f0f0f0; padding-top: 16px;">
            <button onclick="aprovarConsulta(2, 'Maria Oliveira')" style="flex: 1; background: #851e32; color: white; border: none; padding: 12px; border-radius: 10px; font-weight: 600; cursor: pointer; font-size: 14px;">Confirmar</button>
            <button onclick="rejeitarConsulta(2, 'Maria Oliveira')" style="flex: 1; background: transparent; color: #ef4444; border: 1px solid #ef4444; padding: 12px; border-radius: 10px; font-weight: 600; cursor: pointer; font-size: 14px;">Recusar</button>
        </div>
    </div>
</div>