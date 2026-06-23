<?php
$pageTitle = 'Download starting — Thank you! | Pagezy';
include 'includes/head.php';
include 'includes/nav.php';
?>

<style>
.ty-wrap { max-width: 760px; margin: 0 auto; padding: 60px 24px 80px; }
.ty-hero { text-align: center; margin-bottom: 48px; }
.ty-icon { width: 80px; height: 80px; border-radius: 20px; margin: 0 auto 24px; display: block; }
.ty-hero h1 { font-size: clamp(28px,5vw,40px); font-weight: 900; margin-bottom: 12px; }
.ty-hero p  { font-size: 16px; color: var(--muted); line-height: 1.65; max-width: 500px; margin: 0 auto 28px; }
.dl-btn-wrap { margin-bottom: 8px; }
.ty-dl-btn {
    display: inline-flex; align-items: center; gap: 10px; padding: 16px 36px;
    background: linear-gradient(135deg,#4F46E5,#7C3AED); color: #fff; border-radius: 14px;
    font-size: 16px; font-weight: 700; text-decoration: none; transition: opacity .2s, transform .2s;
}
.ty-dl-btn:hover { opacity: .88; transform: translateY(-2px); }

/* ── Setup paths ── */
.setup-paths { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 32px; }
@media(max-width:640px){ .setup-paths { grid-template-columns: 1fr; } }

.path-card { background: var(--dark-card,#0F0D1E); border: 1px solid rgba(255,255,255,.08); border-radius: 18px; padding: 28px 24px; }
.path-card.path-server { border-color: rgba(79,70,229,.35); }
.path-card.path-local  { border-color: rgba(255,255,255,.1); }

.path-label { display: inline-flex; align-items: center; gap: 8px; font-size: 11px; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; padding: 4px 12px; border-radius: 50px; margin-bottom: 14px; }
.path-server .path-label { background: rgba(79,70,229,.15); color: #818CF8; }
.path-local  .path-label { background: rgba(255,255,255,.06); color: #9CA3AF; }

.path-title { font-size: 17px; font-weight: 800; margin-bottom: 6px; }
.path-sub   { font-size: 13px; color: var(--muted,#6B7280); margin-bottom: 20px; line-height: 1.5; }

.step-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px; }
.step-list li { display: flex; gap: 12px; align-items: flex-start; }
.step-num {
    width: 24px; height: 24px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 800; margin-top: 1px;
}
.path-server .step-num { background: rgba(79,70,229,.2); color: #818CF8; }
.path-local  .step-num { background: rgba(255,255,255,.08); color: #9CA3AF; }
.step-text { font-size: 13.5px; color: var(--text,#F1F5F9); line-height: 1.55; }
.step-text code { background: rgba(255,255,255,.08); padding: 1px 7px; border-radius: 5px; font-size: 12px; font-family: monospace; }
.step-text a { color: #818CF8; text-decoration: none; }
.step-text a:hover { text-decoration: underline; }
.step-note { font-size: 12px; color: var(--muted,#6B7280); margin-top: 3px; display: block; }

/* ── Tip box ── */
.tip-box { background: rgba(16,185,129,.07); border: 1px solid rgba(16,185,129,.2); border-radius: 12px; padding: 16px 20px; font-size: 13px; color: #6EE7B7; line-height: 1.6; margin-bottom: 32px; }
.tip-box strong { color: #34D399; }

/* ── Links row ── */
.links-row { display: flex; gap: 12px; flex-wrap: wrap; justify-content: center; margin-bottom: 32px; }
.links-row a {
    display: inline-flex; align-items: center; gap: 7px; padding: 10px 20px;
    border-radius: 10px; font-size: 13px; font-weight: 600; text-decoration: none; transition: all .2s;
}
.link-docs    { background: rgba(99,102,241,.12); color: #818CF8; border: 1px solid rgba(99,102,241,.2); }
.link-docs:hover    { background: rgba(99,102,241,.22); }
.link-contact { background: rgba(255,255,255,.05); color: var(--muted,#6B7280); border: 1px solid rgba(255,255,255,.08); }
.link-contact:hover { background: rgba(255,255,255,.1); color: #fff; }
.link-home    { background: rgba(255,255,255,.05); color: var(--muted,#6B7280); border: 1px solid rgba(255,255,255,.08); }
.link-home:hover { background: rgba(255,255,255,.1); color: #fff; }
</style>

<div class="ty-wrap">

    <!-- Hero -->
    <div class="ty-hero">
        <img src="/assets/img/pagezy-logo.png" alt="Pagezy" class="ty-icon">
        <h1>You're all set!</h1>
        <p>Your download starts in a moment. Pick the setup path that fits you below — server or local machine.</p>
        <div class="dl-btn-wrap">
            <a href="/serve-download.php" class="ty-dl-btn" id="dl-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Download Pagezy CMS
            </a>
        </div>
    </div>

    <!-- Tip -->
    <div class="tip-box">
        <strong>📦 What you downloaded:</strong> A ZIP file of the Pagezy CMS (Laravel). Extract it, follow the steps below, and you'll have a running CMS in minutes — no coding needed.
    </div>

    <!-- Setup paths -->
    <div class="setup-paths">

        <!-- Server (cPanel) -->
        <div class="path-card path-server">
            <div class="path-label">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                Recommended · Live server
            </div>
            <div class="path-title">Deploy on cPanel hosting</div>
            <div class="path-sub">Works with any shared hosting that has PHP 8.1+ and MySQL. No terminal needed.</div>
            <ul class="step-list">
                <li>
                    <span class="step-num">1</span>
                    <span class="step-text">
                        Extract the ZIP — you'll get a folder called <code>Pagezy-main</code> or similar.
                    </span>
                </li>
                <li>
                    <span class="step-num">2</span>
                    <span class="step-text">
                        In cPanel → <strong>File Manager</strong>, upload all the extracted files to <code>public_html</code>.
                        <span class="step-note">Upload the contents of the folder, not the folder itself.</span>
                    </span>
                </li>
                <li>
                    <span class="step-num">3</span>
                    <span class="step-text">
                        Go to cPanel → <strong>Subdomains</strong> (or your domain's Document Root) and set the root to <code>public_html/public</code>.
                        <span class="step-note">This is what makes your site load correctly.</span>
                    </span>
                </li>
                <li>
                    <span class="step-num">4</span>
                    <span class="step-text">
                        Create a MySQL database in cPanel → <strong>MySQL Databases</strong>. Note the database name, username and password.
                    </span>
                </li>
                <li>
                    <span class="step-num">5</span>
                    <span class="step-text">
                        Visit <code>yourdomain.com/setup</code> — the installer walks you through the rest in 2 minutes.
                    </span>
                </li>
            </ul>
        </div>

        <!-- Local -->
        <div class="path-card path-local">
            <div class="path-label">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><polyline points="8 21 12 17 16 21"/></svg>
                For developers · Local machine
            </div>
            <div class="path-title">Run locally on your computer</div>
            <div class="path-sub">Best for testing and building. Requires PHP + MySQL — use Laragon (Windows) or Herd (Mac) for the easiest setup.</div>
            <ul class="step-list">
                <li>
                    <span class="step-num">1</span>
                    <span class="step-text">
                        Install <a href="https://laragon.org/download/" target="_blank">Laragon</a> (Windows) or <a href="https://herd.laravel.com" target="_blank">Laravel Herd</a> (Mac). Both give you PHP + MySQL in one click.
                    </span>
                </li>
                <li>
                    <span class="step-num">2</span>
                    <span class="step-text">
                        Extract the ZIP into Laragon's <code>www</code> folder (or Herd's <code>~/Herd</code> folder).
                    </span>
                </li>
                <li>
                    <span class="step-num">3</span>
                    <span class="step-text">
                        Open a terminal in the project folder and run:
                        <code>composer install</code>
                        <span class="step-note">Installs all dependencies. Composer comes with Laragon/Herd.</span>
                    </span>
                </li>
                <li>
                    <span class="step-num">4</span>
                    <span class="step-text">
                        Copy <code>.env.example</code> → <code>.env</code>, then run: <code>php artisan key:generate</code>
                    </span>
                </li>
                <li>
                    <span class="step-num">5</span>
                    <span class="step-text">
                        Create a local database, update <code>.env</code> with its name, then run: <code>php artisan migrate</code>
                    </span>
                </li>
                <li>
                    <span class="step-num">6</span>
                    <span class="step-text">
                        Visit <code>pagezy.test</code> (Laragon auto-configures this) or <code>localhost/pagezy/public</code>.
                    </span>
                </li>
            </ul>
        </div>

    </div>

    <!-- Links -->
    <div class="links-row">
        <a href="/docs.php" class="link-docs">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Full documentation
        </a>
        <a href="/contact.php" class="link-contact">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            Need help? Contact us
        </a>
        <a href="/" class="link-home">← Back to home</a>
    </div>

</div>

<script>
setTimeout(() => { document.getElementById('dl-btn').click(); }, 1200);
</script>

<?php include 'includes/footer.php'; ?>
