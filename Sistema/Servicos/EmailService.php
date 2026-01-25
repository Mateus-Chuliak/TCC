<?php

namespace Sistema\Servicos;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Serviço responsável pelo envio de e-mails via SMTP.
 */
class EmailService
{
    /**
     * Realiza o envio de e-mail utilizando SMTP.
     */
    public static function enviar(
        string $destinatario,
        string $assunto,
        string $html,
        string $texto = ''
    ): bool {

        // Valida formato básico do e-mail do destinatário
        if (!filter_var($destinatario, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Instância principal do PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Configura parâmetros de conexão SMTP
            $mail->isSMTP();
            $mail->Host       = $_ENV['MAIL_HOST'] ?? '';
            $mail->Port       = (int) ($_ENV['MAIL_PORT'] ?? 587);
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['MAIL_USERNAME'] ?? '';
            $mail->Password   = $_ENV['MAIL_PASSWORD'] ?? '';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

            // Define charset para suporte a acentuação
            $mail->CharSet = 'UTF-8';

            // Configura remetente padrão do sistema
            $mail->setFrom(
                $_ENV['MAIL_FROM_EMAIL'] ?? 'no-reply@localhost',
                $_ENV['MAIL_FROM_NAME'] ?? 'Sistema'
            );

            // Define destinatário do e-mail
            $mail->addAddress($destinatario);

            // Define conteúdo HTML e assunto
            $mail->isHTML(true);
            $mail->Subject = $assunto;
            $mail->Body    = $html;

            // Define texto alternativo para compatibilidade
            $mail->AltBody = $texto ?: strip_tags($html);

            // Executa o envio do e-mail
            return $mail->send();

        } catch (Exception $e) {

            // Registra erro sem expor detalhes ao cliente
            error_log(
                '[EmailService] Erro ao enviar e-mail: ' . $e->getMessage()
            );

            return false;
        }
    }
}
