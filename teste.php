<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Certifique-se de que o PHPMailer está instalado via Composer

$mail = new PHPMailer(true);

try {
    // Configuração do servidor SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; 
    $mail->SMTPAuth = true;
    $mail->Username = 'autoresartigosltcloud@gmail.com'; // Seu e-mail do Gmail
    $mail->Password = 'gjvz gmic xnzu ubas'; // Sua senha de aplicativo do Gmail
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Configuração do e-mail
    $mail->setFrom('autoresartigosltcloud@gmail.com', 'Teste de Envio');
    $mail->addAddress('jhonathannauralves@gmail.com'); // Altere para o seu e-mail de teste

    $mail->isHTML(true);
    $mail->Subject = 'Teste de Envio de E-mail';
    $mail->Body = '<h3>Se você recebeu este e-mail, seu SMTP está funcionando corretamente!</h3>';

    // Enviar e-mail
    if ($mail->send()) {
        echo 'E-mail enviado com sucesso!';
    } else {
        echo 'Falha ao enviar e-mail: ' . $mail->ErrorInfo;
    }
} catch (Exception $e) {
    echo "Erro ao enviar e-mail: {$mail->ErrorInfo}";
}
