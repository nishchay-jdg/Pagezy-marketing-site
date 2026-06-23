<?php
$pageTitle = 'Download starting — Thank you! | Pagezy';
include 'includes/head.php';
include 'includes/nav.php';
?>

<div class="thankyou">
    <div class="hero-bg" style="position:fixed;">
        <div class="hero-bg-blob hero-bg-blob-1"></div>
        <div class="hero-bg-blob hero-bg-blob-2"></div>
    </div>
    <div class="thankyou-card" style="position:relative;z-index:1;">
        <div class="thankyou-icon">
            <img src="/assets/img/pagezy-logo.png" alt="Pagezy" style="width:72px;height:72px;border-radius:18px;">
        </div>
        <h1>You're all set!</h1>
        <p style="color:var(--muted);font-size:15px;line-height:1.65;margin-bottom:28px;">
            Your download is starting automatically. Install Pagezy on your PHP server and go live in minutes.
        </p>

        <a href="/serve-download.php" class="btn btn-primary" id="dl-btn"
           style="width:100%;justify-content:center;padding:15px;margin-bottom:16px;font-size:15px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            Download Pagezy CMS
        </a>

        <div style="border-top:1px solid var(--dark-border);padding-top:24px;text-align:left;margin-top:8px;">
            <p style="font-size:14px;font-weight:700;margin-bottom:14px;color:var(--text);">Quick install guide:</p>
            <ol style="font-size:13px;color:var(--muted);line-height:2.2;padding-left:20px;margin:0;">
                <li>Upload the ZIP to your server and extract it</li>
                <li>Point your domain to the <code style="background:rgba(255,255,255,.08);padding:1px 6px;border-radius:4px;">/public</code> folder</li>
                <li>Visit <code style="background:rgba(255,255,255,.08);padding:1px 6px;border-radius:4px;">yourdomain.com/setup</code></li>
                <li>Follow the installer — done in 5 minutes</li>
            </ol>
        </div>

        <div style="display:flex;gap:12px;margin-top:28px;">
            <a href="/" class="btn btn-dark" style="flex:1;justify-content:center;">← Home</a>
            <a href="/docs.php" class="btn btn-outline" style="flex:1;justify-content:center;">Read docs</a>
        </div>
    </div>
</div>

<script>
// Auto-trigger download after 1.2 seconds
setTimeout(() => { document.getElementById('dl-btn').click(); }, 1200);
</script>

<?php include 'includes/footer.php'; ?>
