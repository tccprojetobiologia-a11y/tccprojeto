<?php
if (!function_exists('getExamesHtml')) {
    function getExamesHtml()
    {
        $html = '<div class="info-card">';
        $html .= '<h3><i class="fas fa-flask"></i> Exames</h3>';
        $html .= '<p style="color:#64748b; margin-bottom:16px;">Nenhum exame cadastrado no momento.</p>';
        $html .= '<div style="padding:18px; border:1px dashed #e2e8f0; border-radius:12px; background:#f8fafc; color:#64748b; text-align:center;">Você ainda não possui exames registrados no sistema.</div>';
        $html .= '</div>';
        return $html;
    }
}

?>
