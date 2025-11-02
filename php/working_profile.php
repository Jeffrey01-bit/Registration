<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

echo json_encode([
    'status' => 'success',
    'user' => [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'email' => $_SESSION['email'],
        'first_name' => 'Test',
        'last_name' => 'User',
        'age' => 25,
        'contact' => '+1234567890',
        'city' => 'Test City',
        'occupation' => 'Developer'
    ]
]);
?>