<?php
require_once 'db.php';

header('Content-Type: application/json');

// Redis connection (optional)
$redis = null;
try {
    if (class_exists('Redis')) {
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
    }
} catch (Exception $e) {
    $redis = null;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $userId = $_GET['id'];
    
    try {
        $stmt = $pdo->prepare("SELECT id, username, email, age, dob, contact, created_at FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Sanitize output data
            $user['username'] = htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8');
            $user['email'] = htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8');
            echo json_encode(['success' => true, 'user' => $user]);
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found']);
        }
    } catch(PDOException $e) {
        error_log("Profile error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}
?>