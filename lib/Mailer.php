<?php
/**
 * Minimal SMTP mailer — no Composer, works on cPanel / shared hosting.
 * Port 465 → implicit SSL (connect via ssl://)
 * Port 587 → STARTTLS (plain TCP then upgrade)
 */
class Mailer {

    private static function read($sock): string {
        $data = '';
        while (!feof($sock)) {
            $line = fgets($sock, 512);
            if ($line === false) break;
            $data .= $line;
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

    public static function send(string $to, string $subject, string $html): void {
        $host     = SMTP_HOST;
        $port     = (int) SMTP_PORT;
        $user     = SMTP_USER;
        $pass     = SMTP_PASS;
        $from     = SMTP_FROM;
        $fromName = SMTP_FROM_NAME;

        $useSSL = ($port === 465);

        // Port 465: implicit SSL — connect directly over ssl://
        // Port 587: plain TCP then STARTTLS upgrade
        $proto = $useSSL ? 'ssl' : 'tcp';
        $ctx   = stream_context_create(['ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
        ]]);
        $sock = @stream_socket_client("{$proto}://{$host}:{$port}", $errno, $errstr, 15, STREAM_CLIENT_CONNECT, $ctx);
        if (!$sock) {
            throw new RuntimeException("Cannot connect to SMTP {$host}:{$port} — {$errstr} ({$errno})");
        }
        stream_set_timeout($sock, 15);

        self::read($sock); // greeting

        self::cmd($sock, 'EHLO ' . (gethostname() ?: 'localhost'), '250');

        if (!$useSSL) {
            // STARTTLS upgrade for port 587
            self::cmd($sock, 'STARTTLS', '220');
            if (!stream_socket_enable_crypto($sock, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT)) {
                stream_socket_enable_crypto($sock, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            }
            self::cmd($sock, 'EHLO ' . (gethostname() ?: 'localhost'), '250');
        }

        self::cmd($sock, 'AUTH LOGIN', '334');
        self::cmd($sock, base64_encode($user), '334');
        self::cmd($sock, base64_encode($pass), '235');

        self::cmd($sock, "MAIL FROM:<{$from}>", '250');
        self::cmd($sock, "RCPT TO:<{$to}>", '250');
        self::cmd($sock, 'DATA', '354');

        $encodedFrom    = '=?UTF-8?B?' . base64_encode($fromName) . '?=';
        $encodedSubject = '=?UTF-8?B?' . base64_encode($subject)  . '?=';

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
