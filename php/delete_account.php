<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=guvi_users", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("DELETE FROM guvi1users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    
    session_destroy();
    
    echo json_encode(['status' => 'success', 'message' => 'Account deleted']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}
?>