<?php

namespace PHPAuth\Mailer;

use PHPMailer\PHPMailer\PHPMailer;

class ArchyPHPAuthMailer implements MailerInterface
{
    private array $settings;
    public string $ErrorInfo = '';

    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    public function isConfigured(): bool
    {
        return !empty($this->settings['sender_email']);
    }

    private function shouldUseSmtp(): bool
    {
        $transport = strtolower((string) ($this->settings['mail_transport'] ?? 'auto'));

        if ($transport === 'smtp') {
            return true;
        }

        if (in_array($transport, ['mail', 'php_mail', 'sendmail'], true)) {
            return false;
        }

        return !empty($this->settings['smtp_host'])
            || !empty($this->settings['smtp_port'])
            || !empty($this->settings['smtp_secure']);
    }

    private function shouldUseSendmail(): bool
    {
        return strtolower((string) ($this->settings['mail_transport'] ?? 'auto')) === 'sendmail';
    }

    public function send(string $to, string $subject = '', string $body = '', string $altbody = '', ?string $from = null, ?string $fromName = null): bool
    {
        try {
            if (!$this->isConfigured()) {
                $this->ErrorInfo = 'Mittente email non configurato.';
                return false;
            }

            $mail = new PHPMailer(true);
            $mail->CharSet = 'utf-8';

            $sendmailFallbackToMail = false;

            if ($this->shouldUseSendmail() && function_exists('popen')) {
                $mail->isSendmail();
            } elseif ($this->shouldUseSendmail()) {
                $sendmailFallbackToMail = true;
                $mail->isMail();
            } elseif ($this->shouldUseSmtp()) {
                $mail->isSMTP();
                $mail->Host = !empty($this->settings['smtp_host']) ? $this->settings['smtp_host'] : '127.0.0.1';
                $mail->SMTPAuth = !empty($this->settings['smtp_username']);
                $mail->Username = $this->settings['smtp_username'] ?? '';
                $mail->Password = $this->settings['smtp_password'] ?? '';
                if (!empty($this->settings['smtp_secure'])) {
                    $mail->SMTPSecure = $this->settings['smtp_secure'];
                }
                $mail->Port = (int) ($this->settings['smtp_port'] ?? 587);
                $mail->Timeout = 15;

                if (!empty($this->settings['mail_smtp_allow_self_signed'])) {
                    $mail->SMTPOptions = [
                        'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        ]
                    ];
                }
            }

            $senderEmail = $from ?: ($this->settings['sender_email'] ?? 'noreply@example.com');
            $senderName = $fromName ?: ($this->settings['sender_name'] ?? 'Archy Art');
            $transport = $sendmailFallbackToMail ? 'mail-sendmail-fallback' : ($this->shouldUseSendmail() ? 'sendmail' : ($this->shouldUseSmtp() ? 'smtp' : 'mail'));

            $mail->setFrom($senderEmail, $senderName);
            $mail->Sender = $senderEmail;
            $mail->addAddress($to);
            $mail->addReplyTo($senderEmail, $senderName);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = $altbody ?: strip_tags(str_replace('<br>', "\n", $body));

            $sent = $mail->send();
            error_log('[ArchyPHPAuthMailer] reset mail sent=' . ($sent ? 'true' : 'false') . ' transport=' . $transport . ' host=' . ($mail->Host ?: '-') . ' port=' . ($mail->Port ?: '-') . ' secure=' . ($mail->SMTPSecure ?: '-') . ' to=' . $to . ' from=' . $senderEmail);

            return $sent;
        } catch (\Throwable $e) {
            $this->ErrorInfo = $e->getMessage();
            $transport = $this->shouldUseSendmail() && !function_exists('popen') ? 'mail-sendmail-fallback' : ($this->shouldUseSendmail() ? 'sendmail' : ($this->shouldUseSmtp() ? 'smtp' : 'mail'));
            error_log('[ArchyPHPAuthMailer] reset mail failed transport=' . $transport . ' host=' . (!empty($this->settings['smtp_host']) ? $this->settings['smtp_host'] : ($this->shouldUseSmtp() ? '127.0.0.1' : '-')) . ' port=' . ($this->settings['smtp_port'] ?? '-') . ' secure=' . ($this->settings['smtp_secure'] ?? '-') . ' to=' . $to . ' error=' . $this->ErrorInfo);
            return false;
        }
    }

    public function sendPasswordReset(string $email, string $resetKey): bool
    {
        return true;
    }

    public function sendActivation(string $email, string $activationKey): bool
    {
        return true;
    }
}
