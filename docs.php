<?php
$pageTitle       = 'Documentation — Pagezy';
$pageDescription = 'Pagezy documentation — installation guide, FlexiBuilder, SEO tools, blog, events, and more.';
include 'includes/head.php';
include 'includes/nav.php';
?>

<section class="legal-hero">
    <div class="container">
        <div class="legal-hero-inner">
            <div class="section-label">Docs</div>
            <h1>Documentation</h1>
            <p class="legal-meta">Everything you need to install, configure, and build with Pagezy.</p>
        </div>
    </div>
</section>

<section class="legal-body">
    <div class="container">
        <div class="legal-layout">

            <!-- Sidebar -->
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
                    <p>Extract the ZIP and upload the contents to your server via FTP or the cPanel File Manager. For a root installation place files in <code>public_html/</code>. For a subdirectory, create a folder first (e.g. <code>public_html/cms/</code>).</p>

                    <h3>Step 4 — Run the web installer</h3>
                    <p>Visit <code>yourdomain.com/install</code> in your browser. The installer will guide you through:</p>
                    <ul>
                        <li>Database connection details</li>
                        <li>Site name, URL, and admin email</li>
                        <li>Creating your admin username and password</li>
                    </ul>
                    <p>The installer automatically creates all required database tables. Once complete, the <code>/install</code> route is locked and inaccessible.</p>

                    <h3>Step 5 — Log in</h3>
                    <p>Go to <code>yourdomain.com/admin</code> and sign in with the credentials you set during installation.</p>
                </section>

                <section id="admin">
                    <h2>Admin Panel</h2>
                    <p>The Pagezy admin panel is accessible at <code>/admin</code>. The dashboard gives you an overview of pages, posts, media, and recent activity.</p>
                    <p>The left sidebar contains all modules:</p>
                    <ul>
                        <li><strong>Pages</strong> — create and manage website pages</li>
                        <li><strong>Posts</strong> — blog posts with categories and tags</li>
                        <li><strong>Events</strong> — event listings with dates and slugs</li>
                        <li><strong>Media</strong> — image and file library</li>
                        <li><strong>Menus</strong> — build and assign navigation menus</li>
                        <li><strong>Global Elements</strong> — reusable content blocks (headers, footers, CTAs)</li>
                        <li><strong>Forms</strong> — contact and lead forms with submission inbox</li>
                        <li><strong>SEO</strong> — sitemaps, redirections, meta defaults</li>
                        <li><strong>Users</strong> — manage team members and roles</li>
                        <li><strong>Settings</strong> — site name, branding, email, timezone</li>
                    </ul>
                </section>

                <section id="flexibuilder">
                    <h2>FlexiBuilder</h2>
                    <p>FlexiBuilder is Pagezy's drag-and-drop visual page editor. Open any page and click <strong>Edit with FlexiBuilder</strong> to launch it.</p>
                    <h3>Adding blocks</h3>
                    <p>Click the <strong>+</strong> button to browse and insert pre-built content blocks — hero sections, text columns, image galleries, CTAs, testimonials, and more.</p>
                    <h3>Editing content</h3>
                    <p>Click any element on the canvas to select it. Use the right panel to change text, colors, fonts, padding, and visibility per device.</p>
                    <h3>Mobile preview</h3>
                    <p>Toggle between Desktop, Tablet, and Mobile views using the toolbar at the top. All layouts are responsive by default.</p>
                    <h3>Publishing</h3>
                    <p>Click <strong>Publish</strong> to save and make the page live immediately. Use <strong>Save draft</strong> to save without publishing.</p>
                </section>

                <section id="pages">
                    <h2>Pages</h2>
                    <p>Go to <strong>Pages → Add New</strong> to create a page. Set the:</p>
                    <ul>
                        <li><strong>Title</strong> — shown in the browser tab and navigation</li>
                        <li><strong>Slug</strong> — the URL path (e.g. <code>/about</code>)</li>
                        <li><strong>Template</strong> — choose a layout (default, full-width, landing)</li>
                        <li><strong>Status</strong> — Draft or Published</li>
                    </ul>
                    <p>Once saved, click <strong>Edit with FlexiBuilder</strong> to design the page content visually.</p>
                </section>

                <section id="blog">
                    <h2>Blog &amp; Posts</h2>
                    <p>Pagezy includes a full blog module. Go to <strong>Posts → Add New</strong> to write a post.</p>
                    <ul>
                        <li>Assign <strong>categories</strong> and <strong>tags</strong> for organization and SEO</li>
                        <li>Set a <strong>featured image</strong> for Open Graph and listing pages</li>
                        <li>Control the <strong>publish date</strong> for scheduled posts</li>
                        <li>Blog listing pages are auto-generated at your configured blog URL</li>
                    </ul>
                </section>

                <section id="seo">
                    <h2>SEO Tools</h2>
                    <p>Every page and post has a dedicated SEO panel:</p>
                    <ul>
                        <li><strong>Meta title &amp; description</strong> — per-page control</li>
                        <li><strong>Open Graph</strong> — title, description, image for social sharing</li>
                        <li><strong>Schema markup</strong> — structured data auto-generated per content type</li>
                        <li><strong>Canonical URL</strong> — set manually or auto-resolved</li>
                        <li><strong>XML Sitemap</strong> — auto-generated and updated on publish</li>
                        <li><strong>Redirections</strong> — manage 301/302 redirects from the admin panel</li>
                        <li><strong>Robots meta</strong> — noindex/nofollow per page</li>
                    </ul>
                </section>

                <section id="media">
                    <h2>Media Library</h2>
                    <p>Upload images, PDFs, and files via <strong>Media → Upload</strong>. Pagezy auto-generates thumbnails in multiple sizes for responsive images.</p>
                    <p>Supported formats: JPG, PNG, GIF, WebP, SVG, PDF. Maximum upload size is determined by your server's <code>upload_max_filesize</code> PHP setting.</p>
                </section>

                <section id="users">
                    <h2>Users &amp; Roles</h2>
                    <p>Go to <strong>Users → Add New</strong> to invite team members. Available roles:</p>
                    <ul>
                        <li><strong>Super Admin</strong> — full access including settings and users</li>
                        <li><strong>Admin</strong> — full access except user management</li>
                        <li><strong>Editor</strong> — can create, edit, and publish all content</li>
                        <li><strong>Author</strong> — can create and edit their own posts only</li>
                    </ul>
                </section>

                <section id="settings">
                    <h2>Settings</h2>
                    <p>Configure your site under <strong>Settings</strong>:</p>
                    <ul>
                        <li><strong>General</strong> — site name, tagline, timezone, date format</li>
                        <li><strong>Branding</strong> — logo, favicon, primary color, custom fonts</li>
                        <li><strong>Email</strong> — SMTP configuration for contact forms and notifications</li>
                        <li><strong>Permalinks</strong> — blog base URL, post slug format</li>
                        <li><strong>Backup</strong> — database and file backup management</li>
                    </ul>
                </section>

                <section id="updates">
                    <h2>Updates</h2>
                    <p>When a new version of Pagezy is available, a notice appears in the admin dashboard. To update:</p>
                    <ul>
                        <li>Download the latest ZIP from <a href="/download.php">pagezy.io/download</a></li>
                        <li>Back up your database and files first</li>
                        <li>Replace the core files via FTP (do not overwrite your <code>/uploads</code> folder or custom theme files)</li>
                        <li>Visit <code>/admin</code> — any database migrations run automatically</li>
                    </ul>
                </section>

                <section id="faq">
                    <h2>FAQ</h2>

                    <h3>Can I use Pagezy on shared hosting?</h3>
                    <p>Yes. Pagezy is specifically designed for shared cPanel hosting. No SSH or root access required.</p>

                    <h3>Does Pagezy support multiple languages?</h3>
                    <p>The admin panel is in English. Multi-language frontend support is on the roadmap.</p>

                    <h3>Can I use a custom domain?</h3>
                    <p>Yes. Point your domain to your hosting server and set the site URL in <strong>Settings → General</strong>.</p>

                    <h3>Is there a file size limit for uploads?</h3>
                    <p>The limit is set by your PHP configuration (<code>upload_max_filesize</code> and <code>post_max_size</code>). Most cPanel hosts default to 50–256 MB.</p>

                    <h3>Where do I get support?</h3>
                    <p>Use the <a href="/contact.php">contact form</a> or email <a href="mailto:hello@pagezy.io">hello@pagezy.io</a>. Pro and Agency plan users receive priority support.</p>
                </section>

            </article>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
