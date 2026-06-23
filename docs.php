<?php
$pageTitle       = 'Setup Guide — Pagezy';
$pageDescription = 'Step-by-step guide to install Pagezy CMS on your server or local machine. No technical knowledge needed.';
include 'includes/head.php';
include 'includes/nav.php';
?>

<style>
/* ── Wizard ────────────────────────────────────────────── */
.wiz-section { padding: 60px 0 80px; }
.wiz-wrap    { max-width: 760px; margin: 0 auto; padding: 0 24px; }

.wiz-hero { text-align: center; margin-bottom: 48px; }
.wiz-hero .section-label { justify-content: center; margin-bottom: 14px; }
.wiz-hero h1 { font-size: clamp(32px,5vw,48px); font-weight: 900; margin-bottom: 12px; }
.wiz-hero p  { font-size: 16px; color: var(--muted); max-width: 480px; margin: 0 auto; line-height: 1.6; }

/* Path chooser */
.path-chooser { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 48px; }
@media(max-width:500px){ .path-chooser { grid-template-columns: 1fr; } }
.path-btn {
    background: var(--dark-card,#0F0D1E); border: 2px solid rgba(255,255,255,.08);
    border-radius: 18px; padding: 28px 24px; cursor: pointer; text-align: left;
    transition: border-color .2s, background .2s; color: inherit;
}
.path-btn:hover  { border-color: rgba(99,102,241,.4); background: rgba(99,102,241,.06); }
.path-btn.active { border-color: #6366F1; background: rgba(99,102,241,.1); }
.path-btn-icon  { font-size: 32px; margin-bottom: 12px; display: block; }
.path-btn-title { font-size: 16px; font-weight: 800; margin-bottom: 6px; }
.path-btn-sub   { font-size: 13px; color: var(--muted); line-height: 1.5; }
.path-btn-tag   { display: inline-flex; margin-top: 10px; font-size: 11px; font-weight: 700; letter-spacing: .06em;
                   text-transform: uppercase; padding: 3px 10px; border-radius: 50px; }
.tag-recommended { background: rgba(79,70,229,.15); color: #818CF8; }
.tag-dev         { background: rgba(255,255,255,.06); color: #9CA3AF; }

/* Progress bar */
.wiz-progress { margin-bottom: 36px; }
.wiz-progress-bar-wrap { height: 4px; background: rgba(255,255,255,.08); border-radius: 4px; overflow: hidden; margin-bottom: 12px; }
.wiz-progress-bar      { height: 100%; background: linear-gradient(90deg,#4F46E5,#7C3AED); border-radius: 4px; transition: width .4s ease; }
.wiz-steps-row { display: flex; gap: 0; overflow-x: auto; }
.wiz-step-dot  {
    display: flex; flex-direction: column; align-items: center; gap: 5px;
    flex: 1; min-width: 0; cursor: pointer; opacity: .4; transition: opacity .2s;
}
.wiz-step-dot.done    { opacity: .7; }
.wiz-step-dot.current { opacity: 1; }
.wiz-step-dot-circle {
    width: 26px; height: 26px; border-radius: 50%; border: 2px solid rgba(255,255,255,.15);
    display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; font-family: monospace;
    transition: background .2s, border-color .2s;
}
.wiz-step-dot.done    .wiz-step-dot-circle { background: rgba(99,102,241,.3); border-color: #6366F1; color: #818CF8; }
.wiz-step-dot.current .wiz-step-dot-circle { background: #6366F1; border-color: #6366F1; color: #fff; }
.wiz-step-dot-label { font-size: 10px; color: var(--muted); text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%; padding: 0 4px; }

/* Step card */
.wiz-card {
    background: var(--dark-card,#0F0D1E); border: 1px solid rgba(255,255,255,.08);
    border-radius: 20px; padding: 40px; display: none; animation: wiz-in .3s ease;
}
.wiz-card.visible { display: block; }
@keyframes wiz-in { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }

.wiz-step-label { font-size: 12px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;
                   color: #818CF8; margin-bottom: 8px; display: flex; align-items: center; gap: 8px; }
.wiz-step-label span { background: rgba(99,102,241,.15); padding: 2px 10px; border-radius: 50px; }
.wiz-card-icon  { font-size: 48px; margin-bottom: 16px; display: block; }
.wiz-card h2    { font-size: clamp(22px,4vw,30px); font-weight: 900; margin-bottom: 10px; line-height: 1.25; }
.wiz-card .wiz-card-sub { font-size: 15px; color: var(--muted); line-height: 1.65; margin-bottom: 28px; }

/* Step list inside card */
.wiz-list { list-style: none; padding: 0; margin: 0 0 28px; display: flex; flex-direction: column; gap: 14px; }
.wiz-list li { display: flex; gap: 14px; align-items: flex-start; }
.wiz-list-num {
    width: 26px; height: 26px; border-radius: 50%; background: rgba(99,102,241,.15); color: #818CF8;
    font-size: 12px; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px;
}
.wiz-list-text { font-size: 14px; color: var(--text,#F1F5F9); line-height: 1.6; }
.wiz-list-text strong { color: #fff; }
.wiz-list-text code { background: rgba(255,255,255,.08); padding: 1px 7px; border-radius: 5px; font-size: 12px; font-family: monospace; }
.wiz-list-text .note { font-size: 12px; color: var(--muted); display: block; margin-top: 3px; }

/* Tip box */
.wiz-tip { background: rgba(245,158,11,.07); border: 1px solid rgba(245,158,11,.2); border-radius: 12px;
            padding: 14px 18px; font-size: 13px; color: #FCD34D; line-height: 1.6; margin-bottom: 24px; }
.wiz-tip strong { color: #FBBF24; }

/* Code block */
.wiz-code { background: rgba(0,0,0,.4); border: 1px solid rgba(255,255,255,.08); border-radius: 10px;
             padding: 14px 18px; font-family: monospace; font-size: 13px; color: #A5F3FC;
             margin-bottom: 20px; overflow-x: auto; }

/* Choice tabs (local OS chooser) */
.os-tabs { display: flex; gap: 8px; margin-bottom: 16px; }
.os-tab  { padding: 6px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;
            border: 1px solid rgba(255,255,255,.1); color: var(--muted); background: none; transition: all .2s; }
.os-tab.active  { background: rgba(99,102,241,.15); border-color: #6366F1; color: #818CF8; }
.os-tab-content { display: none; }
.os-tab-content.active { display: block; }

/* Nav buttons */
.wiz-nav { display: flex; align-items: center; justify-content: space-between; margin-top: 36px; gap: 12px; }
.wiz-btn-prev { padding: 11px 22px; border-radius: 10px; background: rgba(255,255,255,.06);
                 color: var(--muted); font-size: 14px; font-weight: 600; border: 1px solid rgba(255,255,255,.08);
                 cursor: pointer; transition: all .2s; }
.wiz-btn-prev:hover { background: rgba(255,255,255,.1); color: #fff; }
.wiz-btn-next { padding: 11px 26px; border-radius: 10px;
                 background: linear-gradient(135deg,#4F46E5,#7C3AED);
                 color: #fff; font-size: 14px; font-weight: 700; border: none;
                 cursor: pointer; transition: opacity .2s, transform .2s; display: flex; align-items: center; gap: 8px; }
.wiz-btn-next:hover { opacity: .88; transform: translateY(-1px); }
.wiz-btn-next svg, .wiz-btn-prev svg { flex-shrink: 0; }

/* Done card */
.done-card { text-align: center; }
.done-card h2 { font-size: 34px; margin-bottom: 12px; }
.done-card .done-sub { max-width: 440px; margin: 0 auto 32px; }
.done-links { display: flex; gap: 12px; flex-wrap: wrap; justify-content: center; }
.done-links a { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px;
                 border-radius: 12px; font-weight: 700; font-size: 14px; text-decoration: none; }
.done-primary   { background: linear-gradient(135deg,#4F46E5,#7C3AED); color: #fff; }
.done-secondary { background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1); color: var(--muted); }
.done-secondary:hover { background: rgba(255,255,255,.1); color: #fff; }

/* Reference docs below */
.docs-ref-toggle { text-align: center; margin: 48px 0 0; }
.docs-ref-toggle button {
    background: none; border: 1px solid rgba(255,255,255,.1); color: var(--muted); padding: 10px 22px;
    border-radius: 10px; font-size: 13px; cursor: pointer; transition: all .2s;
}
.docs-ref-toggle button:hover { background: rgba(255,255,255,.06); color: #fff; }
.docs-ref { display: none; }
.docs-ref.open { display: block; }
</style>

<section class="wiz-section">
<div class="wiz-wrap">

    <!-- Hero -->
    <div class="wiz-hero">
        <div class="section-label" style="display:flex;justify-content:center;"><span>Setup Guide</span></div>
        <h1>Let's get you up and running</h1>
        <p>Follow the steps below — no technical background needed. Pick your path to get started.</p>
    </div>

    <!-- Path chooser (always visible until a path is selected) -->
    <div id="pathChooser">
        <div class="path-chooser">
            <button class="path-btn" onclick="startWizard('server')">
                <span class="path-btn-icon">🌐</span>
                <div class="path-btn-title">Live Server</div>
                <div class="path-btn-sub">I have a cPanel hosting account and want to put Pagezy on a real domain.</div>
                <span class="path-btn-tag tag-recommended">Recommended</span>
            </button>
            <button class="path-btn" onclick="startWizard('local')">
                <span class="path-btn-icon">💻</span>
                <div class="path-btn-title">Local Computer</div>
                <div class="path-btn-sub">I want to test and build on my own machine before going live.</div>
                <span class="path-btn-tag tag-dev">For developers</span>
            </button>
        </div>
    </div>

    <!-- Wizard (hidden until path chosen) -->
    <div id="wizardWrap" style="display:none;">

        <!-- Progress -->
        <div class="wiz-progress">
            <div class="wiz-progress-bar-wrap">
                <div class="wiz-progress-bar" id="progBar" style="width:0%"></div>
            </div>
            <div class="wiz-steps-row" id="stepsRow"></div>
        </div>

        <!-- ═══════════ SERVER STEPS ═══════════ -->
        <div id="steps-server" style="display:none;">

            <div class="wiz-card" data-step="0">
                <span class="wiz-card-icon">📦</span>
                <div class="wiz-step-label"><span>Step 1 of 7</span></div>
                <h2>Download Pagezy CMS</h2>
                <p class="wiz-card-sub">First things first — let's get the files onto your computer. Click below to download the latest version as a ZIP file.</p>
                <a href="/serve-download.php" class="wiz-btn-next" style="display:inline-flex;text-decoration:none;margin-bottom:20px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Download ZIP
                </a>
                <div class="wiz-tip"><strong>💡 Tip:</strong> Your download will start automatically. Save the ZIP file somewhere you can find it — like your Desktop.</div>
                <div class="wiz-nav">
                    <button class="wiz-btn-prev" onclick="changeStep(-1)">← Back to paths</button>
                    <button class="wiz-btn-next" onclick="changeStep(1)">I've downloaded it →</button>
                </div>
            </div>

            <div class="wiz-card" data-step="1">
                <span class="wiz-card-icon">✅</span>
                <div class="wiz-step-label"><span>Step 2 of 7</span></div>
                <h2>Check your hosting is ready</h2>
                <p class="wiz-card-sub">Pagezy works on nearly all shared hosting. Before we continue, make sure your plan supports the basics.</p>
                <ul class="wiz-list">
                    <li><span class="wiz-list-num">✓</span><span class="wiz-list-text"><strong>PHP 8.1 or higher</strong> — most hosts in 2024+ have this. Check your cPanel → PHP Selector.<span class="note">Not sure? Email your host: "Do you support PHP 8.1?" They'll confirm within minutes.</span></span></li>
                    <li><span class="wiz-list-num">✓</span><span class="wiz-list-text"><strong>MySQL database</strong> — included on all shared hosting plans.</span></li>
                    <li><span class="wiz-list-num">✓</span><span class="wiz-list-text"><strong>Apache or Nginx web server</strong> — this is the default on cPanel hosts. You're likely already set.</span></li>
                    <li><span class="wiz-list-num">✓</span><span class="wiz-list-text"><strong>File Manager access</strong> — available in every cPanel. You'll use this to upload files.</span></li>
                </ul>
                <div class="wiz-tip"><strong>🏆 Works great with:</strong> Hostinger, SiteGround, Namecheap, GoDaddy, A2 Hosting, Bluehost, and most cPanel providers.</div>
                <div class="wiz-nav">
                    <button class="wiz-btn-prev" onclick="changeStep(-1)">← Previous</button>
                    <button class="wiz-btn-next" onclick="changeStep(1)">My host looks good →</button>
                </div>
            </div>

            <div class="wiz-card" data-step="2">
                <span class="wiz-card-icon">🗄️</span>
                <div class="wiz-step-label"><span>Step 3 of 7</span></div>
                <h2>Create a database</h2>
                <p class="wiz-card-sub">Your CMS needs a database to store all pages, posts, and settings. This takes about 2 minutes in cPanel.</p>
                <ul class="wiz-list">
                    <li><span class="wiz-list-num">1</span><span class="wiz-list-text">Log in to <strong>cPanel</strong> → find <strong>MySQL Databases</strong> (usually under Databases section).</span></li>
                    <li><span class="wiz-list-num">2</span><span class="wiz-list-text">Type a name (e.g. <code>mysite</code>) → click <strong>Create Database</strong>.<span class="note">cPanel will add a prefix automatically. The full name might be something like <code>username_mysite</code>.</span></span></li>
                    <li><span class="wiz-list-num">3</span><span class="wiz-list-text">Scroll to <strong>MySQL Users</strong> → type a username and a strong password → <strong>Create User</strong>.</span></li>
                    <li><span class="wiz-list-num">4</span><span class="wiz-list-text">Scroll to <strong>Add User To Database</strong> → pick the user and database you just made → click <strong>Add</strong> → select <strong>All Privileges</strong> → <strong>Make Changes</strong>.</span></li>
                    <li><span class="wiz-list-num">5</span><span class="wiz-list-text"><strong>Write these down</strong> — you'll need them in a later step:<br>Database name · Username · Password · Host (usually <code>localhost</code>)</span></li>
                </ul>
                <div class="wiz-nav">
                    <button class="wiz-btn-prev" onclick="changeStep(-1)">← Previous</button>
                    <button class="wiz-btn-next" onclick="changeStep(1)">Database is ready →</button>
                </div>
            </div>

            <div class="wiz-card" data-step="3">
                <span class="wiz-card-icon">📤</span>
                <div class="wiz-step-label"><span>Step 4 of 7</span></div>
                <h2>Upload the files</h2>
                <p class="wiz-card-sub">Now let's get the Pagezy files onto your server. You'll use cPanel's built-in File Manager — no FTP app needed.</p>
                <ul class="wiz-list">
                    <li><span class="wiz-list-num">1</span><span class="wiz-list-text">In cPanel, open <strong>File Manager</strong>.</span></li>
                    <li><span class="wiz-list-num">2</span><span class="wiz-list-text">Navigate to <code>public_html</code> (this is your website's root folder).</span></li>
                    <li><span class="wiz-list-num">3</span><span class="wiz-list-text">Click <strong>Upload</strong> in the toolbar → upload the ZIP file you downloaded in Step 1.</span></li>
                    <li><span class="wiz-list-num">4</span><span class="wiz-list-text">Once uploaded, right-click the ZIP → <strong>Extract</strong>. A new folder will appear.</span></li>
                    <li><span class="wiz-list-num">5</span><span class="wiz-list-text">Open that folder → select all files (<kbd>Ctrl+A</kbd>) → <strong>Move</strong> them one level up into <code>public_html</code> directly.<span class="note">You want all files (including the hidden <code>.env</code> file) to be directly inside public_html, not in a subfolder.</span></span></li>
                </ul>
                <div class="wiz-tip"><strong>💡 Alternatively:</strong> You can use FTP (FileZilla is free) to upload the extracted files directly from your computer.</div>
                <div class="wiz-nav">
                    <button class="wiz-btn-prev" onclick="changeStep(-1)">← Previous</button>
                    <button class="wiz-btn-next" onclick="changeStep(1)">Files are uploaded →</button>
                </div>
            </div>

            <div class="wiz-card" data-step="4">
                <span class="wiz-card-icon">🔗</span>
                <div class="wiz-step-label"><span>Step 5 of 7</span></div>
                <h2>Point your domain to /public</h2>
                <p class="wiz-card-sub">Pagezy keeps its files organised with a <code>/public</code> folder for security. We need to tell your domain to look there.</p>
                <div class="wiz-tip"><strong>Why?</strong> This means visitors only see the public folder — your config files, database credentials and code stay hidden and safe.</div>
                <ul class="wiz-list">
                    <li><span class="wiz-list-num">1</span><span class="wiz-list-text">In cPanel → go to <strong>Domains</strong> (or <strong>Addon Domains</strong> / <strong>Subdomains</strong>).</span></li>
                    <li><span class="wiz-list-num">2</span><span class="wiz-list-text">Find your domain → click <strong>Manage</strong> or <strong>Edit</strong>.</span></li>
                    <li><span class="wiz-list-num">3</span><span class="wiz-list-text">Change the <strong>Document Root</strong> from <code>public_html</code> to <code>public_html/public</code>.</span></li>
                    <li><span class="wiz-list-num">4</span><span class="wiz-list-text">Save the changes.</span></li>
                </ul>
                <div class="wiz-tip"><strong>Can't find Document Root?</strong> Some hosts call it "Web Root" or "Directory". Contact your host's live chat — they can set this for you in seconds.</div>
                <div class="wiz-nav">
                    <button class="wiz-btn-prev" onclick="changeStep(-1)">← Previous</button>
                    <button class="wiz-btn-next" onclick="changeStep(1)">Domain is pointed →</button>
                </div>
            </div>

            <div class="wiz-card" data-step="5">
                <span class="wiz-card-icon">⚡</span>
                <div class="wiz-step-label"><span>Step 6 of 7</span></div>
                <h2>Run the installer</h2>
                <p class="wiz-card-sub">You're nearly there! Open your browser and visit the installer — it'll finish the setup for you.</p>
                <ul class="wiz-list">
                    <li><span class="wiz-list-num">1</span><span class="wiz-list-text">Open your browser → go to <code>yourdomain.com/setup</code>.</span></li>
                    <li><span class="wiz-list-num">2</span><span class="wiz-list-text">Enter the <strong>database details</strong> you wrote down in Step 3 (name, username, password, host).</span></li>
                    <li><span class="wiz-list-num">3</span><span class="wiz-list-text">Set your <strong>site name</strong> and <strong>admin email</strong>.</span></li>
                    <li><span class="wiz-list-num">4</span><span class="wiz-list-text">Choose a strong <strong>admin password</strong> → click <strong>Install Pagezy</strong>.</span></li>
                    <li><span class="wiz-list-num">5</span><span class="wiz-list-text">The installer creates all the database tables and your admin account. This takes about 10 seconds.<span class="note">The /setup page will lock itself after installation — no one else can run it.</span></span></li>
                </ul>
                <div class="wiz-nav">
                    <button class="wiz-btn-prev" onclick="changeStep(-1)">← Previous</button>
                    <button class="wiz-btn-next" onclick="changeStep(1)">Installation done →</button>
                </div>
            </div>

            <div class="wiz-card done-card" data-step="6">
                <span class="wiz-card-icon">🎉</span>
                <h2>You're live!</h2>
                <p class="wiz-card-sub done-sub">Congratulations — Pagezy CMS is installed and running on your domain. Your admin panel is ready to go.</p>
                <div class="done-links">
                    <a href="#" class="done-primary">Visit your site ↗</a>
                    <a href="#" class="done-primary" style="background:rgba(99,102,241,.2);color:#818CF8;">Go to admin panel</a>
                    <button onclick="resetWizard()" class="done-secondary" style="border:none;cursor:pointer;">← Start over</button>
                </div>
                <div style="margin-top:36px;padding-top:24px;border-top:1px solid rgba(255,255,255,.07);font-size:13px;color:var(--muted);">
                    Need help? <a href="/contact.php" style="color:#818CF8;">Contact support</a> — we respond within 24h.
                </div>
            </div>

        </div><!-- /steps-server -->

        <!-- ═══════════ LOCAL STEPS ═══════════ -->
        <div id="steps-local" style="display:none;">

            <div class="wiz-card" data-step="0">
                <span class="wiz-card-icon">📦</span>
                <div class="wiz-step-label"><span>Step 1 of 6</span></div>
                <h2>Download Pagezy CMS</h2>
                <p class="wiz-card-sub">First, download the Pagezy ZIP to your computer. We'll extract it into your local server folder in the next step.</p>
                <a href="/serve-download.php" class="wiz-btn-next" style="display:inline-flex;text-decoration:none;margin-bottom:20px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Download ZIP
                </a>
                <div class="wiz-nav">
                    <button class="wiz-btn-prev" onclick="changeStep(-1)">← Back to paths</button>
                    <button class="wiz-btn-next" onclick="changeStep(1)">I've downloaded it →</button>
                </div>
            </div>

            <div class="wiz-card" data-step="1">
                <span class="wiz-card-icon">🛠️</span>
                <div class="wiz-step-label"><span>Step 2 of 6</span></div>
                <h2>Install a local server</h2>
                <p class="wiz-card-sub">You need PHP and MySQL running on your machine. These free tools set everything up in one click — no command line needed for this step.</p>
                <div class="os-tabs">
                    <button class="os-tab active" onclick="switchOS(this,'win')">Windows</button>
                    <button class="os-tab" onclick="switchOS(this,'mac')">Mac</button>
                </div>
                <div class="os-tab-content active" id="os-win">
                    <ul class="wiz-list">
                        <li><span class="wiz-list-num">1</span><span class="wiz-list-text">Download <strong><a href="https://laragon.org/download/" target="_blank" style="color:#818CF8;">Laragon</a></strong> (free, ~80 MB). It gives you PHP, MySQL, and a web server all in one installer.</span></li>
                        <li><span class="wiz-list-num">2</span><span class="wiz-list-text">Run the installer and click through the defaults. Laragon will appear in your system tray.</span></li>
                        <li><span class="wiz-list-num">3</span><span class="wiz-list-text">Click <strong>Start All</strong> in the Laragon window. You'll see Apache and MySQL turn green.</span></li>
                    </ul>
                </div>
                <div class="os-tab-content" id="os-mac">
                    <ul class="wiz-list">
                        <li><span class="wiz-list-num">1</span><span class="wiz-list-text">Download <strong><a href="https://herd.laravel.com" target="_blank" style="color:#818CF8;">Laravel Herd</a></strong> (free). It installs PHP and a local server automatically.</span></li>
                        <li><span class="wiz-list-num">2</span><span class="wiz-list-text">Open the DMG file and drag Herd to your Applications folder. Launch it.</span></li>
                        <li><span class="wiz-list-num">3</span><span class="wiz-list-text">Herd starts automatically and sets up PHP. For MySQL, install <strong><a href="https://tableplus.com" target="_blank" style="color:#818CF8;">TablePlus</a></strong> or use Homebrew: <code>brew install mysql</code>.</span></li>
                    </ul>
                </div>
                <div class="wiz-nav">
                    <button class="wiz-btn-prev" onclick="changeStep(-1)">← Previous</button>
                    <button class="wiz-btn-next" onclick="changeStep(1)">Server is running →</button>
                </div>
            </div>

            <div class="wiz-card" data-step="2">
                <span class="wiz-card-icon">📂</span>
                <div class="wiz-step-label"><span>Step 3 of 6</span></div>
                <h2>Extract the files</h2>
                <p class="wiz-card-sub">Put the Pagezy files into the right folder so your local server can find them.</p>
                <div class="os-tabs">
                    <button class="os-tab active" onclick="switchOS(this,'win2')">Windows (Laragon)</button>
                    <button class="os-tab" onclick="switchOS(this,'mac2')">Mac (Herd)</button>
                </div>
                <div class="os-tab-content active" id="os-win2">
                    <ul class="wiz-list">
                        <li><span class="wiz-list-num">1</span><span class="wiz-list-text">Open the Laragon window → click <strong>Root</strong> (or press <kbd>Win+R</kbd> and type <code>C:\laragon\www</code>).</span></li>
                        <li><span class="wiz-list-num">2</span><span class="wiz-list-text">Extract the Pagezy ZIP here. You should end up with a folder like <code>C:\laragon\www\pagezy\</code>.</span></li>
                        <li><span class="wiz-list-num">3</span><span class="wiz-list-text">Laragon will auto-detect it and create a URL like <code>http://pagezy.test</code>.</span></li>
                    </ul>
                </div>
                <div class="os-tab-content" id="os-mac2">
                    <ul class="wiz-list">
                        <li><span class="wiz-list-num">1</span><span class="wiz-list-text">Open Finder → navigate to your home folder → find the <strong>Herd</strong> folder.</span></li>
                        <li><span class="wiz-list-num">2</span><span class="wiz-list-text">Extract the Pagezy ZIP inside the Herd folder: <code>~/Herd/pagezy/</code>.</span></li>
                        <li><span class="wiz-list-num">3</span><span class="wiz-list-text">Herd auto-detects it at <code>http://pagezy.test</code>.</span></li>
                    </ul>
                </div>
                <div class="wiz-nav">
                    <button class="wiz-btn-prev" onclick="changeStep(-1)">← Previous</button>
                    <button class="wiz-btn-next" onclick="changeStep(1)">Files are in place →</button>
                </div>
            </div>

            <div class="wiz-card" data-step="3">
                <span class="wiz-card-icon">⚙️</span>
                <div class="wiz-step-label"><span>Step 4 of 6</span></div>
                <h2>Install dependencies &amp; configure</h2>
                <p class="wiz-card-sub">Pagezy is built on Laravel, which needs a few packages installed. Open a terminal in the project folder and run these commands one at a time.</p>
                <div class="wiz-tip"><strong>Opening a terminal in Laragon:</strong> Right-click the Laragon tray icon → <strong>Quick App → Terminal</strong>. In Herd on Mac: open the built-in terminal from the menu bar icon.</div>
                <ul class="wiz-list">
                    <li><span class="wiz-list-num">1</span><span class="wiz-list-text">Navigate to your project folder in the terminal:<br><code>cd C:\laragon\www\pagezy</code> (Windows) or <code>cd ~/Herd/pagezy</code> (Mac)</span></li>
                    <li><span class="wiz-list-num">2</span><span class="wiz-list-text">Install PHP packages (this takes 1–2 minutes):</span></li>
                </ul>
                <div class="wiz-code">composer install</div>
                <ul class="wiz-list">
                    <li><span class="wiz-list-num">3</span><span class="wiz-list-text">Copy the config file template:</span></li>
                </ul>
                <div class="wiz-code">cp .env.example .env</div>
                <ul class="wiz-list">
                    <li><span class="wiz-list-num">4</span><span class="wiz-list-text">Generate your app key (a security secret for your site):</span></li>
                </ul>
                <div class="wiz-code">php artisan key:generate</div>
                <div class="wiz-nav">
                    <button class="wiz-btn-prev" onclick="changeStep(-1)">← Previous</button>
                    <button class="wiz-btn-next" onclick="changeStep(1)">Commands ran OK →</button>
                </div>
            </div>

            <div class="wiz-card" data-step="4">
                <span class="wiz-card-icon">🗄️</span>
                <div class="wiz-step-label"><span>Step 5 of 6</span></div>
                <h2>Set up the database</h2>
                <p class="wiz-card-sub">Open the <code>.env</code> file in a text editor (Notepad on Windows, TextEdit on Mac) and update the database section.</p>
                <ul class="wiz-list">
                    <li><span class="wiz-list-num">1</span><span class="wiz-list-text">Create a new local database using Laragon's HeidiSQL (Windows) or TablePlus (Mac). Name it <code>pagezy</code>.</span></li>
                    <li><span class="wiz-list-num">2</span><span class="wiz-list-text">Open the <code>.env</code> file and find the DB section. Update these lines:</span></li>
                </ul>
                <div class="wiz-code">DB_DATABASE=pagezy
DB_USERNAME=root
DB_PASSWORD=          ← leave blank for Laragon</div>
                <ul class="wiz-list">
                    <li><span class="wiz-list-num">3</span><span class="wiz-list-text">Save the file. Then in your terminal, run:</span></li>
                </ul>
                <div class="wiz-code">php artisan migrate</div>
                <ul class="wiz-list">
                    <li><span class="wiz-list-num">4</span><span class="wiz-list-text">You'll see a list of tables being created. When it says <strong>"Migration table created"</strong> — you're done!</span></li>
                </ul>
                <div class="wiz-nav">
                    <button class="wiz-btn-prev" onclick="changeStep(-1)">← Previous</button>
                    <button class="wiz-btn-next" onclick="changeStep(1)">Database is set up →</button>
                </div>
            </div>

            <div class="wiz-card done-card" data-step="5">
                <span class="wiz-card-icon">🚀</span>
                <h2>You're running locally!</h2>
                <p class="wiz-card-sub done-sub">Pagezy is now running on your machine. Open your browser and go to the local URL to see it.</p>
                <div class="wiz-code" style="display:inline-block;margin:0 auto 24px;">http://pagezy.test/setup</div>
                <p style="font-size:13px;color:var(--muted);margin-bottom:28px;">Visit <code>/setup</code> to finish installation — create your admin account and you're done.</p>
                <div class="done-links">
                    <button onclick="resetWizard()" class="done-secondary" style="border:none;cursor:pointer;">← Start over</button>
                </div>
                <div style="margin-top:36px;padding-top:24px;border-top:1px solid rgba(255,255,255,.07);font-size:13px;color:var(--muted);">
                    Stuck? <a href="/contact.php" style="color:#818CF8;">Contact us</a> and we'll help you get unstuck.
                </div>
            </div>

        </div><!-- /steps-local -->

    </div><!-- /wizardWrap -->

    <!-- Toggle to show full reference docs -->
    <div class="docs-ref-toggle" id="docsToggle" style="margin-top:60px;">
        <button onclick="toggleDocs()">📖 View full reference documentation ↓</button>
    </div>

</div><!-- /wiz-wrap -->
</section>

<!-- Full reference docs (collapsed by default) -->
<div class="docs-ref" id="docsRef">
<section class="legal-body" style="padding-top:0;">
    <div class="container">
        <div class="legal-layout">
            <aside class="legal-toc">
                <p class="legal-toc-title">Contents</p>
                <ul>
                    <li><a href="#requirements">Requirements</a></li>
                    <li><a href="#install">Installation</a></li>
                    <li><a href="#admin">Admin Panel</a></li>
                    <li><a href="#flexibuilder">FlexiBuilder</a></li>
                    <li><a href="#pages">Pages</a></li>
                    <li><a href="#blog">Blog & Posts</a></li>
                    <li><a href="#seo">SEO Tools</a></li>
                    <li><a href="#media">Media Library</a></li>
                    <li><a href="#users">Users & Roles</a></li>
                    <li><a href="#settings">Settings</a></li>
                    <li><a href="#updates">Updates</a></li>
                    <li><a href="#faq">FAQ</a></li>
                </ul>
            </aside>
            <article class="legal-content">
                <section id="requirements">
                    <h2>System Requirements</h2>
                    <p>Pagezy is designed to run on any standard PHP host — including shared cPanel hosting.</p>
                    <ul>
                        <li><strong>PHP:</strong> 8.1 or higher</li>
                        <li><strong>MySQL:</strong> 5.7+ or MariaDB 10.3+</li>
                        <li><strong>Web server:</strong> Apache (with mod_rewrite) or Nginx</li>
                        <li><strong>Disk space:</strong> Minimum 50 MB for core; more for media uploads</li>
                        <li><strong>PHP extensions:</strong> PDO, pdo_mysql, mbstring, fileinfo, GD or Imagick</li>
                    </ul>
                    <p>No Composer, Node.js, or command-line access required. Works out of the box on shared hosting.</p>
                </section>
                <section id="install">
                    <h2>Installation</h2>
                    <h3>Step 1 — Download</h3>
                    <p>Fill in the download form at <a href="/download.php">pagezy.io/download</a> and save the ZIP file to your computer.</p>
                    <h3>Step 2 — Create a database</h3>
                    <p>In your cPanel (or hosting control panel), create a new MySQL database and user. Grant the user <strong>all privileges</strong> on the database. Note down the database name, username, and password.</p>
                    <h3>Step 3 — Upload files</h3>
                    <p>Extract the ZIP and upload the contents to your server via FTP or the cPanel File Manager. For a root installation place files in <code>public_html/</code>.</p>
                    <h3>Step 4 — Run the web installer</h3>
                    <p>Visit <code>yourdomain.com/setup</code> in your browser. The installer will guide you through database connection, site name, and creating your admin account.</p>
                    <h3>Step 5 — Log in</h3>
                    <p>Go to <code>yourdomain.com/admin</code> and sign in with the credentials you set during installation.</p>
                </section>
                <section id="admin">
                    <h2>Admin Panel</h2>
                    <p>The Pagezy admin panel is accessible at <code>/admin</code>. The left sidebar contains all modules: Pages, Posts, Events, Media, Menus, Global Elements, Forms, SEO, Users, and Settings.</p>
                </section>
                <section id="flexibuilder">
                    <h2>FlexiBuilder</h2>
                    <p>FlexiBuilder is Pagezy's drag-and-drop visual page editor. Open any page and click <strong>Edit with FlexiBuilder</strong> to launch it. Add blocks, edit content, preview on mobile, and publish.</p>
                </section>
                <section id="pages"><h2>Pages</h2><p>Go to <strong>Pages → Add New</strong>. Set the title, slug, template and status, then click <strong>Edit with FlexiBuilder</strong> to design visually.</p></section>
                <section id="blog"><h2>Blog &amp; Posts</h2><p>Go to <strong>Posts → Add New</strong>. Assign categories and tags, set a featured image, and control the publish date.</p></section>
                <section id="seo"><h2>SEO Tools</h2><p>Every page has a dedicated SEO panel: meta title/description, Open Graph, schema markup, canonical URL, XML sitemap (auto-generated), redirections, and robots meta.</p></section>
                <section id="media"><h2>Media Library</h2><p>Upload images and files via <strong>Media → Upload</strong>. Supported formats: JPG, PNG, GIF, WebP, SVG, PDF.</p></section>
                <section id="users"><h2>Users &amp; Roles</h2><p>Available roles: Super Admin, Admin, Editor, Author. Go to <strong>Users → Add New</strong> to invite team members.</p></section>
                <section id="settings"><h2>Settings</h2><p>Configure site name, branding, email (SMTP), permalinks, and backups under <strong>Settings</strong>.</p></section>
                <section id="updates"><h2>Updates</h2><p>Download the latest ZIP from <a href="/download.php">pagezy.io/download</a>, back up first, then replace core files. Visit <code>/admin</code> — migrations run automatically.</p></section>
                <section id="faq">
                    <h2>FAQ</h2>
                    <h3>Can I use Pagezy on shared hosting?</h3><p>Yes — specifically designed for shared cPanel hosting. No SSH required.</p>
                    <h3>Can I use a custom domain?</h3><p>Yes. Point your domain to your server and set the site URL in Settings → General.</p>
                    <h3>Where do I get support?</h3><p>Use the <a href="/contact.php">contact form</a> or email <a href="mailto:hello@pagezy.io">hello@pagezy.io</a>.</p>
                </section>
            </article>
        </div>
    </div>
</section>
</div>

<script>
var currentPath = '';
var currentStep = 0;

var serverSteps = ['Download','Check hosting','Create database','Upload files','Point domain','Run installer','Done!'];
var localSteps  = ['Download','Install server','Extract files','Configure','Set up database','Done!'];

function startWizard(path) {
    currentPath = path;
    currentStep = 0;
    document.getElementById('pathChooser').style.display = 'none';
    document.getElementById('wizardWrap').style.display = 'block';
    document.getElementById('steps-server').style.display = path === 'server' ? 'block' : 'none';
    document.getElementById('steps-local').style.display  = path === 'local'  ? 'block' : 'none';
    buildStepDots(path);
    showStep(0);
}

function buildStepDots(path) {
    var steps = path === 'server' ? serverSteps : localSteps;
    var row = document.getElementById('stepsRow');
    row.innerHTML = '';
    steps.forEach(function(label, i) {
        var dot = document.createElement('div');
        dot.className = 'wiz-step-dot';
        dot.id = 'dot-' + i;
        dot.innerHTML = '<div class="wiz-step-dot-circle">' + (i + 1) + '</div><div class="wiz-step-dot-label">' + label + '</div>';
        dot.onclick = (function(idx){ return function(){ if(idx <= currentStep) showStep(idx); }; })(i);
        row.appendChild(dot);
    });
}

function showStep(n) {
    var steps = currentPath === 'server' ? serverSteps : localSteps;
    var total = steps.length;
    var container = document.getElementById('steps-' + currentPath);
    var cards = container.querySelectorAll('.wiz-card');

    cards.forEach(function(c){ c.classList.remove('visible'); });
    if (cards[n]) cards[n].classList.add('visible');

    currentStep = n;

    // Progress bar
    document.getElementById('progBar').style.width = (((n + 1) / total) * 100) + '%';

    // Step dots
    for (var i = 0; i < total; i++) {
        var dot = document.getElementById('dot-' + i);
        if (!dot) continue;
        dot.className = 'wiz-step-dot' + (i < n ? ' done' : (i === n ? ' current' : ''));
    }
}

function changeStep(delta) {
    var steps = currentPath === 'server' ? serverSteps : localSteps;
    var next = currentStep + delta;
    if (next < 0) {
        resetWizard();
        return;
    }
    if (next >= steps.length) return;
    showStep(next);
    window.scrollTo({ top: document.getElementById('wizardWrap').offsetTop - 80, behavior: 'smooth' });
}

function resetWizard() {
    currentPath = '';
    currentStep = 0;
    document.getElementById('pathChooser').style.display = 'block';
    document.getElementById('wizardWrap').style.display = 'none';
    document.getElementById('steps-server').style.display = 'none';
    document.getElementById('steps-local').style.display  = 'none';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function switchOS(btn, id) {
    var parent = btn.closest('.wiz-card');
    parent.querySelectorAll('.os-tab').forEach(function(t){ t.classList.remove('active'); });
    parent.querySelectorAll('.os-tab-content').forEach(function(c){ c.classList.remove('active'); });
    btn.classList.add('active');
    var el = document.getElementById(id);
    if (el) el.classList.add('active');
}

function toggleDocs() {
    var ref = document.getElementById('docsRef');
    ref.classList.toggle('open');
    document.querySelector('#docsToggle button').textContent = ref.classList.contains('open')
        ? '↑ Hide reference documentation'
        : '📖 View full reference documentation ↓';
}
</script>

<?php include 'includes/footer.php'; ?>
