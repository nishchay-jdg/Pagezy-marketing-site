<?php
session_start();
require_once 'config.php';

// --- Auth ---
$authError = false;
if (isset($_POST['logout'])) {
    $_SESSION['leads_auth'] = false;
    header('Location: /leads.php');
    exit;
}
if (isset($_POST['password'])) {
    if ($_POST['password'] === LEADS_PASSWORD) {
        $_SESSION['leads_auth'] = true;
    } else {
        $authError = true;
    }
}
$authed = !empty($_SESSION['leads_auth']);

// --- CSV export ---
if ($authed && isset($_GET['export'])) {
    $type = $_GET['export'];
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="pagezy-' . $type . '-leads-' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    try {
        if ($type === 'downloads') {
            ensureTable();
            $rows = db()->query("SELECT id, name, email, city, company, use_case, ip_address, downloaded, created_at FROM pagezy_downloads ORDER BY created_at DESC")->fetchAll();
            fputcsv($out, ['ID','Name','Email','City','Company','Use Case','IP','Downloaded','Date']);
            foreach ($rows as $r) fputcsv($out, array_values($r));
        } elseif ($type === 'contacts') {
            ensureContactTable();
            $rows = db()->query("SELECT id, name, email, phone, company, subject, message, ip_address, created_at FROM pagezy_contacts ORDER BY created_at DESC")->fetchAll();
            fputcsv($out, ['ID','Name','Email','Phone','Company','Subject','Message','IP','Date']);
            foreach ($rows as $r) fputcsv($out, array_values($r));
        }
    } catch (Exception $e) {}
    fclose($out);
    exit;
}

// --- Load data ---
$downloads = [];
$contacts  = [];
$dlCount   = 0;
$ctCount   = 0;
$dbError   = null;

if ($authed) {
    try {
        ensureTable();
        $downloads = db()->query("SELECT * FROM pagezy_downloads ORDER BY created_at DESC LIMIT 200")->fetchAll();
        $dlCount   = (int) db()->query("SELECT COUNT(*) FROM pagezy_downloads")->fetchColumn();
    } catch (Exception $e) { $dbError = $e->getMessage(); }
    try {
        ensureContactTable();
        $contacts = db()->query("SELECT * FROM pagezy_contacts ORDER BY created_at DESC LIMIT 200")->fetchAll();
        $ctCount  = (int) db()->query("SELECT COUNT(*) FROM pagezy_contacts")->fetchColumn();
    } catch (Exception $e) { $dbError = $dbError ?? $e->getMessage(); }
}

$tab = $_GET['tab'] ?? 'downloads';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Leads — Pagezy Admin</title>
<meta name="robots" content="noindex,nofollow">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --dark: #07060F;
    --dark-card: #0F0D1E;
    --dark-border: rgba(255,255,255,.08);
    --text: #F1F0F7;
    --muted: #6B7280;
    --grad: linear-gradient(135deg,#6D28D9,#4F46E5);
    --green: #34D399;
    --red: #F87171;
}
body { font-family: 'Inter', sans-serif; background: var(--dark); color: var(--text); min-height: 100vh; }

/* Login */
.login-wrap { display:flex;align-items:center;justify-content:center;min-height:100vh;padding:24px; }
.login-box { background:var(--dark-card);border:1px solid var(--dark-border);border-radius:20px;padding:40px;width:100%;max-width:380px; }
.login-logo { display:flex;align-items:center;gap:10px;margin-bottom:32px; }
.login-logo img { width:32px;height:32px;object-fit:contain; }
.login-logo span { font-size:18px;font-weight:800; }
.login-box h1 { font-size:22px;font-weight:800;margin-bottom:6px; }
.login-box p  { font-size:14px;color:var(--muted);margin-bottom:28px; }
.login-box label { display:block;font-size:13px;font-weight:600;margin-bottom:6px;color:var(--muted); }
.login-box input[type=password] {
    width:100%; padding:12px 16px; background:rgba(255,255,255,.05);
    border:1px solid var(--dark-border); border-radius:10px;
    color:var(--text); font-size:15px; font-family:inherit;
    margin-bottom:16px; outline:none; transition:.2s;
}
.login-box input[type=password]:focus { border-color:rgba(139,92,246,.6); }
.btn-primary { background:var(--grad);color:#fff;border:none;border-radius:10px;padding:13px 24px;font-size:14px;font-weight:700;cursor:pointer;width:100%;font-family:inherit;transition:.15s; }
.btn-primary:hover { opacity:.9; }
.error-msg { background:rgba(248,113,113,.1);border:1px solid rgba(248,113,113,.3);border-radius:8px;padding:12px 16px;font-size:13px;color:#FCA5A5;margin-bottom:16px; }

/* Layout */
.topbar { background:var(--dark-card);border-bottom:1px solid var(--dark-border);padding:0 32px;display:flex;align-items:center;justify-content:space-between;height:60px;position:sticky;top:0;z-index:50; }
.topbar-brand { display:flex;align-items:center;gap:10px;font-weight:800;font-size:16px; }
.topbar-brand img { width:28px;height:28px;object-fit:contain; }
.topbar-right { display:flex;align-items:center;gap:12px; }
.btn-sm { padding:7px 14px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;border:1px solid var(--dark-border);background:transparent;color:var(--muted);font-family:inherit;transition:.15s; }
.btn-sm:hover { color:var(--text);border-color:rgba(255,255,255,.2); }
.btn-export { padding:7px 14px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;border:none;background:rgba(99,102,241,.15);color:#818CF8;font-family:inherit;transition:.15s;text-decoration:none;display:inline-block; }
.btn-export:hover { background:rgba(99,102,241,.25); }

.main { padding:32px; }

/* Stats row */
.stats-row { display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:16px;margin-bottom:32px; }
.stat-card { background:var(--dark-card);border:1px solid var(--dark-border);border-radius:16px;padding:20px; }
.stat-label { font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:8px; }
.stat-value { font-size:32px;font-weight:900; }
.stat-value.green { color:var(--green); }
.stat-value.indigo { color:#818CF8; }

/* Tabs */
.tabs { display:flex;gap:4px;margin-bottom:24px;background:var(--dark-card);border:1px solid var(--dark-border);border-radius:12px;padding:4px;width:fit-content; }
.tab-btn { padding:8px 18px;border-radius:9px;font-size:14px;font-weight:600;cursor:pointer;border:none;background:transparent;color:var(--muted);font-family:inherit;transition:.2s; }
.tab-btn.active { background:rgba(99,102,241,.2);color:#A78BFA; }
.tab-btn:hover:not(.active) { color:var(--text); }

/* Table */
.table-wrap { background:var(--dark-card);border:1px solid var(--dark-border);border-radius:16px;overflow:hidden; }
.table-header { display:flex;align-items:center;justify-content:space-between;padding:18px 24px;border-bottom:1px solid var(--dark-border); }
.table-title { font-size:15px;font-weight:700; }
.table-count { font-size:13px;color:var(--muted); }
.table-scroll { overflow-x:auto; }
table { width:100%;border-collapse:collapse;font-size:13px; }
thead th { padding:12px 16px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);border-bottom:1px solid var(--dark-border);white-space:nowrap; }
tbody tr { border-bottom:1px solid rgba(255,255,255,.04);transition:.15s; }
tbody tr:last-child { border-bottom:none; }
tbody tr:hover { background:rgba(255,255,255,.03); }
tbody td { padding:12px 16px;color:var(--text);max-width:280px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
tbody td.muted { color:var(--muted); }
.badge { display:inline-block;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700; }
.badge-green { background:rgba(52,211,153,.15);color:var(--green); }
.badge-gray  { background:rgba(255,255,255,.07);color:var(--muted); }
.badge-indigo{ background:rgba(99,102,241,.15);color:#818CF8; }

.empty-state { padding:64px 24px;text-align:center;color:var(--muted); }
.empty-state svg { margin:0 auto 16px;display:block;opacity:.3; }

@media(max-width:640px) {
    .main { padding:16px; }
    .topbar { padding:0 16px; }
    .stats-row { grid-template-columns:1fr 1fr; }
}
</style>
</head>
<body>

<?php if (!$authed): ?>
<div class="login-wrap">
    <div class="login-box">
        <div class="login-logo">
            <img src="/assets/img/pagezy-logo.png" alt="Pagezy">
            <span>Pagezy</span>
        </div>
        <h1>Leads Dashboard</h1>
        <p>Enter the admin password to view leads.</p>
        <?php if ($authError): ?>
        <div class="error-msg">Incorrect password. Please try again.</div>
        <?php endif; ?>
        <form method="POST" action="/leads.php">
            <label for="pw">Password</label>
            <input type="password" id="pw" name="password" autofocus placeholder="••••••••••••">
            <button type="submit" class="btn-primary">Sign in</button>
        </form>
    </div>
</div>

<?php else: ?>

<div class="topbar">
    <div class="topbar-brand">
        <img src="/assets/img/pagezy-logo.png" alt="Pagezy">
        Leads Dashboard
    </div>
    <div class="topbar-right">
        <a href="/" class="btn-sm" style="text-decoration:none;">← Site</a>
        <form method="POST" action="/leads.php" style="margin:0;">
            <button type="submit" name="logout" class="btn-sm">Sign out</button>
        </form>
    </div>
</div>

<div class="main">

    <?php if ($dbError): ?>
    <div class="error-msg" style="margin-bottom:24px;border-radius:12px;padding:16px 20px;">
        <strong>DB error:</strong> <?= htmlspecialchars($dbError) ?> — Check your config.php credentials.
    </div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-label">Total downloads</div>
            <div class="stat-value green"><?= $dlCount ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Contact leads</div>
            <div class="stat-value indigo"><?= $ctCount ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total leads</div>
            <div class="stat-value"><?= $dlCount + $ctCount ?></div>
        </div>
        <?php
        $today = 0;
        foreach ($downloads as $r) { if (substr($r['created_at'],0,10) === date('Y-m-d')) $today++; }
        foreach ($contacts  as $r) { if (substr($r['created_at'],0,10) === date('Y-m-d')) $today++; }
        ?>
        <div class="stat-card">
            <div class="stat-label">Today</div>
            <div class="stat-value"><?= $today ?></div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="tabs">
        <a href="/leads.php?tab=downloads" class="tab-btn <?= $tab==='downloads' ? 'active' : '' ?>" style="text-decoration:none;">
            Downloads (<?= $dlCount ?>)
        </a>
        <a href="/leads.php?tab=contacts" class="tab-btn <?= $tab==='contacts' ? 'active' : '' ?>" style="text-decoration:none;">
            Contacts (<?= $ctCount ?>)
        </a>
    </div>

    <?php if ($tab === 'downloads'): ?>
    <div class="table-wrap">
        <div class="table-header">
            <div>
                <div class="table-title">Download leads</div>
                <div class="table-count"><?= $dlCount ?> total <?= $dlCount > 200 ? '(showing latest 200)' : '' ?></div>
            </div>
            <a href="/leads.php?export=downloads" class="btn-export">Export CSV</a>
        </div>
        <div class="table-scroll">
            <?php if (empty($downloads)): ?>
            <div class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                <div>No download leads yet.</div>
            </div>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>City</th>
                        <th>Company</th>
                        <th>Use case</th>
                        <th>Downloaded</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($downloads as $row): ?>
                    <tr>
                        <td class="muted"><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><a href="mailto:<?= htmlspecialchars($row['email']) ?>" style="color:#818CF8;text-decoration:none;"><?= htmlspecialchars($row['email']) ?></a></td>
                        <td class="muted"><?= htmlspecialchars($row['city'] ?? '—') ?></td>
                        <td class="muted"><?= htmlspecialchars($row['company'] ?? '—') ?></td>
                        <td>
                            <?php if ($row['use_case']): ?>
                            <span class="badge badge-indigo"><?= htmlspecialchars($row['use_case']) ?></span>
                            <?php else: ?>
                            <span class="badge badge-gray">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['downloaded']): ?>
                            <span class="badge badge-green">Yes</span>
                            <?php else: ?>
                            <span class="badge badge-gray">No</span>
                            <?php endif; ?>
                        </td>
                        <td class="muted"><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>

    <?php else: ?>
    <div class="table-wrap">
        <div class="table-header">
            <div>
                <div class="table-title">Contact leads</div>
                <div class="table-count"><?= $ctCount ?> total <?= $ctCount > 200 ? '(showing latest 200)' : '' ?></div>
            </div>
            <a href="/leads.php?export=contacts" class="btn-export">Export CSV</a>
        </div>
        <div class="table-scroll">
            <?php if (empty($contacts)): ?>
            <div class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                <div>No contact leads yet.</div>
            </div>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Company</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contacts as $row): ?>
                    <tr>
                        <td class="muted"><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><a href="mailto:<?= htmlspecialchars($row['email']) ?>" style="color:#818CF8;text-decoration:none;"><?= htmlspecialchars($row['email']) ?></a></td>
                        <td class="muted"><?= htmlspecialchars($row['phone'] ?? '—') ?></td>
                        <td class="muted"><?= htmlspecialchars($row['company'] ?? '—') ?></td>
                        <td>
                            <?php if ($row['subject']): ?>
                            <span class="badge badge-indigo"><?= htmlspecialchars($row['subject']) ?></span>
                            <?php else: ?>
                            <span class="badge badge-gray">—</span>
                            <?php endif; ?>
                        </td>
                        <td title="<?= htmlspecialchars($row['message']) ?>" style="max-width:240px;">
                            <?= htmlspecialchars(mb_strimwidth($row['message'], 0, 60, '…')) ?>
                        </td>
                        <td class="muted"><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

</div><!-- /main -->

<?php endif; ?>
</body>
</html>
