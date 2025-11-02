<?php
header('Content-Type: application/json');
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($email) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

// Read users from JSON file
$usersFile = __DIR__ . '/../data/profiles.json';
if (!file_exists($usersFile)) {
    echo json_encode(['status' => 'error', 'message' => 'No users found']);
    exit;
}

$users = json_decode(file_get_contents($usersFile), true);
if (!$users) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid user data']);
    exit;
}

// Find user by email
$user = null;
foreach ($users as $u) {
    if ($u['email'] === $email) {
        $user = $u;
        break;
    }
}

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    
    echo json_encode([
        'status' => 'success',
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email']
        ]
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid credentials']);
}
?>