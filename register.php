<?php
$page_title = 'Register | CCS Sit-In Monitoring System';
require_once 'includes/header.php';
?>

<main class="auth-page">
    <div class="auth-card">
        <div class="auth-card-header">
            <h1>Register</h1>
            <p>Create your CCS sit-in account</p>
        </div>

        <form class="auth-form" action="<?php echo app_url('auth/register.php'); ?>" method="POST">
            <div class="form-group">
                <label for="student_id">Student ID</label>
                <input type="text" id="student_id" name="student_id" placeholder="2024-0001" required>
            </div>

            <div class="form-group">
                <label for="course_level">Course Level</label>
                <select id="course_level" name="course_level" required>
                    <option value="">Select Course Level</option>
                    <option value="1">1st Year</option>
                    <option value="2">2nd Year</option>
                    <option value="3">3rd Year</option>
                    <option value="4">4th Year</option>
                </select>
            </div>

            <div class="form-group">
                <label for="course">Course</label>
                <select id="course" name="course" required>
                    <option value="">Select Course</option>
                    <optgroup label="IT / Computer Courses">
                        <option value="BS CS">BS CS — Computer Science</option>
                        <option value="BS IT">BS IT — Information Technology</option>
                        <option value="BS CIS">BS CIS — Computer Information Systems</option>
                    </optgroup>
                    <optgroup label="Engineering">
                        <option value="BSCE">BSCE</option>
                        <option value="BSEE">BSEE</option>
                        <option value="BSME">BSME</option>
                        <option value="BSECE">BSECE</option>
                    </optgroup>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" placeholder="First Name" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" placeholder="Last Name" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="name@uc.edu.ph" required>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" placeholder="Cebu City">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
            </div>

            <button type="submit" class="btn-submit">Create Account</button>
        </form>

        <p class="auth-footer-text">
            Already have an account? <a href="<?php echo app_url('login.php'); ?>">Login</a>
        </p>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
