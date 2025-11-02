<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

try {
    $host = 'localhost';
    $dbname = 'guvi_users';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("SELECT * FROM guvi1users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
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
                'address' => $user['address'] ?? '',
                'city' => $user['city'] ?? '',
                'state' => $user['state'] ?? '',
                'zip_code' => $user['zip_code'] ?? '',
                'occupation' => $user['occupation'] ?? '',
                'company' => $user['company'] ?? '',
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