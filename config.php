<?php
// Database config — update these for your cPanel MySQL
define('DB_HOST', 'localhost');
define('DB_NAME', 'pagezy_leads');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');
define('DB_CHARSET', 'utf8mb4');

// Download file (place your CMS zip in /downloads/ folder on cPanel)
define('DOWNLOAD_FILE', 'downloads/pagezy-cms-latest.zip');
define('DOWNLOAD_FILENAME', 'pagezy-cms.zip');

// Site config
define('SITE_URL', 'https://pagezy.io');
define('SITE_NAME', 'Pagezy');

// ── SMTP (Gmail) ──────────────────────────────────────────
define('SMTP_HOST',      'smtp.gmail.com');
define('SMTP_PORT',      587);
define('SMTP_USER',      'notification.jdg@gmail.com');
define('SMTP_PASS',      'nmvr whld rawk lwod');
define('SMTP_FROM',      'notification.jdg@gmail.com');
define('SMTP_FROM_NAME', 'Pagezy Site');

// All lead/inquiry notifications go to this address only
define('NOTIFY_TO', 'nishchay.jdg@gmail.com');

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}

// Create downloads table if not exists
function ensureTable(): void {
    db()->exec("CREATE TABLE IF NOT EXISTS pagezy_downloads (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        name        VARCHAR(255) NOT NULL,
        email       VARCHAR(255) NOT NULL,
        city        VARCHAR(255),
        company     VARCHAR(255),
        use_case    VARCHAR(100),
        plan        VARCHAR(50) DEFAULT 'free',
        ip_address  VARCHAR(45),
        downloaded  TINYINT(1) DEFAULT 0,
        created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

// Create contacts table if not exists
function ensureContactTable(): void {
    db()->exec("CREATE TABLE IF NOT EXISTS pagezy_contacts (
        id         INT AUTO_INCREMENT PRIMARY KEY,
        name       VARCHAR(255) NOT NULL,
        email      VARCHAR(255) NOT NULL,
        phone      VARCHAR(50),
        company    VARCHAR(255),
        subject    VARCHAR(100),
        message    TEXT NOT NULL,
        ip_address VARCHAR(45),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

// Leads admin password — change this before going live
define('LEADS_PASSWORD', 'pagezy@leads2024');

require_once __DIR__ . '/lib/Mailer.php';

// Shared email template wrapper
function mailWrap(string $title, string $rows): string {
    return '<!DOCTYPE html><html><head><meta charset="UTF-8">
<style>
body{margin:0;padding:0;background:#07060F;font-family:Inter,Arial,sans-serif;}
.wrap{max-width:560px;margin:32px auto;background:#0F0D1E;border:1px solid rgba(255,255,255,.08);border-radius:16px;overflow:hidden;}
.top{background:linear-gradient(135deg,#3B82F6,#7C3AED);padding:28px 32px;}
.top h1{color:#fff;font-size:20px;margin:0;font-weight:800;}
.top p{color:rgba(255,255,255,.75);font-size:13px;margin:4px 0 0;}
.body{padding:28px 32px;}
table{width:100%;border-collapse:collapse;}
td{padding:10px 0;font-size:14px;vertical-align:top;}
td.label{color:#6B7280;width:36%;font-weight:600;font-size:13px;}
td.val{color:#F1F5F9;}
.divider{border:none;border-top:1px solid rgba(255,255,255,.07);margin:4px 0;}
.foot{padding:20px 32px;border-top:1px solid rgba(255,255,255,.07);font-size:12px;color:#4B5563;text-align:center;}
</style></head><body>
<div class="wrap">
  <div class="top"><h1>' . $title . '</h1><p>Received ' . date('d M Y, H:i:s') . ' IST · Pagezy.io</p></div>
  <div class="body"><table>' . $rows . '</table></div>
  <div class="foot">This notification was sent only to you. The visitor was <strong>not</strong> emailed.<br>View all leads at <a href="https://pagezy.io/leads.php" style="color:#818CF8;">pagezy.io/leads.php</a></div>
</div></body></html>';
}

function mailRow(string $label, string $value): string {
    if (trim($value) === '') return '';
    return '<tr><td class="label">' . htmlspecialchars($label) . '</td>'
         . '<td class="val">' . nl2br(htmlspecialchars($value)) . '</td></tr>'
         . '<tr><td colspan="2"><hr class="divider"></td></tr>';
}

// Notify owner of a new download lead (silent fail — never block the user)
function notifyDownload(array $data): void {
    try {
        $rows = mailRow('Name',     $data['name'])
              . mailRow('Email',    $data['email'])
              . mailRow('City',     $data['city']     ?? '')
              . mailRow('Company',  $data['company']  ?? '')
              . mailRow('Use case', $data['use_case'] ?? '')
              . mailRow('IP',       $data['ip']       ?? '');
        Mailer::send(
            NOTIFY_TO,
            'New Download Lead — ' . $data['name'],
            mailWrap('New Download Lead', $rows)
        );
    } catch (Throwable $e) {
        // Silently swallow — never block the download
        error_log('[Pagezy mailer] ' . $e->getMessage());
    }
}

// Notify owner of a new contact form submission
function notifyContact(array $data): void {
    try {
        $rows = mailRow('Name',    $data['name'])
              . mailRow('Email',   $data['email'])
              . mailRow('Phone',   $data['phone']   ?? '')
              . mailRow('Company', $data['company'] ?? '')
              . mailRow('Subject', $data['subject'] ?? '')
              . mailRow('Message', $data['message'])
              . mailRow('IP',      $data['ip']      ?? '');
        Mailer::send(
            NOTIFY_TO,
            'New Contact Inquiry — ' . $data['name'],
            mailWrap('New Contact Inquiry', $rows)
        );
    } catch (Throwable $e) {
        error_log('[Pagezy mailer] ' . $e->getMessage());
    }
}
