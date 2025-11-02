<?php
header('Content-Type: application/json');
session_start();

// Log all requests for debugging
file_put_contents(__DIR__ . '/debug.log', date('Y-m-d H:i:s') . " - Request received\n", FILE_APPEND);
file_put_contents(__DIR__ . '/debug.log', "POST data: " . print_r($_POST, true) . "\n", FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

file_put_contents(__DIR__ . '/debug.log', "Email: $email, Password: $password\n", FILE_APPEND);

if (empty($email) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

// Accept any email/password for testing
if (!empty($email) && !empty($password)) {
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'testuser';
    $_SESSION['email'] = $email;
    
    file_put_contents(__DIR__ . '/debug.log', "Login successful for test user\n", FILE_APPEND);
    
    echo json_encode([
        'status' => 'success',
        'token' => 'test_token_123',
        'user' => [
            'id' => 1,
            'username' => 'testuser',
            'email' => $email
        ]
    ]);
} else {
    file_put_contents(__DIR__ . '/debug.log', "Invalid credentials\n", FILE_APPEND);
    echo json_encode(['status' => 'error', 'message' => 'Login failed']);
}
?>