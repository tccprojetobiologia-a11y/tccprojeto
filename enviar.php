<?php
// Importar as classes do PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/Exception.php';
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emailUsuario = $_POST['email'];
    $nomeUsuario = $_POST['nome'] ?? 'Usuário';
    
    $mail = new PHPMailer(true);

    try {
        // Configurações do Servidor (SMTP da sua hospedagem)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Altere para seu servidor SMTP
        $mail->SMTPAuth   = true;
        $mail->Username   = 'seu-email@gmail.com'; // Seu email
        $mail->Password   = 'sua-senha'; // Sua senha
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet = 'UTF-8';

        // --- E-MAIL 1: Para o Usuário ---
        $mail->setFrom('sistema@vidaviva.com', 'CardioWeb');
        $mail->addAddress($emailUsuario); 
        $mail->isHTML(true);
        $mail->Subject = 'Confirmação de Acesso - CardioWeb';
        $mail->Body    = "
            <html>
            <body>
                <h2>Olá $nomeUsuario!</h2>
                <p>Recebemos sua solicitação de acesso ao sistema CardioWeb.</p>
                <p>Seu login foi registrado com sucesso.</p>
                <br>
                <p>Atenciosamente,<br>Equipe CardioWeb</p>
            </body>
            </html>
        ";
        $mail->send();

        // --- E-MAIL 2: Para a Coordenação ---
        $mail->clearAddresses();
        $mail->addAddress('coordenacao@vidaviva.com');
        $mail->Subject = 'Alerta: Novo Login Realizado';
        $mail->Body    = "
            <html>
            <body>
                <h2>Novo Login no Sistema</h2>
                <p>O usuário <b>$nomeUsuario</b> ($emailUsuario) acabou de logar no sistema CardioWeb.</p>
                <p>Data/Hora: " . date('d/m/Y H:i:s') . "</p>
            </body>
            </html>
        ";
        $mail->send();

        echo json_encode(["status" => "success", "message" => "E-mails enviados com sucesso!"]);

    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Erro ao enviar e-mail: {$mail->ErrorInfo}"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Método não permitido"]);
}
?>