<?php
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

require_once 'db.php';
require_once 'redis_session.php';

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        throw new Exception('Username and password required');
    }
    
    $stmt = $pdo->prepare("SELECT id, username, email, password FROM guvi1users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $token = bin2hex(random_bytes(32));
        
        // Store session in Redis
        $sessionData = [
            'user_id' => (int)$user['id'],
            'username' => sanitizeInput($user['username']),
            'email' => sanitizeInput($user['email']),
            'logged_in' => true,
            'login_time' => time()
        ];
        
        try {
            RedisSession::store($token, $sessionData);
        } catch (Exception $e) {
            throw new Exception('Session storage failed: ' . $e->getMessage());
        }
        
        echo json_encode([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => (int)$user['id'],
                'username' => sanitizeInput($user['username']),
                'email' => sanitizeInput($user['email'])
            ]
        ]);
    } else {
        throw new Exception('Invalid credentials');
    }
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>