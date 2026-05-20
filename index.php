<?php
$page_title = 'Home | CCS Sit-In Monitoring System';
require_once 'includes/header.php';
?>

<main class="page-main">
    <section class="hero-section">
        <h1>Welcome to the CCS Sit-In Monitoring System</h1>
        <p>
            Track laboratory sit-ins, manage sessions, and stay productive with a fast and secure platform built for CCS students.
        </p>
        <div class="hero-actions">
            <a href="<?php echo app_url('register.php'); ?>" class="btn btn-primary">Get Started</a>
            <a href="<?php echo app_url('login.php'); ?>" class="btn btn-outline">Login</a>
        </div>
    </section>

    <section id="community" class="info-section">
        <h2>Community</h2>
        <p>Connect with fellow CCS students, view announcements, and stay updated on lab schedules and sit-in policies.</p>
    </section>

    <section id="about" class="info-section">
        <h2>About Us</h2>
        <p>
            The College of Computer Studies Sit-In Monitoring System helps students register, log in, and monitor their laboratory attendance efficiently.
        </p>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>
