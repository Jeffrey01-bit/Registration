<?php
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

require_once 'db.php';
require_once 'redis_session.php';
require_once 'mongodb.php';

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Get user_id from localStorage token (sent via AJAX)
$token = $_GET['token'] ?? $_POST['token'] ?? '';

if (empty($token)) {
    // Try to get from Authorization header
    $headers = getallheaders();
    $token = $headers['Authorization'] ?? '';
    if (strpos($token, 'Bearer ') === 0) {
        $token = substr($token, 7);
    }
}

if (empty($token)) {
    echo json_encode(['status' => 'error', 'message' => 'No session token provided']);
    exit;
}

try {
    // Get session from Redis
    $session = RedisSession::get($token);
    
    if (!$session || !isset($session['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid or expired session']);
        exit;
    }
    
    $user_id = (int)$session['user_id'];
    
    // Get basic user data from MySQL
    $stmt = $pdo->prepare("SELECT id, username, email, created_at FROM guvi1users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
        exit;
    }
    
    // Get profile data from MongoDB
    $profile = MongoProfile::get($user_id);
    
    $userData = [
        'id' => (int)$user['id'],
        'username' => sanitizeInput($user['username']),
        'email' => sanitizeInput($user['email']),
        'created_at' => $user['created_at']
    ];
    
    // Merge MongoDB profile data
    if ($profile) {
        $userData = array_merge($userData, [
            'first_name' => sanitizeInput($profile['first_name'] ?? ''),
            'last_name' => sanitizeInput($profile['last_name'] ?? ''),
            'age' => $profile['age'] ?? null,
            'dob' => $profile['dob'] ?? '',
            'contact' => sanitizeInput($profile['contact'] ?? ''),
            'gender' => sanitizeInput($profile['gender'] ?? ''),
            'occupation' => sanitizeInput($profile['occupation'] ?? ''),
            'address' => sanitizeInput($profile['address'] ?? ''),
            'city' => sanitizeInput($profile['city'] ?? ''),
            'state' => sanitizeInput($profile['state'] ?? ''),
            'zip_code' => sanitizeInput($profile['zip_code'] ?? ''),
            'photo' => sanitizeInput($profile['photo'] ?? '')
        ]);
    } else {
        // Default empty profile
        $userData = array_merge($userData, [
            'first_name' => '', 'last_name' => '', 'age' => null, 'dob' => '',
            'contact' => '', 'gender' => '', 'occupation' => '', 'address' => '',
            'city' => '', 'state' => '', 'zip_code' => '', 'photo' => ''
        ]);
    }
    
    echo json_encode(['status' => 'success', 'user' => $userData]);
    
} catch (Exception $e) {
    error_log("Profile fetch error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}
?>