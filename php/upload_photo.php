<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

if (!isset($_FILES['photo'])) {
    echo json_encode(['status' => 'error', 'message' => 'No file uploaded']);
    exit;
}

try {
    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $file = $_FILES['photo'];
    $fileName = 'user_' . $_SESSION['user_id'] . '_' . uniqid() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
    $uploadPath = $uploadDir . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        // Update database
        $host = 'localhost';
        $dbname = 'guvi_users';
        $username = 'root';
        $password = '';
        
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->prepare("UPDATE guvi1users SET photo = ? WHERE id = ?");
        $stmt->execute(['uploads/' . $fileName, $_SESSION['user_id']]);
        
        echo json_encode(['status' => 'success', 'photo_path' => 'uploads/' . $fileName]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Upload failed']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}
?>