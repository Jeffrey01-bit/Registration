<?php
header('Content-Type: application/json');
require_once 'mongodb.php';
require_once 'redis.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $token = $_GET['token'] ?? '';
    
    if (empty($token)) {
        echo json_encode(['status' => 'error', 'message' => 'No token provided']);
        exit;
    }
    
    // Validate session with Redis
    $sessionData = getRedisSession($token);
    if (!$sessionData) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid session']);
        exit;
    }
    
    try {
        $db = getMongoConnection();
        if (!$db) {
            echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
            exit;
        }
        
        // Get basic user data from MongoDB
        $collection = $db->selectCollection('users');
        $userData = $collection->findOne(['id' => $sessionData['user_id']]);
        
        if (!$userData) {
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
            exit;
        }
        
        // Get profile data from MongoDB
        $profileData = getMongoProfile($sessionData['user_id']);
        
        // Merge user and profile data
        $userArray = $userData->toArray();
        unset($userArray['_id'], $userArray['password']); // Remove sensitive data
        $completeUser = array_merge($userArray, $profileData);
        
        echo json_encode([
            'status' => 'success', 
            'user' => $completeUser
        ]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    
    if (empty($token)) {
        echo json_encode(['status' => 'error', 'message' => 'No token provided']);
        exit;
    }
    
    // Validate session with Redis
    $sessionData = getRedisSession($token);
    if (!$sessionData) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid session']);
        exit;
    }
    
    $profileData = [
        'user_id' => $sessionData['user_id'],
        'first_name' => $_POST['firstName'] ?? '',
        'last_name' => $_POST['lastName'] ?? '',
        'age' => !empty($_POST['age']) ? (int)$_POST['age'] : null,
        'dob' => $_POST['dob'] ?? '',
        'gender' => $_POST['gender'] ?? '',
        'contact' => $_POST['contact'] ?? '',
        'address' => $_POST['address'] ?? '',
        'city' => $_POST['city'] ?? '',
        'state' => $_POST['state'] ?? '',
        'zip_code' => $_POST['zipCode'] ?? '',
        'occupation' => $_POST['occupation'] ?? '',
        'company' => $_POST['company'] ?? '',
        'updated_at' => new MongoDB\BSON\UTCDateTime()
    ];
    
    try {
        if (updateMongoProfile($sessionData['user_id'], $profileData)) {
            echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Update failed']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Update failed: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>