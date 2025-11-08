<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $username = sanitizeInput($_POST['username'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($email) || empty($password)) {
            throw new Exception('All required fields must be filled');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }
        
        if (strlen($password) < 6) {
            throw new Exception('Password must be at least 6 characters');
        }
        
        $stmt = $pdo->prepare("SELECT id FROM guvi1users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->rowCount() > 0) {
            throw new Exception('Username or email already exists');
        }
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO guvi1users (username, email, password, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$username, $email, $hashedPassword]);
        
        echo json_encode(['status' => 'success', 'message' => 'Registration successful']);
        
    } catch (Exception $e) {
        error_log("Registration error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
?>