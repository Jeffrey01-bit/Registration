<?php
header('Content-Type: application/json');
require_once 'redis.php';

$token = $_POST['token'] ?? $_GET['token'] ?? '';

if (empty($token)) {
    echo json_encode(['status' => 'error', 'message' => 'No token provided']);
    exit;
}

if (deleteRedisSession($token)) {
    echo json_encode(['status' => 'success', 'message' => 'Logged out successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Logout failed']);
}
?>