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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get user ID from token or direct ID
    if (isset($_GET['token'])) {
        $token = sanitizeInput($_GET['token']);
        $session = RedisSession::get($token);
        if (!$session || !isset($session['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid session']);
            exit;
        }
        $userId = (int)$session['user_id'];
    } elseif (isset($_GET['id'])) {
        $userId = (int)$_GET['id'];
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No user specified']);
        exit;
    }
    
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
            
            // Add MongoDB profile data
            if ($profile) {
                foreach ($profile as $key => $value) {
                    if ($key !== '_id' && $key !== 'user_id') {
                        $userData[$key] = $value;
                    }
                }
            } else {
                // Default empty profile
                $userData = array_merge($userData, [
                    'first_name' => '', 'last_name' => '', 'age' => null, 'dob' => '',
                    'contact' => '', 'gender' => '', 'occupation' => '', 'address' => '',
                    'city' => '', 'state' => '', 'zip_code' => ''
                ]);
            }
            
            echo json_encode(['status' => 'success', 'user' => $userData]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
        }
    } catch(Exception $e) {
        error_log("Profile error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
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
            'zip_code' => sanitizeInput($_POST['zipCode'] ?? '')
        ];
        
        // Don't touch photo field - it's handled separately
        
        // Save profile to MongoDB (preserve photo)
        try {
            require_once __DIR__ . '/../vendor/autoload.php';
            $client = new MongoDB\Client("mongodb://localhost:27017");
            $collection = $client->guvi_profiles->profiles;
            
            $profileData['updated_at'] = new MongoDB\BSON\UTCDateTime();
            $result = $collection->updateOne(
                ['user_id' => $userId],
                ['$set' => $profileData],
                ['upsert' => true]
            );
            $result = ($result->getModifiedCount() > 0 || $result->getUpsertedCount() > 0);
        } catch (Exception $e) {
            // Fallback
            $result = MongoProfile::save($userId, $profileData);
        }
        
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