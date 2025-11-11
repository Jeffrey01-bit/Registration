<?php
header('Content-Type: application/json');
require_once 'redis_session.php';

// Get user ID from session
$token = $_POST['token'] ?? $_GET['token'] ?? '';
if (empty($token)) {
    echo json_encode(['status' => 'error', 'message' => 'No token']);
    exit;
}

$session = RedisSession::get($token);
if (!$session || !isset($session['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid session']);
    exit;
}

$user_id = (int)$session['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
    // UPLOAD PHOTO
    $file = $_FILES['photo'];
    
    if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 5 * 1024 * 1024) {
        echo json_encode(['status' => 'error', 'message' => 'Upload failed']);
        exit;
    }
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid file type']);
        exit;
    }
    
    // Delete old photo
    $old_files = glob(__DIR__ . "/../uploads/user_{$user_id}_*");
    foreach ($old_files as $old_file) {
        @unlink($old_file);
    }
    
    // Save new photo
    $filename = "user_{$user_id}_" . time() . "." . $extension;
    $upload_path = __DIR__ . "/../uploads/" . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        // Save to MongoDB
        $mongo_success = false;
        try {
            require_once __DIR__ . '/../vendor/autoload.php';
            $client = new MongoDB\Client("mongodb://localhost:27017");
            $collection = $client->guvi_profiles->profiles;
            
            $result = $collection->updateOne(
                ['user_id' => $user_id],
                ['$set' => ['photo' => "uploads/$filename", 'updated_at' => new MongoDB\BSON\UTCDateTime()]],
                ['upsert' => true]
            );
            
            $mongo_success = ($result->getModifiedCount() > 0 || $result->getUpsertedCount() > 0);
            error_log("MongoDB photo save for user $user_id: " . ($mongo_success ? 'SUCCESS' : 'FAILED'));
            
        } catch (Exception $e) {
            error_log("MongoDB photo save error for user $user_id: " . $e->getMessage());
        }
        
        // Always save to file as backup
        $profile_file = __DIR__ . "/../profiles/profile_{$user_id}.json";
        $data = file_exists($profile_file) ? json_decode(file_get_contents($profile_file), true) : [];
        $data['photo'] = "uploads/$filename";
        $data['user_id'] = $user_id;
        $data['updated_at'] = date('Y-m-d H:i:s');
        file_put_contents($profile_file, json_encode($data));
        
        echo json_encode([
            'status' => 'success', 
            'photo_path' => "uploads/$filename",
            'mongo_saved' => $mongo_success
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Upload failed']);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove'])) {
    // REMOVE PHOTO
    // Delete physical file
    $old_files = glob(__DIR__ . "/../uploads/user_{$user_id}_*");
    foreach ($old_files as $old_file) {
        @unlink($old_file);
    }
    
    // Remove from MongoDB
    $mongo_success = false;
    try {
        require_once __DIR__ . '/../vendor/autoload.php';
        $client = new MongoDB\Client("mongodb://localhost:27017");
        $collection = $client->guvi_profiles->profiles;
        
        $result = $collection->updateOne(
            ['user_id' => $user_id],
            ['$set' => ['photo' => '', 'updated_at' => new MongoDB\BSON\UTCDateTime()]]
        );
        
        $mongo_success = ($result->getModifiedCount() > 0);
        error_log("MongoDB photo remove for user $user_id: " . ($mongo_success ? 'SUCCESS' : 'FAILED'));
        
    } catch (Exception $e) {
        error_log("MongoDB photo remove error for user $user_id: " . $e->getMessage());
    }
    
    // Remove from file backup
    $profile_file = __DIR__ . "/../profiles/profile_{$user_id}.json";
    if (file_exists($profile_file)) {
        $data = json_decode(file_get_contents($profile_file), true);
        $data['photo'] = '';
        $data['updated_at'] = date('Y-m-d H:i:s');
        file_put_contents($profile_file, json_encode($data));
    }
    
    echo json_encode([
        'status' => 'success', 
        'message' => 'Photo removed',
        'mongo_removed' => $mongo_success
    ]);
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // GET PHOTO
    $photo = '';
    
    // Try MongoDB first
    try {
        require_once __DIR__ . '/../vendor/autoload.php';
        $client = new MongoDB\Client("mongodb://localhost:27017");
        $collection = $client->guvi_profiles->profiles;
        
        $doc = $collection->findOne(['user_id' => $user_id]);
        if ($doc && !empty($doc['photo'])) {
            $photo = $doc['photo'];
        }
    } catch (Exception $e) {
        // Fallback to file
        $profile_file = __DIR__ . "/../profiles/profile_{$user_id}.json";
        if (file_exists($profile_file)) {
            $data = json_decode(file_get_contents($profile_file), true);
            $photo = $data['photo'] ?? '';
        }
    }
    
    echo json_encode(['status' => 'success', 'photo' => $photo]);
    
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>