<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($email) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

// Set session data
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'testuser';
$_SESSION['email'] = $email;
$_SESSION['logged_in'] = true;

echo json_encode([
    'status' => 'success',
    'message' => 'Login successful',
    'redirect' => 'profile.html'
]);
?>