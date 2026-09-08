<?php

namespace App\Support;

class Mailer
{
    private string $to;
    private string $toName  = '';
    private string $subject = '';
    private string $htmlBody = '';
    private string $textBody = '';
    private array $attachments = [];
    private string $replyTo = '';

    public static function to(string $email, string $name = ''): self
    {
        $m = new self();
        $m->to     = $email;
        $m->toName = $name;
        return $m;
    }

    public function subject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    public function html(string $html): self
    {
        $this->htmlBody = $html;
        return $this;
    }

    public function text(string $text): self
    {
        $this->textBody = $text;
        return $this;
    }

    public function replyTo(string $email): self
    {
        $this->replyTo = $email;
        return $this;
    }

    public function view(string $template, array $data = []): self
    {
        $this->htmlBody = View::render($template, $data);
        return $this;
    }

    public function send(): bool
    {
        try {
            $cfg  = config('mail');
            $from = $cfg['from'];

            if ($cfg['mailer'] === 'smtp') {
                return $this->sendSmtp($cfg, $from);
            }

            return $this->sendPhpMail($from);
        } catch (\Throwable $e) {
            Logger::error('Mail send failed', ['error' => $e->getMessage(), 'to' => $this->to]);
            $this->logFailed();
            return false;
        }
    }

    private function sendSmtp(array $cfg, array $from): bool
    {
        $socket = @fsockopen(
            ($cfg['encryption'] === 'ssl' ? 'ssl://' : '') . $cfg['host'],
            (int) $cfg['port'],
            $errno, $errstr, 10
        );

        if (!$socket) {
            throw new \RuntimeException("SMTP connection failed: $errstr ($errno)");
        }

        $read = function () use ($socket): string {
            $response = '';
            while ($line = fgets($socket, 515)) {
                $response .= $line;
                if (substr($line, 3, 1) === ' ') break;
            }
            return $response;
        };

        $write = function (string $cmd) use ($socket): string {
            fputs($socket, $cmd . "\r\n");
            $response = '';
            while ($line = fgets($socket, 515)) {
                $response .= $line;
                if (substr($line, 3, 1) === ' ') break;
            }
            return $response;
        };

        $read(); // greeting
        $write("EHLO " . ($_SERVER['SERVER_NAME'] ?? 'localhost'));

        if ($cfg['encryption'] === 'tls') {
            $write("STARTTLS");
            stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            $write("EHLO " . ($_SERVER['SERVER_NAME'] ?? 'localhost'));
        }

        $write("AUTH LOGIN");
        $write(base64_encode($cfg['username']));
        $write(base64_encode($cfg['password']));
        $write("MAIL FROM:<{$cfg['username']}>");
        $write("RCPT TO:<{$this->to}>");
        $write("DATA");

        $message = $this->buildMessage($from);
        fputs($socket, $message . "\r\n.\r\n");
        $read();
        $write("QUIT");
        fclose($socket);

        $this->logSuccess();
        return true;
    }

    private function sendPhpMail(array $from): bool
    {
        $headers  = "From: {$from['name']} <{$from['address']}>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $result   = mail($this->to, $this->subject, $this->htmlBody, $headers);
        if ($result) $this->logSuccess();
        else $this->logFailed();
        return $result;
    }

    private function buildMessage(array $from): string
    {
        $boundary = md5(uniqid());
        $to       = $this->toName ? "\"{$this->toName}\" <{$this->to}>" : $this->to;
        $fromStr  = "{$from['name']} <{$from['address']}>";

        $headers  = "To: $to\r\n";
        $headers .= "From: $fromStr\r\n";
        if ($this->replyTo) {
            $headers .= "Reply-To: {$this->replyTo}\r\n";
        }
        $headers .= "Subject: =?UTF-8?B?" . base64_encode($this->subject) . "?=\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n";
        $headers .= "Date: " . date('r') . "\r\n";
        $headers .= "Message-ID: <" . uniqid() . "@" . ($_SERVER['SERVER_NAME'] ?? 'localhost') . ">\r\n";

        $body  = "--$boundary\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
        $body .= ($this->textBody ?: strip_tags($this->htmlBody)) . "\r\n";
        $body .= "--$boundary\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
        $body .= $this->htmlBody . "\r\n";
        $body .= "--$boundary--";

        return $headers . "\r\n" . $body;
    }

    private function logSuccess(): void
    {
        try {
            Database::query(
                'INSERT INTO email_logs (to_address, subject, status, created_at) VALUES (?, ?, ?, NOW())',
                [$this->to, $this->subject, 'sent']
            );
        } catch (\Throwable) {}
    }

    private function logFailed(): void
    {
        try {
            Database::query(
                'INSERT INTO email_logs (to_address, subject, status, created_at) VALUES (?, ?, ?, NOW())',
                [$this->to, $this->subject, 'failed']
            );
        } catch (\Throwable) {}
    }
}
