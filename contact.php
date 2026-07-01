<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

$success     = '';
$school_name = getSetting($pdo, 'school_name');
$phone       = getSetting($pdo, 'school_phone');
$email       = getSetting($pdo, 'school_email');
$address     = getSetting($pdo, 'school_address');
$faqs        = getFaqsByCategory($pdo, 'general');

if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}
?>

<section class="page-hero">
    <div class="container page-hero-content">
        <h1>Contact Us</h1>
        <p>We'd love to hear from you &mdash; reach out with any questions</p>
        <ul class="breadcrumb"><li><a href="<?php echo BASE_URL; ?>index.php">Home</a></li><li>Contact</li></ul>
    </div>
</section>

<main id="main-content">
    <section class="section">
        <div class="container">

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="grid-2" style="grid-template-columns: 1.3fr 1fr; align-items:start;">

                <!-- Contact Info + Form -->
                <div>
                    <div class="section-header">
                        <span class="section-label">Reach Out</span>
                        <h2>Get In Touch</h2>
                        <div class="divider"></div>
                    </div>

                    <div class="contact-info-item">
                        <div class="contact-icon">📍</div>
                        <div><h4>Address</h4><p><?php echo htmlspecialchars($address ?: 'Plot 45, Ntinda Road, Kampala, Uganda'); ?></p></div>
                    </div>
                    <div class="contact-info-item">
                        <div class="contact-icon">📞</div>
                        <div><h4>Phone</h4><p><?php echo htmlspecialchars($phone ?: '+256 700 123 456'); ?></p></div>
                    </div>
                    <div class="contact-info-item">
                        <div class="contact-icon">✉️</div>
                        <div><h4>Email</h4><p><?php echo htmlspecialchars($email ?: 'info@stmarys.ac.ug'); ?></p></div>
                    </div>

                    <div class="map-placeholder">
                        🗺️
                        <p>Map location coming soon</p>
                    </div>
                </div>

                <!-- Message Form -->
                <div class="form-card">
                    <h2 style="margin-bottom:1.5rem;font-size:1.2rem;">Send Us a Message</h2>
                    <form action="process_contact.php" method="POST">
                        <div class="form-group">
                            <label>Full Name *</label>
                            <input class="form-control" type="text" name="name" placeholder="Your full name" required>
                        </div>
                        <div class="form-group">
                            <label>Email Address *</label>
                            <input class="form-control" type="email" name="email" placeholder="you@example.com" required>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input class="form-control" type="tel" name="phone" placeholder="+256 7XX XXX XXX">
                        </div>
                        <div class="form-group">
                            <label>Subject *</label>
                            <input class="form-control" type="text" name="subject" placeholder="What is this regarding?" required>
                        </div>
                        <div class="form-group">
                            <label>Message *</label>
                            <textarea class="form-control" name="message" rows="5" placeholder="Type your message here..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width:100%;">Send Message →</button>
                    </form>
                </div>
            </div>

            <!-- FAQs -->
            <?php if ($faqs): ?>
            <div style="margin-top:4rem;max-width:800px;">
                <div class="section-header center">
                    <span class="section-label">Common Questions</span>
                    <h2>Frequently Asked Questions</h2>
                    <div class="divider" style="margin-left:auto;margin-right:auto;"></div>
                </div>
                <?php foreach ($faqs as $faq): ?>
                <details class="faq">
                    <summary><?php echo htmlspecialchars($faq['question']); ?></summary>
                    <div class="faq-body"><?php echo htmlspecialchars($faq['answer']); ?></div>
                </details>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>
