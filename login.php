<?php
$page_title = 'Login | CCS Sit-In Monitoring System';
require_once 'includes/header.php';
?>

<main class="auth-page">
    <div class="auth-card">
        <div class="auth-card-header">
            <h1>Login</h1>
            <p>Sign in to your CCS sit-in account</p>
        </div>

        <form class="auth-form" action="<?php echo app_url('auth/login.php'); ?>" method="POST">
            <div class="form-group">
                <label for="student_id">Student ID</label>
                <input type="text" id="student_id" name="student_id" placeholder="2024-0001" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit" class="btn-submit">Login</button>
        </form>

        <p class="auth-footer-text">
            Don't have an account? <a href="<?php echo app_url('register.php'); ?>">Register</a>
        </p>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
