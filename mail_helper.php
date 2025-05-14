<?php

require_once 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

function sendEmail($to, $subject, $body, $from = '', $fromName = '')
{
    $mail = new PHPMailer(true);

    // SMTP Configuration
    $smtpHost     = SMTP_HOST;
    $smtpUsername = SMTP_USER;
    $smtpPassword = SMTP_PASS;
    $smtpPort     = SMTP_PORT;
    $smtpSecure   = SMTP_SECURE;

    if (empty($from)) {
        $from = $smtpUsername;
    }
    if (empty($fromName)) {
        $fromName = 'Healthcare Appointment Booking';
    }

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = $smtpHost;
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtpUsername;
        $mail->Password   = $smtpPassword;
        $mail->SMTPSecure = $smtpSecure;
        $mail->Port       = $smtpPort;

        // Optional: fix STARTTLS handshake issues
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true
            ]
        ];

        // Recipients
        $mail->setFrom($from, $fromName);
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = nl2br($body);

        $mail->send();
        return true;
    } catch (Exception $e) {
        return $mail->ErrorInfo;
    }
}
