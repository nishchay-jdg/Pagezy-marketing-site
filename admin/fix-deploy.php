<?php
/**
 * One-time git conflict fixer for Pagezy marketing site.
 * Upload this file to public_html/admin/ via cPanel File Manager.
 * Visit: https://pagezy.io/admin/fix-deploy.php?key=pagezy-deploy-fix-2024
 * DELETE this file after it runs successfully.
 */
define('FIX_KEY', 'pagezy-deploy-fix-2024');
if (empty($_GET['key']) || $_GET['key'] !== FIX_KEY) {
    http_response_code(403);
    die('Access denied.');
}

$repoPath   = '/home/pagezy/public_html';
$configPath = $repoPath . '/config.php';
$backupPath = '/home/pagezy/pagezy_config_backup.php'; // outside public_html
$log        = [];

// ── Step 1: Save real credentials to a safe location outside public_html ──
$currentConfig = @file_get_contents($configPath);
if ($currentConfig === false) {
    die("✗ Could not read config.php from $configPath");
}
if (@file_put_contents($backupPath, $currentConfig) === false) {
    die("✗ Could not write backup to $backupPath — check directory permissions");
}
$log[] = "✓ Real credentials backed up to $backupPath";

// ── Step 2: Write a clean config.php that matches what git expects ──
// This removes the "local modifications" that block git pull
$cleanConfig = '<?php
define(\'DB_HOST\',            \'localhost\');
define(\'DB_NAME\',            \'pagezy_pagezy_main\');
define(\'DB_USER\',            \'pagezy_pagezy_main\');
define(\'DB_PASS\',            \'\');
define(\'DB_CHARSET\',         \'utf8mb4\');
define(\'DOWNLOAD_FILE\',      \'downloads/pagezy-cms-latest.zip\');
define(\'DOWNLOAD_FILENAME\',  \'pagezy-cms.zip\');
define(\'SITE_URL\',           \'https://pagezy.io\');
define(\'SITE_NAME\',          \'Pagezy\');
define(\'SMTP_HOST\',          \'smtp.gmail.com\');
define(\'SMTP_PORT\',          587);
define(\'SMTP_USER\',          \'\');
define(\'SMTP_PASS\',          \'\');
define(\'SMTP_FROM\',          \'\');
define(\'SMTP_FROM_NAME\',     \'Pagezy Site\');
define(\'NOTIFY_TO\',          \'\');
define(\'ADMIN_PASSWORD\',     \'\');
define(\'GITHUB_CMS_REPO\',    \'nishchay-jdg/Pagezy\');
define(\'GITHUB_MARKETING_REPO\', \'nishchay-jdg/Pagezy-marketing-site\');
define(\'GITHUB_TOKEN\',       \'\');
define(\'RELEASE_CACHE\',      __DIR__ . \'/downloads/release-cache.json\');
define(\'CPANEL_HOST\',        \'cpanel.pagezy.io\');
define(\'CPANEL_PORT\',        2083);
define(\'CPANEL_USER\',        \'pagezy\');
define(\'CPANEL_API_TOKEN\',   \'\');
define(\'CPANEL_REPO_ROOT\',   \'/home/pagezy/public_html\');
define(\'LEADS_PASSWORD\',     \'\');
';
if (@file_put_contents($configPath, $cleanConfig) === false) {
    die("✗ Could not write clean config.php — check permissions");
}
$log[] = "✓ Wrote clean config.php (git will accept this — no more conflict)";

// ── Step 3: Extract real API token from backup to trigger the pull ──
$apiToken = '';
$cpanelUser = 'pagezy';
$cpanelHost = 'cpanel.pagezy.io';
$cpanelPort = 2083;
$repoRoot   = '/home/pagezy/public_html';

if (preg_match("/define\('CPANEL_API_TOKEN',\s*'([^']+)'\)/", $currentConfig, $m)) {
    $apiToken = $m[1];
}
if (preg_match("/define\('CPANEL_USER',\s*'([^']+)'\)/", $currentConfig, $m)) {
    $cpanelUser = $m[1];
}

// ── Step 4: Trigger git pull via cPanel UAPI ──
if ($apiToken) {
    $ctx = stream_context_create([
        'http' => [
            'method'        => 'GET',
            'header'        => "Authorization: cpanel {$cpanelUser}:{$apiToken}\r\n",
            'timeout'       => 30,
            'ignore_errors' => true,
        ],
        'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
    ]);

    $pullUrl = "https://{$cpanelHost}:{$cpanelPort}/execute/VersionControl/update"
             . "?repository_root=" . urlencode($repoRoot);
    $resp = @file_get_contents($pullUrl, false, $ctx);
    $data = $resp ? (json_decode($resp, true) ?? []) : [];

    if (($data['status'] ?? 0) === 1) {
        $log[] = "✓ Git pull triggered via cPanel API!";

        $deployUrl = "https://{$cpanelHost}:{$cpanelPort}/execute/VersionControlDeployment/create"
                   . "?repository_root=" . urlencode($repoRoot);
        @file_get_contents($deployUrl, false, $ctx);
        $log[] = "✓ Deploy triggered — .cpanel.yml will restore your real credentials from backup";
        $log[] = "";
        $log[] = "DONE! Wait 15–20 seconds, then visit https://pagezy.io/admin/ to confirm.";
        $log[] = "Then DELETE this file from cPanel File Manager for security.";
    } else {
        $err = $data['errors'][0] ?? ($data['error'] ?? 'No response');
        $log[] = "⚠ API call failed: $err";
        $log[] = "";
        $log[] = "But config.php is now clean! Do this manually:";
        $log[] = "  → cPanel → Git Version Control → Manage → Update from Remote";
        $log[] = "  → Then click Deploy HEAD Commit";
        $log[] = "  → .cpanel.yml will restore your real credentials automatically";
    }
} else {
    $log[] = "ℹ No API token found in backup.";
    $log[] = "";
    $log[] = "config.php is now clean. Do this manually:";
    $log[] = "  → cPanel → Git Version Control → Manage → Update from Remote";
    $log[] = "  → Then click Deploy HEAD Commit";
    $log[] = "  → .cpanel.yml will restore your real credentials automatically";
}

?><!DOCTYPE html>
<html>
<head>
<title>Pagezy Deploy Fix</title>
<style>
body { background: #07060F; color: #e2e8f0; font-family: monospace; padding: 40px; }
pre { background: #0F0D1E; border: 1px solid rgba(255,255,255,.1); border-radius: 12px; padding: 24px; line-height: 1.8; }
h2 { color: #818CF8; }
</style>
</head>
<body>
<h2>Pagezy Deploy Fix</h2>
<pre><?php echo htmlspecialchars(implode("\n", $log)); ?></pre>
</body>
</html>
