<?php
$page_title = 'Home | Student Dashboard';
require_once __DIR__ . '/includes/header.php';

$user = get_logged_in_student($conn);
$announcements = get_announcements($conn, 15);
$initials = strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1));
$full_name = htmlspecialchars($user['first_name'] . ' ' . $user['last_name']);
$address = !empty($user['address']) ? htmlspecialchars($user['address']) : 'Not set';
?>

<main class="student-wrap">
    <div class="student-home-grid">
        <!-- Student Information -->
        <section class="student-panel">
            <div class="student-panel-header">Student Information</div>
            <div class="profile-photo-wrap">
                <?php if (!empty($user['photo'])): ?>
                    <img class="profile-avatar" src="<?php echo htmlspecialchars(asset_url($user['photo'])); ?>" alt="Profile">
                <?php else: ?>
                    <div class="profile-avatar"><?php echo htmlspecialchars($initials); ?></div>
                <?php endif; ?>
            </div>
            <ul class="info-list">
                <li>
                    <span class="info-icon">👤</span>
                    <span>
                        <span class="info-label">Name</span>
                        <?php echo $full_name; ?>
                    </span>
                </li>
                <li>
                    <span class="info-icon">🎓</span>
                    <span>
                        <span class="info-label">Course</span>
                        <?php echo htmlspecialchars($user['course'] ?? 'Not set'); ?>
                    </span>
                </li>
                <li>
                    <span class="info-icon">📅</span>
                    <span>
                        <span class="info-label">Year</span>
                        <?php echo htmlspecialchars(year_label($user['course_level'])); ?>
                    </span>
                </li>
                <li>
                    <span class="info-icon">✉️</span>
                    <span>
                        <span class="info-label">Email</span>
                        <?php echo htmlspecialchars($user['email'] ?? 'Not set'); ?>
                    </span>
                </li>
                <li>
                    <span class="info-icon">📍</span>
                    <span>
                        <span class="info-label">Address</span>
                        <?php echo $address; ?>
                    </span>
                </li>
            </ul>
            <div class="sessions-box">
                🕐 Sessions: <strong><?php echo (int) $user['sitin_remaining']; ?></strong> Sessions
            </div>
        </section>

        <!-- Announcements -->
        <section class="student-panel">
            <div class="student-panel-header">📢 Announcement</div>
            <div class="announce-panel-body">
                <?php if (empty($announcements)): ?>
                    <p class="empty-note">No announcements yet.</p>
                <?php else: ?>
                    <?php foreach ($announcements as $item): ?>
                        <article class="announce-card">
                            <div class="announce-card-meta">
                                <?php echo htmlspecialchars($item['author_name']); ?>
                                | <?php echo date('M d, Y', strtotime($item['created_at'])); ?>
                            </div>
                            <div class="announce-card-text"><?php echo htmlspecialchars($item['content']); ?></div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- Rules and Regulations -->
        <section class="student-panel">
            <div class="student-panel-header">Rules and Regulations</div>
            <div class="rules-body">
                <div class="rules-title-block">
                    <h3>University of Cebu</h3>
                    <h3>COLLEGE OF INFORMATION &amp; COMPUTER STUDIES</h3>
                    <h3>LABORATORY RULES AND REGULATIONS</h3>
                </div>
                <p class="rules-intro">
                    To maintain a safe and productive learning environment, all students must follow these laboratory rules.
                </p>
                <div class="rule-item">
                    <strong>1. Silence and Decorum</strong>
                    Maintain silence and proper decorum inside the laboratory at all times. Respect other students who are working.
                </div>
                <div class="rule-item">
                    <strong>2. No Games</strong>
                    Playing games, watching unrelated videos, or using the computer for non-academic purposes is strictly prohibited.
                </div>
                <div class="rule-item">
                    <strong>3. Internet Usage</strong>
                    Internet access is for academic purposes only. Downloading unauthorized software or visiting inappropriate websites is not allowed.
                </div>
                <div class="rule-item">
                    <strong>4. Equipment Care</strong>
                    Handle all computer equipment with care. Report any damage or malfunction to the laboratory instructor immediately.
                </div>
                <div class="rule-item">
                    <strong>5. Cleanliness</strong>
                    Keep your workstation clean. No food, drinks, or pets are allowed inside the laboratory.
                </div>
            </div>
        </section>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
