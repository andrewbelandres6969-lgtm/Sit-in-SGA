<?php
require_once __DIR__ . '/../includes/app.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . app_url('register.php'));
    exit();
}

$student_id = trim($_POST['student_id'] ?? '');
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$course_level = trim($_POST['course_level'] ?? '');
$course = trim($_POST['course'] ?? '');
$email = trim($_POST['email'] ?? '');
$address = trim($_POST['address'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if ($password !== $confirm_password) {
    redirect_with_message('register.php', 'error', 'Passwords do not match.');
}

if (strlen($password) < 6) {
    redirect_with_message('register.php', 'error', 'Password must be at least 6 characters.');
}

$check = $conn->prepare('SELECT id FROM users WHERE student_id = ?');
$check->bind_param('s', $student_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    redirect_with_message('register.php', 'error', 'Student ID already exists.');
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    'INSERT INTO users (student_id, first_name, last_name, course_level, course, email, address, password, role, sitin_remaining)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, \'student\', 30)'
);
$stmt->bind_param(
    'ssssssss',
    $student_id,
    $first_name,
    $last_name,
    $course_level,
    $course,
    $email,
    $address,
    $hashed_password
);

if ($stmt->execute()) {
    redirect_with_message('login.php', 'success', 'Registration successful. Please log in.');
}

redirect_with_message('register.php', 'error', 'Registration failed. Please try again.');
