<?php
if (!function_exists('getDashboardSuporteHtml')) {
    function getDashboardSuporteHtml()
    {
        return <<<HTML
            <div class="info-card"><h3><i class="fas fa-headset"></i> Central de Suporte</h3><p>Estamos aqui para ajudar! Escolha uma opção abaixo:</p></div>
            <div class="support-card"><div style="display:flex; align-items:center; gap:15px;"><div style="width:50px; height:50px; background:#e8f5e9; border-radius:12px; display:flex; align-items:center; justify-content:center;"><i class="fas fa-phone" style="color:#2e7d32; font-size:24px;"></i></div><div><div style="font-weight:600;">Atendimento Telefônico</div><div style="font-size:12px; color:#666;">Segunda a Sexta, 8h às 18h</div><div style="font-size:14px; color:#851e32; margin-top:5px;">(11) 4002-8922</div></div></div></div>
            <div class="support-card"><div style="display:flex; align-items:center; gap:15px;"><div style="width:50px; height:50px; background:#e3f2fd; border-radius:12px; display:flex; align-items:center; justify-content:center;"><i class="fas fa-envelope" style="color:#1976d2; font-size:24px;"></i></div><div><div style="font-weight:600;">E-mail</div><div style="font-size:12px; color:#666;">Respondemos em até 24h</div><div style="font-size:14px; color:#851e32; margin-top:5px;">suporte@cardioweb.com</div></div></div></div>
            <div class="support-card"><div style="display:flex; align-items:center; gap:15px;"><div style="width:50px; height:50px; background:#fff3e0; border-radius:12px; display:flex; align-items:center; justify-content:center;"><i class="fab fa-whatsapp" style="color:#25d366; font-size:28px;"></i></div><div><div style="font-weight:600;">WhatsApp</div><div style="font-size:12px; color:#666;">Atendimento 24h</div><div style="font-size:14px; color:#851e32; margin-top:5px;">(11) 9 9999-9999</div></div></div></div>
            <div class="info-card"><h3><i class="fas fa-question-circle"></i> Perguntas Frequentes</h3>
                <details style="margin-bottom:10px;"><summary style="cursor:pointer; font-weight:500; padding:10px; background:#f8fafc; border-radius:8px;">Como agendar uma consulta?</summary><p style="padding:10px; color:#666;">Acesse o menu "Consultas" e clique em "Agendar nova consulta". Escolha o médico e horário disponível.</p></details>
                <details style="margin-bottom:10px;"><summary style="cursor:pointer; font-weight:500; padding:10px; background:#f8fafc; border-radius:8px;">Como acessar meus exames?</summary><p style="padding:10px; color:#666;">Os exames ficam disponíveis na seção "Exames" após liberação do médico responsável.</p></details>
            </div>
HTML;
    }
}
?>
