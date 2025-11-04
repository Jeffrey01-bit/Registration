<?php
session_start();
header('Content-Type: application/json');

require_once 'db.php';

// Get user_id from session
$user_id = $_SESSION['user_id'] ?? null;

// If no session, try to get from most recent user (fallback)
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
        // First check if user exists
        $check_stmt = $pdo->prepare("SELECT id FROM guvi1users WHERE id = ?");
        $check_stmt->execute([$user_id]);
        $user_exists = $check_stmt->fetch();
        
        if (!$user_exists) {
            echo json_encode(['status' => 'error', 'message' => 'User not found', 'user_id' => $user_id]);
            exit;
        }
        
        // Update photo
        $stmt = $pdo->prepare("UPDATE guvi1users SET photo = ? WHERE id = ?");
        $result = $stmt->execute([$photo_path, $user_id]);
        
        // Check if update was successful
        $affected_rows = $stmt->rowCount();
        
        if ($result && $affected_rows > 0) {
            // Verify the update
            $verify_stmt = $pdo->prepare("SELECT photo FROM guvi1users WHERE id = ?");
            $verify_stmt->execute([$user_id]);
            $updated_user = $verify_stmt->fetch();
            
            echo json_encode([
                'status' => 'success', 
                'photo_path' => $photo_path, 
                'user_id' => $user_id,
                'affected_rows' => $affected_rows,
                'db_photo' => $updated_user['photo']
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database update failed', 'affected_rows' => $affected_rows]);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Upload failed']);
}
?>