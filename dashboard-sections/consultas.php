<?php
if (!function_exists('getConsultasHtml')) {
    function getConsultasHtml()
    {
        // Exemplo mínimo de HTML para a seção de consultas
        $html = '<div class="info-card"><h3><i class="fas fa-calendar-check"></i> Minhas Consultas</h3>';
        $html .= '<div style="padding:12px 0; border-bottom:1px solid #f0f0f0;"><strong>15/04/2024 - 10:00</strong> - Consulta com Dr. Carlos</div>';
        $html .= '<div style="padding:12px 0;"><strong>22/04/2024 - 08:30</strong> - Exame de rotina</div>';
        $html .= '</div>';
        return $html;
    }
}

?>
