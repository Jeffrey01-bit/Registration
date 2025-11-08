<?php
require_once 'db.php';
require_once 'redis_session.php';
require_once 'mongodb.php';

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $userId = (int)$_GET['id'];
    
    try {
        // Get basic user data from MySQL
        $stmt = $pdo->prepare("SELECT id, username, email, created_at FROM guvi1users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Get profile data from MongoDB
            $profile = MongoProfile::get($userId);
            
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
            
            echo json_encode(['success' => true, 'user' => $userData]);
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found']);
        }
    } catch(Exception $e) {
        error_log("Profile error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $token = sanitizeInput($_POST['token'] ?? '');
        
        if (empty($token)) {
            throw new Exception('Session token required');
        }
        
        $session = RedisSession::get($token);
        
        if (!$session || !isset($session['user_id'])) {
            throw new Exception('Invalid or expired session');
        }
        
        $userId = (int)$session['user_id'];
        
        // Validate user exists in MySQL
        $stmt = $pdo->prepare("SELECT id FROM guvi1users WHERE id = ?");
        $stmt->execute([$userId]);
        if (!$stmt->fetch()) {
            throw new Exception('User not found');
        }
        
        $profileData = [
            'first_name' => sanitizeInput($_POST['firstName'] ?? ''),
            'last_name' => sanitizeInput($_POST['lastName'] ?? ''),
            'age' => !empty($_POST['age']) ? (int)$_POST['age'] : null,
            'dob' => sanitizeInput($_POST['dob'] ?? ''),
            'contact' => sanitizeInput($_POST['contact'] ?? ''),
            'gender' => sanitizeInput($_POST['gender'] ?? ''),
            'occupation' => sanitizeInput($_POST['occupation'] ?? ''),
            'address' => sanitizeInput($_POST['address'] ?? ''),
            'city' => sanitizeInput($_POST['city'] ?? ''),
            'state' => sanitizeInput($_POST['state'] ?? ''),
            'zip_code' => sanitizeInput($_POST['zipCode'] ?? ''),
            'photo' => sanitizeInput($_POST['photo'] ?? '')
        ];
        
        // Save profile to MongoDB
        $result = MongoProfile::save($userId, $profileData);
        
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);
        } else {
            throw new Exception('Failed to update profile');
        }
        
    } catch(Exception $e) {
        error_log("Profile update error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
?>