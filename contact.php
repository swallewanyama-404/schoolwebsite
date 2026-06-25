<?php
// ============================================================
//  contact.php — Contact Page
//  Open at: http://localhost/school-website/contact.php
// ============================================================
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Contact Us';
$schoolName = getSetting($pdo, 'school_name');

// Pick up flash messages set by process_contact.php
$success = $_SESSION['success'] ?? '';
$errors  = $_SESSION['errors']  ?? [];
unset($_SESSION['success'], $_SESSION['errors']);

$mapEmbed = getSetting($pdo, 'google_maps_embed');
?>
<?php require_once 'includes/header.php'; ?>

<div class="page-hero">
    <div class="container">
        <h1>Contact Us</h1>
        <p>We'd love to hear from you</p>
        <div class="breadcrumb"><a href="/">Home</a> &rsaquo; Contact</div>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="contact-grid">

            <!-- School details + map -->
            <div>
                <h2 style="color:var(--navy);margin-bottom:1.25rem">Get In Touch</h2>

                <div class="contact-detail">
                    <span class="contact-icon">📍</span>
                    <div>
                        <strong>Address</strong><br>
                        <span style="color:var(--muted)"><?= htmlspecialchars(getSetting($pdo, 'school_address')) ?></span>
                    </div>
                </div>

                <div class="contact-detail">
                    <span class="contact-icon">📞</span>
                    <div>
                        <strong>Phone</strong><br>
                        <a href="tel:<?= htmlspecialchars(getSetting($pdo,'school_phone')) ?>" style="color:var(--muted)">
                            <?= htmlspecialchars(getSetting($pdo, 'school_phone')) ?>
                        </a>
                    </div>
                </div>

                <div class="contact-detail">
                    <span class="contact-icon">✉</span>
                    <div>
                        <strong>Email</strong><br>
                        <a href="mailto:<?= htmlspecialchars(getSetting($pdo,'school_email')) ?>" style="color:var(--muted)">
                            <?= htmlspecialchars(getSetting($pdo, 'school_email')) ?>
                        </a>
                    </div>
                </div>

                <!-- Google Maps embed (added in admin → school_info) -->
                <?php if ($mapEmbed): ?>
                <div class="map-embed">
                    <?= $mapEmbed ?>
                </div>
                <?php else: ?>
                <div style="background:var(--off);border-radius:var(--radius);padding:2rem;text-align:center;margin-top:1.5rem;color:var(--muted)">
                    <p>Map will appear here once added in the admin panel.</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Contact form -->
            <div>
                <h2 style="color:var(--navy);margin-bottom:1.25rem">Send a Message</h2>

                <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <?php if ($errors): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $e): ?>
                    <p><?= htmlspecialchars($e) ?></p>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <form action="process_contact.php" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Your Name *</label>
                            <input type="text" id="name" name="name"
                                   placeholder="Full name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email"
                                   placeholder="your@email.com" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="subject">Subject *</label>
                        <input type="text" id="subject" name="subject"
                               placeholder="What is your message about?" required>
                    </div>

                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message"
                                  placeholder="Type your message here..."
                                  maxlength="2000" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-blue">Send Message</button>
                </form>
            </div>

        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
