<?php
/**
 * Minimal SMTP mailer — no Composer, works on cPanel / shared hosting.
 * Supports Gmail SMTP with STARTTLS on port 587.
 */
class Mailer {

    private static function read($sock): string {
        $data = '';
        while (!feof($sock)) {
            $line = fgets($sock, 512);
            if ($line === false) break;
            $data .= $line;
            // Multi-line responses have a dash at position 3; space means last line
            if (isset($line[3]) && $line[3] === ' ') break;
        }
        return $data;
    }

    private static function cmd($sock, string $cmd, string $expect): string {
        fwrite($sock, $cmd . "\r\n");
        $resp = self::read($sock);
        if (substr($resp, 0, 3) !== $expect) {
            throw new RuntimeException("SMTP error (expected $expect): $resp");
        }
        return $resp;
    }

    /**
     * Send an HTML email via SMTP.
     *
     * @param string $to      Recipient address
     * @param string $subject Email subject
     * @param string $html    HTML body
     */
    public static function send(string $to, string $subject, string $html): void {
        $host     = SMTP_HOST;
        $port     = (int) SMTP_PORT;
        $user     = SMTP_USER;
        $pass     = SMTP_PASS;
        $from     = SMTP_FROM;
        $fromName = SMTP_FROM_NAME;

        // Open plain TCP connection first (STARTTLS upgrades it)
        $sock = @fsockopen("tcp://{$host}", $port, $errno, $errstr, 15);
        if (!$sock) {
            throw new RuntimeException("Cannot connect to SMTP {$host}:{$port} — {$errstr} ({$errno})");
        }
        stream_set_timeout($sock, 15);

        self::read($sock); // greeting

        self::cmd($sock, 'EHLO ' . (gethostname() ?: 'localhost'), '250');
        self::cmd($sock, 'STARTTLS', '220');

        // Upgrade to TLS
        if (!stream_socket_enable_crypto($sock, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT)) {
            // Fallback to generic TLS
            stream_socket_enable_crypto($sock, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        }

        self::cmd($sock, 'EHLO ' . (gethostname() ?: 'localhost'), '250');
        self::cmd($sock, 'AUTH LOGIN', '334');
        self::cmd($sock, base64_encode($user), '334');
        self::cmd($sock, base64_encode($pass), '235');

        self::cmd($sock, "MAIL FROM:<{$from}>", '250');
        self::cmd($sock, "RCPT TO:<{$to}>", '250');
        self::cmd($sock, 'DATA', '354');

        // Build RFC 2822 message
        $encodedFrom    = '=?UTF-8?B?' . base64_encode($fromName) . '?=';
        $encodedSubject = '=?UTF-8?B?' . base64_encode($subject)  . '?=';
        $boundary       = 'pagezy_' . md5(uniqid('', true));

        $msg  = "Date: " . date('r') . "\r\n";
        $msg .= "From: {$encodedFrom} <{$from}>\r\n";
        $msg .= "To: {$to}\r\n";
        $msg .= "Subject: {$encodedSubject}\r\n";
        $msg .= "MIME-Version: 1.0\r\n";
        $msg .= "Content-Type: text/html; charset=UTF-8\r\n";
        $msg .= "Content-Transfer-Encoding: base64\r\n";
        $msg .= "\r\n";
        $msg .= chunk_split(base64_encode($html));
        $msg .= "\r\n.";

        fwrite($sock, $msg . "\r\n");
        $resp = self::read($sock);
        if (substr($resp, 0, 3) !== '250') {
            throw new RuntimeException("SMTP DATA error: {$resp}");
        }

        fwrite($sock, "QUIT\r\n");
        fclose($sock);
    }
}
