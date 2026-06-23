<?php
session_start();
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
if ($authed) {

    // Git pull
    if (isset($_POST['git_pull'])) {
        $dir = realpath(__DIR__ . '/..');
        $out = shell_exec("cd " . escapeshellarg($dir) . " && git pull 2>&1");
        $actionMsg = $out ?: 'No output from git pull.';
    }

    // Fetch latest CMS release from GitHub
    if (isset($_POST['fetch_release'])) {
        $api = 'https://api.github.com/repos/' . GITHUB_CMS_REPO . '/releases/latest';
        $ctx = stream_context_create(['http' => [
            'header' => "User-Agent: Pagezy-Admin/1.0\r\nAccept: application/vnd.github.v3+json\r\n",
            'timeout' => 10,
        ]]);
        $json = @file_get_contents($api, false, $ctx);
        if ($json) {
            $data = json_decode($json, true);
            $tag  = $data['tag_name'] ?? 'unknown';
            // Prefer uploaded zip asset, fall back to zipball
            $url  = $data['zipball_url'] ?? '';
            foreach ($data['assets'] ?? [] as $asset) {
                if (str_ends_with($asset['name'], '.zip')) {
                    $url = $asset['browser_download_url'];
                    break;
                }
            }
            $cache = [
                'tag'          => $tag,
                'name'         => $data['name'] ?? $tag,
                'download_url' => $url,
                'body'         => $data['body'] ?? '',
                'published_at' => $data['published_at'] ?? '',
                'fetched_at'   => date('c'),
            ];
            file_put_contents(RELEASE_CACHE, json_encode($cache, JSON_PRETTY_PRINT));
            $actionMsg = "✓ Release cache updated — " . $tag;
        } else {
            $actionMsg = "✗ Could not reach GitHub API. Check server internet access.";
        }
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
    $diff = time() - strtotime($ts);
    if ($diff < 60)     return $diff . 's ago';
    if ($diff < 3600)   return floor($diff/60) . 'm ago';
    if ($diff < 86400)  return floor($diff/3600) . 'h ago';
    return floor($diff/86400) . 'd ago';
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
        <div class="action-msg <?= str_starts_with($actionMsg,'✗')?'err':'' ?>"><?= h($actionMsg) ?></div>
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
    elseif ($tab === 'deploy'): ?>

        <div class="page-title">Deploy</div>
        <div class="page-sub">Auto-deploy via GitHub Actions → FTP, or manually via cPanel Git Version Control.</div>

        <!-- Method 1: GitHub Actions (primary) -->
        <div class="card">
            <div class="card-title">
                Method 1 — GitHub Actions FTP Deploy <span class="badge badge-green">Recommended</span>
            </div>
            <p style="color:var(--muted);font-size:13px;margin-bottom:18px;">Every push to <code style="background:rgba(255,255,255,.07);padding:2px 6px;border-radius:5px;">main</code> automatically deploys to cPanel via FTP. No terminal needed. Set these 3 secrets in your GitHub repo once:</p>

            <div style="display:grid;gap:10px;margin-bottom:18px;">
                <?php
                $secrets = [
                    ['FTP_HOST',     'Your cPanel FTP hostname', 'e.g. ftp.pagezy.io  or  files.yourdomain.com'],
                    ['FTP_USER',     'Your cPanel FTP username', 'e.g. pagezy@pagezy.io  (shown in cPanel → FTP Accounts)'],
                    ['FTP_PASSWORD', 'Your cPanel FTP password', 'Same as cPanel login or FTP-specific password'],
                    ['FTP_PATH',     'Server path to public_html', 'e.g.  /public_html/  or  /public_html/subdomain/'],
                ];
                foreach ($secrets as [$name, $label, $hint]): ?>
                <div style="background:rgba(255,255,255,.03);border:1px solid var(--border);border-radius:10px;padding:14px 16px;">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
                        <code style="background:rgba(99,102,241,.15);color:#A5B4FC;padding:3px 10px;border-radius:6px;font-size:12px;font-weight:700;"><?= $name ?></code>
                        <span style="font-size:13px;font-weight:600;"><?= $label ?></span>
                    </div>
                    <div style="font-size:12px;color:var(--muted);"><?= $hint ?></div>
                </div>
                <?php endforeach; ?>
            </div>

            <div style="background:rgba(255,255,255,.03);border:1px solid var(--border);border-radius:10px;padding:16px;margin-bottom:18px;">
                <div style="font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--muted);margin-bottom:10px;">Steps to set up</div>
                <?php
                $steps = [
                    'Go to your GitHub repo → <strong>Settings → Secrets and variables → Actions</strong>',
                    'Click <strong>New repository secret</strong> and add each of the 4 secrets above',
                    'The workflow file is already in the repo at <code style="background:rgba(255,255,255,.07);padding:1px 5px;border-radius:4px;">.github/workflows/deploy.yml</code>',
                    'Push any change to <code style="background:rgba(255,255,255,.07);padding:1px 5px;border-radius:4px;">main</code> — GitHub Actions will deploy automatically',
                    'Or trigger manually: GitHub repo → <strong>Actions → Deploy to Pagezy.io → Run workflow</strong>',
                ];
                foreach ($steps as $i => $step): ?>
                <div style="display:flex;gap:12px;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.04);font-size:13px;">
                    <span style="width:22px;height:22px;border-radius:50%;background:rgba(99,102,241,.2);color:#818CF8;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><?= $i+1 ?></span>
                    <span style="color:var(--muted);"><?= $step ?></span>
                </div>
                <?php endforeach; ?>
            </div>

            <a href="https://github.com/<?= GITHUB_MARKETING_REPO ?>/settings/secrets/actions" target="_blank" class="btn btn-primary">Open GitHub Secrets →</a>
            <a href="https://github.com/<?= GITHUB_MARKETING_REPO ?>/actions" target="_blank" class="btn btn-outline" style="margin-left:8px;">View Deploy Runs ↗</a>
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
