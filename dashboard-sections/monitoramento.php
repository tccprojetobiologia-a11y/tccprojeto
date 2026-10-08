<?php
if (!function_exists('getDashboardMonitoramentoHtml')) {
    function getDashboardMonitoramentoHtml()
    {
        return <<<HTML
            <div class="info-card">
                <h3><i class="fas fa-heartbeat"></i> Monitoramento</h3>
                <p>Esta área será utilizada para registrar sinais vitais, evolução e gráficos cardíacos no próximo módulo.</p>
            </div>
HTML;
    }
}
?>
