<?php
session_start();
set_time_limit(120);
ignore_user_abort(true);
require_once __DIR__ . '/../config.php';

// ── Auth ─────────────────────────────────────────────────
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if ($_POST['password'] === ADMIN_PASSWORD) {
        $_SESSION['pagezy_admin'] = true;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    $error = 'Incorrect password.';
}
if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
$authed = !empty($_SESSION['pagezy_admin']);

// ── Actions (require auth) ────────────────────────────────
$actionMsg = '';
// Pull flash from session (set by POST actions before redirect)
if (!empty($_SESSION['flash'])) {
    $actionMsg     = $_SESSION['flash']['msg'];
    $actionMsgType = $_SESSION['flash']['type'] ?? 'ok';
    unset($_SESSION['flash']);
} else {
    $actionMsgType = 'ok';
}
if ($authed) {

    // ── cPanel UAPI helper ────────────────────────────────
    function cpanel_api(string $module, string $func, array $params = []): array {
        if (!CPANEL_API_TOKEN || !CPANEL_USER) {
            return ['status' => 0, 'error' => 'cPanel API token not configured in config.php'];
        }
        $noSlash = !empty($params['_no_slash']);
        unset($params['_no_slash']);
        $root  = $noSlash ? rtrim(CPANEL_REPO_ROOT, '/') : rtrim(CPANEL_REPO_ROOT, '/') . '/';
        $query = http_build_query(array_merge(['repository_root' => $root], $params));
        $url   = 'https://' . CPANEL_HOST . ':' . CPANEL_PORT . '/execute/' . $module . '/' . $func . '?' . $query;
        $ctx   = stream_context_create(['http' => [
            'method'        => 'GET',
            'header'        => "Authorization: cpanel " . CPANEL_USER . ":" . CPANEL_API_TOKEN . "\r\n",
            'timeout'       => 15,
            'ignore_errors' => true,
        ], 'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]);
        $resp = @file_get_contents($url, false, $ctx);
        return $resp ? (json_decode($resp, true) ?? ['status' => 0, 'error' => 'Invalid JSON']) : ['status' => 0, 'error' => 'No response from cPanel API'];
    }

    // ── Check if exec() is usable ─────────────────────────
    function can_exec(): bool {
        if (!function_exists('exec')) return false;
        $disabled = array_map('trim', explode(',', (string)ini_get('disable_functions')));
        return !in_array('exec', $disabled);
    }

    // One-click deploy: try direct git, fall back to cPanel UAPI
    if (isset($_POST['cpanel_deploy'])) {
        $repo   = CPANEL_REPO_ROOT;
        $backup = '/home/pagezy/pagezy_config_backup.php';
        $ok     = false;
        $msg    = '';

        $token  = defined('GITHUB_TOKEN') ? GITHUB_TOKEN : '';
        $ghRepo = defined('GITHUB_MARKETING_REPO') ? GITHUB_MARKETING_REPO : '';

        // Method 1: direct git via exec()
        if (can_exec()) {
            $safeRepo = escapeshellarg($repo);

            // Permanently protect config.php from git reset/checkout — harmless to run every time
            exec('cd ' . $safeRepo . ' && git update-index --skip-worktree config.php 2>&1');

            if ($token && $ghRepo) {
                $authUrl  = 'https://' . $token . '@github.com/' . $ghRepo . '.git';
                $fetchCmd = 'cd ' . $safeRepo . ' && git fetch ' . escapeshellarg($authUrl) . ' +refs/heads/main:refs/heads/main 2>&1';
            } else {
                $fetchCmd = 'cd ' . $safeRepo . ' && git fetch origin +refs/heads/main:refs/remotes/origin/main 2>&1';
            }
            $resetCmd = 'cd ' . $safeRepo . ' && git reset --hard refs/heads/main 2>&1';

            $out = []; $ret = 0;
            exec($fetchCmd, $out, $ret);
            if ($ret === 0) {
                $out2 = []; $ret2 = 0;
                exec($resetCmd, $out2, $ret2);
                if ($ret2 === 0) {
                    $ok  = true;
                    $msg = "✓ Deployed successfully via git! " . trim(end($out2));
                } else {
                    $msg = "✗ git reset failed: " . implode(' ', $out2);
                }
            } else {
                $msg = "✗ git fetch failed: " . implode(' ', $out);
            }
        } else {
            $msg = "exec() disabled.";
        }

        // Method 2: cPanel UAPI fallback
        if (!$ok) {
            // Snapshot real config.php NOW before cPanel's git reset can delete/overwrite it
            $configFile = $repo . '/config.php';
            $configSnap = file_exists($configFile) ? file_get_contents($configFile) : null;

            $pull  = cpanel_api('VersionControl', 'update');
            $pull2 = (($pull['status'] ?? 0) !== 1) ? cpanel_api('VersionControl', 'update', ['_no_slash' => true]) : null;
            $pull  = (($pull['status'] ?? 0) === 1) ? $pull : ($pull2 ?? $pull);
            if (($pull['status'] ?? 0) === 1) {
                // Restore real config.php — cPanel's git reset may delete it (was previously tracked)
                if ($configSnap !== null) file_put_contents($configFile, $configSnap);
                $ok  = true;
                $msg = "✓ Deployed via cPanel API.";
            } else {
                $errDetail = $pull['errors'][0] ?? $pull['error'] ?? json_encode($pull);
                $msg .= " | cPanel API: " . $errDetail;
                $msg .= " | exec: " . (can_exec() ? 'yes' : 'no') . " | token: " . ($token ? 'yes ('.strlen($token).'ch)' : 'no');
            }
        }

        $_SESSION['flash'] = ['type' => $ok ? 'ok' : 'err', 'msg' => $msg];
        header('Location: ?tab=deploy'); exit;
    }

    // Fetch latest CMS release from GitHub
    if (isset($_POST['fetch_release'])) {
        $headers = "User-Agent: Pagezy-Admin/1.0\r\nAccept: application/vnd.github.v3+json\r\n";
        if (GITHUB_TOKEN) $headers .= "Authorization: Bearer " . GITHUB_TOKEN . "\r\n";
        $ctx = stream_context_create(['http' => ['header' => $headers, 'timeout' => 10]]);

        // Try latest release first
        $json = @file_get_contents('https://api.github.com/repos/' . GITHUB_CMS_REPO . '/releases/latest', false, $ctx);
        $data = $json ? json_decode($json, true) : null;

        if (!empty($data['tag_name'])) {
            // Has a formal release
            $tag = $data['tag_name'];
            $url = $data['zipball_url'] ?? '';
            foreach ($data['assets'] ?? [] as $asset) {
                if (str_ends_with($asset['name'], '.zip')) { $url = $asset['browser_download_url']; break; }
            }
            $name = $data['name'] ?? $tag;
            $body = $data['body'] ?? '';
            $published = $data['published_at'] ?? '';
        } else {
            // No release — fall back to latest commit on main branch
            $branchJson = @file_get_contents('https://api.github.com/repos/' . GITHUB_CMS_REPO . '/branches/main', false, $ctx);
            $branch = $branchJson ? json_decode($branchJson, true) : null;
            $sha  = $branch['commit']['sha'] ?? 'main';
            $short = substr($sha, 0, 7);
            $tag  = 'main@' . $short;
            $name = 'Latest build — ' . $short;
            $url  = 'https://github.com/' . GITHUB_CMS_REPO . '/archive/refs/heads/main.zip';
            $body = 'No formal release yet — serving latest main branch.';
            $published = $branch['commit']['commit']['author']['date'] ?? date('c');
        }

        $cache = [
            'tag'          => $tag,
            'name'         => $name,
            'download_url' => $url,
            'body'         => $body,
            'published_at' => $published,
            'fetched_at'   => date('c'),
        ];
        if (!is_dir(dirname(RELEASE_CACHE))) mkdir(dirname(RELEASE_CACHE), 0755, true);
        file_put_contents(RELEASE_CACHE, json_encode($cache, JSON_PRETTY_PRINT));
        $_SESSION['flash'] = ['type'=>'ok', 'msg' => "✓ Download link updated — " . $tag];
        header('Location: ?tab=releases'); exit;
    }

    // Delete a lead
    if (isset($_POST['delete_lead'])) {
        $id    = (int)$_POST['delete_lead'];
        $table = $_POST['lead_type'] === 'contact' ? 'pagezy_contacts' : 'pagezy_downloads';
        try { db()->prepare("DELETE FROM $table WHERE id=?")->execute([$id]); } catch(Exception $e){}
        header('Location: ' . $_SERVER['PHP_SELF'] . '?tab=' . ($_POST['lead_type'] === 'contact' ? 'contacts' : 'downloads'));
        exit;
    }

    // CSV export
    if (isset($_GET['export'])) {
        $type = $_GET['export'];
        if ($type === 'downloads') {
            ensureTable();
            $rows = db()->query("SELECT * FROM pagezy_downloads ORDER BY created_at DESC")->fetchAll();
            $cols = ['id','name','email','city','company','use_case','plan','ip_address','downloaded','created_at'];
        } else {
            ensureContactTable();
            $rows = db()->query("SELECT * FROM pagezy_contacts ORDER BY created_at DESC")->fetchAll();
            $cols = ['id','name','email','phone','company','subject','message','ip_address','created_at'];
        }
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="pagezy-' . $type . '-' . date('Y-m-d') . '.csv"');
        $f = fopen('php://output','w');
        fputcsv($f, $cols);
        foreach ($rows as $r) { fputcsv($f, array_map(fn($c) => $r[$c] ?? '', $cols)); }
        fclose($f); exit;
    }
}

// ── Data ──────────────────────────────────────────────────
$tab     = $_GET['tab'] ?? 'dashboard';
$stats   = [];
$downloads = [];
$contacts  = [];
$release   = file_exists(RELEASE_CACHE) ? json_decode(file_get_contents(RELEASE_CACHE), true) : null;

if ($authed) {
    try {
        ensureTable(); ensureContactTable();
        $stats['total_dl']      = db()->query("SELECT COUNT(*) FROM pagezy_downloads")->fetchColumn();
        $stats['total_ct']      = db()->query("SELECT COUNT(*) FROM pagezy_contacts")->fetchColumn();
        $stats['today_dl']      = db()->query("SELECT COUNT(*) FROM pagezy_downloads WHERE DATE(created_at)=CURDATE()")->fetchColumn();
        $stats['today_ct']      = db()->query("SELECT COUNT(*) FROM pagezy_contacts WHERE DATE(created_at)=CURDATE()")->fetchColumn();
        $stats['week_dl']       = db()->query("SELECT COUNT(*) FROM pagezy_downloads WHERE created_at >= DATE_SUB(NOW(),INTERVAL 7 DAY)")->fetchColumn();
        $stats['week_ct']       = db()->query("SELECT COUNT(*) FROM pagezy_contacts WHERE created_at >= DATE_SUB(NOW(),INTERVAL 7 DAY)")->fetchColumn();
        $downloads = db()->query("SELECT * FROM pagezy_downloads ORDER BY created_at DESC")->fetchAll();
        $contacts  = db()->query("SELECT * FROM pagezy_contacts ORDER BY created_at DESC")->fetchAll();
    } catch (Exception $e) {
        $stats = ['total_dl'=>0,'total_ct'=>0,'today_dl'=>0,'today_ct'=>0,'week_dl'=>0,'week_ct'=>0];
    }
}

// ── Helpers ───────────────────────────────────────────────
function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function timeAgo(string $ts): string {
    if (!$ts) return '—';
    // Format directly from MySQL datetime string to avoid PHP/MySQL timezone mismatch
    $months = ['','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    if (preg_match('/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2})/', $ts, $m)) {
        return $m[3] . ' ' . ($months[(int)$m[2]] ?? $m[2]) . ' ' . $m[1] . ', ' . $m[4] . ':' . $m[5];
    }
    return $ts;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pagezy Admin</title>
<link rel="icon" href="/assets/img/pagezy-logo.png" type="image/png">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --bg:      #07060F;
    --card:    #0F0D1E;
    --border:  rgba(255,255,255,.08);
    --text:    #F1F5F9;
    --muted:   #6B7280;
    --indigo:  #4F46E5;
    --purple:  #7C3AED;
    --green:   #10B981;
    --red:     #EF4444;
    --yellow:  #F59E0B;
}
body { background: var(--bg); color: var(--text); font-family: Inter, system-ui, sans-serif; font-size: 14px; min-height: 100vh; }

/* ── Login ── */
.login-wrap { min-height: 100vh; display: flex; align-items: center; justify-content: center; }
.login-card { background: var(--card); border: 1px solid var(--border); border-radius: 20px; padding: 40px; width: 360px; text-align: center; }
.login-logo { font-size: 24px; font-weight: 900; letter-spacing: -.04em; margin-bottom: 6px; }
.login-logo span { background: linear-gradient(135deg,#5B8DEF,#7C3AED); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.login-sub  { color: var(--muted); font-size: 13px; margin-bottom: 28px; }
.login-card input { width: 100%; padding: 12px 16px; background: rgba(255,255,255,.05); border: 1px solid var(--border); border-radius: 10px; color: var(--text); font-size: 14px; margin-bottom: 12px; }
.login-card input:focus { outline: none; border-color: rgba(99,102,241,.5); }
.login-error { color: var(--red); font-size: 13px; margin-bottom: 12px; }

/* ── Layout ── */
.shell { display: grid; grid-template-columns: 220px 1fr; min-height: 100vh; }
.sidebar { background: var(--card); border-right: 1px solid var(--border); padding: 24px 0; display: flex; flex-direction: column; position: sticky; top: 0; height: 100vh; overflow-y: auto; }
.sidebar-logo { padding: 0 20px 20px; border-bottom: 1px solid var(--border); margin-bottom: 12px; }
.sidebar-logo-text { font-size: 18px; font-weight: 900; letter-spacing: -.04em; }
.sidebar-logo-text span { background: linear-gradient(135deg,#5B8DEF,#7C3AED); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.sidebar-label { font-size: 10px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--muted); padding: 16px 20px 6px; }
.sidebar nav a {
    display: flex; align-items: center; gap: 10px;
    padding: 9px 20px; color: var(--muted); text-decoration: none;
    font-size: 13.5px; font-weight: 500; border-radius: 0;
    transition: color .15s, background .15s;
}
.sidebar nav a:hover { color: var(--text); background: rgba(255,255,255,.04); }
.sidebar nav a.active { color: var(--text); background: rgba(99,102,241,.12); border-right: 2px solid var(--indigo); }
.sidebar nav a svg { width: 16px; height: 16px; flex-shrink: 0; }
.sidebar-footer { margin-top: auto; padding: 16px 20px; border-top: 1px solid var(--border); }
.sidebar-footer form button { background: none; border: 1px solid var(--border); color: var(--muted); padding: 7px 14px; border-radius: 8px; cursor: pointer; font-size: 12px; width: 100%; }
.sidebar-footer form button:hover { color: var(--text); border-color: rgba(255,255,255,.2); }

/* ── Main ── */
.main { padding: 32px 36px; min-width: 0; }
.page-title { font-size: 22px; font-weight: 800; letter-spacing: -.03em; margin-bottom: 4px; }
.page-sub   { color: var(--muted); font-size: 13px; margin-bottom: 28px; }

/* ── Stats grid ── */
.stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 28px; }
.stat-card { background: var(--card); border: 1px solid var(--border); border-radius: 14px; padding: 20px 22px; }
.stat-label { font-size: 11px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: var(--muted); margin-bottom: 8px; }
.stat-val   { font-size: 32px; font-weight: 900; letter-spacing: -.04em; line-height: 1; }
.stat-sub   { font-size: 12px; color: var(--muted); margin-top: 6px; }
.stat-val.green { color: var(--green); }
.stat-val.blue  { background: linear-gradient(135deg,#5B8DEF,#7C3AED); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }

/* ── Cards ── */
.card { background: var(--card); border: 1px solid var(--border); border-radius: 16px; padding: 24px; margin-bottom: 20px; }
.card-title { font-size: 15px; font-weight: 700; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between; }
.badge { display: inline-flex; align-items: center; font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 50px; }
.badge-green  { background: rgba(16,185,129,.15); color: var(--green); }
.badge-indigo { background: rgba(79,70,229,.15); color: #818CF8; }
.badge-yellow { background: rgba(245,158,11,.12); color: var(--yellow); }
.badge-red    { background: rgba(239,68,68,.12); color: var(--red); }

/* ── Table ── */
.tbl-wrap { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; font-size: 13px; }
th { text-align: left; padding: 8px 12px; font-size: 11px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: var(--muted); border-bottom: 1px solid var(--border); }
td { padding: 11px 12px; border-bottom: 1px solid rgba(255,255,255,.04); vertical-align: top; }
tr:last-child td { border-bottom: none; }
tr:hover td { background: rgba(255,255,255,.02); }
td a { color: #818CF8; text-decoration: none; }
td a:hover { text-decoration: underline; }
.td-muted { color: var(--muted); }

/* ── Buttons ── */
.btn { display: inline-flex; align-items: center; gap: 7px; padding: 9px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; border: none; text-decoration: none; transition: all .2s; }
.btn-primary { background: linear-gradient(135deg,var(--indigo),var(--purple)); color: #fff; }
.btn-primary:hover { opacity: .88; transform: translateY(-1px); }
.btn-outline { background: transparent; color: var(--muted); border: 1px solid var(--border); }
.btn-outline:hover { color: var(--text); border-color: rgba(255,255,255,.2); }
.btn-red { background: rgba(239,68,68,.1); color: var(--red); border: 1px solid rgba(239,68,68,.2); }
.btn-red:hover { background: rgba(239,68,68,.2); }
.btn-sm { padding: 5px 12px; font-size: 12px; border-radius: 7px; }
.btn-green { background: rgba(16,185,129,.12); color: var(--green); border: 1px solid rgba(16,185,129,.2); }
.btn-green:hover { background: rgba(16,185,129,.22); }

/* ── Action message ── */
.action-msg { background: rgba(16,185,129,.08); border: 1px solid rgba(16,185,129,.2); border-radius: 10px; padding: 12px 16px; font-size: 13px; color: var(--green); margin-bottom: 20px; white-space: pre-wrap; font-family: monospace; }
.action-msg.err { background: rgba(239,68,68,.08); border-color: rgba(239,68,68,.2); color: var(--red); }

/* ── Deploy / Release ── */
.deploy-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.info-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid var(--border); font-size: 13px; }
.info-row:last-child { border-bottom: none; }
.info-row .label { color: var(--muted); }
.info-row .val   { font-weight: 600; font-family: monospace; font-size: 12px; }
.release-notes { background: rgba(255,255,255,.03); border: 1px solid var(--border); border-radius: 10px; padding: 14px; font-size: 12px; color: var(--muted); white-space: pre-wrap; max-height: 160px; overflow-y: auto; font-family: monospace; margin-top: 12px; }

/* ── Tabs ── */
.tab-bar { display: flex; gap: 4px; margin-bottom: 20px; background: rgba(255,255,255,.04); border-radius: 10px; padding: 4px; width: fit-content; }
.tab-bar a { padding: 7px 18px; border-radius: 7px; color: var(--muted); text-decoration: none; font-size: 13px; font-weight: 500; }
.tab-bar a.active { background: var(--card); color: var(--text); box-shadow: 0 1px 4px rgba(0,0,0,.3); }

/* ── Recent activity ── */
.activity-item { display: flex; align-items: flex-start; gap: 12px; padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,.04); }
.activity-item:last-child { border-bottom: none; }
.activity-avatar { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0; }
.activity-info { flex: 1; min-width: 0; }
.activity-name  { font-weight: 600; font-size: 13px; }
.activity-meta  { font-size: 12px; color: var(--muted); margin-top: 2px; }
.activity-time  { font-size: 11px; color: var(--muted); flex-shrink: 0; }

@media (max-width: 900px) {
    .shell { grid-template-columns: 1fr; }
    .sidebar { height: auto; position: static; flex-direction: row; flex-wrap: wrap; }
    .stats-grid { grid-template-columns: 1fr 1fr; }
    .deploy-grid { grid-template-columns: 1fr; }
    .main { padding: 20px; }
}
</style>
</head>
<body>

<?php if (!$authed): ?>
<!-- ── Login Screen ── -->
<div class="login-wrap">
    <div class="login-card">
        <div class="login-logo">Page<span>zy</span></div>
        <div class="login-sub">Marketing Site Admin</div>
        <?php if ($error): ?><div class="login-error"><?= h($error) ?></div><?php endif; ?>
        <form method="POST">
            <input type="password" name="password" placeholder="Admin password" autofocus>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Sign in →</button>
        </form>
    </div>
</div>

<?php else: ?>
<!-- ── Admin Shell ── -->
<div class="shell">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="sidebar-logo-text">Page<span>zy</span> Admin</div>
        </div>
        <div class="sidebar-label">Main</div>
        <nav>
            <a href="?tab=dashboard" class="<?= $tab==='dashboard'?'active':'' ?>">
                <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                Dashboard
            </a>
            <a href="?tab=downloads" class="<?= $tab==='downloads'?'active':'' ?>">
                <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Downloads
                <?php if(($stats['today_dl']??0) > 0): ?><span class="badge badge-green" style="margin-left:auto;"><?= $stats['today_dl'] ?></span><?php endif; ?>
            </a>
            <a href="?tab=contacts" class="<?= $tab==='contacts'?'active':'' ?>">
                <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                Enquiries
                <?php if(($stats['today_ct']??0) > 0): ?><span class="badge badge-yellow" style="margin-left:auto;"><?= $stats['today_ct'] ?></span><?php endif; ?>
            </a>
        </nav>
        <div class="sidebar-label">System</div>
        <nav>
            <a href="?tab=deploy" class="<?= $tab==='deploy'?'active':'' ?>">
                <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                Deploy
            </a>
            <a href="?tab=releases" class="<?= $tab==='releases'?'active':'' ?>">
                <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                CMS Releases
            </a>
        </nav>
        <div class="sidebar-label">Links</div>
        <nav>
            <a href="/" target="_blank">
                <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                View Live Site
            </a>
            <a href="https://github.com/<?= GITHUB_MARKETING_REPO ?>" target="_blank">
                <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.3 3.44 9.8 8.2 11.38.6.11.82-.26.82-.58v-2.17c-3.34.73-4.04-1.61-4.04-1.61-.55-1.38-1.33-1.75-1.33-1.75-1.09-.74.08-.73.08-.73 1.2.08 1.84 1.24 1.84 1.24 1.07 1.83 2.8 1.3 3.49 1 .1-.78.42-1.3.76-1.6-2.67-.3-5.47-1.33-5.47-5.93 0-1.31.47-2.38 1.24-3.22-.13-.3-.54-1.52.12-3.18 0 0 1.01-.32 3.3 1.23a11.5 11.5 0 0 1 3-.4c1.02 0 2.04.13 3 .4 2.28-1.55 3.3-1.23 3.3-1.23.66 1.66.24 2.88.12 3.18.77.84 1.23 1.91 1.23 3.22 0 4.61-2.81 5.63-5.48 5.92.43.37.81 1.1.81 2.22v3.29c0 .32.22.7.83.58C20.57 21.8 24 17.3 24 12c0-6.63-5.37-12-12-12z"/></svg>
                Marketing Repo
            </a>
            <a href="https://github.com/<?= GITHUB_CMS_REPO ?>" target="_blank">
                <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.3 3.44 9.8 8.2 11.38.6.11.82-.26.82-.58v-2.17c-3.34.73-4.04-1.61-4.04-1.61-.55-1.38-1.33-1.75-1.33-1.75-1.09-.74.08-.73.08-.73 1.2.08 1.84 1.24 1.84 1.24 1.07 1.83 2.8 1.3 3.49 1 .1-.78.42-1.3.76-1.6-2.67-.3-5.47-1.33-5.47-5.93 0-1.31.47-2.38 1.24-3.22-.13-.3-.54-1.52.12-3.18 0 0 1.01-.32 3.3 1.23a11.5 11.5 0 0 1 3-.4c1.02 0 2.04.13 3 .4 2.28-1.55 3.3-1.23 3.3-1.23.66 1.66.24 2.88.12 3.18.77.84 1.23 1.91 1.23 3.22 0 4.61-2.81 5.63-5.48 5.92.43.37.81 1.1.81 2.22v3.29c0 .32.22.7.83.58C20.57 21.8 24 17.3 24 12c0-6.63-5.37-12-12-12z"/></svg>
                CMS Repo
            </a>
        </nav>
        <div class="sidebar-footer">
            <form method="POST"><button type="submit" name="logout" value="1">Sign out</button></form>
        </div>
    </aside>

    <!-- Main content -->
    <main class="main">

    <?php if ($actionMsg): ?>
        <div class="action-msg <?= ($actionMsgType === 'err') ? 'err' : '' ?>"><?= h($actionMsg) ?></div>
    <?php endif; ?>

    <?php
    // ── Dashboard ────────────────────────────────────────
    if ($tab === 'dashboard'): ?>

        <div class="page-title">Dashboard</div>
        <div class="page-sub">Welcome back. Here's what's happening on Pagezy.io</div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Downloads</div>
                <div class="stat-val blue"><?= number_format($stats['total_dl']) ?></div>
                <div class="stat-sub"><?= $stats['week_dl'] ?> this week · <?= $stats['today_dl'] ?> today</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Contact Enquiries</div>
                <div class="stat-val blue"><?= number_format($stats['total_ct']) ?></div>
                <div class="stat-sub"><?= $stats['week_ct'] ?> this week · <?= $stats['today_ct'] ?> today</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Leads</div>
                <div class="stat-val green"><?= number_format($stats['total_dl'] + $stats['total_ct']) ?></div>
                <div class="stat-sub"><?= ($stats['today_dl']+$stats['today_ct']) ?> new today</div>
            </div>
        </div>

        <!-- CMS Release status -->
        <div class="card">
            <div class="card-title">
                CMS Release
                <?php if ($release): ?>
                    <span class="badge badge-green"><?= h($release['tag']) ?></span>
                <?php else: ?>
                    <span class="badge badge-red">Not configured</span>
                <?php endif; ?>
            </div>
            <?php if ($release): ?>
                <div class="info-row"><span class="label">Version</span><span class="val"><?= h($release['name']) ?></span></div>
                <div class="info-row"><span class="label">Download URL</span><span class="val" style="max-width:380px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= h($release['download_url']) ?></span></div>
                <div class="info-row"><span class="label">Cache updated</span><span class="val"><?= h($release['fetched_at']) ?></span></div>
            <?php else: ?>
                <p style="color:var(--muted);font-size:13px;">No release cached yet. Go to <a href="?tab=releases" style="color:#818CF8;">CMS Releases</a> to fetch the latest from GitHub.</p>
            <?php endif; ?>
        </div>

        <!-- Recent activity -->
        <div class="card">
            <div class="card-title">Recent Leads</div>
            <?php
            $gradients = ['linear-gradient(135deg,#6D28D9,#4F46E5)','linear-gradient(135deg,#0EA5E9,#6D28D9)','linear-gradient(135deg,#10B981,#0EA5E9)','linear-gradient(135deg,#F59E0B,#EF4444)','linear-gradient(135deg,#EC4899,#8B5CF6)'];
            $recent = array_slice(array_merge(
                array_map(fn($r) => array_merge($r, ['_type'=>'download']), $downloads),
                array_map(fn($r) => array_merge($r, ['_type'=>'contact']), $contacts)
            ), 0);
            usort($recent, fn($a,$b) => strtotime($b['created_at']) - strtotime($a['created_at']));
            $recent = array_slice($recent, 0, 10);
            foreach ($recent as $i => $r): ?>
            <div class="activity-item">
                <div class="activity-avatar" style="background:<?= $gradients[$i%5] ?>"><?= strtoupper(substr($r['name'],0,1)) ?></div>
                <div class="activity-info">
                    <div class="activity-name"><?= h($r['name']) ?> <span class="badge <?= $r['_type']==='download'?'badge-indigo':'badge-yellow' ?>" style="font-size:10px;"><?= $r['_type'] === 'download' ? 'download' : 'enquiry' ?></span></div>
                    <div class="activity-meta"><?= h($r['email']) ?><?= !empty($r['company']) ? ' · ' . h($r['company']) : '' ?></div>
                </div>
                <div class="activity-time"><?= timeAgo($r['created_at']) ?></div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($recent)): ?><p style="color:var(--muted);font-size:13px;">No leads yet.</p><?php endif; ?>
        </div>

    <?php
    // ── Downloads ────────────────────────────────────────
    elseif ($tab === 'downloads'): ?>

        <div class="page-title">Download Leads</div>
        <div class="page-sub"><?= count($downloads) ?> total download requests</div>

        <div style="display:flex;gap:10px;margin-bottom:20px;">
            <a href="?export=downloads" class="btn btn-outline btn-sm">Export CSV</a>
        </div>

        <div class="card" style="padding:0;">
            <div class="tbl-wrap">
            <table>
                <thead><tr>
                    <th>Name</th><th>Email</th><th>Company</th><th>City</th>
                    <th>Use Case</th><th>Plan</th><th>Downloaded</th><th>Date</th><th></th>
                </tr></thead>
                <tbody>
                <?php foreach ($downloads as $r): ?>
                <tr>
                    <td><strong><?= h($r['name']) ?></strong></td>
                    <td><a href="mailto:<?= h($r['email']) ?>"><?= h($r['email']) ?></a></td>
                    <td class="td-muted"><?= h($r['company'] ?? '—') ?></td>
                    <td class="td-muted"><?= h($r['city'] ?? '—') ?></td>
                    <td class="td-muted"><?= h($r['use_case'] ?? '—') ?></td>
                    <td><span class="badge badge-indigo"><?= h($r['plan'] ?? 'free') ?></span></td>
                    <td><?= $r['downloaded'] ? '<span class="badge badge-green">Yes</span>' : '<span class="badge" style="background:rgba(255,255,255,.05);color:var(--muted);">No</span>' ?></td>
                    <td class="td-muted" title="<?= h($r['created_at']) ?>"><?= timeAgo($r['created_at']) ?></td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Delete this lead?')">
                            <input type="hidden" name="lead_type" value="download">
                            <button name="delete_lead" value="<?= $r['id'] ?>" class="btn btn-red btn-sm">Del</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($downloads)): ?><tr><td colspan="9" style="color:var(--muted);text-align:center;padding:32px;">No download leads yet.</td></tr><?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>

    <?php
    // ── Contacts ─────────────────────────────────────────
    elseif ($tab === 'contacts'): ?>

        <div class="page-title">Contact Enquiries</div>
        <div class="page-sub"><?= count($contacts) ?> total enquiries</div>

        <div style="display:flex;gap:10px;margin-bottom:20px;">
            <a href="?export=contacts" class="btn btn-outline btn-sm">Export CSV</a>
        </div>

        <div class="card" style="padding:0;">
            <div class="tbl-wrap">
            <table>
                <thead><tr>
                    <th>Name</th><th>Email</th><th>Phone</th><th>Company</th>
                    <th>Subject</th><th>Message</th><th>Date</th><th></th>
                </tr></thead>
                <tbody>
                <?php foreach ($contacts as $r): ?>
                <tr>
                    <td><strong><?= h($r['name']) ?></strong></td>
                    <td><a href="mailto:<?= h($r['email']) ?>"><?= h($r['email']) ?></a></td>
                    <td class="td-muted"><?= h($r['phone'] ?? '—') ?></td>
                    <td class="td-muted"><?= h($r['company'] ?? '—') ?></td>
                    <td><span class="badge badge-yellow"><?= h($r['subject'] ?? '—') ?></span></td>
                    <td class="td-muted" style="max-width:240px;white-space:pre-line;font-size:12px;"><?= h(mb_substr($r['message'],0,120)) ?><?= mb_strlen($r['message'])>120?'…':'' ?></td>
                    <td class="td-muted" title="<?= h($r['created_at']) ?>"><?= timeAgo($r['created_at']) ?></td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Delete this enquiry?')">
                            <input type="hidden" name="lead_type" value="contact">
                            <button name="delete_lead" value="<?= $r['id'] ?>" class="btn btn-red btn-sm">Del</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($contacts)): ?><tr><td colspan="8" style="color:var(--muted);text-align:center;padding:32px;">No enquiries yet.</td></tr><?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>

    <?php
    // ── Deploy ───────────────────────────────────────────
    elseif ($tab === 'deploy'):
        $tokenSet = defined('CPANEL_API_TOKEN') && CPANEL_API_TOKEN !== '';

        // System health checks
        $dbOk = false; $dbMsg = '';
        try { db()->query('SELECT 1'); $dbOk = true; $dbMsg = 'Connected to ' . DB_NAME; }
        catch (Throwable $e) { $dbMsg = $e->getMessage(); }

        $dlCount = 0; $enqCount = 0;
        if ($dbOk) {
            try { $dlCount  = (int) db()->query("SELECT COUNT(*) FROM pagezy_downloads")->fetchColumn(); } catch(Throwable $e) {}
            try { $enqCount = (int) db()->query("SELECT COUNT(*) FROM pagezy_contacts")->fetchColumn(); } catch(Throwable $e) {}
        }

        $smtpConfigured = defined('SMTP_HOST') && SMTP_HOST !== '' && SMTP_USER !== '';
        $smtpInfo = SMTP_USER . ' via ' . SMTP_HOST . ':' . SMTP_PORT;

        // Handle test email
        $testEmailResult = '';
        if (isset($_POST['test_email'])) {
            try {
                Mailer::send(NOTIFY_TO, 'Pagezy SMTP Test', '<p style="font-family:sans-serif;">SMTP is working correctly from <strong>' . SMTP_USER . '</strong>.</p>');
                $testEmailResult = 'ok';
            } catch (Throwable $e) {
                $testEmailResult = $e->getMessage();
            }
        }
    ?>

        <div class="page-title">Deploy</div>
        <div class="page-sub">Pull latest code from GitHub and go live — directly from this panel.</div>

        <!-- System Status -->
        <div class="card" style="border-color:<?= ($dbOk && $smtpConfigured) ? 'rgba(52,211,153,.25)' : 'rgba(245,158,11,.3)' ?>;">
            <div class="card-title">System Status</div>
            <div class="info-row">
                <span class="label">Database</span>
                <span class="val" style="color:<?= $dbOk ? '#34D399' : '#F87171' ?>;">
                    <?= $dbOk ? '✓ ' : '✗ ' ?><?= h($dbMsg) ?>
                    <?php if ($dbOk): ?> &nbsp;·&nbsp; <?= $dlCount ?> download leads &nbsp;·&nbsp; <?= $enqCount ?> enquiries<?php endif; ?>
                </span>
            </div>
            <div class="info-row">
                <span class="label">Email (SMTP)</span>
                <span class="val" style="color:<?= $smtpConfigured ? '#34D399' : '#F87171' ?>;">
                    <?= $smtpConfigured ? '✓ ' : '✗ Not configured' ?><?= $smtpConfigured ? h($smtpInfo) : '' ?>
                </span>
            </div>
            <?php if ($testEmailResult === 'ok'): ?>
            <div style="background:rgba(52,211,153,.1);border:1px solid rgba(52,211,153,.25);border-radius:8px;padding:10px 14px;font-size:13px;color:#34D399;margin-top:12px;">
                ✓ Test email sent to <?= h(NOTIFY_TO) ?> — check your inbox.
            </div>
            <?php elseif ($testEmailResult): ?>
            <div style="background:rgba(248,113,113,.1);border:1px solid rgba(248,113,113,.25);border-radius:8px;padding:10px 14px;font-size:13px;color:#F87171;margin-top:12px;">
                ✗ SMTP error: <?= h($testEmailResult) ?>
            </div>
            <?php endif; ?>
            <form method="POST" style="margin-top:14px;">
                <button name="test_email" value="1" class="btn btn-outline btn-sm">Send test email → <?= h(NOTIFY_TO) ?></button>
            </form>
        </div>

        <!-- One-click deploy -->
        <div class="card" style="border-color:<?= $tokenSet ? 'rgba(99,102,241,.3)' : 'rgba(245,158,11,.3)' ?>;">
            <div class="card-title">
                One-Click Deploy
                <?= $tokenSet ? '<span class="badge badge-green">Ready</span>' : '<span class="badge badge-yellow">Setup required</span>' ?>
            </div>
            <?php if ($tokenSet): ?>
                <p style="color:var(--muted);font-size:13px;margin-bottom:20px;">Pulls the latest code from GitHub (<code style="background:rgba(255,255,255,.07);padding:1px 6px;border-radius:5px;"><?= GITHUB_MARKETING_REPO ?></code>) and deploys it to <code style="background:rgba(255,255,255,.07);padding:1px 6px;border-radius:5px;"><?= CPANEL_REPO_ROOT ?></code> — same as clicking "Update from Remote" + "Deploy HEAD Commit" in cPanel.</p>
                <form method="POST">
                    <button name="cpanel_deploy" value="1" class="btn btn-primary" style="font-size:15px;padding:12px 28px;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                        Pull &amp; Deploy Now
                    </button>
                </form>
            <?php else: ?>
                <p style="color:var(--muted);font-size:13px;margin-bottom:16px;">Add your cPanel API token to <code style="background:rgba(255,255,255,.07);padding:1px 6px;border-radius:5px;">config.php</code> to enable one-click deploy.</p>
                <div style="background:rgba(255,255,255,.03);border:1px solid var(--border);border-radius:10px;padding:16px;margin-bottom:16px;">
                    <div style="font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--muted);margin-bottom:12px;">How to get your cPanel API token</div>
                    <?php foreach ([
                        'Log in to <strong>cPanel</strong> → <strong>Security</strong> → <strong>Manage API Tokens</strong>',
                        'Click <strong>Create API Token</strong> → give it a name (e.g. <code style="background:rgba(255,255,255,.07);padding:1px 5px;border-radius:4px;">pagezy-admin</code>) → no expiry',
                        'Copy the token → open <code style="background:rgba(255,255,255,.07);padding:1px 5px;border-radius:4px;">config.php</code> on the server → paste it into <code style="background:rgba(255,255,255,.07);padding:1px 5px;border-radius:4px;">CPANEL_API_TOKEN</code>',
                    ] as $i => $s): ?>
                    <div style="display:flex;gap:12px;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.04);font-size:13px;color:var(--muted);">
                        <span style="width:22px;height:22px;border-radius:50%;background:rgba(99,102,241,.2);color:#818CF8;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><?= $i+1 ?></span>
                        <span><?= $s ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <a href="https://cpanel.<?= str_replace(['https://','http://'], '', SITE_URL) ?>/security/manage-api-tokens" target="_blank" class="btn btn-outline">Open cPanel API Tokens ↗</a>
            <?php endif; ?>
        </div>

        <!-- Last deployed commit -->
        <?php
        $gitDir    = CPANEL_REPO_ROOT . '/.git';
        $liveSha   = '';
        $liveMsg   = '';
        $liveTime  = '';
        $liveAuthor = '';
        // Read SHA from local .git
        if (is_dir($gitDir)) {
            $shaFull = trim((string)@file_get_contents($gitDir . '/refs/heads/main'));
            if ($shaFull) $liveSha = substr($shaFull, 0, 7);
        }
        // Fetch commit details from GitHub API (reliable for message/author/date)
        if ($liveSha) {
            $ghHeaders = "User-Agent: Pagezy-Admin/1.0\r\nAccept: application/vnd.github.v3+json\r\n";
            if (GITHUB_TOKEN) $ghHeaders .= "Authorization: Bearer " . GITHUB_TOKEN . "\r\n";
            $ghCtx = stream_context_create(['http' => ['header' => $ghHeaders, 'timeout' => 5, 'ignore_errors' => true]]);
            $ghJson = @file_get_contents(
                'https://api.github.com/repos/' . GITHUB_MARKETING_REPO . '/commits/' . $liveSha,
                false, $ghCtx
            );
            $ghData = $ghJson ? json_decode($ghJson, true) : null;
            if (!empty($ghData['commit'])) {
                $lines      = explode("\n", trim($ghData['commit']['message'] ?? ''));
                $liveMsg    = $lines[0];
                $liveAuthor = $ghData['commit']['author']['name'] ?? '';
                $rawDate    = $ghData['commit']['author']['date'] ?? '';
                if ($rawDate) $liveTime = date('d M Y, H:i', strtotime($rawDate)) . ' UTC';
            }
        }
        ?>
        <div class="card">
            <div class="card-title">
                Live Deployment
                <?php if ($liveSha): ?>
                <a href="https://github.com/<?= GITHUB_MARKETING_REPO ?>/commit/<?= $liveSha ?>" target="_blank"
                   style="font-family:monospace;font-size:12px;color:#818CF8;font-weight:600;text-decoration:none;background:rgba(99,102,241,.12);padding:3px 10px;border-radius:6px;">
                    <?= $liveSha ?> ↗
                </a>
                <?php endif; ?>
            </div>
            <?php if ($liveSha): ?>
            <div class="info-row">
                <span class="label">Commit</span>
                <span class="val" style="max-width:70%;text-align:right;white-space:normal;line-height:1.4;"><?= h($liveMsg) ?></span>
            </div>
            <?php if ($liveAuthor): ?>
            <div class="info-row"><span class="label">Author</span><span class="val"><?= h($liveAuthor) ?></span></div>
            <?php endif; ?>
            <?php if ($liveTime): ?>
            <div class="info-row"><span class="label">Deployed at</span><span class="val"><?= h($liveTime) ?></span></div>
            <?php endif; ?>
            <div class="info-row">
                <span class="label">Branch</span>
                <span class="val">main</span>
            </div>
            <?php else: ?>
            <p style="color:var(--muted);font-size:13px;">No deployment info found — deploy first using the button above.</p>
            <?php endif; ?>
            <div style="margin-top:16px;">
                <a href="https://github.com/<?= GITHUB_MARKETING_REPO ?>/commits/main" target="_blank" class="btn btn-outline btn-sm">View commit history ↗</a>
            </div>
        </div>

        <!-- Method 2: cPanel Git Version Control -->
        <div class="card">
            <div class="card-title">
                Method 2 — cPanel Git Version Control <span class="badge badge-indigo">One-click pull</span>
            </div>
            <p style="color:var(--muted);font-size:13px;margin-bottom:18px;">Clone the repo directly inside cPanel. Then pull updates manually from the cPanel UI whenever needed. Good as a backup or for one-off updates.</p>

            <div style="background:rgba(255,255,255,.03);border:1px solid var(--border);border-radius:10px;padding:16px;margin-bottom:18px;">
                <div style="font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--muted);margin-bottom:10px;">Setup steps</div>
                <?php
                $steps2 = [
                    'Log in to cPanel → search for <strong>Git™ Version Control</strong>',
                    'Click <strong>Create</strong> → paste the repo URL: <code style="background:rgba(255,255,255,.07);padding:1px 6px;border-radius:4px;">https://github.com/' . GITHUB_MARKETING_REPO . '.git</code>',
                    'Set <strong>Repository Path</strong> to your public_html folder (e.g. <code style="background:rgba(255,255,255,.07);padding:1px 5px;border-radius:4px;">/home/username/public_html</code>)',
                    'If the repo is private: generate a <strong>GitHub Personal Access Token</strong> (classic, repo scope) and use it as the password in the clone URL: <code style="background:rgba(255,255,255,.07);padding:1px 5px;border-radius:4px;">https://TOKEN@github.com/' . GITHUB_MARKETING_REPO . '.git</code>',
                    'Click <strong>Create</strong> — cPanel clones the repo. The <code style="background:rgba(255,255,255,.07);padding:1px 5px;border-radius:4px;">.cpanel.yml</code> file in the repo handles copying files to public_html',
                    'To update: go back to Git Version Control → click <strong>Update</strong> (or <strong>Deploy HEAD Commit</strong>)',
                ];
                foreach ($steps2 as $i => $step): ?>
                <div style="display:flex;gap:12px;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.04);font-size:13px;">
                    <span style="width:22px;height:22px;border-radius:50%;background:rgba(79,70,229,.2);color:#818CF8;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><?= $i+1 ?></span>
                    <span style="color:var(--muted);"><?= $step ?></span>
                </div>
                <?php endforeach; ?>
            </div>

            <div style="background:rgba(245,158,11,.06);border:1px solid rgba(245,158,11,.2);border-radius:10px;padding:14px 16px;font-size:13px;color:var(--yellow);">
                💡 <strong>Generate a GitHub token:</strong> GitHub → Settings → Developer settings → Personal access tokens → Tokens (classic) → Generate new token → check <strong>repo</strong> scope → copy token
            </div>
        </div>

    <?php
    // ── Releases ─────────────────────────────────────────
    elseif ($tab === 'releases'): ?>

        <div class="page-title">CMS Releases</div>
        <div class="page-sub">Manage the Pagezy CMS version served to users via the download button.</div>

        <div class="deploy-grid">
            <div class="card">
                <div class="card-title">
                    Current Cached Release
                    <?php if ($release): ?><span class="badge badge-green"><?= h($release['tag']) ?></span><?php else: ?><span class="badge badge-red">None</span><?php endif; ?>
                </div>
                <?php if ($release): ?>
                    <div class="info-row"><span class="label">Tag</span><span class="val"><?= h($release['tag']) ?></span></div>
                    <div class="info-row"><span class="label">Name</span><span class="val"><?= h($release['name']) ?></span></div>
                    <div class="info-row"><span class="label">Published</span><span class="val"><?= h(substr($release['published_at'],0,10)) ?></span></div>
                    <div class="info-row"><span class="label">Cache age</span><span class="val"><?= timeAgo($release['fetched_at']) ?></span></div>
                    <div class="info-row"><span class="label">Download URL</span><span class="val" style="word-break:break-all;font-size:11px;"><?= h($release['download_url']) ?></span></div>
                    <?php if (!empty($release['body'])): ?>
                    <div class="release-notes"><?= h($release['body']) ?></div>
                    <?php endif; ?>
                <?php else: ?>
                    <p style="color:var(--muted);font-size:13px;">No release cached yet. Click "Fetch Latest" to pull from GitHub.</p>
                <?php endif; ?>
            </div>
            <div class="card">
                <div class="card-title">Fetch from GitHub</div>
                <p style="color:var(--muted);font-size:13px;margin-bottom:8px;">Pulls the latest release from <strong style="color:var(--text);"><?= GITHUB_CMS_REPO ?></strong> and updates the download link for all users instantly.</p>
                <p style="color:var(--muted);font-size:12px;margin-bottom:16px;">The download button on pagezy.io will redirect users to the new GitHub release asset automatically after fetching.</p>
                <form method="POST" style="margin-bottom:12px;">
                    <button name="fetch_release" value="1" class="btn btn-primary">Fetch Latest Release →</button>
                </form>
                <a href="https://github.com/<?= GITHUB_CMS_REPO ?>/releases" target="_blank" class="btn btn-outline btn-sm">View all releases on GitHub ↗</a>
            </div>
        </div>

        <div class="card">
            <div class="card-title">How It Works</div>
            <div class="info-row"><span class="label">1. New release</span><span class="val" style="color:var(--muted);">Push a tagged release to <?= GITHUB_CMS_REPO ?></span></div>
            <div class="info-row"><span class="label">2. Fetch here</span><span class="val" style="color:var(--muted);">Click "Fetch Latest Release" — updates release-cache.json</span></div>
            <div class="info-row"><span class="label">3. Users download</span><span class="val" style="color:var(--muted);">serve-download.php reads cache and redirects to GitHub ZIP</span></div>
        </div>

    <?php endif; ?>

    </main>
</div>
<?php endif; ?>
</body>
</html>
