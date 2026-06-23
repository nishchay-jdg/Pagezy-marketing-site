<?php
/**
 * Fix 3: writes config.php to EXACTLY what git's index has at the server's HEAD commit.
 * This resolves the conflict so git pull can proceed.
 * Upload to public_html root. Visit: https://pagezy.io/fix3.php?key=pagezy-fix3-2024
 * DELETE after use.
 */
if (($_GET['key'] ?? '') !== 'pagezy-fix3-2024') { http_response_code(403); die('No access.'); }

$configPath = '/home/pagezy/public_html/config.php';
$backupPath = '/home/pagezy/pagezy_config_backup.php';
$log        = [];

// Verify backup is in place before touching anything
if (!file_exists($backupPath) || filesize($backupPath) < 200) {
    die("✗ Backup not found at $backupPath. Run fix-deploy.php first.");
}
$log[] = "✓ Backup verified (" . filesize($backupPath) . " bytes)";

// This is the EXACT content of config.php as stored in git's index at
// the server's current HEAD commit (6cbcadd). Writing this makes the
// working tree match the index — no more conflict.
$exactGitContent = '<?php
// Database config — update these for your cPanel MySQL
define(\'DB_HOST\', \'localhost\');
define(\'DB_NAME\', \'pagezy_leads\');
define(\'DB_USER\', \'your_db_user\');
define(\'DB_PASS\', \'your_db_password\');
define(\'DB_CHARSET\', \'utf8mb4\');

// Download file (place your CMS zip in /downloads/ folder on cPanel)
define(\'DOWNLOAD_FILE\', \'downloads/pagezy-cms-latest.zip\');
define(\'DOWNLOAD_FILENAME\', \'pagezy-cms.zip\');

// Site config
define(\'SITE_URL\', \'https://pagezy.io\');
define(\'SITE_NAME\', \'Pagezy\');

// ── SMTP (Gmail) ──────────────────────────────────────────
define(\'SMTP_HOST\',      \'smtp.gmail.com\');
define(\'SMTP_PORT\',      587);
define(\'SMTP_USER\',      \'notification.jdg@gmail.com\');
define(\'SMTP_PASS\',      \'nmvr whld rawk lwod\');
define(\'SMTP_FROM\',      \'notification.jdg@gmail.com\');
define(\'SMTP_FROM_NAME\', \'Pagezy Site\');

// All lead/inquiry notifications go to this address only
define(\'NOTIFY_TO\', \'nishchay.jdg@gmail.com\');

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
        plan        VARCHAR(50) DEFAULT \'free\',
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

// Admin panel password
define(\'ADMIN_PASSWORD\', \'pagezy@admin2024\');

// GitHub repos
define(\'GITHUB_CMS_REPO\',        \'nishchay-jdg/Pagezy\');
define(\'GITHUB_MARKETING_REPO\',  \'nishchay-jdg/Pagezy-marketing-site\');
define(\'GITHUB_TOKEN\',           \'\');  // Optional: personal access token for higher rate limits

// Release cache file (auto-written by admin panel)
define(\'RELEASE_CACHE\',  __DIR__ . \'/downloads/release-cache.json\');

// cPanel UAPI — used by admin panel to trigger git pull + deploy without terminal
// Generate token: cPanel → Security → Manage API Tokens → Create
define(\'CPANEL_HOST\',       \'cpanel.pagezy.io\');
define(\'CPANEL_PORT\',       2083);
define(\'CPANEL_USER\',       \'pagezy\');
define(\'CPANEL_API_TOKEN\',  \'\');  // Paste your cPanel API token here
define(\'CPANEL_REPO_ROOT\',  \'/home/pagezy/public_html\');

// Leads admin password — change this before going live
define(\'LEADS_PASSWORD\', \'pagezy@leads2024\');

require_once __DIR__ . \'/lib/Mailer.php\';

// Shared email template wrapper
function mailWrap(string $title, string $rows): string {
    return \'<!DOCTYPE html><html><head><meta charset="UTF-8">
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
  <div class="top"><h1>\' . $title . \'</h1><p>Received \' . date(\'d M Y, H:i:s\') . \' IST · Pagezy.io</p></div>
  <div class="body"><table>\' . $rows . \'</table></div>
  <div class="foot">This notification was sent only to you. The visitor was <strong>not</strong> emailed.<br>View all leads at <a href="https://pagezy.io/leads.php" style="color:#818CF8;">pagezy.io/leads.php</a></div>
</div></body></html>\';
}

function mailRow(string $label, string $value): string {
    if (trim($value) === \'\') return \'\';
    return \'<tr><td class="label">\' . htmlspecialchars($label) . \'</td>\'
         . \'<td class="val">\' . nl2br(htmlspecialchars($value)) . \'</td></tr>\'
         . \'<tr><td colspan="2"><hr class="divider"></td></tr>\';
}

// Notify owner of a new download lead (silent fail — never block the user)
function notifyDownload(array $data): void {
    try {
        $rows = mailRow(\'Name\',     $data[\'name\'])
              . mailRow(\'Email\',    $data[\'email\'])
              . mailRow(\'City\',     $data[\'city\']     ?? \'\')
              . mailRow(\'Company\',  $data[\'company\']  ?? \'\')
              . mailRow(\'Use case\', $data[\'use_case\'] ?? \'\')
              . mailRow(\'IP\',       $data[\'ip\']       ?? \'\');
        Mailer::send(
            NOTIFY_TO,
            \'New Download Lead — \' . $data[\'name\'],
            mailWrap(\'New Download Lead\', $rows)
        );
    } catch (Throwable $e) {
        // Silently swallow — never block the download
        error_log(\'[Pagezy mailer] \' . $e->getMessage());
    }
}

// Notify owner of a new contact form submission
function notifyContact(array $data): void {
    try {
        $rows = mailRow(\'Name\',    $data[\'name\'])
              . mailRow(\'Email\',   $data[\'email\'])
              . mailRow(\'Phone\',   $data[\'phone\']   ?? \'\')
              . mailRow(\'Company\', $data[\'company\'] ?? \'\')
              . mailRow(\'Subject\', $data[\'subject\'] ?? \'\')
              . mailRow(\'Message\', $data[\'message\'])
              . mailRow(\'IP\',      $data[\'ip\']      ?? \'\');
        Mailer::send(
            NOTIFY_TO,
            \'New Contact Inquiry — \' . $data[\'name\'],
            mailWrap(\'New Contact Inquiry\', $rows)
        );
    } catch (Throwable $e) {
        error_log(\'[Pagezy mailer] \' . $e->getMessage());
    }
}
';

if (@file_put_contents($configPath, $exactGitContent) === false) {
    die("✗ Could not write config.php — permission issue");
}
$log[] = "✓ Wrote config.php with EXACT git-tracked content (conflict resolved)";

// Now trigger git pull using real API token from backup
$backup   = file_get_contents($backupPath);
$token    = '';
$cpUser   = 'pagezy';
if (preg_match("/define\('CPANEL_API_TOKEN',\s*'([^']+)'\)/", $backup, $m)) $token  = $m[1];
if (preg_match("/define\('CPANEL_USER',\s*'([^']+)'\)/",      $backup, $m)) $cpUser = $m[1];

if ($token) {
    $ctx = stream_context_create([
        'http' => [
            'method'        => 'GET',
            'header'        => "Authorization: cpanel {$cpUser}:{$token}\r\n",
            'timeout'       => 30,
            'ignore_errors' => true,
        ],
        'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
    ]);

    // Try both path formats (cPanel is picky about trailing slash)
    foreach (['/home/pagezy/public_html/', '/home/pagezy/public_html'] as $root) {
        $resp = @file_get_contents(
            'https://cpanel.pagezy.io:2083/execute/VersionControl/update?repository_root=' . urlencode($root),
            false, $ctx
        );
        $data = $resp ? (json_decode($resp, true) ?? []) : [];
        if (($data['status'] ?? 0) === 1) {
            $log[] = "✓ Git pull triggered via cPanel API (root: $root)!";
            @file_get_contents(
                'https://cpanel.pagezy.io:2083/execute/VersionControlDeployment/create?repository_root=' . urlencode($root),
                false, $ctx
            );
            $log[] = "✓ Deploy triggered — .cpanel.yml will restore real credentials";
            $log[] = "";
            $log[] = "ALL DONE! Wait 20 seconds → visit https://pagezy.io/admin/";
            $log[] = "Then delete fix3.php, fix2.php and fix-deploy.php from File Manager.";
            break;
        } else {
            $log[] = "⚠ root=$root → " . json_encode($data['errors'] ?? $data['error'] ?? 'no response');
        }
    }
} else {
    $log[] = "ℹ No API token in backup — do manually:";
}

$log[] = "";
$log[] = "If API didn't auto-trigger: go to cPanel → Git Version Control → Manage";
$log[] = "→ Update from Remote  (will work now — conflict is gone!)";
$log[] = "→ Deploy HEAD Commit  (.cpanel.yml restores real config from backup)";

?><!DOCTYPE html>
<html>
<head><title>Pagezy Fix 3</title>
<style>
body{background:#07060F;color:#e2e8f0;font-family:monospace;padding:40px}
pre{background:#0F0D1E;border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:24px;line-height:1.8;white-space:pre-wrap}
h2{color:#818CF8}
</style>
</head>
<body>
<h2>Pagezy Deploy Fix — Step 3</h2>
<pre><?php echo htmlspecialchars(implode("\n", $log)); ?></pre>
</body>
</html>
