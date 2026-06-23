<?php
require_once 'config.php';

$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $phone   = trim($_POST['phone']   ?? '');
    $company = trim($_POST['company'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name)                                       $errors['name']    = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))   $errors['email']   = 'Valid email required.';
    if (!$message)                                    $errors['message'] = 'Message is required.';

    if (empty($errors)) {
        try {
            ensureContactTable();
            $stmt = db()->prepare("INSERT INTO pagezy_contacts
                (name, email, phone, company, subject, message, ip_address)
                VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $name, $email, $phone, $company, $subject, $message,
                $_SERVER['REMOTE_ADDR'] ?? ''
            ]);
            $success = true;
            notifyContact([
                'name'    => $name,
                'email'   => $email,
                'phone'   => $phone,
                'company' => $company,
                'subject' => $subject,
                'message' => $message,
                'ip'      => $_SERVER['REMOTE_ADDR'] ?? '',
            ]);
        } catch (Exception $e) {
            $errors['_'] = 'Something went wrong. Please try again or email us directly.';
        }
    }
}

$pageTitle       = 'Contact — Pagezy CMS';
$pageDescription = 'Get in touch with the Pagezy team. We\'d love to hear from you.';
include 'includes/head.php';
include 'includes/nav.php';
?>

<section class="download-hero" style="padding-bottom:80px;">
    <div class="hero-bg">
        <div class="hero-bg-blob hero-bg-blob-1"></div>
        <div class="hero-bg-blob hero-bg-blob-2"></div>
        <div class="hero-bg-grid"></div>
    </div>
    <div class="container" style="position:relative;z-index:1;">

        <div style="max-width:600px;margin:0 auto;text-align:center;margin-bottom:48px;">
            <div class="hero-badge" style="justify-content:center;">
                <span class="hero-badge-dot"></span>
                We reply within 24 hours
            </div>
            <h1 style="font-size:clamp(36px,5vw,56px);font-weight:900;margin-bottom:16px;">
                Get in <span class="grad-text">touch</span>
            </h1>
            <p style="font-size:17px;color:var(--muted);line-height:1.65;">
                Need help getting started, want a custom build, or just have a question? We're here for you.
            </p>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:48px;max-width:960px;margin:0 auto;align-items:start;">

            <!-- Left: form -->
            <div class="download-form-wrap fade-up" style="margin:0;">

                <?php if ($success): ?>
                <div style="text-align:center;padding:40px 20px;">
                    <div style="width:64px;height:64px;border-radius:50%;background:rgba(52,211,153,.1);border:1px solid rgba(52,211,153,.3);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#34D399" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <h2 style="font-size:22px;font-weight:800;margin-bottom:8px;">Message sent!</h2>
                    <p style="font-size:15px;color:var(--muted);">Thanks <?= htmlspecialchars($name) ?>! We'll get back to you at <strong style="color:var(--text);"><?= htmlspecialchars($email) ?></strong> within 24 hours.</p>
                    <a href="/contact.php" style="display:inline-block;margin-top:24px;font-size:14px;color:#A78BFA;text-decoration:none;">Send another message →</a>
                </div>

                <?php else: ?>

                <?php if (!empty($errors['_'])): ?>
                <div style="background:rgba(248,113,113,.1);border:1px solid rgba(248,113,113,.3);border-radius:10px;padding:14px 18px;margin-bottom:24px;font-size:14px;color:#FCA5A5;">
                    <?= htmlspecialchars($errors['_']) ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($errors) && empty($errors['_'])): ?>
                <div style="background:rgba(248,113,113,.1);border:1px solid rgba(248,113,113,.3);border-radius:10px;padding:14px 18px;margin-bottom:24px;font-size:14px;color:#FCA5A5;">
                    Please fix the errors below.
                </div>
                <?php endif; ?>

                <h2 style="font-size:22px;font-weight:800;margin-bottom:6px;">Send us a message</h2>
                <p style="font-size:14px;color:var(--muted);margin-bottom:28px;">No spam, ever. Your info is safe with us.</p>

                <form method="POST" action="/contact.php">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Full name *</label>
                            <input type="text" id="name" name="name" placeholder="John Smith"
                                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                            <?php if (!empty($errors['name'])): ?>
                            <div class="form-error"><?= $errors['name'] ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="email">Email address *</label>
                            <input type="email" id="email" name="email" placeholder="you@example.com"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                            <?php if (!empty($errors['email'])): ?>
                            <div class="form-error"><?= $errors['email'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Phone number</label>
                            <input type="tel" id="phone" name="phone" placeholder="+1 555 000 0000"
                                   value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="company">Company / website</label>
                            <input type="text" id="company" name="company" placeholder="Acme Inc."
                                   value="<?= htmlspecialchars($_POST['company'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="subject">What can we help with?</label>
                        <select id="subject" name="subject">
                            <option value="">Select one...</option>
                            <option value="general"    <?= ($_POST['subject']??'')==='general'     ? 'selected':'' ?>>General question</option>
                            <option value="custom"     <?= ($_POST['subject']??'')==='custom'      ? 'selected':'' ?>>Custom website build</option>
                            <option value="pro"        <?= ($_POST['subject']??'')==='pro'         ? 'selected':'' ?>>Pro / Agency plan</option>
                            <option value="support"    <?= ($_POST['subject']??'')==='support'     ? 'selected':'' ?>>Technical support</option>
                            <option value="enterprise" <?= ($_POST['subject']??'')==='enterprise'  ? 'selected':'' ?>>Enterprise / reseller</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="message">Your message *</label>
                        <textarea id="message" name="message" rows="5" placeholder="Tell us about your project or question..." required
                            style="resize:vertical;"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                        <?php if (!empty($errors['message'])): ?>
                        <div class="form-error"><?= $errors['message'] ?></div>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:15px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        Send message
                    </button>
                    <p class="form-terms">By submitting you agree to our <a href="#">Privacy Policy</a>.</p>
                </form>

                <?php endif; ?>
            </div>

            <!-- Right: contact info -->
            <div class="fade-up" style="display:flex;flex-direction:column;gap:24px;padding-top:8px;">
                <div class="contact-info-card">
                    <div class="contact-info-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </div>
                    <div>
                        <div class="contact-info-label">Email us</div>
                        <div class="contact-info-value">hello@pagezy.io</div>
                        <div class="contact-info-note">We reply within 24 hours</div>
                    </div>
                </div>

                <div class="contact-info-card">
                    <div class="contact-info-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div>
                        <div class="contact-info-label">Response time</div>
                        <div class="contact-info-value">Within 24 hours</div>
                        <div class="contact-info-note">Mon–Fri, 9am–6pm IST</div>
                    </div>
                </div>

                <div class="contact-info-card">
                    <div class="contact-info-icon" style="background:rgba(99,102,241,.1);border-color:rgba(99,102,241,.25);">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#818CF8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    </div>
                    <div>
                        <div class="contact-info-label">Need a custom website?</div>
                        <div class="contact-info-value" style="font-size:14px;font-weight:500;color:var(--muted);line-height:1.5;margin-top:4px;">Our team can design and build your site from scratch. Starting at an affordable price — just tell us what you need.</div>
                    </div>
                </div>

                <div style="background:var(--dark-card);border:1px solid var(--dark-border);border-radius:16px;padding:24px;">
                    <div style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:16px;">Quick answers</div>
                    <div style="display:flex;flex-direction:column;gap:12px;">
                        <a href="/pricing.php" style="display:flex;align-items:center;gap:10px;font-size:14px;color:var(--text);text-decoration:none;padding:10px 14px;border-radius:10px;border:1px solid var(--dark-border);transition:.2s;" onmouseover="this.style.borderColor='rgba(139,92,246,.4)'" onmouseout="this.style.borderColor='var(--dark-border)'">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                            View pricing & plans
                        </a>
                        <a href="/download.php" style="display:flex;align-items:center;gap:10px;font-size:14px;color:var(--text);text-decoration:none;padding:10px 14px;border-radius:10px;border:1px solid var(--dark-border);transition:.2s;" onmouseover="this.style.borderColor='rgba(139,92,246,.4)'" onmouseout="this.style.borderColor='var(--dark-border)'">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            Download Pagezy free
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<style>
.contact-info-card {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    background: var(--dark-card);
    border: 1px solid var(--dark-border);
    border-radius: 16px;
    padding: 20px;
}
.contact-info-icon {
    width: 44px;
    height: 44px;
    flex-shrink: 0;
    border-radius: 12px;
    background: rgba(139,92,246,.1);
    border: 1px solid rgba(139,92,246,.2);
    display: flex;
    align-items: center;
    justify-content: center;
}
.contact-info-icon svg { width:20px;height:20px;stroke:#A78BFA; }
.contact-info-label { font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:4px; }
.contact-info-value { font-size:15px;font-weight:700;color:var(--text); }
.contact-info-note  { font-size:13px;color:var(--muted);margin-top:2px; }

@media(max-width:768px){
    .contact-info-card { flex-direction:column;gap:12px; }
    section > .container > div[style*="grid-template-columns"] {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
