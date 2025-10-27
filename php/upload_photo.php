<?php
header('Content-Type: application/json');
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$token = $_POST['token'] ?? '';

if (empty($token)) {
    echo json_encode(['status' => 'error', 'message' => 'No token provided']);
    exit;
}

// Get user from token
$user = json_decode(base64_decode($token), true);
if (!$user || !isset($user['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid token']);
    exit;
}

if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== 0) {
    echo json_encode(['status' => 'error', 'message' => 'No file uploaded or upload error']);
    exit;
}

$uploadDir = __DIR__ . '/../uploads/';
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        echo json_encode(['status' => 'error', 'message' => 'Cannot create upload directory']);
        exit;
    }
}

$fileInfo = pathinfo($_FILES['photo']['name']);
$fileExtension = strtolower($fileInfo['extension'] ?? '');
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

if (!in_array($fileExtension, $allowedExtensions)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Only JPG, JPEG, PNG, GIF allowed']);
    exit;
}

if ($_FILES['photo']['size'] > 2000000) { // 2MB limit
    echo json_encode(['status' => 'error', 'message' => 'File too large. Maximum 2MB allowed']);
    exit;
}

// Verify it's actually an image
$imageInfo = getimagesize($_FILES['photo']['tmp_name']);
if ($imageInfo === false) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid image file']);
    exit;
}

$fileName = 'user_' . $user['id'] . '_' . uniqid() . '.' . $fileExtension;
$uploadPath = $uploadDir . $fileName;

if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadPath)) {
    try {
        $stmt = $conn->prepare("UPDATE users SET photo = ? WHERE id = ?");
        $stmt->bind_param("si", $fileName, $user['id']);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'photo' => $fileName]);
        } else {
            unlink($uploadPath);
            echo json_encode(['status' => 'error', 'message' => 'Database update failed']);
        }
        $stmt->close();
    } catch (Exception $e) {
        unlink($uploadPath);
        echo json_encode(['status' => 'error', 'message' => 'Upload failed']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'File upload failed']);
}
?>