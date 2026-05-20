<?php
require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function normalize_path($path)
{
    return str_replace('\\', '/', (string) $path);
}

function app_base_path()
{
    static $base_path = null;

    if ($base_path !== null) {
        return $base_path;
    }

    $document_root = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
    $app_root = realpath(__DIR__ . '/..');

    if ($document_root && $app_root) {
        $document_root = rtrim(normalize_path($document_root), '/');
        $app_root = normalize_path($app_root);

        if (strpos($app_root, $document_root) === 0) {
            $relative_path = substr($app_root, strlen($document_root));
            $base_path = rtrim($relative_path, '/');
            return $base_path;
        }
    }

    $base_path = '';
    return $base_path;
}

function app_url($path = '')
{
    $base_path = app_base_path();
    $path = ltrim($path, '/');

    if ($path === '') {
        return $base_path !== '' ? $base_path . '/' : '/';
    }

    return ($base_path !== '' ? $base_path . '/' : '/') . $path;
}

function asset_url($path)
{
    return app_url($path);
}

function redirect_with_message($path, $type, $message)
{
    header('Location: ' . app_url($path) . '?' . $type . '=' . urlencode($message));
    exit();
}

function current_page()
{
    return basename($_SERVER['PHP_SELF'] ?? 'index.php');
}

function require_role($role)
{
    if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== $role) {
        redirect_with_message('login.php', 'error', 'Please log in as ' . $role . '.');
    }
}

function get_announcements(mysqli $conn, $limit = 20)
{
    $stmt = $conn->prepare('SELECT id, content, author_name, created_at FROM announcements ORDER BY created_at DESC LIMIT ?');
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function column_exists(mysqli $conn, string $table, string $column)
{
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $table) || !preg_match('/^[a-zA-Z0-9_]+$/', $column)) {
        return false;
    }

    $query = sprintf("SHOW COLUMNS FROM `%s` LIKE '%s'", $table, $column);
    $result = $conn->query($query);
    return $result && $result->num_rows > 0;
}

function get_admin_stats(mysqli $conn)
{
    $row = $conn->query("
        SELECT
            (SELECT COUNT(*) FROM users WHERE role = 'student') AS students_registered,
            (SELECT COUNT(*) FROM sitin_records WHERE status = 'Approved' AND time_out IS NULL) AS currently_sitin,
            (SELECT COUNT(*) FROM sitin_records) AS total_sitin
    ")->fetch_assoc();

    return [
        'students_registered' => (int) ($row['students_registered'] ?? 0),
        'currently_sitin' => (int) ($row['currently_sitin'] ?? 0),
        'total_sitin' => (int) ($row['total_sitin'] ?? 0),
    ];
}

function get_course_chart_data(mysqli $conn)
{
    $result = $conn->query("
        SELECT course AS label, COUNT(*) AS total
        FROM users
        WHERE role = 'student' AND course IS NOT NULL AND course <> ''
        GROUP BY course
        ORDER BY total DESC
        LIMIT 8
    ");

    $labels = [];
    $values = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $labels[] = $row['label'];
            $values[] = (int) $row['total'];
        }
    }

    if (empty($labels)) {
        return [
            'labels' => ['BS IT', 'BS CS', 'BS CIS', 'Other'],
            'values' => [0, 0, 0, 0],
        ];
    }

    return ['labels' => $labels, 'values' => $values];
}

function require_student()
{
    require_role('student');
}

function get_logged_in_student(mysqli $conn)
{
    $user_id = (int) $_SESSION['user_id'];
    $stmt = $conn->prepare('SELECT * FROM users WHERE id = ? AND role = \'student\'');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function year_label($level)
{
    $map = ['1' => '1st Year', '2' => '2nd Year', '3' => '3rd Year', '4' => '4th Year'];
    return $map[(string) $level] ?? 'Not set';
}

function get_student_sitin_summary(mysqli $conn, $user_id)
{
    $stmt = $conn->prepare("
        SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) AS pending,
            SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) AS approved,
            SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) AS completed,
            SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) AS rejected,
            SUM(CASE WHEN status = 'Expired' THEN 1 ELSE 0 END) AS expired
        FROM sitin_records
        WHERE user_id = ?
    ");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function get_leaderboard(mysqli $conn, $limit = 10)
{
    $stmt = $conn->prepare("
        SELECT u.first_name, u.last_name, u.course, COUNT(s.id) AS completed_sessions
        FROM users u
        LEFT JOIN sitin_records s ON s.user_id = u.id AND s.status = 'Completed'
        WHERE u.role = 'student'
        GROUP BY u.id
        ORDER BY completed_sessions DESC, u.last_name ASC
        LIMIT ?
    ");
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
