<?php
$page_title = 'Edit Profile | Student';
require_once __DIR__ . '/includes/header.php';

$user = get_logged_in_student($conn);
?>

<main class="student-wrap">
    <section class="student-page-panel">
        <div class="student-panel-header">Edit Profile</div>
        <div class="student-page-body">
            <h2>Update Your Information</h2>
            <p class="page-desc">Keep your profile details up to date.</p>

            <form class="student-form student-profile-form" method="POST" enctype="multipart/form-data" action="<?php echo app_url('student/update_profile.php'); ?>">
                <div class="student-profile-photo-field">
                    <label for="photo">Profile Picture</label>
                    <input type="file" id="photo" name="photo" accept="image/png, image/jpeg, image/gif">
                    <?php if (!empty($user['photo'])): ?>
                        <p class="field-note">Current photo: <a href="<?php echo htmlspecialchars(asset_url($user['photo'])); ?>" target="_blank">View</a></p>
                    <?php endif; ?>
                    <p class="field-note">Allowed: JPG, PNG, GIF. Max 2MB.</p>
                </div>
                <div>
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                </div>
                <div>
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                </div>
                <div>
                    <label for="course">Course</label>
                    <input type="text" id="course" name="course" value="<?php echo htmlspecialchars($user['course'] ?? ''); ?>">
                </div>
                <div>
                    <label for="course_level">Year Level</label>
                    <select id="course_level" name="course_level" required>
                        <option value="1" <?php echo $user['course_level'] === '1' ? 'selected' : ''; ?>>1st Year</option>
                        <option value="2" <?php echo $user['course_level'] === '2' ? 'selected' : ''; ?>>2nd Year</option>
                        <option value="3" <?php echo $user['course_level'] === '3' ? 'selected' : ''; ?>>3rd Year</option>
                        <option value="4" <?php echo $user['course_level'] === '4' ? 'selected' : ''; ?>>4th Year</option>
                    </select>
                </div>
                <div>
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
                </div>
                <div>
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>" placeholder="Cebu City">
                </div>
                <button type="submit" class="btn-student-primary">Save Changes</button>
            </form>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
