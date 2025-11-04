<?php
session_start();
header('Content-Type: application/json');

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE guvi1users SET 
        username = ?, 
        first_name = ?, 
        last_name = ?, 
        age = ?, 
        dob = ?, 
        contact = ?, 
        gender = ?, 
        occupation = ?, 
        address = ?, 
        city = ?, 
        state = ?, 
        zip_code = ? 
        WHERE id = ?");
    
    $result = $stmt->execute([
        $_POST['username'] ?: null,
        $_POST['firstName'] ?: null,
        $_POST['lastName'] ?: null,
        $_POST['age'] ?: null,
        $_POST['dob'] ?: null,
        $_POST['contact'] ?: null,
        $_POST['gender'] ?: null,
        $_POST['occupation'] ?: null,
        $_POST['address'] ?: null,
        $_POST['city'] ?: null,
        $_POST['state'] ?: null,
        $_POST['zipCode'] ?: null,
        $user_id
    ]);

    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'Profile updated']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Update failed']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}
?>