<?php
$pageTitle       = 'Pricing — Pagezy CMS';
$pageDescription = 'Simple, transparent pricing. Start free and upgrade when you need more. No hidden fees.';
include 'includes/head.php';
include 'includes/nav.php';
?>

<!-- ── Hero ── -->
<section style="padding:120px 24px 64px;text-align:center;position:relative;overflow:hidden;">
    <div class="hero-bg">
        <div class="hero-glow-center" style="opacity:.6;"></div>
        <div class="hero-bg-grid"></div>
    </div>
    <div class="container" style="position:relative;z-index:1;">
        <div class="hero-badge" style="justify-content:center;margin-bottom:20px;">
            <span class="hero-badge-dot"></span>
            Free forever during launch
        </div>
        <h1 style="font-size:clamp(40px,6vw,68px);font-weight:900;margin-bottom:16px;letter-spacing:-.03em;">
            Plans that <span class="grad-text">scale with you</span>
        </h1>
        <p style="font-size:18px;color:var(--muted);max-width:480px;margin:0 auto 40px;line-height:1.6;">
            Start free. Upgrade when you need more sites, support, or power.
        </p>

        <!-- Billing toggle -->
        <div class="billing-toggle" id="billingToggle">
            <button class="billing-btn active" id="btnMonthly" onclick="setBilling('monthly')">Monthly</button>
            <button class="billing-btn" id="btnYearly"  onclick="setBilling('yearly')">
                Yearly
                <span class="save-badge">Save 20%</span>
            </button>
        </div>
    </div>
</section>

<!-- ── Pricing cards ── -->
<section style="padding:0 24px 80px;position:relative;z-index:1;">
    <div class="container">
        <div class="pricing-v2-grid">

            <!-- Starter -->
            <div class="pv2-card">
                <div class="pv2-head">
                    <div class="pv2-plan">Starter</div>
                    <div class="pv2-desc">For individuals and personal projects.</div>
                    <div class="pv2-price-wrap">
                        <div class="pv2-price">
                            <span class="pv2-amount">$0</span>
                            <span class="pv2-per"> / mo</span>
                        </div>
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
                        <span class="pv2-check">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                        <span>
                            <strong>1 website</strong>
                            <small>Install on any PHP hosting</small>
                        </span>
                    </li>
                    <li>
                        <span class="pv2-check">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                        <span>
                            <strong>Unlimited pages & posts</strong>
                            <small>No page cap, ever</small>
                        </span>
                    </li>
                    <li>
                        <span class="pv2-check">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                        <span>
                            <strong>FlexiBuilder drag & drop</strong>
                            <small>Visual page builder included</small>
                        </span>
                    </li>
                    <li>
                        <span class="pv2-check">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                        <span>
                            <strong>Built-in SEO tools</strong>
                            <small>Meta, schema & sitemap</small>
                        </span>
                    </li>
                    <li>
                        <span class="pv2-check">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                        <span>
                            <strong>Blog, events & deals modules</strong>
                        </span>
                    </li>
                    <li class="pv2-feature-no">
                        <span class="pv2-check pv2-check-no">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </span>
                        <span><strong>Premium templates</strong></span>
                    </li>
                    <li class="pv2-feature-no">
                        <span class="pv2-check pv2-check-no">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </span>
                        <span><strong>White-label admin</strong></span>
                    </li>
                </ul>
                <a href="/download.php" class="pv2-cta pv2-cta-ghost">Download free</a>
            </div>

            <!-- Pro (featured) -->
            <div class="pv2-card pv2-card-featured">
                <div class="pv2-popular-badge">Most popular</div>
                <div class="pv2-head">
                    <div class="pv2-plan">Pro</div>
                    <div class="pv2-desc">For professionals building client sites.</div>
                    <div class="pv2-price-wrap">
                        <div class="pv2-price">
                            <span class="pv2-amount" data-monthly="19" data-yearly="15">$19</span>
                            <span class="pv2-per"> / mo</span>
                        </div>
                        <div class="pv2-price-note pv2-yearly-note" style="display:none;">Billed $180/year · Save $48</div>
                        <div class="pv2-price-note pv2-monthly-note">Billed monthly</div>
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
                        <span class="pv2-check pv2-check-featured">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                        <span>
                            <strong>5 websites</strong>
                            <small>+$5/mo per extra site</small>
                        </span>
                    </li>
                    <li>
                        <span class="pv2-check pv2-check-featured">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                        <span>
                            <strong>Premium templates</strong>
                            <small>1-click install, brand-ready</small>
                        </span>
                    </li>
                    <li>
                        <span class="pv2-check pv2-check-featured">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                        <span>
                            <strong>Priority email support</strong>
                            <small>Response within 12 hours</small>
                        </span>
                    </li>
                    <li>
                        <span class="pv2-check pv2-check-featured">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                        <span>
                            <strong>Automatic CMS updates</strong>
                            <small>Stay up to date effortlessly</small>
                        </span>
                    </li>
                    <li>
                        <span class="pv2-check pv2-check-featured">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                        <span>
                            <strong>Advanced SEO & schema</strong>
                        </span>
                    </li>
                    <li>
                        <span class="pv2-check pv2-check-featured">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                        <span>
                            <strong>Age gate & popups</strong>
                        </span>
                    </li>
                    <li class="pv2-feature-no">
                        <span class="pv2-check pv2-check-no">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </span>
                        <span><strong>White-label admin</strong></span>
                    </li>
                </ul>
                <a href="/contact.php?subject=pro" class="pv2-cta pv2-cta-primary">Get Pro →</a>
            </div>

            <!-- Agency -->
            <div class="pv2-card">
                <div class="pv2-head">
                    <div class="pv2-plan">Agency</div>
                    <div class="pv2-desc">For agencies managing multiple clients.</div>
                    <div class="pv2-price-wrap">
                        <div class="pv2-price">
                            <span class="pv2-amount" data-monthly="49" data-yearly="39">$49</span>
                            <span class="pv2-per"> / mo</span>
                        </div>
                        <div class="pv2-price-note pv2-yearly-note" style="display:none;">Billed $468/year · Save $120</div>
                        <div class="pv2-price-note pv2-monthly-note">Billed monthly</div>
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
                        <span class="pv2-check">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                        <span>
                            <strong>Unlimited websites</strong>
                            <small>No cap on client installs</small>
                        </span>
                    </li>
                    <li>
                        <span class="pv2-check">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                        <span>
                            <strong>White-label admin panel</strong>
                            <small>Your brand, not ours</small>
                        </span>
                    </li>
                    <li>
                        <span class="pv2-check">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                        <span>
                            <strong>Client management</strong>
                            <small>Manage all clients in one place</small>
                        </span>
                    </li>
                    <li>
                        <span class="pv2-check">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                        <span>
                            <strong>Team member accounts</strong>
                            <small>Up to 5 team seats</small>
                        </span>
                    </li>
                    <li>
                        <span class="pv2-check">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                        <span>
                            <strong>Dedicated support channel</strong>
                            <small>Response within 4 hours</small>
                        </span>
                    </li>
                    <li>
                        <span class="pv2-check">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                        <span>
                            <strong>Custom branding on installer</strong>
                        </span>
                    </li>
                    <li>
                        <span class="pv2-check">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                        <span>
                            <strong>Early feature access</strong>
                        </span>
                    </li>
                </ul>
                <a href="/contact.php?subject=enterprise" class="pv2-cta pv2-cta-ghost">Get Agency →</a>
            </div>

        </div>

        <!-- Lifetime deal banner -->
        <div class="lifetime-banner fade-up">
            <div class="lifetime-left">
                <div class="lifetime-badge">One-time · Founding member</div>
                <h3>Pagezy Lifetime Deal <span class="grad-text">— $299</span></h3>
                <p>Pay once. Get everything in Agency forever. No subscriptions, no renewals. Includes lifetime updates + early access to Pagezy v2.</p>
            </div>
            <div class="lifetime-right">
                <div class="lifetime-perks">
                    <div class="lifetime-perk">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Unlimited websites, forever
                    </div>
                    <div class="lifetime-perk">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Lifetime software updates
                    </div>
                    <div class="lifetime-perk">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        White-label + reseller license
                    </div>
                    <div class="lifetime-perk">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Private roadmap + feature requests
                    </div>
                </div>
                <a href="/contact.php?subject=enterprise" class="pv2-cta pv2-cta-primary" style="margin-top:0;">Get Lifetime →</a>
            </div>
        </div>

        <!-- Compare all plans toggle -->
        <div style="text-align:center;margin-top:48px;">
            <button onclick="toggleCompare()" id="compareBtn"
                style="background:none;border:none;color:#A78BFA;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;display:inline-flex;align-items:center;gap:6px;">
                Compare all plans
                <svg id="compareArrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="transition:.2s;"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
        </div>

        <!-- Compare table (hidden by default) -->
        <div id="compareTable" style="display:none;margin-top:32px;overflow-x:auto;">
            <table class="compare-table">
                <thead>
                    <tr>
                        <th style="text-align:left;">Feature</th>
                        <th>Starter</th>
                        <th class="col-featured">Pro</th>
                        <th>Agency</th>
                        <th>Lifetime</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $Y = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#34D399" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>';
                    $N = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4B5563" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';
                    $rows = [
                        ['Websites included',           '1',          '5',   'Unlimited', 'Unlimited'],
                        ['Unlimited pages & posts',     $Y,           $Y,    $Y,          $Y        ],
                        ['FlexiBuilder drag & drop',    $Y,           $Y,    $Y,          $Y        ],
                        ['Built-in SEO & sitemap',      $Y,           $Y,    $Y,          $Y        ],
                        ['Blog, events & deals',        $Y,           $Y,    $Y,          $Y        ],
                        ['Mobile responsive',           $Y,           $Y,    $Y,          $Y        ],
                        ['Premium templates',           $N,           $Y,    $Y,          $Y        ],
                        ['1-click template install',    $N,           $Y,    $Y,          $Y        ],
                        ['Automatic updates',           $N,           $Y,    $Y,          $Y        ],
                        ['Advanced SEO & schema',       $N,           $Y,    $Y,          $Y        ],
                        ['Age gate & popups',           $N,           $Y,    $Y,          $Y        ],
                        ['Priority email support',      $N,           $Y,    $Y,          $Y        ],
                        ['White-label admin panel',     $N,           $N,    $Y,          $Y        ],
                        ['Client management',           $N,           $N,    $Y,          $Y        ],
                        ['Team member accounts',        $N,           $N,    $Y,          $Y        ],
                        ['Dedicated support (4hr SLA)', $N,           $N,    $Y,          $Y        ],
                        ['Reseller license',            $N,           $N,    $N,          $Y        ],
                        ['Lifetime updates',            $N,           $N,    $N,          $Y        ],
                        ['Early access to Pagezy v2',   $N,           $N,    $N,          $Y        ],
                        ['Private roadmap access',      $N,           $N,    $N,          $Y        ],
                        ['Monthly fee',                 'Free',       '$19', '$49',       '$0 (one-time $299)'],
                    ];
                    foreach ($rows as $r): ?>
                    <tr>
                        <td style="text-align:left;"><?= $r[0] ?></td>
                        <td><?= $r[1] ?></td>
                        <td class="col-featured"><?= $r[2] ?></td>
                        <td><?= $r[3] ?></td>
                        <td><?= $r[4] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</section>

<!-- ── FAQ ── -->
<section class="section" style="padding-top:0;">
    <div class="container">
        <div style="max-width:680px;margin:0 auto;">
            <h2 style="font-size:clamp(28px,4vw,40px);font-weight:900;text-align:center;margin-bottom:40px;">
                Frequently asked <span class="grad-text">questions</span>
            </h2>
            <div style="display:flex;flex-direction:column;gap:3px;">
                <?php
                $faqs = [
                    ['What counts as "1 website"?', 'One website = one installation of Pagezy on one domain. Install it on yourdomain.com and it counts as 1 site. A subdomain (blog.yourdomain.com) on the same server also counts as a separate install.'],
                    ['Can I upgrade or downgrade anytime?', 'Yes. You can upgrade to Pro or Agency at any time and the new features activate immediately. Downgrading takes effect at the start of your next billing cycle.'],
                    ['What hosting do I need?', 'Any PHP 8.1+ server with MySQL 5.7+ works — shared cPanel hosting, VPS, DigitalOcean, Hostinger, etc. Pagezy is self-hosted so your data never touches our servers.'],
                    ['Do you offer a money-back guarantee?', '30-day no-questions-asked refund on all paid plans. Lifetime deal included.'],
                    ['What are "premium templates"?', 'Ready-made, professionally designed site layouts you can install in one click and customise with your brand — logo, colours, content. Available in Pro and above.'],
                    ['Can I use Pagezy for client websites?', 'Yes! The Pro plan lets you manage up to 5 client sites. The Agency plan removes all limits and adds white-labelling so your clients see your brand.'],
                ];
                foreach ($faqs as [$q, $a]): ?>
                <div class="faq-item">
                    <button class="faq-q" onclick="this.nextElementSibling.classList.toggle('open');this.querySelector('.faq-icon').style.transform=this.nextElementSibling.classList.contains('open')?'rotate(180deg)':'rotate(0deg)'">
                        <?= htmlspecialchars($q) ?>
                        <svg class="faq-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;transition:.2s;"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div class="faq-a">
                        <?= htmlspecialchars($a) ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- ── CTA ── -->
<section class="cta-section">
    <div class="hero-bg">
        <div class="hero-bg-blob hero-bg-blob-1"></div>
        <div class="hero-bg-blob hero-bg-blob-2"></div>
    </div>
    <div class="container" style="position:relative;z-index:1;">
        <h2>Still unsure? <span class="grad-text">Start free.</span></h2>
        <p>No credit card. No commitment. Download and install in 5 minutes.</p>
        <div class="hero-actions">
            <a href="/download.php" class="btn btn-primary btn-lg">Download Pagezy free</a>
            <a href="/contact.php" class="btn btn-outline btn-lg">Talk to us</a>
        </div>
    </div>
</section>

<!-- pv2 styles are in assets/css/style.css -->

<script>
function setBilling(mode) {
    const isYearly = mode === 'yearly';
    document.getElementById('btnMonthly').classList.toggle('active', !isYearly);
    document.getElementById('btnYearly').classList.toggle('active', isYearly);

    document.querySelectorAll('.pv2-amount[data-monthly]').forEach(el => {
        el.textContent = '$' + (isYearly ? el.dataset.yearly : el.dataset.monthly);
    });
    document.querySelectorAll('.pv2-yearly-note').forEach(el => el.style.display = isYearly ? 'block' : 'none');
    document.querySelectorAll('.pv2-monthly-note').forEach(el => el.style.display = isYearly ? 'none' : 'block');
}

function toggleCompare() {
    const el = document.getElementById('compareTable');
    const arrow = document.getElementById('compareArrow');
    const open = el.style.display !== 'none';
    el.style.display = open ? 'none' : 'block';
    arrow.style.transform = open ? 'rotate(0deg)' : 'rotate(180deg)';
}
</script>

<?php include 'includes/footer.php'; ?>
