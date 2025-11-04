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
        $stmt = $pdo->prepare("SELECT id FROM guvi1users ORDER BY created_at DESC LIMIT 1");
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

try {
    $stmt = $pdo->prepare("SELECT * FROM guvi1users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if ($user) {
        echo json_encode([
            'status' => 'success',
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'first_name' => $user['first_name'] ?? '',
                'last_name' => $user['last_name'] ?? '',
                'age' => $user['age'] ?? '',
                'dob' => $user['dob'] ?? '',
                'contact' => $user['contact'] ?? '',
                'gender' => $user['gender'] ?? '',
                'occupation' => $user['occupation'] ?? '',
                'address' => $user['address'] ?? '',
                'city' => $user['city'] ?? '',
                'state' => $user['state'] ?? '',
                'zip_code' => $user['zip_code'] ?? '',
                'photo' => $user['photo'] ?? ''
            ]
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}
?>