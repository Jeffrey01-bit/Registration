<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');



// Redis connection (optional)
$redis = null;
try {
    if (class_exists('Redis')) {
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
    }
} catch (Exception $e) {
    $redis = null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $age = $_POST['age'] ?? null;
    $dob = $_POST['dob'] ?? null;
    $contact = $_POST['contact'] ?? null;

    if (empty($username) || empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'All required fields must be filled']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id FROM guvi1users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Username or email already exists']);
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO guvi1users (username, email, password, age, dob, contact) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$username, $email, $hashedPassword, $age, $dob, $contact]);
        
        echo json_encode(['status' => 'success', 'message' => 'Registration successful']);
    } catch(PDOException $e) {
        error_log("Registration error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Registration failed']);
    }
}
?>