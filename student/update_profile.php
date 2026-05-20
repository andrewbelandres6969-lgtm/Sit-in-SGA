<?php
require_once __DIR__ . '/../includes/app.php';
require_student();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . app_url('student/edit-profile.php'));
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$user = get_logged_in_student($conn);

$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$course = trim($_POST['course'] ?? '');
$course_level = trim($_POST['course_level'] ?? '');
$email = trim($_POST['email'] ?? '');
$address = trim($_POST['address'] ?? '');

$photo_path = null;
if (!empty($_FILES['photo']['name'])) {
    if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        redirect_with_message('student/edit-profile.php', 'error', 'Failed to upload file.');
    }

    $allowed_mimes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
    ];

    $tmp_name = $_FILES['photo']['tmp_name'];
    $detected_type = mime_content_type($tmp_name);

    if (!isset($allowed_mimes[$detected_type])) {
        redirect_with_message('student/edit-profile.php', 'error', 'Only JPG, PNG, and GIF images are allowed.');
    }

    if ($_FILES['photo']['size'] > 2 * 1024 * 1024) {
        redirect_with_message('student/edit-profile.php', 'error', 'Image must be 2MB or smaller.');
    }

    $extension = $allowed_mimes[$detected_type];
    $upload_dir = __DIR__ . '/../assets/uploads/students/';
    if (!is_dir($upload_dir) && !mkdir($upload_dir, 0755, true) && !is_dir($upload_dir)) {
        redirect_with_message('student/edit-profile.php', 'error', 'Unable to create upload directory.');
    }

    $filename = 'student_' . $user_id . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
    $destination = $upload_dir . $filename;

    if (!move_uploaded_file($tmp_name, $destination)) {
        redirect_with_message('student/edit-profile.php', 'error', 'Failed to save uploaded image.');
    }

    $photo_path = 'assets/uploads/students/' . $filename;

    if (!empty($user['photo'])) {
        $old_photo_file = __DIR__ . '/../' . str_replace('/', DIRECTORY_SEPARATOR, $user['photo']);
        if (file_exists($old_photo_file)) {
            @unlink($old_photo_file);
        }
    }
}

$update_sql = 'UPDATE users SET first_name = ?, last_name = ?, course = ?, course_level = ?, email = ?, address = ?';
$params = [$first_name, $last_name, $course, $course_level, $email, $address];
$types = 'ssssss';

if ($photo_path !== null) {
    $update_sql .= ', photo = ?';
    $params[] = $photo_path;
    $types .= 's';
}

$update_sql .= ' WHERE id = ?';
$params[] = $user_id;
$types .= 'i';

$stmt = $conn->prepare($update_sql);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    $_SESSION['name'] = $first_name . ' ' . $last_name;
    redirect_with_message('student/edit-profile.php', 'success', 'Profile updated successfully.');
}

redirect_with_message('student/edit-profile.php', 'error', 'Failed to update profile.');
