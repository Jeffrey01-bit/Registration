<?php
session_start();
header('Content-Type: application/json');

require_once 'db.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

try {
    // Get current photo path
    $stmt = $pdo->prepare("SELECT photo FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if ($user && $user['photo']) {
        // Delete file if exists
        $file_path = '../' . $user['photo'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    // Remove photo from database
    $stmt = $pdo->prepare("UPDATE users SET photo = NULL WHERE id = ?");
    $stmt->execute([$user_id]);
    
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}
?>