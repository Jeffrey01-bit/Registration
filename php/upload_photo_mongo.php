<?php
header('Content-Type: application/json');
require_once 'db.php';
require_once 'redis_session.php';

// Get session token
$token = $_POST['token'] ?? '';
if (empty($token)) {
    echo json_encode(['status' => 'error', 'message' => 'No session token']);
    exit;
}

// Validate session
try {
    $session = RedisSession::get($token);
    if (!$session || !isset($session['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid session']);
        exit;
    }
    $user_id = (int)$session['user_id'];
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Session error']);
    exit;
}

if (!isset($_FILES['photo'])) {
    echo json_encode(['status' => 'error', 'message' => 'No file uploaded']);
    exit;
}

$file = $_FILES['photo'];
$allowed = ['jpg', 'jpeg', 'png', 'gif'];
$filetype = pathinfo($file['name'], PATHINFO_EXTENSION);

if (!in_array(strtolower($filetype), $allowed)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid file type']);
    exit;
}

// Create uploads directory
$upload_dir = __DIR__ . '/../uploads/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$new_filename = 'user_' . $user_id . '_' . uniqid() . '.' . $filetype;
$upload_path = $upload_dir . $new_filename;

if (move_uploaded_file($file['tmp_name'], $upload_path)) {
    $photo_path = 'uploads/' . $new_filename;
    
    try {
        // Direct MongoDB connection
        require_once __DIR__ . '/../vendor/autoload.php';
        $client = new MongoDB\Client("mongodb://localhost:27017");
        $collection = $client->guvi_profiles->profiles;
        
        // Get existing profile
        $existing = $collection->findOne(['user_id' => $user_id]);
        
        // Convert to array properly
        $profileData = [];
        if ($existing) {
            foreach ($existing as $key => $value) {
                if ($key !== '_id') {
                    $profileData[$key] = $value;
                }
            }
        }
        
        // Update photo
        $profileData['user_id'] = $user_id;
        $profileData['photo'] = $photo_path;
        $profileData['updated_at'] = new MongoDB\BSON\UTCDateTime();
        
        // Save to MongoDB
        $result = $collection->replaceOne(
            ['user_id' => $user_id], 
            $profileData, 
            ['upsert' => true]
        );
        
        if ($result->getUpsertedCount() > 0 || $result->getModifiedCount() > 0) {
            echo json_encode([
                'status' => 'success', 
                'photo_path' => $photo_path,
                'message' => 'Photo saved to MongoDB'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'MongoDB save failed']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'MongoDB error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Upload failed']);
}
?>