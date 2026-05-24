<?php
require_once __DIR__ . '/../includes/app.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . app_url('admin/students.php'));
    exit();
}

$user_id = (int) ($_POST['user_id'] ?? 0);
$student_id = trim($_POST['student_id'] ?? '');
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$course = trim($_POST['course'] ?? '');
$course_level = trim($_POST['course_level'] ?? '');
$email = trim($_POST['email'] ?? '');
$sitin_remaining = (int) ($_POST['sitin_remaining'] ?? 0);

if ($user_id <= 0 || $student_id === '' || $first_name === '' || $last_name === '' || $course === '' || $course_level === '' || $email === '') {
    redirect_with_message('admin/students.php', 'error', 'Please complete all required fields.');
}

if ($sitin_remaining < 0 || $sitin_remaining > 30) {
    redirect_with_message('admin/students.php', 'error', 'Sit-in left must be between 0 and 30.');
}

$student = $conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'student' LIMIT 1");
$student->bind_param('i', $user_id);
$student->execute();

if ($student->get_result()->num_rows === 0) {
    redirect_with_message('admin/students.php', 'error', 'Student account was not found.');
}

$check = $conn->prepare('SELECT id FROM users WHERE student_id = ? AND id <> ? LIMIT 1');
$check->bind_param('si', $student_id, $user_id);
$check->execute();

if ($check->get_result()->num_rows > 0) {
    redirect_with_message('admin/students.php', 'error', 'Student ID is already used by another account.');
}

$stmt = $conn->prepare("
    UPDATE users
    SET student_id = ?, first_name = ?, last_name = ?, course = ?, course_level = ?, email = ?, sitin_remaining = ?
    WHERE id = ?
");
$stmt->bind_param('ssssssii', $student_id, $first_name, $last_name, $course, $course_level, $email, $sitin_remaining, $user_id);

if ($stmt->execute()) {
    redirect_with_message('admin/students.php', 'success', 'Student profile updated successfully.');
}

redirect_with_message('admin/students.php', 'error', 'Failed to update student profile.');
