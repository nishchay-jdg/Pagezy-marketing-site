<!-- ── Announcement bar ── -->
<div class="announce-bar">
    <span class="announce-ticker" id="announceTicker"></span>
</div>
<script>
(function(){
    var msgs = [
        'Create high-converting web pages — faster than ever.',
        '🚀 Now in free launch — download Pagezy and go live today.',
        '✓ Self-hosted · No subscriptions · No vendor lock-in.'
    ];
    var i = 0, el = document.getElementById('announceTicker');
    function show(){ el.style.opacity=0; setTimeout(function(){ el.textContent=msgs[i]; el.style.opacity=1; i=(i+1)%msgs.length; },300); }
    el.style.transition='opacity .3s';
    show();
    setInterval(show, 4000);
})();
</script>

<!-- ── Nav ── -->
<nav class="nav" id="nav">
    <div class="nav-pill-wrap">
        <div class="nav-pill">
            <a href="/" class="nav-logo">
                <span class="nav-logo-text">Page<span class="nav-logo-grad">zy</span></span>
            </a>
            <ul class="nav-links">
                <li><a href="/#features">Features</a></li>
                <li><a href="/#how">How it works</a></li>
                <li><a href="/pricing.php">Pricing</a></li>
                <li><a href="/docs.php">Docs</a></li>
                <li><a href="/contact.php">Contact</a></li>
            </ul>
            <div class="nav-actions">
                <a href="/pricing.php" class="btn btn-ghost btn-sm">Sign In</a>
                <a href="/download.php" class="btn btn-primary btn-sm">Download free</a>
            </div>
            <button class="nav-hamburger" onclick="mobileNavOpen()" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</nav>

<!-- Mobile nav -->
<div id="mobile-overlay" class="mobile-nav-overlay" onclick="mobileNavClose()"></div>
<div id="mobile-drawer" class="mobile-nav-drawer">
    <button class="mobile-nav-close" onclick="mobileNavClose()">&#x2715;</button>
    <nav class="mobile-nav-links">
        <a href="/#features" onclick="mobileNavClose()">Features</a>
        <a href="/#how" onclick="mobileNavClose()">How it works</a>
        <a href="/pricing.php" onclick="mobileNavClose()">Pricing</a>
        <a href="/docs.php" onclick="mobileNavClose()">Docs</a>
        <a href="/contact.php" onclick="mobileNavClose()">Contact</a>
    </nav>
    <div class="mobile-nav-cta">
        <a href="/download.php" class="btn btn-primary" style="width:100%;justify-content:center;">Download free</a>
    </div>
</div>
