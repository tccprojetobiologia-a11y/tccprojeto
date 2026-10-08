<?php
if (!function_exists('getExamesHtml')) {
    function getExamesHtml()
    {
        $exames = [
            [
                'nome' => 'Eletrocardiograma',
                'data' => '12/04/2026',
                'status' => 'Concluído',
                'tipo' => 'Cardiologia',
                'obs' => 'Sem alterações relevantes.',
                'cor' => '#e8f5e9',
                'texto' => '#2e7d32'
            ],
            [
                'nome' => 'Hemograma Completo',
                'data' => '08/04/2026',
                'status' => 'Concluído',
                'tipo' => 'Laboratório',
                'obs' => 'Dentro dos parâmetros esperados.',
                'cor' => '#e3f2fd',
                'texto' => '#1976d2'
            ],
            [
                'nome' => 'Holter 24h',
                'data' => '02/04/2026',
                'status' => 'Em análise',
                'tipo' => 'Monitoramento',
                'obs' => 'Laudo pendente do cardiologista.',
                'cor' => '#fff3e0',
                'texto' => '#ef6c00'
            ],
            [
                'nome' => 'Ecocardiograma',
                'data' => '26/03/2026',
                'status' => 'Concluído',
                'tipo' => 'Imagem',
                'obs' => 'Função ventricular preservada.',
                'cor' => '#f3e5f5',
                'texto' => '#7b1fa2'
            ]
        ];

        $cards = '';
        foreach ($exames as $exame) {
            $cards .= '<div style="background:#fff; border:1px solid #edf2f7; border-radius:18px; padding:18px; box-shadow:0 8px 18px rgba(15,23,42,0.04);">
                <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:12px;">
                    <div style="width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; background:' . $exame['cor'] . '; color:' . $exame['texto'] . '; font-size:20px;">
                        <i class="fas fa-file-medical"></i>
                    </div>
                    <span style="background:' . $exame['cor'] . '; color:' . $exame['texto'] . '; border-radius:999px; padding:7px 10px; font-size:11px; font-weight:700;">' . htmlspecialchars($exame['status']) . '</span>
                </div>
                <h4 style="margin:0 0 8px; font-size:18px; color:#1e2a3a;">' . htmlspecialchars($exame['nome']) . '</h4>
                <div style="display:flex; flex-wrap:wrap; gap:10px; margin-bottom:12px; font-size:12px; color:#64748b;">
                    <span style="background:#f8fafc; padding:5px 8px; border-radius:8px;">' . htmlspecialchars($exame['tipo']) . '</span>
                    <span style="background:#f8fafc; padding:5px 8px; border-radius:8px;">' . htmlspecialchars($exame['data']) . '</span>
                </div>
                <p style="margin:0; color:#475569; line-height:1.6; font-size:14px;">' . htmlspecialchars($exame['obs']) . '</p>
            </div>';
        }

        $historico = '<div style="display:flex; flex-direction:column; gap:12px; margin-top:10px;">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; padding:14px 16px; border:1px solid #eef2f7; border-radius:12px; background:#f8fafc;">
                <div>
                    <div style="font-weight:700; color:#1e2a3a;">Exame de rotina</div>
                    <div style="font-size:12px; color:#64748b;">Entrega: 15/04/2026</div>
                </div>
                <span style="color:#2e7d32; font-weight:700;">Disponível</span>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; padding:14px 16px; border:1px solid #eef2f7; border-radius:12px; background:#f8fafc;">
                <div>
                    <div style="font-weight:700; color:#1e2a3a;">Pressão arterial</div>
                    <div style="font-size:12px; color:#64748b;">Acompanhamento de rotina</div>
                </div>
                <span style="color:#1d4ed8; font-weight:700;">Em dia</span>
            </div>
        </div>';

        return <<<HTML
        <div style="display:flex; flex-direction:column; gap:20px;">
            <div class="info-card" style="margin-bottom:0;">
                <h3><i class="fas fa-flask"></i> Exames</h3>
                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
                    {$cards}
                </div>
            </div>
            <div class="info-card" style="margin-bottom:0;">
                <h3><i class="fas fa-clipboard-list"></i> Histórico</h3>
                {$historico}
            </div>
        </div>
HTML;
    }
}
?>
