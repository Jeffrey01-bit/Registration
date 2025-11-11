<?php
header('Content-Type: application/json');
require_once 'db.php';
require_once 'redis_session.php';
require_once 'mongodb.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

$token = $_POST['token'] ?? '';
if (empty($token)) {
    echo json_encode(['status' => 'error', 'message' => 'No session token']);
    exit;
}

try {
    $session = RedisSession::get($token);
    if (!$session || !isset($session['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid session']);
        exit;
    }
    
    $user_id = (int)$session['user_id'];
    
    // Delete user photos
    $photo_files = glob(__DIR__ . "/../uploads/user_{$user_id}_*");
    foreach ($photo_files as $file) {
        @unlink($file);
    }
    
    // Delete from MongoDB
    try {
        require_once __DIR__ . '/../vendor/autoload.php';
        $client = new MongoDB\Client("mongodb://localhost:27017");
        $collection = $client->guvi_profiles->profiles;
        $collection->deleteOne(['user_id' => $user_id]);
    } catch (Exception $e) {
        // Continue even if MongoDB fails
    }
    
    // Delete profile file fallback
    $profile_file = __DIR__ . "/../profiles/profile_{$user_id}.json";
    if (file_exists($profile_file)) {
        @unlink($profile_file);
    }
    
    // Delete from MySQL
    $stmt = $pdo->prepare("DELETE FROM guvi1users WHERE id = ?");
    $result = $stmt->execute([$user_id]);
    
    if ($result) {
        // Delete session
        RedisSession::delete($token);
        echo json_encode(['status' => 'success', 'message' => 'Account deleted completely']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete account']);
    }
    
} catch (Exception $e) {
    error_log("Delete account error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}
?>