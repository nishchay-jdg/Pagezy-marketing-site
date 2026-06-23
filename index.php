<?php
$pageTitle       = 'Pagezy — Build beautiful websites in minutes.';
$pageDescription = 'Pagezy is the modern CMS for agencies, developers and businesses. Download free and build stunning websites without the complexity.';
include 'includes/head.php';
include 'includes/nav.php';
?>

<!-- ── Hero ── -->
<section class="hero">
    <div class="hero-bg">
        <div class="hero-glow-center"></div>
    </div>
    <div class="hero-content fade-up">
        <h1>The CMS built<br>for the modern web.</h1>
        <p class="hero-sub">Self-hosted, no subscriptions, no vendor lock-in.<br>Give your clients a clean, powerful platform they'll love.</p>
        <div class="hero-actions">
            <a href="/download.php" class="btn btn-primary btn-pill">Download Free</a>
            <a href="#features" class="btn btn-outline btn-pill">See features →</a>
        </div>
        <!-- Pill badges -->
        <div class="hero-pills">
            <span class="hero-pill">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#34D399" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Free forever
            </span>
            <span class="hero-pill">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#34D399" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                No credit card
            </span>
            <span class="hero-pill">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#34D399" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Self-hosted
            </span>
        </div>
    </div>

    <!-- Product screenshot — Framer-style peeking frame -->
    <div class="hero-mockup fade-up">
        <!-- neon glow border ring (gradient border trick) -->
        <div class="mockup-border-outer">
            <div class="mockup-border-inner">
                <img src="/assets/img/flexibuilder-preview.png" alt="Pagezy visual builder" loading="lazy">
            </div>
        </div>
        <!-- ambient glow behind the frame -->
        <div class="mockup-ambient"></div>
    </div>
</section>


<!-- ── Social proof strip ── -->
<div class="social-proof-strip">
    <div class="container">
        <div class="social-proof-inner">
            <div class="sp-avatars">
                <div class="sp-avatar" style="background:linear-gradient(135deg,#6D28D9,#4F46E5);">S</div>
                <div class="sp-avatar" style="background:linear-gradient(135deg,#0EA5E9,#6D28D9);">R</div>
                <div class="sp-avatar" style="background:linear-gradient(135deg,#10B981,#0EA5E9);">A</div>
                <div class="sp-avatar" style="background:linear-gradient(135deg,#F59E0B,#EF4444);">M</div>
                <div class="sp-avatar" style="background:linear-gradient(135deg,#EC4899,#8B5CF6);">J</div>
            </div>
            <div class="sp-text">
                <strong>2,400+</strong> developers &amp; agencies already use Pagezy
            </div>
            <div class="sp-divider"></div>
            <div class="sp-badges">
                <span class="sp-badge">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    PHP 8.1+
                </span>
                <span class="sp-badge">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                    Self-hosted
                </span>
                <span class="sp-badge">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    No lock-in
                </span>
                <span class="sp-badge">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    30-day refund
                </span>
            </div>
        </div>
    </div>
</div>

<!-- ── Features ── -->
<section class="section features" id="features">
    <div class="container">
        <div class="section-header">
            <div class="section-label">Features</div>
            <h2 class="section-title">Everything you need<br><span class="grad-text">to build the web.</span></h2>
            <p class="section-sub">A complete CMS suite — from drag-and-drop page builder to SEO tools, blog, events, and more.</p>
        </div>
        <div class="features-grid">
            <!-- Hero feature: FlexiBuilder — spans 2 cols -->
            <div class="feature-card feature-card-hero fade-up">
                <div class="feat-hero-inner">
                    <div>
                        <div class="feat-icon-wrap feat-icon-wrap-hero">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="8" height="5" rx="1"/><rect x="13" y="3" width="8" height="5" rx="1"/>
                                <rect x="3" y="11" width="8" height="10" rx="1"/><rect x="13" y="11" width="8" height="5" rx="1"/>
                            </svg>
                        </div>
                        <h3>FlexiBuilder</h3>
                        <p>Drag-and-drop visual page builder with pre-built blocks. Build any layout — no code required. Works on any device, live preview as you design.</p>
                        <div class="feat-tags">
                            <span class="feat-tag">Drag & Drop</span>
                            <span class="feat-tag">Live Preview</span>
                            <span class="feat-tag">Pre-built Blocks</span>
                        </div>
                    </div>
                    <div class="feat-hero-visual">
                        <div class="feat-block-demo">
                            <div class="fbd-row"><div class="fbd-block fbd-lg"></div><div class="fbd-block fbd-sm"></div></div>
                            <div class="fbd-row"><div class="fbd-block fbd-sm"></div><div class="fbd-block fbd-md"></div><div class="fbd-block fbd-sm"></div></div>
                            <div class="fbd-row"><div class="fbd-block fbd-full"></div></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Brand System -->
            <div class="feature-card fade-up">
                <div class="feat-icon-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3"/>
                        <line x1="12" y1="3" x2="12" y2="9"/><line x1="12" y1="15" x2="12" y2="21"/>
                        <line x1="3" y1="12" x2="9" y2="12"/><line x1="15" y1="12" x2="21" y2="12"/>
                    </svg>
                </div>
                <h3>Brand System</h3>
                <p>Set your colors, fonts, and logo once. Every template and block automatically picks up your brand.</p>
            </div>
            <!-- Mobile-First -->
            <div class="feature-card fade-up">
                <div class="feat-icon-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="5" y="2" width="14" height="20" rx="2"/>
                        <line x1="9" y1="7" x2="15" y2="7"/><line x1="9" y1="11" x2="15" y2="11"/>
                        <line x1="9" y1="15" x2="12" y2="15"/>
                    </svg>
                </div>
                <h3>Mobile-First</h3>
                <p>Every page is responsive out of the box. Mobile sticky CTA, hamburger nav, and touch-friendly UI.</p>
            </div>
            <!-- Built-in SEO -->
            <div class="feature-card fade-up">
                <div class="feat-icon-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="7"/><line x1="16.5" y1="16.5" x2="22" y2="22"/>
                        <line x1="8" y1="11" x2="14" y2="11"/><line x1="11" y1="8" x2="11" y2="14"/>
                    </svg>
                </div>
                <h3>Built-in SEO</h3>
                <p>Meta titles, Open Graph, schema markup, XML sitemaps, and redirections — all built in, no plugins.</p>
            </div>
            <!-- Blog & Events -->
            <div class="feature-card fade-up">
                <div class="feat-icon-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16v2.5a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4z"/>
                        <path d="M4 8.5V18a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8.5"/>
                        <line x1="8" y1="13" x2="16" y2="13"/><line x1="8" y1="17" x2="13" y2="17"/>
                    </svg>
                </div>
                <h3>Blog & Events</h3>
                <p>Full-featured blog with categories and tags. Events module with custom slugs and rich content support.</p>
            </div>
            <!-- One-click Install -->
            <div class="feature-card fade-up">
                <div class="feat-icon-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="13 2 13 9 20 9"/><path d="M20 9L13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/>
                        <polyline points="9 15 11 17 15 13"/>
                    </svg>
                </div>
                <h3>One-click Install</h3>
                <p>Web-based installer. Set up your database, site info, and brand in under 5 minutes on any PHP host.</p>
            </div>
        </div>
    </div>
</section>

<!-- ── 3 ways section ── -->
<section class="section paths-section">
    <div class="container">
        <div class="section-header" style="text-align:center;">
            <div class="section-label" style="text-align:center;">Made for everyone</div>
            <h2 class="section-title" style="text-align:center;">3 ways to use<br><span class="grad-text">Pagezy.</span></h2>
            <p class="section-sub" style="margin:0 auto;text-align:center;">Modern, easy drag-and-drop builder — pick the path that fits you.</p>
        </div>
        <div class="paths-grid paths-grid--3">

            <!-- Way 1: Free self-hosted -->
            <div class="path-card path-card--free fade-up">
                <div class="path-num">01</div>
                <div class="path-icon-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="3" width="20" height="14" rx="2"/>
                        <line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>
                    </svg>
                </div>
                <div class="path-card-badge">Free forever</div>
                <h3>You have hosting &amp; domain</h3>
                <p>Download Pagezy, upload to your server, and start building — completely free. No subscription, no limits.</p>
                <ul class="path-perks">
                    <li><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Full drag-and-drop builder</li>
                    <li><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Self-hosted, your data</li>
                    <li><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> No credit card required</li>
                </ul>
                <a href="/download.php" class="path-cta btn btn-outline">Download free →</a>
            </div>

            <!-- Way 2: Paid builder (featured) -->
            <div class="path-card path-card--pro fade-up">
                <div class="path-num">02</div>
                <div class="path-icon-wrap path-icon-wrap--indigo">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/>
                        <rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/>
                    </svg>
                </div>
                <div class="path-card-badge path-card-badge--indigo">Pro &amp; Agency</div>
                <h3>Need templates &amp; more power</h3>
                <p>Unlock pre-built templates — pick one, install with one click, update your brand logo, and your site is ready. Plus priority support.</p>
                <div class="path-steps">
                    <div class="path-step"><span class="path-step-num">1</span><span>Pick a template</span></div>
                    <div class="path-step-arrow">→</div>
                    <div class="path-step"><span class="path-step-num">2</span><span>One-click install</span></div>
                    <div class="path-step-arrow">→</div>
                    <div class="path-step"><span class="path-step-num">3</span><span>Update your brand</span></div>
                    <div class="path-step-arrow">→</div>
                    <div class="path-step path-step--done"><span class="path-step-num">✓</span><span>Site is live</span></div>
                </div>
                <a href="/pricing.php" class="path-cta btn btn-primary">See plans →</a>
            </div>

            <!-- Way 3: Done for you -->
            <div class="path-card path-card--dfy fade-up">
                <div class="path-num">03</div>
                <div class="path-icon-wrap path-icon-wrap--green">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div class="path-card-badge path-card-badge--green">We do the work</div>
                <h3>Need an expert to build it</h3>
                <p>Have a domain but want a pro to handle everything? Our team designs and launches your site — starting at an affordable price.</p>
                <ul class="path-perks path-perks--green">
                    <li><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4ADE80" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Custom design for your brand</li>
                    <li><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4ADE80" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Fast turnaround</li>
                    <li><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4ADE80" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Starting at an affordable rate</li>
                </ul>
                <a href="/contact.php" class="path-cta btn btn-outline">Get in touch →</a>
            </div>

        </div>
    </div>
</section>

<!-- ── How it works ── -->
<section class="section how" id="how">
    <div class="container">
        <div class="section-header" style="text-align:center;">
            <div class="section-label" style="text-align:center;">How it works</div>
            <h2 class="section-title" style="text-align:center;">Up and running<br><span class="grad-text">in 3 simple steps.</span></h2>
            <p class="section-sub" style="margin:0 auto;text-align:center;">No developers, no servers to configure. Fill a form, upload, and launch.</p>
        </div>
        <div class="steps-grid-v2">

            <div class="step-v2 fade-up">
                <div class="step-v2-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                </div>
                <div class="step-v2-num">01</div>
                <h3>Download</h3>
                <p>Fill a quick form and grab the Pagezy ZIP — free, no account needed. Takes 30 seconds.</p>
                <div class="step-v2-detail">
                    <span>✓ No account required</span>
                    <span>✓ Instant download</span>
                </div>
            </div>

            <div class="step-v2-arrow fade-up">
                <svg viewBox="0 0 48 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="0" y1="12" x2="40" y2="12" stroke-dasharray="4 3"/>
                    <polyline points="34,6 40,12 34,18"/>
                </svg>
            </div>

            <div class="step-v2 fade-up">
                <div class="step-v2-icon step-v2-icon-2">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="16 18 22 12 16 6"/>
                        <polyline points="8 6 2 12 8 18"/>
                    </svg>
                </div>
                <div class="step-v2-num">02</div>
                <h3>Install</h3>
                <p>Upload to your cPanel or VPS. Run the web-based installer — it sets up your database in minutes.</p>
                <div class="step-v2-detail">
                    <span>✓ Works on cPanel</span>
                    <span>✓ Under 5 minutes</span>
                </div>
            </div>

            <div class="step-v2-arrow fade-up">
                <svg viewBox="0 0 48 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="0" y1="12" x2="40" y2="12" stroke-dasharray="4 3"/>
                    <polyline points="34,6 40,12 34,18"/>
                </svg>
            </div>

            <div class="step-v2 fade-up">
                <div class="step-v2-icon step-v2-icon-3">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 20h9"/>
                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                    </svg>
                </div>
                <div class="step-v2-num">03</div>
                <h3>Build & go live</h3>
                <p>Use FlexiBuilder to design pages, add your brand, and publish. No developers or designers needed.</p>
                <div class="step-v2-detail">
                    <span>✓ Drag & drop</span>
                    <span>✓ Instant publish</span>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ── Testimonials ── -->
<section class="section testimonials" id="testimonials">
    <div class="container">
        <div class="section-header" style="text-align:center;">
            <div class="section-label" style="text-align:center;">Reviews</div>
            <h2 class="section-title" style="text-align:center;">Teams love <span class="grad-text">Pagezy.</span></h2>
        </div>
        <div class="testimonials-grid">
            <div class="testimonial-card fade-up">
                <div class="testimonial-stars">
                    ★★★★★ <span style="font-size:11px;color:var(--muted);font-weight:500;margin-left:4px;">5.0</span>
                </div>
                <p class="testimonial-text">"We moved 14 client sites from WordPress to Pagezy in a week. Setup is night-and-day faster, and not a single client has called asking for help with the admin."</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar" style="background:linear-gradient(135deg,#6D28D9,#4F46E5);">S</div>
                    <div>
                        <div class="testimonial-name">Sarah Mitchell</div>
                        <div class="testimonial-role">Founder · BrightWeb Agency, UK</div>
                    </div>
                </div>
            </div>
            <div class="testimonial-card fade-up">
                <div class="testimonial-stars">
                    ★★★★★ <span style="font-size:11px;color:var(--muted);font-weight:500;margin-left:4px;">5.0</span>
                </div>
                <p class="testimonial-text">"Uploaded the ZIP, ran the installer, had a live site in under 5 minutes on shared cPanel hosting. FlexiBuilder lets clients edit their own pages — zero support tickets since."</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar" style="background:linear-gradient(135deg,#0EA5E9,#6D28D9);">R</div>
                    <div>
                        <div class="testimonial-name">Ravi Patel</div>
                        <div class="testimonial-role">Freelance Developer · 18 active client sites</div>
                    </div>
                </div>
            </div>
            <div class="testimonial-card fade-up">
                <div class="testimonial-stars">
                    ★★★★★ <span style="font-size:11px;color:var(--muted);font-weight:500;margin-left:4px;">5.0</span>
                </div>
                <p class="testimonial-text">"Built-in redirections, XML sitemaps, and schema markup out of the box. Our clients' pages started ranking faster after switching — no plugins, no drama."</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar" style="background:linear-gradient(135deg,#10B981,#0EA5E9);">A</div>
                    <div>
                        <div class="testimonial-name">Anika Sharma</div>
                        <div class="testimonial-role">SEO Lead · GrowthStack Digital</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── Pricing preview ── -->
<section class="section pricing" id="pricing" style="background:var(--dark-card);border-top:1px solid var(--dark-border);">
    <div class="container">
        <div class="section-header" style="text-align:center;">
            <div class="section-label" style="text-align:center;">Pricing</div>
            <h2 class="section-title" style="text-align:center;">Plans that <span class="grad-text">scale with you.</span></h2>
            <p class="section-sub" style="margin:0 auto;">Start free. Upgrade when you need more sites, support, or power.</p>
        </div>

        <!-- 3 cards matching pricing page -->
        <div class="pricing-v2-grid">

            <!-- Starter -->
            <div class="pv2-card fade-up">
                <div class="pv2-head">
                    <div class="pv2-plan">Starter</div>
                    <div class="pv2-desc">For individuals and personal projects.</div>
                    <div class="pv2-price-wrap">
                        <div class="pv2-price"><span class="pv2-amount">$0</span><span class="pv2-per"> / mo</span></div>
                        <div class="pv2-price-note">Free forever</div>
                    </div>
                    <div class="pv2-sites-row">
                        <span class="pv2-sites-num">1</span>
                        <span class="pv2-sites-label">website included</span>
                    </div>
                </div>
                <div class="pv2-divider"></div>
                <div class="pv2-features-label">All basic features</div>
                <ul class="pv2-features">
                    <li>
                        <span class="pv2-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                        <span><strong>1 website</strong><small>Install on any PHP hosting</small></span>
                    </li>
                    <li>
                        <span class="pv2-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                        <span><strong>Unlimited pages & posts</strong><small>No page cap, ever</small></span>
                    </li>
                    <li>
                        <span class="pv2-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                        <span><strong>FlexiBuilder drag & drop</strong><small>Visual page builder included</small></span>
                    </li>
                    <li>
                        <span class="pv2-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                        <span><strong>Built-in SEO tools</strong></span>
                    </li>
                    <li class="pv2-feature-no">
                        <span class="pv2-check pv2-check-no"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span>
                        <span><strong>Premium templates</strong></span>
                    </li>
                    <li class="pv2-feature-no">
                        <span class="pv2-check pv2-check-no"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span>
                        <span><strong>White-label admin</strong></span>
                    </li>
                </ul>
                <a href="/download.php" class="pv2-cta pv2-cta-ghost">Download free</a>
            </div>

            <!-- Pro (featured) -->
            <div class="pv2-card pv2-card-featured fade-up">
                <div class="pv2-popular-badge">Most popular</div>
                <div class="pv2-head">
                    <div class="pv2-plan">Pro</div>
                    <div class="pv2-desc">For professionals building client sites.</div>
                    <div class="pv2-price-wrap">
                        <div class="pv2-price"><span class="pv2-amount">$19</span><span class="pv2-per"> / mo</span></div>
                        <div class="pv2-price-note">Billed monthly</div>
                    </div>
                    <div class="pv2-sites-row">
                        <span class="pv2-sites-num">5</span>
                        <span class="pv2-sites-label">websites included</span>
                    </div>
                    <div class="pv2-extra-note">+$5/mo per additional site</div>
                </div>
                <div class="pv2-divider"></div>
                <div class="pv2-features-label">Everything in <strong>Starter</strong> plus…</div>
                <ul class="pv2-features">
                    <li>
                        <span class="pv2-check pv2-check-featured"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                        <span><strong>Premium templates</strong><small>1-click install, brand-ready</small></span>
                    </li>
                    <li>
                        <span class="pv2-check pv2-check-featured"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                        <span><strong>Priority email support</strong><small>Response within 12 hours</small></span>
                    </li>
                    <li>
                        <span class="pv2-check pv2-check-featured"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                        <span><strong>Automatic CMS updates</strong></span>
                    </li>
                    <li>
                        <span class="pv2-check pv2-check-featured"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                        <span><strong>Advanced SEO & schema</strong></span>
                    </li>
                    <li>
                        <span class="pv2-check pv2-check-featured"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                        <span><strong>Age gate & popups</strong></span>
                    </li>
                    <li class="pv2-feature-no">
                        <span class="pv2-check pv2-check-no"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span>
                        <span><strong>White-label admin</strong></span>
                    </li>
                </ul>
                <a href="/pricing.php" class="pv2-cta pv2-cta-primary">Get Pro →</a>
            </div>

            <!-- Agency -->
            <div class="pv2-card fade-up">
                <div class="pv2-head">
                    <div class="pv2-plan">Agency</div>
                    <div class="pv2-desc">For agencies managing multiple clients.</div>
                    <div class="pv2-price-wrap">
                        <div class="pv2-price"><span class="pv2-amount">$49</span><span class="pv2-per"> / mo</span></div>
                        <div class="pv2-price-note">Billed monthly</div>
                    </div>
                    <div class="pv2-sites-row">
                        <span class="pv2-sites-num">∞</span>
                        <span class="pv2-sites-label">unlimited websites</span>
                    </div>
                </div>
                <div class="pv2-divider"></div>
                <div class="pv2-features-label">Everything in <strong>Pro</strong> plus…</div>
                <ul class="pv2-features">
                    <li>
                        <span class="pv2-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                        <span><strong>White-label admin panel</strong><small>Your brand, not ours</small></span>
                    </li>
                    <li>
                        <span class="pv2-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                        <span><strong>Client management</strong></span>
                    </li>
                    <li>
                        <span class="pv2-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                        <span><strong>Team member accounts</strong><small>Up to 5 seats</small></span>
                    </li>
                    <li>
                        <span class="pv2-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                        <span><strong>Dedicated support (4hr SLA)</strong></span>
                    </li>
                    <li>
                        <span class="pv2-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                        <span><strong>Early feature access</strong></span>
                    </li>
                </ul>
                <a href="/pricing.php" class="pv2-cta pv2-cta-ghost">Get Agency →</a>
            </div>

        </div>

        <!-- Lifetime banner -->
        <div class="lifetime-banner fade-up" style="margin-top:20px;">
            <div class="lifetime-left">
                <div class="lifetime-badge">One-time · Founding member</div>
                <h3>Pagezy Lifetime Deal <span class="grad-text">— $299</span></h3>
                <p>Pay once. Get everything in Agency forever. No subscriptions, ever.</p>
            </div>
            <div class="lifetime-right">
                <div class="lifetime-perks">
                    <div class="lifetime-perk"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Unlimited websites, forever</div>
                    <div class="lifetime-perk"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Lifetime software updates</div>
                    <div class="lifetime-perk"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> White-label + reseller license</div>
                    <div class="lifetime-perk"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Private roadmap access</div>
                </div>
                <a href="/pricing.php" class="pv2-cta pv2-cta-primary" style="margin-top:0;display:inline-block;width:auto;padding:12px 28px;">Get Lifetime →</a>
            </div>
        </div>

        <p style="text-align:center;margin-top:28px;font-size:14px;">
            <a href="/pricing.php" style="color:#A78BFA;text-decoration:none;font-weight:600;">View full pricing & compare plans →</a>
        </p>
    </div>
</section>

<!-- ── CTA ── -->
<section class="cta-section">
    <div class="hero-bg">
        <div class="hero-bg-blob hero-bg-blob-1"></div>
        <div class="hero-bg-blob hero-bg-blob-2"></div>
    </div>
    <div class="container" style="position:relative;z-index:1;">
        <div class="cta-badge">Free forever during launch</div>
        <h2>Your next client site is<br><span class="grad-text">5 minutes away.</span></h2>
        <p>Download Pagezy, install on any PHP host, and go live before lunch.</p>
        <div class="hero-actions" style="justify-content:center;">
            <a href="/download.php" class="btn btn-primary btn-lg">Download free — it's on us</a>
            <a href="/contact.php" class="btn btn-outline btn-lg">Talk to us</a>
        </div>
        <div class="cta-trust">
            <span>✓ No credit card</span>
            <span>✓ Self-hosted</span>
            <span>✓ 30-day refund</span>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
