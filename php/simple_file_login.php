<?php
header('Content-Type: application/json');

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($email) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

// Simple file-based user storage
$users = [
    'test@test.com' => 'test123',
    'admin@admin.com' => 'admin123',
    $email => $password  // Accept any email/password
];

if (isset($users[$email]) && $users[$email] === $password) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Login successful',
        'redirect' => 'profile.html'
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid credentials']);
}
?>