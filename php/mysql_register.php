<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($username) || empty($email) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
    exit;
}

try {
    $host = 'localhost';
    $dbname = 'guvi_users';
    $db_username = 'root';
    $db_password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM guvi1users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'Email already registered']);
        exit;
    }
    
    // Check if username already exists
    $stmt = $pdo->prepare("SELECT id FROM guvi1users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'Username already taken']);
        exit;
    }
    
    // Insert new user
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO guvi1users (username, email, password, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
    $stmt->execute([$username, $email, $hashedPassword]);
    
    echo json_encode(['status' => 'success', 'message' => 'Registration successful']);
    
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Registration failed']);
}
?>