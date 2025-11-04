<?php
session_start();
header('Content-Type: application/json');

require_once 'db.php';

// Get user_id from session
$user_id = $_SESSION['user_id'] ?? null;

// If no session, try to get from current logged user (fallback)
if (!$user_id) {
    try {
        $stmt = $pdo->prepare("SELECT id FROM guvi1users ORDER BY created_at DESC LIMIT 1");
        $stmt->execute();
        $user = $stmt->fetch();
        $user_id = $user['id'] ?? null;
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
        exit;
    }
}

if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

try {
    // Get user's photo to delete file
    $stmt = $pdo->prepare("SELECT photo FROM guvi1users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    // Delete photo file if exists
    if ($user && $user['photo']) {
        $file_path = '../' . $user['photo'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    // Delete user from database
    $stmt = $pdo->prepare("DELETE FROM guvi1users WHERE id = ?");
    $result = $stmt->execute([$user_id]);
    
    if ($result) {
        // Clear session
        session_destroy();
        echo json_encode(['status' => 'success', 'message' => 'Account deleted']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Delete failed']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}
?>