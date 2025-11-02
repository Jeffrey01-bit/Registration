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
    
    // Get current photo path before removing
    $stmt = $pdo->prepare("SELECT photo FROM guvi1users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Delete photo file if exists
    if ($user && $user['photo']) {
        $filePath = __DIR__ . '/../' . $user['photo'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
    
    // Remove photo path from database
    $stmt = $pdo->prepare("UPDATE guvi1users SET photo = NULL WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    
    echo json_encode(['status' => 'success', 'message' => 'Photo removed']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to remove photo']);
}
?>