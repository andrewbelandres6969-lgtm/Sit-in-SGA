<?php
$page_title = 'Home | CCS Sit-In Monitoring System';
require_once 'includes/header.php';

$leaders = get_leaderboard($conn, 5);
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

    <section class="home-leaderboard-section">
        <div class="section-kicker">Leaderboard</div>
        <div class="home-section-heading">
            <div>
                <h2>Top Sit-In Students</h2>
                <p>Students with the most completed sit-in sessions appear here.</p>
            </div>
            <a href="<?php echo app_url('login.php'); ?>" class="section-link">View More</a>
        </div>

        <div class="leaderboard-preview">
            <?php if (empty($leaders)): ?>
                <div class="leaderboard-empty">No leaderboard data yet.</div>
            <?php else: ?>
                <?php $rank = 1; foreach ($leaders as $row): ?>
                    <div class="leader-card rank-card-<?php echo $rank; ?>">
                        <div class="leader-rank"><?php echo $rank; ?></div>
                        <div class="leader-info">
                            <strong><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></strong>
                            <span><?php echo htmlspecialchars($row['course'] ?? 'Not set'); ?></span>
                        </div>
                        <div class="leader-score">
                            <strong><?php echo (int) $row['completed_sessions']; ?></strong>
                            <span>Sessions</span>
                        </div>
                    </div>
                <?php $rank++; endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <section id="community" class="info-section">
        <div class="section-kicker">Community</div>
        <h2>Community</h2>
        <div class="info-card">
            <p>
                Connect with fellow CCS students, collaborate on programming projects, and stay updated with the latest laboratory schedules, announcements, and sit-in activities.
                The CCS Sit-In Monitoring System helps build a productive and supportive learning environment where students can improve their technical skills, share ideas, and participate in academic activities efficiently.
            </p>
        </div>
    </section>

    <section id="about" class="info-section">
        <div class="section-kicker">About Us</div>
        <h2>About Us</h2>
        <div class="info-card info-card-grid">
            <p>
                The College of Computer Studies Sit-In Monitoring System is designed to help students manage their laboratory attendance, reservations, and sessions in a fast, secure, and organized way.
                Our goal is to provide a reliable platform that improves laboratory monitoring, simplifies student transactions, and enhances the overall learning experience inside the CCS laboratories.
            </p>

            <p>
                This system allows students to register online, reserve computer stations, monitor available sessions, and receive important announcements from administrators.
                By using modern web technologies, the platform ensures accessibility, convenience, and efficiency for both students and faculty members.
            </p>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>
