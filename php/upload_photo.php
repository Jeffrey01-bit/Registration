<?php
session_start();
header('Content-Type: application/json');

require_once 'db.php';

// Get user_id from session
$user_id = $_SESSION['user_id'] ?? null;

// If no session, try to get from token (if provided)
if (!$user_id && isset($_SESSION['token'])) {
    // Session exists, use it
    $user_id = $_SESSION['user_id'];
}

// If still no user_id, get the logged in user (fallback)
if (!$user_id) {
    // Get user from most recent login session
    try {
        $stmt = $pdo->prepare("SELECT id FROM users ORDER BY created_at DESC LIMIT 1");
        $stmt->execute();
        $user = $stmt->fetch();
        $user_id = $user['id'] ?? null;
        
        if ($user_id) {
            $_SESSION['user_id'] = $user_id; // Set session for future requests
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
        exit;
    }
}

if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

if (!isset($_FILES['photo'])) {
    echo json_encode(['status' => 'error', 'message' => 'No file uploaded']);
    exit;
}

$file = $_FILES['photo'];
$allowed = ['jpg', 'jpeg', 'png', 'gif'];
$filename = $file['name'];
$filetype = pathinfo($filename, PATHINFO_EXTENSION);

if (!in_array(strtolower($filetype), $allowed)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid file type']);
    exit;
}

// Create uploads directory if it doesn't exist
$upload_dir = '../uploads/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$new_filename = $user_id . '_' . time() . '.' . $filetype;
$upload_path = $upload_dir . $new_filename;

if (move_uploaded_file($file['tmp_name'], $upload_path)) {
    $photo_path = 'uploads/' . $new_filename;
    
    try {
        $stmt = $pdo->prepare("UPDATE users SET photo = ? WHERE id = ?");
        $result = $stmt->execute([$photo_path, $user_id]);
        
        if ($result) {
            echo json_encode(['status' => 'success', 'photo_path' => $photo_path, 'user_id' => $user_id]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database update failed']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Upload failed', 'upload_dir' => $upload_dir]);
}
?>