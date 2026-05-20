<?php
require_once __DIR__ . '/../includes/app.php';
require_student();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . app_url('student/reservation.php'));
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$lab_id = (int) ($_POST['lab_id'] ?? 0);
$reservation_date = trim($_POST['reservation_date'] ?? '');
$time_slot = trim($_POST['time_slot'] ?? '');
$purpose = trim($_POST['purpose'] ?? '');
$computer_number = trim($_POST['computer_number'] ?? '');

if ($lab_id <= 0 || $reservation_date === '' || $time_slot === '' || $purpose === '') {
    redirect_with_message('student/reservation.php', 'error', 'Please fill in all fields.');
}

if ($computer_number === '') {
    redirect_with_message('student/reservation.php', 'error', 'Please select a workstation before submitting.');
}

$has_computer_column = column_exists($conn, 'reservations', 'computer_number');

if ($has_computer_column) {
    $stmt = $conn->prepare('INSERT INTO reservations (user_id, lab_id, reservation_date, time_slot, purpose, computer_number) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('iissss', $user_id, $lab_id, $reservation_date, $time_slot, $purpose, $computer_number);
} else {
    $stmt = $conn->prepare('INSERT INTO reservations (user_id, lab_id, reservation_date, time_slot, purpose) VALUES (?, ?, ?, ?, ?)');
    $stmt->bind_param('iisss', $user_id, $lab_id, $reservation_date, $time_slot, $purpose);
}

if ($stmt->execute()) {
    // Decrement remaining sessions
    $decrement_stmt = $conn->prepare('UPDATE users SET sitin_remaining = sitin_remaining - 1 WHERE id = ? AND sitin_remaining > 0');
    $decrement_stmt->bind_param('i', $user_id);
    $decrement_stmt->execute();
    
    redirect_with_message('student/reservation.php', 'success', 'Reservation submitted successfully.');
}

redirect_with_message('student/reservation.php', 'error', 'Failed to submit reservation.');
