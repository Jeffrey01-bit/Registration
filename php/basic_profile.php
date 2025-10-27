<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $token = $_GET['token'] ?? '';
    
    if (empty($token)) {
        echo json_encode(['status' => 'error', 'message' => 'No token provided']);
        exit;
    }
    
    try {
        // Validate Redis session
        $redis = new Predis\Client(['scheme' => 'tcp', 'host' => '127.0.0.1', 'port' => 6379]);
        $sessionData = $redis->get("session:$token");
        
        if (!$sessionData) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid session']);
            exit;
        }
        
        $session = json_decode($sessionData, true);
        
        // Get user from MongoDB
        $client = new MongoDB\Client("mongodb://localhost:27017");
        $db = $client->selectDatabase('guvi_users');
        $userCollection = $db->selectCollection('users');
        $profileCollection = $db->selectCollection('profiles');
        
        $user = $userCollection->findOne(['id' => $session['user_id']]);
        $profile = $profileCollection->findOne(['user_id' => $session['user_id']]);
        
        if (!$user) {
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
            exit;
        }
        
        $userData = $user->toArray();
        unset($userData['_id'], $userData['password']);
        
        if ($profile) {
            $profileData = $profile->toArray();
            unset($profileData['_id']);
            $userData = array_merge($userData, $profileData);
        }
        
        echo json_encode(['status' => 'success', 'user' => $userData]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    
    if (empty($token)) {
        echo json_encode(['status' => 'error', 'message' => 'No token provided']);
        exit;
    }
    
    try {
        // Validate Redis session
        $redis = new Predis\Client(['scheme' => 'tcp', 'host' => '127.0.0.1', 'port' => 6379]);
        $sessionData = $redis->get("session:$token");
        
        if (!$sessionData) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid session']);
            exit;
        }
        
        $session = json_decode($sessionData, true);
        
        // Update profile in MongoDB
        $client = new MongoDB\Client("mongodb://localhost:27017");
        $db = $client->selectDatabase('guvi_users');
        $collection = $db->selectCollection('profiles');
        
        $profileData = [
            'user_id' => $session['user_id'],
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
        
        $result = $collection->replaceOne(
            ['user_id' => $session['user_id']],
            $profileData,
            ['upsert' => true]
        );
        
        echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Update failed']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>