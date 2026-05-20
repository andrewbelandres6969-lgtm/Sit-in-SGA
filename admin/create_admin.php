<?php
/**
 * Run once: http://localhost/Sit-in-SGA/admin/create_admin.php
 * Default login: admin / admin123
 */
require_once __DIR__ . '/../config.php';

$student_id = 'admin';
$hashed = password_hash('admin123', PASSWORD_DEFAULT);

$check = $conn->prepare("SELECT id FROM users WHERE student_id = ?");
$check->bind_param('s', $student_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    $stmt = $conn->prepare("UPDATE users SET password = ?, role = 'admin' WHERE student_id = ?");
    $stmt->bind_param('ss', $hashed, $student_id);
    $stmt->execute();
    echo 'Admin account updated. Login: admin / admin123';
} else {
    $stmt = $conn->prepare("
        INSERT INTO users (student_id, first_name, last_name, course_level, course, email, password, role, sitin_remaining)
        VALUES ('admin', 'System', 'Administrator', 'N/A', 'Admin', 'admin@ccs.com', ?, 'admin', 30)
    ");
    $stmt->bind_param('s', $hashed);
    $stmt->execute();
    echo 'Admin account created. Login: admin / admin123';
}
