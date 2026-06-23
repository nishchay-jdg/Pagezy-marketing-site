<?php
/**
 * Step 2 fix: deletes config.php so git pull has nothing to conflict with.
 * Backup was already saved to /home/pagezy/pagezy_config_backup.php by fix-deploy.php
 * Upload to public_html root. Visit: https://pagezy.io/fix2.php?key=pagezy-fix2-2024
 * DELETE after use.
 */
if (($_GET['key'] ?? '') !== 'pagezy-fix2-2024') { http_response_code(403); die('No access.'); }

$configPath = '/home/pagezy/public_html/config.php';
$backupPath = '/home/pagezy/pagezy_config_backup.php';
$log = [];

// Verify the backup is in place before we delete anything
if (!file_exists($backupPath) || filesize($backupPath) < 200) {
    die("✗ Backup not found at $backupPath — run fix-deploy.php first before running this.");
}
$log[] = "✓ Backup verified at $backupPath (" . filesize($backupPath) . " bytes)";

// Delete config.php from the working tree
// Once it's gone, git pull has nothing to conflict with
if (file_exists($configPath)) {
    if (@unlink($configPath)) {
        $log[] = "✓ Deleted config.php from working tree — git pull will now succeed";
    } else {
        die("✗ Could not delete config.php — file permission issue");
    }
} else {
    $log[] = "ℹ config.php already absent";
}

// Read real credentials from backup for API call
$backup  = file_get_contents($backupPath);
$token   = '';
$cpUser  = 'pagezy';
if (preg_match("/define\('CPANEL_API_TOKEN',\s*'([^']+)'\)/", $backup, $m)) $token  = $m[1];
if (preg_match("/define\('CPANEL_USER',\s*'([^']+)'\)/",      $backup, $m)) $cpUser = $m[1];

// Trigger git pull via cPanel UAPI (trailing slash is required by cPanel)
$root = '/home/pagezy/public_html/';
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

    $resp = @file_get_contents(
        'https://cpanel.pagezy.io:2083/execute/VersionControl/update?repository_root=' . urlencode($root),
        false, $ctx
    );
    $data = $resp ? (json_decode($resp, true) ?? []) : [];

    if (($data['status'] ?? 0) === 1) {
        $log[] = "✓ Git pull triggered via cPanel API!";
        @file_get_contents(
            'https://cpanel.pagezy.io:2083/execute/VersionControlDeployment/create?repository_root=' . urlencode($root),
            false, $ctx
        );
        $log[] = "✓ Deploy triggered — .cpanel.yml will restore config.php from backup";
        $log[] = "";
        $log[] = "ALL DONE. Wait 20 seconds then visit https://pagezy.io/admin/";
        $log[] = "Then delete fix2.php and fix-deploy.php from File Manager.";
    } else {
        $errs = $data['errors'] ?? [$data['error'] ?? json_encode($data)];
        $log[] = "⚠ API: " . implode(' | ', $errs);
        $log[] = "";
        $log[] = "config.php is deleted. Now do manually in cPanel:";
        $log[] = "  1. Git Version Control → Manage → Update from Remote";
        $log[] = "     (no conflict now — config.php is gone from working tree)";
        $log[] = "  2. Deploy HEAD Commit";
        $log[] = "     (.cpanel.yml restores config.php from backup automatically)";
    }
} else {
    $log[] = "ℹ No API token found in backup.";
    $log[] = "";
    $log[] = "config.php is deleted. Do manually in cPanel:";
    $log[] = "  1. Git Version Control → Manage → Update from Remote";
    $log[] = "  2. Deploy HEAD Commit";
}

?><!DOCTYPE html>
<html>
<head><title>Pagezy Fix 2</title>
<style>
body{background:#07060F;color:#e2e8f0;font-family:monospace;padding:40px}
pre{background:#0F0D1E;border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:24px;line-height:1.8}
h2{color:#818CF8}
</style>
</head>
<body>
<h2>Pagezy Deploy Fix — Step 2</h2>
<pre><?php echo htmlspecialchars(implode("\n", $log)); ?></pre>
</body>
</html>
