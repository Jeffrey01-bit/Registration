<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

require_once 'mongodb.php';

$username = filter_var(trim($_POST['username'] ?? ''), FILTER_SANITIZE_STRING);
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$password = trim($_POST['password'] ?? '');

if (empty($username) || empty($email) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['status' => 'error', 'message' => 'Password must be at least 6 characters']);
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    $db = getMongoConnection();
    if (!$db) {
        echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
        exit;
    }
    
    $collection = $db->selectCollection('users');
    
    // Check if user exists
    $existingUser = $collection->findOne([
        '$or' => [
            ['email' => $email],
            ['username' => $username]
        ]
    ]);

    if ($existingUser) {
        echo json_encode(['status' => 'error', 'message' => 'User with this email or username already exists']);
        exit;
    }

    // Get next user ID
    $lastUser = $collection->findOne([], ['sort' => ['id' => -1]]);
    $nextId = $lastUser ? $lastUser['id'] + 1 : 1;
    
    // Insert new user
    $result = $collection->insertOne([
        'id' => $nextId,
        'username' => $username,
        'email' => $email,
        'password' => $hashedPassword,
        'created_at' => new MongoDB\BSON\UTCDateTime()
    ]);
    
    if ($result->getInsertedCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Registration successful']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Registration failed']);
    }
    
} catch (Exception $e) {
    error_log("Registration error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Registration failed']);
}
?>
