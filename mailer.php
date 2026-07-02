<?php
/**
 * Smooth Goals — SMTP mail helper.
 *
 * Uses PHPMailer (vendored in /vendor/phpmailer/).
 * If PHPMailer is not installed yet, run:
 *   cd /home/smoothgoalscom/public_html
 *   composer require phpmailer/phpmailer
 *
 * OR manually download from https://github.com/PHPMailer/PHPMailer/releases
 * and extract src/ into vendor/phpmailer/src/
 */

require_once '/home/smoothgoalscom/config.php';
require_once __DIR__ . '/vendor/phpmailer/src/Exception.php';
require_once __DIR__ . '/vendor/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/vendor/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Send an email via authenticated SMTP.
 *
 * @param string $toAddr    Recipient email
 * @param string $toName    Recipient name (can be '')
 * @param string $subject   Subject line
 * @param string $body      Plain-text body
 * @param string $replyTo   Reply-To address (optional)
 * @return bool             true on success
 */
function sg_send_mail(string $toAddr, string $toName, string $subject, string $body, string $replyTo = ''): bool
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = (SMTP_PORT === 587) ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom(MAIL_FROM_ADDR, MAIL_FROM_NAME);
        $mail->addAddress($toAddr, $toName);

        if ($replyTo) {
            $mail->addReplyTo($replyTo);
        }

        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("SG mail error [{$toAddr}]: " . $mail->ErrorInfo);
        return false;
    }
}
