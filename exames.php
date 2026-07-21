<?php
if (!function_exists('getExamesHtml')) {
    function getExamesHtml()
    {
        $html = '<div class="info-card">';
        $html .= '<h3><i class="fas fa-flask"></i> Meus Exames</h3>';
        $html .= '<p style="color:#64748b; margin-bottom:16px;">Acompanhe seus exames, resultados e datas de retorno em um só lugar.</p>';
        $html .= '<div style="display:grid; gap:12px;">';
        $html .= '<div style="padding:16px; border-radius:12px; background:#fdf8f9; border:1px solid #f0f0f0;"><div style="font-weight:700; color:#1e2a3a;">Hemograma completo</div><div style="margin-top:6px; color:#475569;">01/04/2024 • Status: Normal</div></div>';
        $html .= '<div style="padding:16px; border-radius:12px; background:#ffffff; border:1px solid #f0f0f0;"><div style="font-weight:700; color:#1e2a3a;">Colesterol total</div><div style="margin-top:6px; color:#475569;">01/04/2024 • Status: Levemente elevado</div></div>';
        $html .= '<div style="padding:16px; border-radius:12px; background:#f8fafc; border:1px solid #f0f0f0;"><div style="font-weight:700; color:#1e2a3a;">Eletrocardiograma</div><div style="margin-top:6px; color:#475569;">15/03/2024 • Status: Dentro do esperado</div></div>';
        $html .= '</div></div>';
        return $html;
    }
}

?>
