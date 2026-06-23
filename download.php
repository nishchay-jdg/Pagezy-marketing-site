<?php
require_once 'config.php';

$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $city     = trim($_POST['city']     ?? '');
    $company  = trim($_POST['company']  ?? '');
    $use_case = trim($_POST['use_case'] ?? '');

    if (!$name)              $errors['name']  = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Valid email required.';
    if (!$city)              $errors['city']  = 'City is required.';

    if (empty($errors)) {
        try {
            ensureTable();
            $stmt = db()->prepare("INSERT INTO pagezy_downloads
                (name, email, city, company, use_case, ip_address)
                VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $name, $email, $city, $company, $use_case,
                $_SERVER['REMOTE_ADDR'] ?? ''
            ]);
            notifyDownload([
                'name'     => $name,
                'email'    => $email,
                'city'     => $city,
                'company'  => $company,
                'use_case' => $use_case,
                'ip'       => $_SERVER['REMOTE_ADDR'] ?? '',
            ]);
            // Redirect to thank-you page which triggers download
            header('Location: /thank-you.php?ref=' . urlencode($email));
            exit;
        } catch (Exception $e) {
            // Log silently, still let them download
            header('Location: /thank-you.php');
            exit;
        }
    }
}

$pageTitle = 'Download Pagezy — Free CMS';
include 'includes/head.php';
include 'includes/nav.php';
?>

<section class="download-hero">
    <div class="hero-bg">
        <div class="hero-bg-blob hero-bg-blob-1"></div>
        <div class="hero-bg-blob hero-bg-blob-2"></div>
        <div class="hero-bg-grid"></div>
    </div>
    <div class="container" style="position:relative;z-index:1;">
        <div style="max-width:600px;margin:0 auto;text-align:center;margin-bottom:48px;">
            <div class="hero-badge" style="justify-content:center;">
                <span class="hero-badge-dot"></span>
                Free during launch — no credit card
            </div>
            <h1 style="font-size:clamp(36px,5vw,56px);font-weight:900;margin-bottom:16px;">
                Download <span class="grad-text">Pagezy</span> free
            </h1>
            <p style="font-size:17px;color:var(--muted);line-height:1.65;">
                Enter your details below and get instant access to the Pagezy CMS ZIP — ready to install on any PHP server.
            </p>
        </div>

        <div class="download-form-wrap fade-up">
            <?php if (!empty($errors)): ?>
            <div style="background:rgba(248,113,113,.1);border:1px solid rgba(248,113,113,.3);border-radius:10px;padding:14px 18px;margin-bottom:24px;font-size:14px;color:#FCA5A5;">
                Please fix the errors below.
            </div>
            <?php endif; ?>

            <h2 style="font-size:22px;font-weight:800;margin-bottom:6px;">Get your free copy</h2>
            <p style="font-size:14px;color:var(--muted);margin-bottom:28px;">Takes 30 seconds. No spam, ever.</p>

            <form method="POST" action="/download.php">
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
                        <label for="city">City *</label>
                        <input type="text" id="city" name="city" placeholder="New York"
                               value="<?= htmlspecialchars($_POST['city'] ?? '') ?>" required>
                        <?php if (!empty($errors['city'])): ?>
                        <div class="form-error"><?= $errors['city'] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="company">Company / website</label>
                        <input type="text" id="company" name="company" placeholder="Acme Inc."
                               value="<?= htmlspecialchars($_POST['company'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="use_case">How will you use Pagezy?</label>
                    <select id="use_case" name="use_case">
                        <option value="">Select one...</option>
                        <option value="agency"    <?= ($_POST['use_case']??'')==='agency'    ? 'selected':'' ?>>Agency — building client sites</option>
                        <option value="freelancer"<?= ($_POST['use_case']??'')==='freelancer'? 'selected':'' ?>>Freelancer — personal projects</option>
                        <option value="business"  <?= ($_POST['use_case']??'')==='business'  ? 'selected':'' ?>>Business — company website</option>
                        <option value="developer" <?= ($_POST['use_case']??'')==='developer' ? 'selected':'' ?>>Developer — testing / evaluating</option>
                        <option value="other"     <?= ($_POST['use_case']??'')==='other'     ? 'selected':'' ?>>Other</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:15px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Download Pagezy Free
                </button>
                <p class="form-terms">By downloading you agree to our <a href="/terms.php">Terms of Service</a> and <a href="/privacy.php">Privacy Policy</a>.</p>
            </form>
        </div>

        <!-- Trust signals -->
        <div style="max-width:600px;margin:40px auto 0;display:flex;justify-content:center;gap:32px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--muted);">
                <span style="color:#34D399;">✓</span> No credit card
            </div>
            <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--muted);">
                <span style="color:#34D399;">✓</span> Self-hosted
            </div>
            <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--muted);">
                <span style="color:#34D399;">✓</span> Free forever during launch
            </div>
            <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--muted);">
                <span style="color:#34D399;">✓</span> Your data stays yours
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
