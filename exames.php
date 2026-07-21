<?php
if (!function_exists('getExamesHtml')) {
    function getExamesHtml()
    {
        // Exemplo mínimo de HTML para a seção de exames
        $html = '<div class="info-card"><h3><i class="fas fa-flask"></i> Meus Exames</h3>';
        $html .= '<div style="padding:12px 0; border-bottom:1px solid #f0f0f0;"><strong>Hemograma</strong> - 01/04/2024 - Resultado: Normal</div>';
        $html .= '<div style="padding:12px 0;"><strong>Colesterol</strong> - 01/04/2024 - Resultado: 180 mg/dL</div>';
        $html .= '</div>';
        return $html;
    }
}

?>
